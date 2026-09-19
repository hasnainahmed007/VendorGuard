<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vendor;

class VerificationService
{
    /**
     * Record the vendor verification wizard outcome: the trusted callback
     * phone number becomes the permanent onboarding record every future
     * incident is verified against.
     */
    public function verifyVendor(Vendor $vendor, string $phone, User $verifiedBy): Vendor
    {
        $vendor->verified_phone = $phone;
        $vendor->verified_at = now();
        $vendor->verified_by_user_id = $verifiedBy->getKey();
        $vendor->save();

        return $vendor->refresh();
    }
}
