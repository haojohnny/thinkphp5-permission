<h1 align="center">haojohnny/permission </h1>

<p align="center">基于thinkphp5框架，将用户与权限、角色相关联的工具</p>

## Requires

    php: >=7.0
    topthink/think-migration: ^2.0
    thinkphp: >=5.0

## Installing

```shell
$ composer require haojohnny/permission -vvv

$ mkdir -p ./database/migrations

$ cp ./vendor/haojohnny/permission/config/permission.php ./config/
$ cp ./vendor/haojohnny/permission/database/migrations/20190531110604_create_permission_tables.php ./database/migrations/
```
 
在permission.php的配置文件中设置表名

    'permissions' => 'permissions', # 权限表
    'model_has_permissions' => 'model_has_permissions', # 用户权限表
    'roles' => 'roles', # 角色表
    'model_has_roles' => 'model_has_roles', # 用户角色表
    'role_has_permissions' => 'role_has_permissions'  # 角色权限表 
    

执行数据库迁移
```shell
$ php think migrate:create CreatePermissionTables
```

## Usage

`Roles`和`Permissions`继承自`\think\Model`，传入`name`即可创建对应角色和权限

```php
<?php

use Haojohnny\Permission\Models\Roles;
use Haojohnny\Permission\Models\Permissions;

$role = Roles::create(['name' => 'writer']);
$permission = Permissions::create(['name' => 'edit articles']);

```
而`model`中的`create`方法使用的是`REPLACE INTO`，因此推荐使用`findOrCreate`
```php
<?php

use Haojohnny\Permission\Models\Roles;
use Haojohnny\Permission\Models\Permissions;

$role = Roles::findOrCreate(['name' => 'editor']);
$permission = Permissions::findOrCreate(['name' => 'permission1']);
```

在model中使用`use HasRoles`

```php
<?php

namespace app\index\model;

use think\Model;
use Haojohnny\Permission\Traits\HasRoles;

class User extends Model
{
    use HasRoles;

    // ...
}
```

给用户添加权限
```php
<?php

$user = User::get(1);
// 添加一个权限
$user->givePermission('permission1');

// 添加多个权限
$user->givePermission('permission1', 'permission2');

// 支持数组
$user->givePermission(['permission1', 'permission2']);
$user->givePermission(['permission1', 'permission2'], ['pemission3', 'permission4']);

// 支持字符串和数组混合
$user->givePermission('permission1', ['permission2', 'permission3']);
```

撤销用户权限
```php
<?php

$user = User::get(1);
// 撤销一个权限
$user->revokePermission('permission1');

// 也可以像权限管理那样支持字符串和数组及两种混合形式。
$user->revokePermission('permission1', ['permission2', 'permission2']);
```

为角色分配权限，撤销权限

`HasRoles`中使用了`use HasPermissions`，因此可以使用`givePermission`和`revokePermission`方法
```php
<?php
use Haojohnny\Permission\Models\Roles;

$role = Roles::create(['name' => 'writer']);

// 为角色添加权限
$role->givePermission('权限1');
$role->givePermission('permission1', ['permission2', 'permission2']);

// 为角色撤销权限
$role->revokePermission('permission1', ['permission2', 'permission2']);
```
注意：角色权限和直接权限是相互独立的。

给用户为分配角色
```php
<?php

$user = User::get(1);
// 分配一个角色
$user->assignRole('editor');

// 也支持字符串和数组及两种混合形式。
$user->assignRole('editor1'，['editor2', 'editor3']);
```
给用户撤销角色
```php
<?php

$user = User::get(1);
// 撤销一个角色
$user->revokeRole('editor');

// 也支持字符串和数组及两种混合形式。
$user->revokeRole('editor1', ['editor2', 'editor3']);
```

权限判断

```php
<?php

$user = User::get(1);
$user->hasPermission('permission1');
```

角色判断
```php
<?php
$user = User::get(1);
$user->hasRole('editor1');
```

TODO

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/haojohnny/permission/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/haojohnny/permission/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
