# MVP Spec — Vendor Payment Fraud Guardian
*(working name: "VendorGuard" — rename as you like)*

Backend: **Laravel 13 / PHP 8.5** · Consolidates every decision made in our research + planning conversation. Nothing skipped.

---

## 1. Problem Recap

Fraudsters monitor a real vendor email thread for weeks, then send a perfectly-worded "our banking details changed" message timed to a real invoice. No typos, no red flags. 76% of orgs hit by payment fraud in 2025; vendor impersonation is the #1 BEC tactic; average SMB loss per incident is $30K+. Enterprise tools (Abnormal.ai, Ironscales) are too expensive/complex for 5–50 employee businesses. QuickBooks and Xero have both been asked for this exact protection by their own users since 2022 and have explicitly said it's **not on their roadmap**. One small early competitor exists for Xero (OutflowGuard) — validates demand, but leaves QuickBooks and the email-layer largely open.

## 2. Target Customer (ICP)

- US small businesses, 5–50 employees, using **QuickBooks Online** (primary beachhead)
- Secondary: UK/Australia/Canada businesses on **Xero**
- Buyer persona: the business owner, office manager, or the outsourced bookkeeper/accountant who manages their books
- They pay vendors by wire/ACH, have real (if small) vendor relationships, and have no dedicated security staff

## 3. Core Mechanism (the one thing this product must do perfectly)

> **Any change to a vendor's payment details is blocked from taking effect until someone confirms it by calling the phone number on file from the vendor's original onboarding record — never the number in the email/message requesting the change.**

This is a narrow, mechanical control — not a general AI fraud detector. Two entry points feed it:

1. **Accounting-software layer** — watches QuickBooks/Xero vendor records for bank account / routing number edits.
2. **Email layer** — watches connected inboxes (Gmail/Outlook) for language patterns requesting a banking-detail change *before* it ever reaches the accounting software. This is the differentiator OutflowGuard doesn't appear to cover — catching the fraud attempt at the email stage, not just at the accounting-record stage.

## 4. Full Feature List (MVP — nothing deferred that we discussed)

### 4.1 Onboarding & Setup
- Sign up (email + password, or "Sign in with QuickBooks/Xero")
- Connect accounting software (QuickBooks OAuth2, Xero OAuth2) — pull existing vendor list
- Connect email (Gmail API / Microsoft Graph API, read-only scope) — for the email-layer detection
- **Vendor verification wizard**: for each existing vendor, prompt the owner/bookkeeper to confirm/enter the *trusted callback phone number* — this becomes the permanent "original onboarding record" used for all future verification. This step is the foundation the whole product depends on; it must run at day one.
- Invite team members (accountant, bookkeeper, office manager) via the `invitations` flow — an invited bookkeeper who already has an account elsewhere gets a `tenant_user_access` row for this tenant, alongside their own home tenant and any others they already have
- Tenant switcher in the dashboard header for any user who has more than one tenant (home tenant + any granted via `tenant_user_access`) — this is the bookkeeper's normal daily view

### 4.2 Detection Engine
- **Accounting-software watcher**: polls/webhooks on vendor bank account + routing number + address changes
- **Email watcher**: NLP classifier flags messages containing banking-detail-change language, urgency markers, "please update," "new account," etc. — tuned narrowly (this is a rules+NLP hybrid, not a general LLM fraud judge, to keep false positives low and costs predictable)
- **New-vendor risk flag**: any brand-new vendor whose first invoice includes a bank-detail-change-style justification (e.g., "use this account, not our usual one") gets auto-flagged
- Every detection creates an **Incident** record — the central object of the system

### 4.3 Verification Workflow
- Incident dashboard shows: vendor name, what changed, when, source (email/accounting software), and — front and center — **the trusted callback number on file**
- One-click states: "Call now" (shows the number to dial), "Verified — release payment," "Confirmed fraud — block vendor," "Needs more info"
- Payment stays **held** in a "pending verification" state until manually cleared — MVP does not auto-integrate with the actual bank/payment rail (see Out of Scope); it holds the *vendor record* / flags the *invoice* as unsafe to pay, and relies on the human not to pay it until cleared
- Full audit trail per incident: who reviewed it, when, what action taken — this directly answers the exact compliance need Xero's own users were begging for ("our auditors require this")

### 4.4 Notifications
- Email alert the moment an incident is created (MVP default channel)
- WhatsApp alert as an option (reuses your existing Twilio/WhatsApp automation experience) — good differentiator, cheap to add
- Configurable: which team members get notified per incident type

### 4.5 Dashboard (Web)
- **Incidents queue** (open / verified / blocked / dismissed)
- **Vendor directory** — every vendor with their verified callback number, last-verified date, risk history
- **Audit log / compliance report** — exportable (PDF/CSV) for the business's accountant/auditor
- **Team & roles management**
- **Settings** — connected accounts, notification preferences, billing

