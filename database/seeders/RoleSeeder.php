<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $roles = config('permissions.roles');
        $allPermissions = Permission::where('guard_name', 'web')->pluck('id');

        foreach ($roles as $roleName => $config) {
            $role = Role::firstOrCreate([
                'name' => str($roleName)->lower()->replace(' ', ''),
                'guard_name' => $config['guard_name'] ?? 'web',
            ]);

            $this->command->info('Creating role: '.$roleName);

            if (isset($config['permissions']) && $config['permissions'] === 'all') {
                $role->permissions()->sync($allPermissions);
                $this->command->info('Assigned ALL permissions to '.$roleName);
            } else {
                $permissionNames = $config['permissions'] ?? [];
                $permissions = Permission::where('guard_name', 'web')
                    ->whereIn('name', $permissionNames)
                    ->pluck('id');
                $role->permissions()->sync($permissions);
                $this->command->info('Assigned '.count($permissions).' permissions to '.$roleName);
            }

            $user = User::create([
                'name' => $roleName,
                'role' => str($roleName)->lower()->replace(' ', ''),
                'email' => str($roleName)->lower()->replace(' ', '').'@roarnext.com',
                'password' => bcrypt(str($roleName)->lower()->replace(' ', '')),
            ]);
            $user->assignRole($role);
            $this->command->info('Created user: '.$roleName);
        }

        $this->command->info('All roles seeded successfully.');
    }
}
