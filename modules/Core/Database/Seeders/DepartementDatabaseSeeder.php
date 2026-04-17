<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Modules\Core\Models\CompanyDepartment;
use Modules\Core\Models\CompanyPosition;
use Modules\Core\Models\CompanyPositionType;

class DepartementDatabaseSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['kd' => 'management', 'name' => 'Management'],
            ['kd' => 'front_office', 'name' => 'Front Office'],
            ['kd' => 'kitchen', 'name' => 'Kitchen / Dapur'],
            ['kd' => 'service', 'name' => 'Service / Pelayanan'],
            ['kd' => 'finance', 'name' => 'Keuangan'],
            ['kd' => 'purchasing', 'name' => 'Purchasing / Gudang'],
            ['kd' => 'hr', 'name' => 'HR / Admin'],
            ['kd' => 'security', 'name' => 'Keamanan'],
            ['kd' => 'external', 'name' => 'Pihak Luar / Vendor']
        ];

        $deptMap = [];
        foreach ($departments as $department) {
            $d = CompanyDepartment::create($department);
            $deptMap[$department['kd']] = $d->id;
        }

        $types = [
            ['kd' => 'owner', 'name' => 'Owner'],
            ['kd' => 'management', 'name' => 'Management'],
            ['kd' => 'staff', 'name' => 'Staff'],
            ['kd' => 'support', 'name' => 'Support'],
            ['kd' => 'vendor', 'name' => 'Vendor / Supplier'], // Tambahan tipe vendor
        ];

        $typeMap = [];
        foreach ($types as $type) {
            $t = CompanyPositionType::create($type);
            $typeMap[$type['kd']] = $t->id;
        }

        $positions = [
            // LEVEL 1 (Top)
            [
                'kd' => 'owner',
                'name' => 'Owner',
                'dept_id' => $deptMap['management'],
                'level' => 1,
                'position_type_id' => $typeMap['owner'],
            ],


            [
                'kd' => 'manager',
                'name' => 'Manager',
                'dept_id' => $deptMap['management'],
                'level' => 2,
                'position_type_id' => $typeMap['management'],
                'parent_ids' => [1], // owner
            ],

            [
                'kd' => 'kasir',
                'name' => 'Kasir',
                'dept_id' => $deptMap['front_office'],
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2], // manager
            ],
            [
                'kd' => 'waiter',
                'name' => 'Waiter / Waitress',
                'dept_id' => $deptMap['service'],
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'koki',
                'name' => 'Koki',
                'dept_id' => $deptMap['kitchen'],
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'admin',
                'name' => 'Admin',
                'dept_id' => $deptMap['hr'],
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'finance_staff',
                'name' => 'Finance Staff',
                'dept_id' => $deptMap['finance'],
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'purchasing_staff',
                'name' => 'Purchasing Staff',
                'dept_id' => $deptMap['purchasing'],
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'supplier',
                'name' => 'Supplier / Vendor',
                'dept_id' => $deptMap['external'],
                'level' => 3,
                'position_type_id' => $typeMap['vendor'],
                'parent_ids' => [2], // Di bawah manager atau purchasing
            ],
            [
                'kd' => 'helper_kitchen',
                'name' => 'Helper Kitchen',
                'dept_id' => $deptMap['kitchen'],
                'level' => 4,
                'position_type_id' => $typeMap['support'],
                'parent_ids' => [5], // koki
            ],
            [
                'kd' => 'cleaning_service',
                'name' => 'Cleaning Service',
                'dept_id' => $deptMap['service'],
                'level' => 4,
                'position_type_id' => $typeMap['support'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'security',
                'name' => 'Security',
                'dept_id' => $deptMap['security'],
                'level' => 3,
                'position_type_id' => $typeMap['support'],
                'parent_ids' => [2],
            ],
        ];

        foreach ($positions as $position) {
            $_p = CompanyPosition::create(Arr::only($position, [
                'kd',
                'name',
                'dept_id',
                'position_type_id',
                'level'
            ]));

            if (isset($position['parent_ids'])) {
                $_p->parents()->sync($position['parent_ids']);
            }
        }
    }
}
