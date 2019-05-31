<?php

return [
    'tables' => [
        'permissions' => 'permissions',
        'user_has_permissions' => 'user_has_permissions',
        'roles' => 'roles',
        'user_has_roles' => 'user_has_roles',
        'role_has_permission' => 'role_has_permission',
    ],
    'models' => [
        'permissions' => \Haojohnny\Permission\Models\Permissions::class,
        'roles' => \Haojohnny\Permission\Models\Roles::class
    ]
];
