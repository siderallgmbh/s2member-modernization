<?php

declare(strict_types=1);

namespace Siderall\S2Modernization\Access;

final class AccessDecision
{
    private bool $allowed;
    private string $reason;
    /** @var string[] */
    private array $checkedCapabilities;

    /**
     * @param string[] $checkedCapabilities
     */
    private function __construct(bool $allowed, string $reason, array $checkedCapabilities)
    {
        $this->allowed = $allowed;
        $this->reason = $reason;
        $this->checkedCapabilities = array_values($checkedCapabilities);
    }

    /**
     * @param string[] $checkedCapabilities
     */
    public static function allow(string $reason, array $checkedCapabilities): self
    {
        return new self(true, $reason, $checkedCapabilities);
    }

    /**
     * @param string[] $checkedCapabilities
     */
    public static function deny(string $reason, array $checkedCapabilities): self
    {
        return new self(false, $reason, $checkedCapabilities);
    }

    public function allowed(): bool
    {
        return $this->allowed;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    /**
     * @return string[]
     */
    public function checkedCapabilities(): array
    {
        return $this->checkedCapabilities;
    }
}
