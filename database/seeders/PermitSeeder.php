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

        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $outletRole = Role::firstOrCreate(['name' => 'outlet', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());

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

        $adminEmpl = Employee::updateOrCreate(
            ['user_id' => $superAdmin->id],
            ['joined_at' => Carbon::parse('2023-01-01')]
        );

        $adminContract = EmployeeContract::updateOrCreate(
            ['empl_id' => $adminEmpl->id],
            [
                'kd' => '000/SLCMS/ADMIN/' . date('Y'),
                'contract_id' => 2,
                'work_location' => 1,
                'start_at' => '2023-01-01 00:00:00',
                'end_at' => null,
                'created_by' => $superAdmin->id,
                'updated_by' => $superAdmin->id
            ]
        );

        EmployeePosition::updateOrCreate(
            ['contract_id' => $adminContract->id],
            [
                'empl_id' => $adminEmpl->id,
                'position_id' => 1,
                'start_at' => $adminContract->start_at,
                'end_at' => $adminContract->end_at,
            ]
        );

        $names = [
            'Budi Santoso', 'Siti Aminah', 'Ahmad Hidayat', 'Dewi Lestari', 'Eko Prasetyo',
            'Rina Wijaya', 'Andi Hermawan', 'Maya Sofia', 'Hendra Kusuma', 'Lusi Natalia',
            'Taufik Hidayat', 'Sari Indah', 'Rizky Pratama', 'Putri Utami', 'Dedi Kurniawan',
            'Yanti Puspita', 'Bambang Subianto', 'Ratna Sari', 'fajar Nugraha', 'Ani Suryani'
        ];

        foreach ($names as $key => $name) {
            $username = strtolower(str_replace(' ', '', substr($name, 0, 8))) . ($key + 1);

            $user = User::updateOrCreate(
                ['email' => str_replace(' ', '', strtolower($name)) . '@mail.com'],
                [
                    'username' => $username,
                    'name' => $name,
                    'password' => 'password',
                    'current_team_id' => 2
                ]
            );

            $user->syncRoles([$outletRole]);

            $user->setMeta('profile_sex', ($key % 2 == 0) ? 'male' : 'female');
            $user->setMeta('profile_mariage', 'single');
            $user->setMeta('profile_child', 0);

            $empl = Employee::updateOrCreate(
                ['user_id' => $user->id],
                ['joined_at' => Carbon::parse('2024-01-01')]
            );

            $contract = EmployeeContract::updateOrCreate(
                ['empl_id' => $empl->id],
                [
                    'kd' => ($key + 1) . '/SLCMS/CONT/' . date('Y'),
                    'contract_id' => 2,
                    'work_location' => 1,
                    'start_at' => '2024-01-01 00:00:00',
                    'end_at' => null,
                    'created_by' => $superAdmin->id,
                    'updated_by' => $superAdmin->id
                ]
            );

            EmployeePosition::updateOrCreate(
                ['contract_id' => $contract->id],
                [
                    'empl_id' => $empl->id,
                    'position_id' => ($key < 5) ? 2 : 3,
                    'start_at' => $contract->start_at,
                    'end_at' => $contract->end_at,
                ]
            );
        }

        $this->command->info('PermitSeeder: Super Admin dan 20 Staff berhasil disinkronkan ke HRMS.');
    }
}
