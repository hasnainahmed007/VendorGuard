<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

#[Fillable(['tenant_id', 'email_enabled', 'whatsapp_enabled', 'whatsapp_to'])]
class NotificationSetting extends Model
{
    use BelongsToTenant;

    protected function casts(): array
    {
        return [
            'email_enabled' => 'boolean',
            'whatsapp_enabled' => 'boolean',
        ];
    }

    public static function for(string $tenantId): self
    {
        return static::withoutTenancy()->firstOrCreate(
            ['tenant_id' => $tenantId],
            ['email_enabled' => true, 'whatsapp_enabled' => false]
        );
    }
}
