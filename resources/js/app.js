import $ from "jquery";

// Expandable sidebar groups (Notification, CMS, Payments, Settings).
// Active highlighting is server-rendered via request()->routeIs() in the
// sidebar Blade partial; this only toggles accordion open/close.
$("[data-nav-toggle]").on("click", function () {
  const $parent = $(this).closest("[data-nav-parent]");
  const isOpen = $parent.attr("data-open") === "true";

  $("[data-nav-parent]")
    .attr("data-open", "false")
    .find("[data-nav-children]")
    .addClass("hidden")
    .end()
    .find("[data-nav-chev]")
    .removeClass("rotate-180");

  if (!isOpen) {
    $parent
      .attr("data-open", "true")
      .find("[data-nav-children]")
      .removeClass("hidden")
      .end()
      .find("[data-nav-chev]")
      .addClass("rotate-180");
  }
});

// Toggle switches (visual only).
$("[data-switch]").on("click", function () {
  const $sw = $(this);
  $sw.attr("data-on", $sw.attr("data-on") === "true" ? "false" : "true");
});

// Tab row on the Settings page (jumps to page anchors).
const settingsSections = [
  "general-settings",
  "settings-notification",
  "languages",
  "currencies",
  "privacy-policy",
];

function syncSettingsTabs() {
  const $tabs = $("[data-tab-btn]");
  if (!$tabs.length) {
    return;
  }
  const current = window.location.hash.replace("#sec-", "");
  const index = Math.max(
    settingsSections.indexOf(current),
    0
  );
  $tabs.attr("data-active", "false").eq(index).attr("data-active", "true");
}

$("[data-tab-btn]").on("click", function () {
  const index = $("[data-tab-btn]").index(this);
  window.location.hash = `sec-${settingsSections[index] ?? settingsSections[0]}`;
  syncSettingsTabs();
});

// Dark / light mode toggle (class strategy, persisted).
$("#themeToggle").on("click", () => {
  const isDark = document.documentElement.classList.toggle("dark");
  try {
    localStorage.setItem("cashpilot-theme", isDark ? "dark" : "light");
  } catch (_) {}
});

// Profile dropdown (topbar) — clickable avatar with logout.
function closeProfileDropdown() {
  $("#profileDropdown").addClass("hidden");
  $("#profileMenuButton").attr("aria-expanded", "false");
  $("#profileMenuChevron").removeClass("rotate-180");
}
function openProfileDropdown() {
  $("#profileDropdown").removeClass("hidden");
  $("#profileMenuButton").attr("aria-expanded", "true");
  $("#profileMenuChevron").addClass("rotate-180");
}
$("#profileMenuButton").on("click", function (e) {
  e.stopPropagation();
  const isHidden = $("#profileDropdown").hasClass("hidden");
  if (isHidden) {
    openProfileDropdown();
  } else {
    closeProfileDropdown();
  }
});
$(document).on("click", function (e) {
  if (!$(e.target).closest("#profileDropdown, #profileMenuButton").length) {
    closeProfileDropdown();
  }
});
$(document).on("keydown", function (e) {
  if (e.key === "Escape") {
    closeProfileDropdown();
  }
});

// Tenant users directory (superadmin users page): live data.
const tenantUsersState = { page: 1, lastPage: 1, search: "" };

function escHtml(value) {
  return String(value ?? "").replace(/[&<>"']/g, (c) => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;",
  })[c]);
}

function tenantUserInitials(name) {
  const parts = String(name ?? "?").trim().split(/\s+/);
  return ((parts[0]?.[0] ?? "?") + (parts[1]?.[0] ?? "")).toUpperCase();
}

function tenantUserColor(name) {
  const palette = ["#6e62f2", "#e9a23b", "#d2483c", "#1aa97a", "#5b4fe9", "#9e94f8"];
  let hash = 0;
  for (const ch of String(name ?? "?")) {
    hash = (hash * 31 + ch.charCodeAt(0)) >>> 0;
  }
  return palette[hash % palette.length];
}

