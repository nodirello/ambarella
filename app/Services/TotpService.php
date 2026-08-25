<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Dependency-free TOTP (RFC 6238) implementation — SHA1, 30s window, 6 digits.
 * Recovery codes are random one-time strings stored hashed.
 */
class TotpService
{
    private const STEP_SECONDS = 30;
    private const WINDOW = 1; // ±1 step tolerance for clock drift

    public function generateSecret(): string
    {
        return $this->encodeBase32(random_bytes(20));
    }

    public function currentCode(string $secret, ?int $timestamp = null): string
    {
        return $this->codeAt($secret, $timestamp ?? time());
    }

    public function verify(string $secret, string $code, ?int $timestamp = null): bool
    {
        $timestamp ??= time();
        $code = trim($code);

        for ($offset = -self::WINDOW; $offset <= self::WINDOW; $offset++) {
            if (hash_equals($this->codeAt($secret, $timestamp + $offset * self::STEP_SECONDS), $code)) {
                return true;
            }
        }

        return false;
    }

    public function otpauthUri(string $secret, string $account, string $issuer = 'AMBARELLA'): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=%d',
            rawurlencode($issuer),
            rawurlencode($account),
            $secret,
            rawurlencode($issuer),
            self::STEP_SECONDS
        );
    }

    public function makeRecoveryCodes(int $count = 8): array
    {
        return collect(range(1, $count))
            ->map(fn () => Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4)))
            ->all();
    }

    private function codeAt(string $secret, int $timestamp): string
    {
        $counter = intdiv($timestamp, self::STEP_SECONDS);
        $binary = pack('N*', 0, $counter);
        $hash = hash_hmac('sha1', $binary, $this->decodeSecret($secret), true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0f;
        $value = (
            ((ord($hash[$offset]) & 0x7f) << 24)
            | ((ord($hash[$offset + 1]) & 0xff) << 16)
            | ((ord($hash[$offset + 2]) & 0xff) << 8)
            | (ord($hash[$offset + 3]) & 0xff)
        ) % 1_000_000;

        return str_pad((string) $value, 6, '0', STR_PAD_LEFT);
    }

    private function encodeBase32(string $binary): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $bits = '';

        foreach (str_split($binary) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        foreach (str_split($bits, 5) as $chunk) {
            $output .= $alphabet[bindec(str_pad($chunk, 5, '0', STR_PAD_RIGHT))];
        }

        return $output;
    }

    private function decodeSecret(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $map = array_flip(str_split($alphabet));
        $bits = '';
        $decoded = '';

        foreach (str_split(strtoupper($secret)) as $char) {
            if (! isset($map[$char])) {
                continue;
            }
            $bits .= str_pad(decbin($map[$char]), 5, '0', STR_PAD_LEFT);
        }

        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $decoded .= chr(bindec($byte));
            }
        }

        return $decoded;
    }
}
