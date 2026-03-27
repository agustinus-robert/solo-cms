<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Account\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class PermitSeeder extends Seeder
{
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $menus = [
            'outlet', 'product', 'adjustment', 'supplier', 'report', 'pos',
            'brand', 'taxrate', 'tier', 'unit', 'category', 'reporting',
            'promotion', 'purchase', 'quotation', 'return', 'sale', 'transfer',
            'role', 'user', 'posting', 'custom', 'postImage', 'postVideo',
            'menu', 'order', 'category', 'categoryName', 'tags', 'quotation',
            'warehouse', 'casier'
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($menus as $menu) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => $action . '_' . $menu,
                    'guard_name' => 'web'
                ]);
            }
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'administrator',
            'guard_name' => 'web'
        ]);

        $outletRole = Role::firstOrCreate([
            'name' => 'outlet',
            'guard_name' => 'web'
        ]);

        $adminRole->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'username' => 'administrator',
                'name' => 'Super Admin',
                'password' => 'password',
            ]
        );

        $admin->syncRoles([$adminRole]);
        $staff = User::firstOrCreate(
            ['email' => 'outlet@mail.com'],
            [
                'username' => 'staff',
                'name' => 'Staff Outlet',
                'password' => 'password',
            ]
        );

        $staff->syncRoles([$outletRole]);
    }
}
