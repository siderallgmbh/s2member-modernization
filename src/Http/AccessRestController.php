<?php

declare(strict_types=1);

namespace Siderall\S2Modernization\Http;

use Siderall\S2Modernization\Access\CapabilityGate;
use Siderall\S2Modernization\Support\AuditLogger;
use WP_REST_Request;
use WP_REST_Response;

final class AccessRestController
{
    private CapabilityGate $gate;
    private AuditLogger $logger;

    public function __construct(CapabilityGate $gate, AuditLogger $logger)
    {
        $this->gate = $gate;
        $this->logger = $logger;
    }

    public function registerRoutes(): void
    {
        register_rest_route(
            's2modern/v1',
            '/access',
            [
                'methods' => 'GET',
                'callback' => [$this, 'checkAccess'],
                'permission_callback' => static function (): bool {
                    return is_user_logged_in();
                },
                'args' => [
                    'level' => [
                        'required' => true,
                        'type' => 'integer',
                        'minimum' => 0,
                        'maximum' => 4,
                    ],
                    'ccaps' => [
                        'required' => false,
                        'type' => 'string',
                        'default' => '',
                    ],
                ],
            ]
        );
    }

    public function checkAccess(WP_REST_Request $request): WP_REST_Response
    {
        $level = (int) $request->get_param('level');
        $rawCustomCapabilities = (string) $request->get_param('ccaps');
        $customCapabilities = array_filter(array_map('trim', explode(',', $rawCustomCapabilities)));

        $decision = $this->gate->decide($level, $customCapabilities);
        $this->logger->log($decision, get_current_user_id());

        return new WP_REST_Response(
            [
                'allowed' => $decision->allowed(),
                'reason' => $decision->reason(),
                'checked_capabilities' => $decision->checkedCapabilities(),
            ],
            $decision->allowed() ? 200 : 403
        );
    }
}