### 4.6 Mobile (companion only — not full app in MVP)
- Push notification the moment an incident fires
- One-tap "Hold payment" / "Call vendor now" (deep-links to phone dialer with the verified number pre-filled)
- No full dashboard parity on mobile in MVP — by design, per our earlier analysis; build this after web MVP validates, as a lightweight Flutter shell

### 4.7 Billing
- Stripe subscription, single tier for MVP: **$29/month**, up to 2 connected accounting orgs, unlimited vendors
- 14-day free trial, no credit card required for trial (lowers signup friction for a cold SMB audience)

## 5. Data Model — Shared Database, Shared Schema Multi-Tenancy

This follows the pattern you asked for: **one database, one set of tables, every table carries a `tenant_id`** — including `users`. This is the standard "shared database, shared schema" model (as opposed to database-per-tenant or schema-per-tenant): cheapest to run, simplest migrations, and the right choice for an MVP at this scale. In this product, a **tenant is a company** — the terms are used interchangeably below; the `companies` table from the last draft is renamed `tenants` to match the pattern you want.

**The one wrinkle to be upfront about**: a strict "every user has exactly one `tenant_id`" model conflicts with the bookkeeping-firm channel identified in §11 — a bookkeeper needs a *role* across many client tenants, not a lock to one. The fix that keeps both things true: `users.tenant_id` is the user's **home tenant** (a real, required, not-null column, satisfying what you asked for), and a separate `tenant_user_access` table grants additional tenants to users who need them (bookkeepers). A normal single-business owner just has their one home tenant and never touches that extra table.

```
tenants                (id, name, plan, stripe_customer_id, stripe_subscription_id,
                         subscription_status, trial_ends_at, created_at)

users                  (id, tenant_id, name, email, password, created_at)
                        -- tenant_id = home tenant, required not-null FK to tenants

tenant_user_access     (id, tenant_id, user_id, role [owner|admin|bookkeeper|viewer],
                         invited_by_user_id, joined_at)
                        -- grants a user access to a tenant beyond their home one —
                        -- this is what lets one bookkeeper work across many client
                        -- tenants without breaking the tenant_id-on-users rule

invitations            (id, tenant_id, email, role, token, invited_by_user_id,
                         expires_at, accepted_at)

integrations           (id, tenant_id, provider [quickbooks|xero|gmail|outlook],
                         external_account_id, access_token, refresh_token,
                         expires_at, status)
                        -- unique(tenant_id, provider, external_account_id)

vendors                (id, tenant_id, provider, external_id, name,
                         verified_phone, verified_at, verified_by_user_id,
                         current_bank_last4, current_routing_hash, risk_score,
                         created_at, deleted_at)
                        -- unique(tenant_id, provider, external_id)
                        -- soft-deleted only: a vendor with fraud history is never
                        -- hard-deleted, for audit/compliance reasons

vendor_change_log      (id, tenant_id, vendor_id, field_changed,
                         old_value_hash, new_value_hash, source [accounting|email],
                         raw_source_ref, detected_at)

incidents              (id, tenant_id, vendor_id, change_log_id, status
                         [open|verified|blocked|dismissed], severity,
                         assigned_to_user_id, resolved_at, resolution_note,
                         created_at, deleted_at)

notifications_log      (id, tenant_id, incident_id, channel, sent_at, delivered)

audit_trail            (id, tenant_id, incident_id, user_id, action, note, created_at)

integration_sync_log   (id, tenant_id, integration_id, event_type, payload_hash,
                         status, created_at)
                        -- raw webhook/poll debug trail — you will need this the
                        -- first time a customer asks "why didn't it catch this one"
```

**Every table above gets `tenant_id` as the first column after `id`, and a composite index `(tenant_id, id)` or `(tenant_id, <common filter column>)`** — e.g. `(tenant_id, status)` on `incidents`, since almost every real query in this app starts with "for this tenant, give me...". This is the main performance discipline that shared-schema tenancy demands: every index needs `tenant_id` leading, or queries silently start scanning across tenants' data as the table grows.

All bank account/routing numbers stored as **hashed values only** — the product never needs the raw number, only "did it change," which sidesteps a large chunk of PCI/financial-data compliance burden.

**Tenant isolation** (the part that matters most in shared-schema tenancy): apply a global Eloquent scope — a `BelongsToTenant` trait added to every tenant-scoped model — that automatically adds `WHERE tenant_id = ?` to *every* query, using the tenant resolved from the authenticated user's current session (their home tenant, or whichever tenant they've switched to via `tenant_user_access`). No controller or service should ever be trusted to remember to filter by tenant manually — the model layer enforces it unconditionally. For a product handling fraud-sensitive financial data, this is a hard security requirement: write an automated test that logs in as a user from Tenant A, tries to fetch a vendor/incident belonging to Tenant B by ID, and asserts it 404s — run that test in CI on every deploy, not just once.

