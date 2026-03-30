<?php

namespace Modules\HRMS\Enums;

enum DataRecapitulationTypeEnum: int
{
    case ATTENDANCE = 1;
    case OVERTIME = 2;
    case OUTWORK = 3;
    case REIMBURSEMENT = 4;
    case G13 = 5;
    case THR = 6;
    case BONUS = 7;
    case HONOR = 8;
    case DEDUCTION = 9;
    case PPH = 10;
    case COORD = 11;
    case HONOREXTRA = 12;
    case PPH21 = 13;

    /**
     * Get the label accessor with label() object
     */
    public function label(): string
    {
        return match ($this) {
            self::ATTENDANCE => 'Kehadiran, izin, cuti, dan lembur',
            self::OVERTIME => 'Lembur',
            self::OUTWORK => 'Kegiatan lainnya',
            self::REIMBURSEMENT => 'Reimbursement',
            self::G13 => 'Gaji ke-13',
            self::THR => 'Tunjangan Hari Raya',
            self::BONUS => 'Bonus tahunan',
            self::HONOR => 'Rapelan',
            self::DEDUCTION => 'Potongan',
            self::PPH => 'Potongan PPh 21',
            self::COORD => 'Koordinator siswa',
            self::HONOREXTRA => 'Honor Extra',
            self::PPH21 => 'Potongan PPH 21'
        };
    }
}
