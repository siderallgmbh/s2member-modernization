<?php
/**
 * Plugin Name: s2Member Modernization Lab
 * Description: Portfolio-oriented modernization of selected s2Member-style access-control concepts.
 * Version: 0.1.0
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$autoload = __DIR__ . '/vendor/autoload.php';

if (is_readable($autoload)) {
    require_once $autoload;
}

if (class_exists(\Siderall\S2Modernization\Infrastructure\Plugin::class)) {
    (new \Siderall\S2Modernization\Infrastructure\Plugin())->boot();
}
