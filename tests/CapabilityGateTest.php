<?php

declare(strict_types=1);

namespace Siderall\S2Modernization\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Siderall\S2Modernization\Access\CapabilityGate;

final class CapabilityGateTest extends TestCase
{
    public function testAllowsUserWithRequiredLevel(): void
    {
        $gate = new CapabilityGate(
            static fn(string $capability): bool => $capability === 'access_s2member_level2'
        );

        $decision = $gate->decide(2);

        self::assertTrue($decision->allowed());
        self::assertSame(['access_s2member_level2'], $decision->checkedCapabilities());
    }

    public function testDeniesUserMissingCustomCapability(): void
    {
        $allowed = ['access_s2member_level1'];

        $gate = new CapabilityGate(
            static fn(string $capability): bool => in_array($capability, $allowed, true)
        );

        $decision = $gate->decide(1, ['premium_reports']);

        self::assertFalse($decision->allowed());
        self::assertStringContainsString('premium_reports', $decision->reason());
    }

    public function testNormalizesCustomCapabilityName(): void
    {
        $seen = [];

        $gate = new CapabilityGate(
            static function (string $capability) use (&$seen): bool {
                $seen[] = $capability;
                return true;
            }
        );

        $gate->decide(0, [' Premium Reports! ']);

        self::assertContains('access_s2member_ccap_premiumreports', $seen);
    }

    public function testRejectsInvalidMembershipLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $gate = new CapabilityGate(static fn(string $capability): bool => true);
        $gate->decide(5);
    }
}
