<?php
use Phinx\Migration\AbstractMigration;

class CreatePermissionTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     */
    public function change()
    {
        $default_options = ['default' => 'CURRENT_TIMESTAMP'];
        $update_options = ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'];

        // permissions
        $permissions = $this->table(config('permission.tables.permissions'));
        $permissions->addColumn('name', 'string');
        $permissions->addColumn('create_time', 'timestamp', $default_options);
        $permissions->addColumn('update_time', 'timestamp', $update_options);
        $permissions->create();

        // model_has_permissions
        $model_has_permissions = $this->table(
            config('permission.tables.model_has_permissions'),
            ['id' => false, 'primary_key' => ['permission_id', 'model_id']]
        );
        $model_has_permissions->addColumn('permission_id', 'integer');
        $model_has_permissions->addColumn('model_id', 'integer');
        $model_has_permissions->addColumn('create_time', 'timestamp', $default_options);
        $model_has_permissions->addColumn('update_time', 'timestamp', $update_options);
        $model_has_permissions->create();

        // model_has_roles
        $model_has_roles = $this->table(
            config('permission.tables.model_has_roles'),
            ['id' => false, 'primary_key' => ['role_id', 'model_id']]
        );
        $model_has_roles->addColumn('role_id', 'integer');
        $model_has_roles->addColumn('model_id', 'integer');
        $model_has_roles->addColumn('create_time', 'timestamp', $default_options);
        $model_has_roles->addColumn('update_time', 'timestamp', $update_options);
        $model_has_roles->create();

        // roles
        $roles = $this->table(config('permission.tables.roles'));
        $roles->addColumn('name', 'string');
        $roles->addColumn('create_time', 'timestamp', $default_options);
        $roles->addColumn('update_time', 'timestamp', $update_options);
        $roles->create();

        // role_has_permissions
        $role_has_permissions = $this->table(
            config('permission.tables.role_has_permissions'),
            ['id' => false, 'primary_key' => ['permission_id', 'role_id']]
        );
        $role_has_permissions->addColumn('permission_id', 'integer');
        $role_has_permissions->addColumn('role_id', 'integer');
        $role_has_permissions->addColumn('create_time', 'timestamp', $default_options);
        $role_has_permissions->addColumn('update_time', 'timestamp', $update_options);
        $role_has_permissions->create();
    }

    /**
     * Migrate Up.
     */
    public function up()
    {
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
    }
}
