<?php

namespace Modules\HRMS\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class ExportEmployee implements FromView, WithEvents, ShouldAutoSize, WithTitle
{
    use Exportable;

    public $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function title(): string
    {
        return 'Daftar karyawan';
    }

    public function view(): View
    {
        return view('hrms::employment.employees.excel', ['employees' => $this->employees]);
    }

    public function registerEvents(): array
    {
        $max = ($this->employees->count() ?? 0) + 3;
        return [
            AfterSheet::class => function (AfterSheet $event) use ($max) {
                $event->sheet->mergeCells('A1:M1');
                $event->sheet->mergeCells('A2:M2');
                $cellRange = 'A3:M3';
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                            'color' => ['argb' => 'FFFFFFFF'],
                        ],

                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startColor' => [
                            'argb' => 'FFA0A0A0',
                        ],
                        'endColor' => [
                            'argb' => 'FFA0A0A0',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle('A1:M' . $max)->getAlignment()->setWrapText(true);
                $event->sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);
                $event->sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                $event->sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(25);
                $event->sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(25);
                $event->sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                $event->sheet->getColumnDimension('H')->setAutoSize(false)->setWidth(20);
                $event->sheet->getColumnDimension('I')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('J')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('K')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('L')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('M')->setAutoSize(false)->setWidth(35);
            },
        ];
    }
}
