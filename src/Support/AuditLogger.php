<?php

declare(strict_types=1);

namespace Siderall\S2Modernization\Support;

use Siderall\S2Modernization\Access\AccessDecision;

final class AuditLogger
{
    public function log(AccessDecision $decision, int $userId): void
    {
        if (!defined('WP_DEBUG_LOG') || !WP_DEBUG_LOG) {
            return;
        }

        $payload = [
            'component' => 's2member-modernization',
            'user_id' => $userId,
            'allowed' => $decision->allowed(),
            'reason' => $decision->reason(),
            'capabilities' => $decision->checkedCapabilities(),
        ];

        error_log(wp_json_encode($payload)); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
    }
}
