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
        // ======================
        // DEPARTMENTS
        // ======================
        $departments = [
            ['kd' => 'management', 'name' => 'Management'],
            ['kd' => 'front_office', 'name' => 'Front Office'],
            ['kd' => 'kitchen', 'name' => 'Kitchen / Dapur'],
            ['kd' => 'service', 'name' => 'Service / Pelayanan'],
            ['kd' => 'finance', 'name' => 'Keuangan'],
            ['kd' => 'purchasing', 'name' => 'Purchasing / Gudang'],
            ['kd' => 'hr', 'name' => 'HR / Admin'],
            ['kd' => 'security', 'name' => 'Keamanan'],
        ];

        foreach ($departments as $department) {
            CompanyDepartment::create($department);
        }

        // ======================
        // POSITION TYPES
        // ======================
        $types = [
            ['kd' => 'owner', 'name' => 'Owner'],
            ['kd' => 'management', 'name' => 'Management'],
            ['kd' => 'staff', 'name' => 'Staff'],
            ['kd' => 'support', 'name' => 'Support'],
        ];

        $typeMap = [];
        foreach ($types as $type) {
            $t = CompanyPositionType::create($type);
            $typeMap[$type['kd']] = $t->id;
        }

        // ======================
        // POSITIONS + LEVEL
        // ======================
        $positions = [
            // LEVEL 1 (Top)
            [
                'kd' => 'owner',
                'name' => 'Owner',
                'dept_id' => 1,
                'level' => 1,
                'position_type_id' => $typeMap['owner'],
            ],

            // LEVEL 2
            [
                'kd' => 'manager',
                'name' => 'Manager',
                'dept_id' => 1,
                'level' => 2,
                'position_type_id' => $typeMap['management'],
                'parent_ids' => [1], // owner
            ],

            [
                'kd' => 'kasir',
                'name' => 'Kasir',
                'dept_id' => 2,
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2], // manager
            ],
            [
                'kd' => 'waiter',
                'name' => 'Waiter / Waitress',
                'dept_id' => 4,
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'koki',
                'name' => 'Koki',
                'dept_id' => 3,
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'admin',
                'name' => 'Admin',
                'dept_id' => 7,
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'finance_staff',
                'name' => 'Finance Staff',
                'dept_id' => 5,
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'purchasing_staff',
                'name' => 'Purchasing Staff',
                'dept_id' => 6,
                'level' => 3,
                'position_type_id' => $typeMap['staff'],
                'parent_ids' => [2],
            ],

            // LEVEL 4 (Support / Helper)
            [
                'kd' => 'helper_kitchen',
                'name' => 'Helper Kitchen',
                'dept_id' => 3,
                'level' => 4,
                'position_type_id' => $typeMap['support'],
                'parent_ids' => [5], // koki
            ],
            [
                'kd' => 'cleaning_service',
                'name' => 'Cleaning Service',
                'dept_id' => 4,
                'level' => 4,
                'position_type_id' => $typeMap['support'],
                'parent_ids' => [2],
            ],
            [
                'kd' => 'security',
                'name' => 'Security',
                'dept_id' => 8,
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