**Roles**: the `role` enum on `tenant_user_access` (and implicitly on the home-tenant relationship) is enough for MVP. If you need finer-grained permissions later (e.g. "can verify incidents but not manage billing"), Spatie's `laravel-permission` package layers on top of this without a schema rewrite.

## 6. Backend Architecture (Laravel 13 / PHP 8.5, following clean-architecture conventions)

- **Thin controllers** → all detection/verification logic lives in Services (`VendorChangeDetectionService`, `IncidentService`, `VerificationService`)
- **FormRequest** classes for every input (vendor verification, team invites, settings)
- **API Resources** for the dashboard's API responses (Laravel 13 + a lightweight frontend — Vue/Inertia or a simple Blade+Alpine stack both work; recommend **Inertia + Vue** for fast iteration without building a separate SPA)
- **Jobs/Queues** (Redis + Horizon) for:
  - Polling QuickBooks/Xero APIs on a schedule
  - Processing inbound email webhooks (Gmail push notifications / Microsoft Graph subscriptions)
  - Running the NLP classifier on flagged emails (offload — this should never block a request)
- **Global tenant scope**: a `BelongsToTenant` trait applied to every tenant-scoped model, auto-filtering by `tenant_id` — see §5 for why this is non-negotiable in a shared-schema design
- **Policies/Gates** for role-based access (owner vs. bookkeeper vs. read-only team member)
- **Sanctum** for API auth if a public API or the mobile companion app needs token auth
- OAuth token storage: encrypted at rest (Laravel's built-in encrypted cast on the `integrations` table)
- Idempotency on all webhook/job handlers — accounting software webhooks can and will fire duplicates

## 7. Third-Party Integrations Needed

| Integration | Purpose | Notes |
|---|---|---|
| QuickBooks Online API | Vendor + transaction watching | OAuth2, sandbox available for dev |
| Xero API | Same, for UK/AU/CA expansion | OAuth2, well-documented webhooks |
| Gmail API | Email-layer detection | Read-only scope, push notifications via Pub/Sub |
| Microsoft Graph API | Outlook email-layer detection | For Microsoft 365 shops |
| Stripe | Billing | Standard subscriptions |
| Twilio (WhatsApp) | Alerting | You already have this built — reuse directly |
| An LLM/NLP API | Email risk classification | Keep this narrow-scoped and cheap per call — this is a classification task, not a chat task |

## 8. Differentiation vs. OutflowGuard (the existing Xero competitor)

1. **QuickBooks-first** — they're Xero-only
2. **Email-layer detection**, not just accounting-record detection — catches fraud earlier
3. **Built-in verified-callback-number workflow**, not just "pause the payment" — closes the loop instead of leaving the human to figure out next steps
4. **WhatsApp alerting option** — most competitors default to email-only

## 9. Explicit Out of Scope for MVP (v2+)

- Direct bank/payment-rail integration to auto-block the actual money movement (MVP relies on holding the *record* and alerting the human — full payment-rail integration is a v2 goal once you have bank/PSP partnerships)
- Full native mobile app with dashboard parity
- NetSuite / other ERP integrations beyond QuickBooks + Xero
- Automated voice-call verification (having the system place the callback call itself) — interesting future direction, not MVP
- Multi-language support beyond English
- SOC 2 certification (needed eventually to sell to slightly larger customers, not required to launch)

## 10. Build Sequence (realistic milestones)

1. **Weeks 1–2**: Auth, `tenants` table + `tenant_id` on every table, `tenant_user_access` pivot, global tenant scope, QuickBooks OAuth connection, pull + display vendor list
2. **Weeks 3–4**: Vendor verification wizard (capture trusted callback numbers) — this has to exist before detection is useful
3. **Weeks 5–6**: Accounting-software change detection (QuickBooks watcher) → Incident creation → basic dashboard
4. **Weeks 7–8**: Email-layer detection (Gmail integration + NLP classifier) feeding the same Incident pipeline
5. **Week 9**: Notifications (email + WhatsApp), audit trail/export
6. **Week 10**: Stripe billing, trial flow, polish onboarding
7. **Week 11–12**: Closed beta with 10–15 real small businesses (US, QuickBooks users) — recruit via small-business Facebook groups, local accountant/bookkeeper networks, or cold outreach to bookkeeping firms (they manage many clients' books — one bookkeeper signing up could bring several company accounts)
8. **Post-beta**: Xero integration for UK/AU/CA expansion, mobile companion app

## 11. Go-to-Market Recap

- Beachhead: US small businesses on QuickBooks, wire/ACH-heavy B2B payments
- Best acquisition channel: **bookkeeping and accounting firms** that manage multiple small-business clients — one firm adopting this can onboard 10-20 company accounts at once, and audit-trail/compliance reporting is something their own auditors already ask them for
- Expansion: UK/Australia/Canada via Xero, once QuickBooks side is validated
