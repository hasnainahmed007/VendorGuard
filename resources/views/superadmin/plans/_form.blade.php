<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Plan name</label>
        <input name="name" value="{{ old('name', $planName ?? '') }}" placeholder="e.g. Pro" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
    </div>
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Price (USD)</label>
        <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $planPrice ?? '') }}" placeholder="e.g. 12" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
    </div>
</div>
<div class="mt-4">
    <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Description</label>
    <input name="description" value="{{ old('description', $planDescription ?? '') }}" placeholder="e.g. For individuals serious about budgeting" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
</div>
<div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Billing cycle</label>
        <select name="billing_cycle" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
            <option value="monthly" @selected(old('billing_cycle', $planBilling ?? 'monthly') === 'monthly')>Monthly</option>
            <option value="yearly" @selected(old('billing_cycle', $planBilling ?? 'monthly') === 'yearly')>Yearly</option>
        </select>
    </div>
    <div class="flex items-end gap-2 pb-1">
        <input id="is_popular" name="is_popular" type="checkbox" value="1" @checked(old('is_popular', $planPopular ?? false)) class="size-4 accent-[#6e62f2]">
        <label for="is_popular" class="text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Mark as most popular</label>
    </div>
</div>
<div class="mt-4">
    <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Features (one per line)</label>
    <textarea name="features" rows="4" placeholder="Unlimited accounts&#10;Advanced reports&#10;No ads" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">{{ old('features', $planFeatures ?? '') }}</textarea>
</div>
