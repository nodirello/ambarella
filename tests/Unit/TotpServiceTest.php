<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\TotpService;
use PHPUnit\Framework\TestCase;

class TotpServiceTest extends TestCase
{
    public function test_generated_secret_is_base32(): void
    {
        $totp = new TotpService();
        $secret = $totp->generateSecret();

        $this->assertMatchesRegularExpression('/^[A-Z2-7]{20,}$/', $secret);
    }

    public function test_current_code_is_six_digits(): void
    {
        $totp = new TotpService();
        $code = $totp->currentCode($totp->generateSecret());

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
    }

    public function test_verify_accepts_valid_code(): void
    {
        $totp = new TotpService();
        $secret = $totp->generateSecret();
        $timestamp = time();

        $this->assertTrue($totp->verify($secret, $totp->currentCode($secret, $timestamp), $timestamp));
    }

    public function test_verify_rejects_wrong_code(): void
    {
        $totp = new TotpService();
        $secret = $totp->generateSecret();

        $this->assertFalse($totp->verify($secret, '000000', time()));
    }

    public function test_verify_tolerates_clock_drift(): void
    {
        $totp = new TotpService();
        $secret = $totp->generateSecret();
        $timestamp = time();

        $this->assertTrue($totp->verify($secret, $totp->currentCode($secret, $timestamp + 30), $timestamp));
    }

    public function test_recovery_codes_count_and_format(): void
    {
        $totp = new TotpService();
        $codes = $totp->makeRecoveryCodes(8);

        $this->assertCount(8, $codes);
        foreach ($codes as $code) {
            $this->assertMatchesRegularExpression('/^[A-Z0-9]{4}-[A-Z0-9]{4}$/', $code);
        }
    }
}
