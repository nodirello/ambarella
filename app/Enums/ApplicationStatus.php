<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case Viewed = 'viewed';
    case Shortlisted = 'shortlisted';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Yuborilgan',
            self::Viewed => 'Ko\'rib chiqilmoqda',
            self::Shortlisted => 'Qisqa ro\'yxatda',
            self::Accepted => 'Qabul qilindi',
            self::Rejected => 'Rad etildi',
        };
    }
}
