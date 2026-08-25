<?php

declare(strict_types=1);

namespace App\Enums;

enum JobType: string
{
    case FullTime = 'full';
    case PartTime = 'part';
    case Remote = 'remote';
    case Internship = 'internship';

    public function label(): string
    {
        return match ($this) {
            self::FullTime => 'To\'liq stavka',
            self::PartTime => 'Yarim stavka',
            self::Remote => 'Masofaviy',
            self::Internship => 'Amaliyot',
        };
    }
}
