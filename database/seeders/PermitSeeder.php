<?php
namespace Database\Seeders;

use Modules\Cms\Models\CmsLiveEditorsAccess;
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
            'menu', 'order', 'category', 'categoryName', 'tags',
            'warehouse', 'casier', 'department', 'slip', 'slip_category', 'slip_component',
            'position', 'slip_component', 'slip_template', 'leave_category', 'outwork_category',
            'vacation_category', 'validations', 'employee'
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

        CmsLiveEditorsAccess::updateOrCreate(
            ['user_id' => $admin->id],
            ['status' => 1]
        );

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
