<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Support\Str;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\Core\Models\CompanySalarySlip;
use Modules\HRMS\Enums\DataRecapitulationTypeEnum;
use Modules\HRMS\Models\EmployeeDataRecapitulation;
use Modules\HRMS\Models\EmployeeInsurance;
use Modules\HRMS\Models\EmployeeTax;

class SlipDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $slips = [
            'Slip PPh 21' => [
                'Penghasilan Upah' => [
                    [
                        'name' => 'Gaji Pokok',
                        'currency' => true,
                        'allowance' => 1,
                        'meta' => [
                            'prorate' => 'WORKDAYS', // GP / Hari kerja * Presensi. (1.000.000 / 22 * 22)
                            'default_component' => true,
                            'as_thr' => true,
                            'as_g13' => true,
                            'as_pph' => true
                        ]
                    ],
                    [
                        'name' => 'Tambahan/Kegiatan',
                        'currency' => false,
                        'unit' => 4,
                        'allowance' => null,
                        'operate' => 0,
                        'meta' => [
                            'default' => 0,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::OVERTIME]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->overtime->total_hour'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Jabatan Struktural',
                        'currency' => true,
                        'meta' => [
                            'default' => 0,
                            'as_thr' => true,
                            'as_g13' => true,
                            'as_pph' => true,
                        ]
                    ],
                    [
                        'name' => 'TJ. BPJS',
                        'currency' => true,
                        'allowance' => 7,
                        'meta' => [
                            'default' => 0,
                            'as_pph' => true,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeInsurance::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'whereIn', 'p' => ['price_id', [2, 8]]]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'cmp_price'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Keluarga',
                        'currency' => true,
                        'meta' => [
                            'default' => 0,
                            'description' => 'per bulan',
                            'as_thr' => false,
                            'as_g13' => true,
                            'as_pph' => true
                        ]
                    ],
                    [
                        'name' => 'Sosial',
                        'currency' => true,
                        'meta' => [
                            'default' => 0,
                            'as_thr' => false,
                            'as_g13' => true,
                            'as_pph' => true
                        ]
                    ],
                ],
                'Kegiatan Tambahan' => [
                    [
                        'name' => 'Kehadiran',
                        'currency' => true,
                        'allowance' => 3,
                        'meta' => [
                            'default' => 0,
                            'description' => 'per kehadiran',
                            'as_multiplier' => true,
                            'editable' => true,
                            'as_thr' => true,
                            'as_g13' => true,
                            'as_pph' => true,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::ATTENDANCE]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->attendance_total'
                                    ],
                                ]
                            ],
                        ]
                    ],
                    [
                        'name' => 'Tugas Luar',
                        'currency' => true,
                        'allowance' => 4,
                        'meta' => [
                            'default' => 0,
                            'isset_attachment' => 'outworks',
                            'editable' => true,
                            'as_pph' => true,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::OUTWORK]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']],
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->amount_total'
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                'Potongan' => [
                    [
                        'name' => 'THR',
                        'currency' => true,
                        'allowance' => 7,
                        'operate' => 2,
                        'meta' => [
                            'default' => 0,
                            'as_pph' => true,
                            'as_g13' => false,
                            'as_thr' => false
                        ]
                    ],
                    [
                        'name' => 'Jasa',
                        'currency' => true,
                        'allowance' => 7,
                        'operate' => 2,
                        'meta' => [
                            'default' => 0,
                            'as_pph' => true,
                            'as_g13' => false,
                            'as_thr' => false
                        ]
                    ],
                    [
                        'name' => 'Sosial',
                        'currency' => true,
                        'allowance' => 7,
                        'operate' => 2,
                        'meta' => [
                            'default' => 0,
                            'as_pph' => true,
                            'as_g13' => false,
                            'as_thr' => false
                        ]
                    ],
                    [
                        'name' => 'Angsuran',
                        'currency' => true,
                        'allowance' => 7,
                        'operate' => 2,
                        'meta' => [
                            'default' => 0,
                            'as_pph' => true,
                            'as_g13' => false,
                            'as_thr' => false
                        ]
                    ],
                ],
            ],
            'Slip Reimbursment dan lain-lain' => [
                'Rekapitulasi' => [
                    [
                        'name' => 'Hari Efektif Periode Penggajian',
                        'currency' => false,
                        'unit' => 3,
                        'allowance' => null,
                        'operate' => 0,
                        'meta' => [
                            'default' => 0,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::ATTENDANCE]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']],
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->workdays'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Jumlah Presensi',
                        'currency' => false,
                        'unit' => 5,
                        'allowance' => null,
                        'operate' => 0,
                        'meta' => [
                            'default' => 0,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::ATTENDANCE]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->attendance_total'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Jumlah Keterlambatan',
                        'currency' => false,
                        'unit' => 3,
                        'allowance' => null,
                        'operate' => 0,
                        'meta' => [
                            'default' => 0,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::ATTENDANCE]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->late_total'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Jumlah Cuti',
                        'currency' => false,
                        'unit' => 3,
                        'allowance' => null,
                        'operate' => 0,
                        'meta' => [
                            'default' => 0,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::ATTENDANCE]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->unpresence->vacation->yearly'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Jumlah Izin Periode Ini',
                        'currency' => false,
                        'unit' => 3,
                        'allowance' => null,
                        'operate' => 0,
                        'meta' => [
                            'default' => 0,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::ATTENDANCE]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->unpresence->leave'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Jumlah Lembur Periode Ini',
                        'currency' => false,
                        'unit' => 4,
                        'allowance' => null,
                        'operate' => 0,
                        'meta' => [
                            'default' => 0,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::OVERTIME]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->overtime->total_hour'
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                'Natura dan kenikmatan' => [
                    [
                        'name' => 'Lembur',
                        'currency' => true,
                        'allowance' => 4,
                        'meta' => [
                            'default' => 0,
                            'description' => 'per jam',
                            'as_multiplier' => false,
                            'editable' => true,
                            'as_pph' => true,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::OVERTIME]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->amount_total'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Rapelan Lembur',
                        'currency' => true,
                        'allowance' => 4,
                        'meta' => [
                            'default' => 0,
                            'editable' => true,
                            'as_pph' => true,
                        ]
                    ],
                    [
                        'name' => 'Bonus Bulanan',
                        'currency' => true,
                        'allowance' => 4,
                        'meta' => [
                            'default' => 0,
                            'editable' => true,
                            'as_pph' => true,
                        ]
                    ],
                ],
                'Potongan Non PPh 21' => [
                    [
                        'name' => 'Potongan lainnya',
                        'currency' => true,
                        'allowance' => 99,
                        'operate' => 2,
                        'meta' => [
                            'default' => 0,
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::DEDUCTION]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']],
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->total'
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]
            ],
            'Slip PPh 21 (THR)' => [
                'Penghasilan Upah' => [
                    [
                        'name' => 'Gaji Pokok',
                        'currency' => true,
                        'kd' => 'thr-gaji-pokok',
                        'allowance' => 1,
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'feastday_gp',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::THR]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->feastday_gp->total',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Jabatan',
                        'currency' => true,
                        'kd' => 'thr-tj-jabatan',
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'feastday_tj',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::THR]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->feastday_tj->total',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Profesi',
                        'currency' => true,
                        'kd' => 'thr-tj-profesi',
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'feastday_tp',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::THR]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->feastday_tp->total',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Fungsional',
                        'currency' => true,
                        'kd' => 'thr-tj-fungsional',
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'feastday_fg',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::THR]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->feastday_fg->total',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Masa Kerja',
                        'currency' => true,
                        'kd' => 'thr-tj-masa-kerja',
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'feastday_mk',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::THR]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->feastday_mk->total',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Slip PPh 21 (Gaji ke-13)' => [
                'Penghasilan Upah' => [
                    [
                        'name' => 'Gaji Pokok',
                        'currency' => true,
                        'kd' => 'g13-gaji-pokok',
                        'allowance' => 1,
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'postyear_gp',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::G13]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->postyear_gp->total',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Jabatan',
                        'currency' => true,
                        'kd' => 'g13-tj-jabatan',
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'postyear_tj',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::G13]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->postyear_tj->total',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Profesi',
                        'currency' => true,
                        'kd' => 'g13-tj-profesi',
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'postyear_tp',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::G13]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->postyear_tp->total',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Fungsional',
                        'currency' => true,
                        'kd' => 'g13-tj-fungsional',
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'postyear_tf',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::G13]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->postyear_tf->total',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tj. Masa Kerja',
                        'currency' => true,
                        'kd' => 'g13-tj-masa-kerja',
                        'meta' => [
                            'editable' => true,
                            'as_pph' => true,
                            'kd' => 'postyear_mk',
                            'as_benefits' => [
                                DataRecapitulationTypeEnum::THR,
                                DataRecapitulationTypeEnum::G13
                            ],
                            'algorithm' => [
                                'method' => 'MODEL',
                                'models' => [
                                    EmployeeDataRecapitulation::class => [
                                        'conditions' => [
                                            ['f' => 'where', 'p' => ['type', DataRecapitulationTypeEnum::G13]],
                                            ['f' => 'where', 'p' => ['empl_id', '%CURRENT_EMPL_ID%']],
                                            ['f' => 'where', 'p' => ['start_at', '%START_AT%']],
                                            ['f' => 'where', 'p' => ['end_at', '%END_AT%']]
                                        ],
                                        'action' => 'sum',
                                        'action_column' => 'result->components->postyear_mk->total',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        foreach ($slips as $_slip => $_cs) {
            $slip = CompanySalarySlip::create([
                'name' => $_slip,
                'az' => CompanySalarySlip::count() + 1,
            ]);

            // pendapatan upah, dll
            foreach ($_cs as $_c => $salaries) {
                $category = $slip->categories()->create([
                    'name' => $_c,
                    'az' => $slip->categories()->count() + 1,
                ]);
                // gp, dll
                foreach ($salaries as $s => $salary) {
                    $data = CompanySalarySlipComponent::create([
                        ...$salary,
                        'kd' => isset($salary['kd']) ? $salary['kd'] : ($_c == 'Potongan' ? 'potongan-' : '') . Str::slug($salary['name'].'-'),
                        'slip_id' => $slip->id,
                        'ctg_id' => $category->id
                    ]);
                }
            }
        }

        // Salary default
        $settings = [
            'cmp_payroll_default_components' => json_decode('[{"slip_az":"1","slip_name":"Slip PPh 21","ctg_az":"1","ctg_name":"Penghasilan Upah","component_id":"1","amount":0,"description":null,"name":"Gaji Pokok"},{"slip_az":"1","slip_name":"Slip PPh 21","ctg_az":"1","ctg_name":"Penghasilan Upah","component_id":"3","amount":0,"description":null,"name":"Tj. Jabatan Struktural"},{"slip_az":"1","slip_name":"Slip PPh 21","ctg_az":"1","ctg_name":"Penghasilan Upah","component_id":"4","amount":0,"description":null,"name":"TJ. BPJS"},{"slip_az":"1","slip_name":"Slip PPh 21","ctg_az":"2","ctg_name":"Kegiatan Tambahan","component_id":"7","amount":0,"description":null,"name":"Kehadiran"},{"slip_az":"1","slip_name":"Slip PPh 21","ctg_az":"2","ctg_name":"Kegiatan Tambahan","component_id":"8","amount":0,"description":null,"name":"Tugas Luar"},{"slip_az":"1","slip_name":"Slip PPh 21","ctg_az":"3","ctg_name":"Potongan","component_id":"10","amount":0,"description":null,"name":"Jasa"},{"slip_az":"2","slip_name":"Slip Reimbursment dan lain-lain","ctg_az":"1","ctg_name":"Rekapitulasi","component_id":"13","name":"Hari Efektif Periode Penggajian","amount":0,"description":null},{"slip_az":"2","slip_name":"Slip Reimbursment dan lain-lain","ctg_az":"1","ctg_name":"Rekapitulasi","component_id":"14","name":"Jumlah Presensi","amount":0,"description":null},{"slip_az":"2","slip_name":"Slip Reimbursment dan lain-lain","ctg_az":"1","ctg_name":"Rekapitulasi","component_id":"15","name":"Jumlah Keterlambatan","amount":0,"description":null},{"slip_az":"2","slip_name":"Slip Reimbursment dan lain-lain","ctg_az":"1","ctg_name":"Rekapitulasi","component_id":"16","name":"Jumlah Cuti","amount":0,"description":null},{"slip_az":"2","slip_name":"Slip Reimbursment dan lain-lain","ctg_az":"1","ctg_name":"Rekapitulasi","component_id":"17","name":"Jumlah Izin Periode Ini","amount":0,"description":null},{"slip_az":"2","slip_name":"Slip Reimbursment dan lain-lain","ctg_az":"1","ctg_name":"Rekapitulasi","component_id":"18","name":"Jumlah Lembur Periode Ini","amount":0,"description":null},{"slip_az":"2","slip_name":"Slip Reimbursment dan lain-lain","ctg_az":"2","ctg_name":"Natura dan kenikmatan","component_id":"19","amount":0,"description":null,"name":"Lembur"},{"slip_az":"2","slip_name":"Slip Reimbursment dan lain-lain","ctg_az":"3","ctg_name":"Potongan Non PPh 21","component_id":"22","amount":0,"description":null,"name":"Potongan lainnya"}]'),
            'cmp_payroll_default_postyear_components' => json_decode('[{"slip_az":"4","slip_name":"Slip PPh 21 (Gaji ke-13)","ctg_az":"1","ctg_name":"Penghasilan Upah","component_id":"28","amount":0,"description":null,"name":"Gaji Pokok"},{"slip_az":"4","slip_name":"Slip PPh 21 (Gaji ke-13)","ctg_az":"1","ctg_name":"Penghasilan Upah","component_id":"29","amount":0,"description":null,"name":"Tj. Jabatan"}]'),
            'cmp_payroll_default_feastday_components' => json_decode('[{"slip_az":"3","slip_name":"Slip PPh 21 (THR)","ctg_az":"1","ctg_name":"Penghasilan Upah","component_id":"23","amount":0,"description":null,"name":"Gaji Pokok"},{"slip_az":"3","slip_name":"Slip PPh 21 (THR)","ctg_az":"1","ctg_name":"Penghasilan Upah","component_id":"24","amount":0,"description":null,"name":"Tj. Jabatan"},{"slip_az":"3","slip_name":"Slip PPh 21 (THR)","ctg_az":"1","ctg_name":"Penghasilan Upah","component_id":"27","amount":0,"description":null,"name":"Tj. Masa Kerja"}]')
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key'      => $key,
                'value'    => $value
            ]);
        }
    }
}
