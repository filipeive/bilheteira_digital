<?php
// Debug: show server vars and index.php content
header('Content-Type: text/plain');
echo "=== SERVER VARS ===\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'N/A') . "\n";
echo "\n=== INDEX.PHP CONTENT ===\n";
echo file_get_contents(__DIR__ . '/index.php');
echo "\n\n=== .HTACCESS CONTENT ===\n";
echo file_get_contents(__DIR__ . '/.htaccess');
