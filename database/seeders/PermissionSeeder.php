<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $modules = config('permissions.modules');

        foreach ($modules as $module => $actions) {
            foreach ($actions as $permissionName) {
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);

                $this->command->info('Created permission: '.$permissionName);
            }
        }

        $this->command->info('All permissions seeded successfully.');
    }
}
