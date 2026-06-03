<?php
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logPath)) {
    echo nl2br(htmlspecialchars(substr(file_get_contents($logPath), -3000)));
} else {
    echo "Log file not found at " . $logPath;
}
