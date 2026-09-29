<?php

declare(strict_types=1);

namespace Siderall\S2Modernization\Infrastructure;

use Siderall\S2Modernization\Access\CapabilityGate;
use Siderall\S2Modernization\Http\AccessRestController;
use Siderall\S2Modernization\Support\AuditLogger;

final class Plugin
{
    public function boot(): void
    {
        $gate = new CapabilityGate(
            static function (string $capability): bool {
                return current_user_can($capability);
            }
        );

        $controller = new AccessRestController($gate, new AuditLogger());

        add_action(
            'rest_api_init',
            static function () use ($controller): void {
                $controller->registerRoutes();
            }
        );
    }
}
