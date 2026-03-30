<?php

namespace Modules\HRMS\Enums;

use Carbon\Carbon;

enum TeacherShiftEnum: int
{
    case SHIFT1 = 1;
    case SHIFT2 = 2;
    case SHIFT3 = 3;
    case SHIFT4 = 4;
    case EXTRA  = 5;

    public function label(): string
    {
        return match ($this) {
            self::SHIFT1 => 'Sesi 1',
            self::SHIFT2 => 'Sesi 2',
            self::SHIFT3 => 'Sesi 3',
            self::SHIFT4 => 'Sesi 4',
            self::EXTRA  => 'Sesi 5'
        };
    }

    public function defaultTime(): array
    {
        return match ($this) {
            self::SHIFT1 => [
                'in' => ['08:00'],
                'out' => ['09:45']
            ],
            self::SHIFT2 => [
                'in' => ['10:15'],
                'out' => ['12:00']
            ],
            self::SHIFT3 => [
                'in' => ['13:00'],
                'out' => ['14:45']
            ],
            self::SHIFT4 => [
                'in' => ['15:15'],
                'out' => ['17:00']
            ],
            self::EXTRA => [
                'in' => ['17:15'],
                'out' => ['19:00']
            ],
        };
    }

    public function activePresenceTime(): array
    {
        return match ($this){
            self::SHIFT1 => [
                'in' => ['06:45'],
                'out' => ['10:00']
            ],
            self::SHIFT2 => [
                'in' => ['10:01'],
                'out' => ['12:30']
            ],
            self::SHIFT3 => [
                'in' => ['12:31'],
                'out' => ['15:00']
            ],
            self::SHIFT4 => [
                'in' => ['15:01'],
                'out' => ['17:15']
            ],
            self::EXTRA => [
                'in' => ['17:16'],
                'out' => ['19:30']
            ]
        };
    }

    public function startTime(): array
    {
        return $this->defaultTime()['in'];
    }

    public function endTime(): array
    {
        return $this->defaultTime()['out'];
    }

    public function activeStartTime(): array
    {
        return $this->activePresenceTime()['in'];
    }

    public function activeEndTime(): array
    {
        return $this->activePresenceTime()['out'];
    }

    public function timeRange(): array
    {
        return [$this->startTime(), $this->endTime()];
    }
    
    public function isWithinPresenceActive(string $time): bool
    {
        // Mengambil jam awal dari array (indeks 0)
        $start = $this->activeStartTime()[0];
        $end = $this->activeEndTime()[0];

        return $time >= $start && $time <= $end;
    }

    public function isTimeBetween(string $time): bool
    {
        return $time >= $this->defaultTime()['in'][0] && $time <= $this->defaultTime()['out'][0];
    }

    public function isWithinPresenceWindow(string $currentTime): bool
    {
        $start = $this->activeStartTime()[0];
        $end   = $this->activeEndTime()[0];

        return $currentTime >= $start && $currentTime <= $end;
    }

    public static function nextShift(?string $time = null): ?self
    {
        $time ??= now()->format('H:i');
        $isWeekend = now()->isWeekend();

        foreach (self::cases() as $shift) {
            if ($isWeekend && in_array($shift, [self::SHIFT4, self::EXTRA])) {
                continue;
            }

            $start = $shift->activeStartTime()[0];
            if ($time < $start) {
                return $shift;
            }
        }

        return null;
    }

    public static function currentShift(?string $time = null): ?self
    {
        $time ??= now()->format('H:i');
        $isWeekend = now()->isWeekend();

        foreach (self::cases() as $shift) {
            if ($isWeekend && in_array($shift, [self::SHIFT4, self::EXTRA])) {
                continue;
            }

            if ($shift->isWithinPresenceWindow($time)) {
                return $shift;
            }
        }

        return null;
    }


    public function teachingHours(): int
    {
        return 2;
    }

    public function adjustTime(array $customTimes = []): array
    {
        $defaultTimes = [
            self::SHIFT1->value => ['in' => '08:00', 'out' => '09:45'],
            self::SHIFT2->value => ['in' => '10:15', 'out' => '12:00'],
            self::SHIFT3->value => ['in' => '13:00', 'out' => '14:45'],
            self::SHIFT4->value => ['in' => '15:15', 'out' => '17:00'],
            self::EXTRA->value  => ['in' => '17:15', 'out' => '19:00'],
        ];

        return [
            'in'  => $customTimes[$this->value]['in']  ?? $defaultTimes[$this->value]['in'],
            'out' => $customTimes[$this->value]['out'] ?? $defaultTimes[$this->value]['out'],
        ];
    }
}
