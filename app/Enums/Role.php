<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case User = 'user';
    case Business = 'business';
    case Moderator = 'moderator';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'Foydalanuvchi',
            self::Business => 'Biznes',
            self::Moderator => 'Moderator',
            self::Admin => 'Administrator',
        };
    }

    public function isStaff(): bool
    {
        return in_array($this, [self::Moderator, self::Admin], true);
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
