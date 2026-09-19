<?php

return [
    'modules' => [
        'dashboard' => ['dashboard.read'],
        'staff' => ['staff.read', 'staff.create', 'staff.edit', 'staff.delete'],
        'roles' => ['roles.read', 'roles.create', 'roles.edit', 'roles.delete'],
        'permissions' => ['permissions.read', 'permissions.create', 'permissions.edit', 'permissions.delete'],
        'users' => ['users.read', 'users.create', 'users.edit', 'users.delete'],
        'plans' => ['plans.read', 'plans.create', 'plans.edit', 'plans.delete'],
        'subscriptions' => ['subscriptions.read', 'subscriptions.create', 'subscriptions.edit', 'subscriptions.delete'],
        'notifications' => ['notifications.read', 'notifications.create', 'notifications.edit', 'notifications.delete'],
        'cms' => ['cms.read', 'cms.create', 'cms.edit', 'cms.delete'],
        'payments' => ['payments.read', 'payments.create', 'payments.edit', 'payments.delete'],
        'settings' => ['settings.read', 'settings.edit'],
        'audit-logs' => ['audit-logs.read'],
        'login-activities' => ['login-activities.read'],
    ],

    'roles' => [
        'Super Admin' => [
            'guard_name' => 'web',
            'permissions' => 'all',
        ],
        'Admin' => [
            'guard_name' => 'web',
            'permissions' => [
                'dashboard.read',
                'staff.read',
                'cms.read',
                'cms.create',
                'cms.edit',
                'cms.delete',
                'settings.read',
            ],
        ],
        'Manager' => [
            'guard_name' => 'web',
            'permissions' => [
                'dashboard.read',
                'staff.read',
                'settings.read',
            ],
        ],
    ],
];