function formatJoined(value) {
  if (!value) {
    return "–";
  }
  const date = new Date(String(value).replace(" ", "T"));
  if (Number.isNaN(date.getTime())) {
    return escHtml(value);
  }
  return date.toLocaleDateString("en-GB", { day: "numeric", month: "short", year: "numeric" });
}

function loadTenantUsersStats() {
  $.getJSON("/dashboard/tenant-users/stats")
    .done((stats) => {
      $("#tenantUsersTotal").text(Number(stats.total_users ?? 0).toLocaleString());
      $("#tenantUsersTenants").text(Number(stats.total_tenants ?? 0).toLocaleString());
      $("#tenantUsersNew").text(Number(stats.new_users_7d ?? 0).toLocaleString());
    })
    .fail(() => {});
}

function loadTenantUsers() {
  const $body = $("#tenantUsersBody");
  if (!$body.length) {
    return;
  }
  const params = { page: tenantUsersState.page };
  if (tenantUsersState.search) {
    params.search = tenantUsersState.search;
  }
  $.getJSON("/dashboard/tenant-users", params)
    .done((page) => {
      tenantUsersState.lastPage = page.last_page ?? 1;
      const rows = page.data ?? [];
      if (rows.length === 0) {
        $body.html(
          `<tr><td colspan="4" class="px-5 py-8 text-center text-[13px] text-inksoft">No tenant users found.</td></tr>`
        );
      } else {
        $body.html(
          rows
            .map(
              (u) => `<tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]">
                <td class="whitespace-nowrap px-5 py-[13px] align-middle font-semibold text-ink dark:text-[#ededf5]">${escHtml(u.tenant_company ?? u.tenant_id)}</td>
                <td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full text-[11px] font-bold text-white" style="background:${tenantUserColor(u.name)}">${escHtml(tenantUserInitials(u.name))}</div><div class="font-semibold text-ink dark:text-[#ededf5]">${escHtml(u.name)}</div></div></td>
                <td class="whitespace-nowrap px-5 py-[13px] align-middle text-inksoft">${escHtml(u.email)}</td>
                <td class="whitespace-nowrap px-5 py-[13px] align-middle">${formatJoined(u.joined_at)}</td>
              </tr>`
            )
            .join("")
        );
      }
      $("#tenantUsersRange").text(
        page.total === 0
          ? "No users"
          : `Showing ${page.from}–${page.to} of ${Number(page.total).toLocaleString()} users`
      );
      $("#tenantUsersPrev")
        .prop("disabled", tenantUsersState.page <= 1)
        .toggleClass("opacity-40", tenantUsersState.page <= 1);
      $("#tenantUsersNext")
        .prop("disabled", tenantUsersState.page >= tenantUsersState.lastPage)
        .toggleClass("opacity-40", tenantUsersState.page >= tenantUsersState.lastPage);
    })
    .fail(() => {
      $body.html(
        `<tr><td colspan="4" class="px-5 py-8 text-center text-[13px] text-bad">Could not load tenant users.</td></tr>`
      );
    });
}

let tenantUsersSearchTimer = null;
$("#tenantUsersSearch").on("input", function () {
  clearTimeout(tenantUsersSearchTimer);
  const query = $(this).val().trim();
  tenantUsersSearchTimer = setTimeout(() => {
    tenantUsersState.search = query;
    tenantUsersState.page = 1;
    loadTenantUsers();
  }, 300);
});
$("#tenantUsersPrev").on("click", () => {
  if (tenantUsersState.page > 1) {
    tenantUsersState.page -= 1;
    loadTenantUsers();
  }
});
$("#tenantUsersNext").on("click", () => {
  if (tenantUsersState.page < tenantUsersState.lastPage) {
    tenantUsersState.page += 1;
    loadTenantUsers();
  }
});

// Initial state.
syncSettingsTabs();
loadTenantUsersStats();
loadTenantUsers();
$(window).on("hashchange", () => {
  syncSettingsTabs();
});
