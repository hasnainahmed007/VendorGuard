<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment hold: {{ $incident->vendor->name }} bank details changed</title>
</head>
<body style="font-family: sans-serif; color: #1a1a1a; line-height: 1.5;">
    <h2>Payment hold: {{ $incident->vendor->name }}</h2>
    <p>A payment-detail change was detected and the vendor record is on hold:</p>
    <ul>
        <li><strong>Field:</strong> {{ $incident->changeLog?->field_changed ?? '—' }}</li>
        <li><strong>Source:</strong> {{ $incident->changeLog?->source ?? '—' }}</li>
        <li><strong>Severity:</strong> {{ ucfirst($incident->severity) }}</li>
        <li><strong>Detected:</strong> {{ $incident->created_at->format('j M Y, g:i A') }}</li>
    </ul>
    <p><strong>Call the trusted number on file to verify — never the number in the change request:</strong></p>
    <p style="font-size: 20px;"><a href="tel:{{ $incident->vendor->verified_phone }}">{{ $incident->vendor->verified_phone ?? 'No verified number on file' }}</a></p>
    <p><a href="{{ route('tenant.incidents.show', $incident) }}">Open the incident</a> to release the payment or confirm fraud.</p>
    <p style="color: #666; font-size: 12px;">VendorGuard · incident #{{ $incident->id }}</p>
</body>
</html>
