<?php
namespace Modules\Portal\Excel;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RowSchedule
{
    public $rowData = [];
    public $shiftDate = [];
    public $shiftLine = [];
    public $people = [];

    public static function afterSheet(AfterSheet $event)
    {
        $worksheet = $event->sheet->getDelegate(); // Ambil worksheet aktif
        $instance = $event->getConcernable(); // Akses instance RowSchedule

        $instance->processRows($worksheet); // Proses data menggunakan worksheet
    }

    private function processRows(Worksheet $worksheet)
    {
        $lastRow = $worksheet->getHighestRow();
        $lastColumn = $worksheet->getHighestColumn();
        dd($lastRow);
        $lastColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);

    }
}
