<?php

declare(strict_types=1);

namespace App\Enums;

enum Locale: string
{
    case Uz = 'uz';
    case Ru = 'ru';
    case En = 'en';

    public static function supported(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromAny(?string $value): self
    {
        $normalized = strtolower(substr((string) $value, 0, 2));

        return self::tryFrom($normalized) ?? self::Uz;
    }

    public function label(): string
    {
        return match ($this) {
            self::Uz => "O'zbekcha",
            self::Ru => 'Русский',
            self::En => 'English',
        };
    }
}
