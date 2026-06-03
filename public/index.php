<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ── Subdirectory fix ──────────────────────────────────────────
// Force SCRIPT_NAME to include the subdirectory so that Laravel's
// URL generator produces correct URLs including for Livewire.
if (str_contains($_SERVER['HTTP_HOST'] ?? '', 'ineds.org')) {
    $_SERVER['SCRIPT_NAME'] = '/alpha/bilhetes/index.php';
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
