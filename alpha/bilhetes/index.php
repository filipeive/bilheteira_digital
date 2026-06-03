<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ── Subdirectory fix ──────────────────────────────────────────
// On shared hosting where the Laravel public folder lives inside
// a subdirectory (e.g. /alpha/bilhetes/), we must ensure that
// SCRIPT_NAME reflects the real URL path. This allows Laravel's
// URL generator (and consequently Livewire) to produce correct
// URLs with the subdirectory prefix.
$_SERVER['SCRIPT_NAME'] = '/alpha/bilhetes/index.php';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../../bootstrap/app.php';

$app->handleRequest(Request::capture());
