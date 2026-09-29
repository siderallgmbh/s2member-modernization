<?php

declare(strict_types=1);

namespace Siderall\S2Modernization\Access;

use InvalidArgumentException;

final class CapabilityGate
{
    /** @var callable(string): bool */
    private $capabilityChecker;

    /**
     * @param callable(string): bool $capabilityChecker
     */
    public function __construct(callable $capabilityChecker)
    {
        $this->capabilityChecker = $capabilityChecker;
    }

    /**
     * @param string[] $customCapabilities
     */
    public function decide(int $minimumLevel, array $customCapabilities = []): AccessDecision
    {
        if ($minimumLevel < 0 || $minimumLevel > 4) {
            throw new InvalidArgumentException('Membership level must be between 0 and 4.');
        }

        $required = ['access_s2member_level' . $minimumLevel];

        foreach ($customCapabilities as $customCapability) {
            $normalized = $this->normalizeCustomCapability($customCapability);

            if ($normalized !== '') {
                $required[] = 'access_s2member_ccap_' . $normalized;
            }
        }

        foreach ($required as $capability) {
            if (!(bool) call_user_func($this->capabilityChecker, $capability)) {
                return AccessDecision::deny(
                    sprintf('Missing required capability: %s', $capability),
                    $required
                );
            }
        }

        return AccessDecision::allow('All required capabilities are available.', $required);
    }

    private function normalizeCustomCapability(string $capability): string
    {
        $capability = strtolower(trim($capability));
        $capability = preg_replace('/[^a-z0-9_\-]/', '', $capability);

        return is_string($capability) ? $capability : '';
    }
}
