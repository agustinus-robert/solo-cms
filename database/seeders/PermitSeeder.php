<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Account\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Modules\Cms\Models\CmsLiveEditorsAccess;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeContract;
use Modules\HRMS\Models\EmployeePosition;

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
            'menu', 'order', 'categoryName', 'tags',
            'warehouse', 'casier', 'department', 'slip', 'slip_category', 'slip_component',
            'position', 'slip_template', 'leave_category', 'outwork_category',
            'vacation_category', 'validations', 'employee', 'employee_schedule', 'employee_scanlog',
            'vacation_quota', 'employee_leave', 'employee_vacation', 'isurance_registration', 'isurance_template_bpjs',
            'salaray_approval', 'employee_report', 'employee_report_salary', 'ticketing', 'employee_isurance',
            'employee_overtime', 'employee_outwork', 'employee_loan', 'employee_feastday', 'employee_postyear',
            'employee_payroll_template', 'employee_payroll_calculation', 'employee_ter_taxs', 'employee_income_yearly',
            'employee_validations_salary', 'employee_report_overtime', 'employee_tax',
            'attendance', 'builder', 'configure'
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

        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $outletRole = Role::firstOrCreate(['name' => 'outlet', 'guard_name' => 'web']);
        $supplierRole = Role::firstOrCreate(['name' => 'supplier', 'guard_name' => 'web']);

        $allPermissions = Permission::where('guard_name', 'web')->get();
        $ownerRole->syncPermissions($allPermissions);
        $adminRole->syncPermissions($allPermissions);

        $supplierRole->syncPermissions(Permission::whereIn('name', [
            'view_product', 'view_purchase', 'create_purchase', 'view_brand', 'view_category'
        ])->get());

        // 3. CREATE SUPER ADMIN
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'username' => 'administrator',
                'name' => 'Super Admin',
                'password' => 'password',
                'current_team_id' => 1
            ]
        );
        $superAdmin->syncRoles([$adminRole]);
        CmsLiveEditorsAccess::updateOrCreate(['user_id' => $superAdmin->id], ['status' => 1]);

        $this->seedHRMSData($superAdmin, 1, 'ADMIN', 0, $superAdmin->id);

        $ownerUser = User::updateOrCreate(
            ['email' => 'owner@mail.com'],
            [
                'username' => 'owner',
                'name' => 'Main Owner',
                'password' => 'password',
                'current_team_id' => 1
            ]
        );
        $ownerUser->syncRoles([$ownerRole]);
        $this->seedHRMSData($ownerUser, 1, 'OWNER', 99, $superAdmin->id);

        $names = [
            'Budi Santoso', 'Siti Aminah', 'Ahmad Hidayat', 'Dewi Lestari', 'Eko Prasetyo',
            'Rina Wijaya', // Manager (5)
            'Andi Hermawan', 'Maya Sofia', 'Hendra Kusuma', 'Lusi Natalia',
            'Taufik Hidayat', 'Sari Indah', 'Rizky Pratama', 'Putri Utami', 'Dedi Kurniawan',
            'Yanti Puspita', 'Bambang Subianto', 'Ratna Sari', 'fajar Nugraha', 'Ani Suryani'
        ];

        foreach ($names as $key => $name) {
            $username = strtolower(str_replace(' ', '', substr($name, 0, 8))) . ($key + 1);

            if ($key < 5) {
                $assignedRole = $supplierRole;
                $teamId = 3;
                $posId = 9;
                $typeCode = 'SUPP';
            } elseif ($key == 5) {
                $assignedRole = $adminRole;
                $teamId = 1;
                $posId = 2;
                $typeCode = 'MNG';
            } else {
                $assignedRole = $outletRole;
                $teamId = 2;
                $typeCode = 'CONT';

                if ($key < 11) $posId = 3;
                elseif ($key < 16) $posId = 4;
                else $posId = 5;
            }

            $user = User::updateOrCreate(
                ['email' => str_replace(' ', '', strtolower($name)) . '@mail.com'],
                [
                    'username' => $username,
                    'name' => $name,
                    'password' => 'password',
                    'current_team_id' => $teamId
                ]
            );

            $user->syncRoles([$assignedRole]);

            $user->setMeta('profile_sex', ($key % 2 == 0) ? 'male' : 'female');
            $user->setMeta('profile_mariage', 'single');
            $user->setMeta('profile_child', 0);

            $this->seedHRMSData($user, $posId, $typeCode, $key + 1, $superAdmin->id);
        }

        $this->command->info('PermitSeeder: Selesai! Role Owner telah ditambahkan.');
    }

    private function seedHRMSData($user, $positionId, $type, $index, $creatorId)
    {
        $empl = Employee::updateOrCreate(
            ['user_id' => $user->id],
            ['joined_at' => Carbon::parse('2024-01-01')]
        );

        $contract = EmployeeContract::updateOrCreate(
            ['empl_id' => $empl->id],
            [
                'kd' => $index . '/SLCMS/' . $type . '/' . date('Y'),
                'contract_id' => 2,
                'work_location' => 1,
                'start_at' => '2024-01-01 00:00:00',
                'end_at' => null,
                'created_by' => $creatorId,
                'updated_by' => $creatorId
            ]
        );

        EmployeePosition::updateOrCreate(
            ['contract_id' => $contract->id],
            [
                'empl_id' => $empl->id,
                'position_id' => $positionId,
                'start_at' => $contract->start_at,
                'end_at' => $contract->end_at,
            ]
        );
    }
}
