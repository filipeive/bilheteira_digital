<?php
// ============================================================
// SETUP & DEPLOY — Bilheteira Digital · Concerto Renúncia 2026
// Repositório: git@github.com:filipeive/bilheteira_digital.git
// ⚠ APAGAR ESTE FICHEIRO APÓS O SETUP INICIAL!
// ============================================================

$secret = $_GET['key'] ?? '';
if ($secret !== 'renuncia2026') {
    http_response_code(403);
    die('Acesso negado.');
}

// ── CAMINHOS ────────────────────────────────────────────────
$base        = '/home1/fili3528/public_html';          // raiz Laravel
$publicAlpha = '/home1/fili3528/public_html/alpha/bilhetes'; // public path
$repo        = 'https://github.com/filipeive/bilheteira_digital.git';
$branch      = 'main';

// Caminhos dos executáveis no HostGator
$php      = '/usr/local/bin/php';
$composer = '/usr/local/bin/composer';
$git      = '/usr/local/bin/git';

// ── HELPERS ─────────────────────────────────────────────────
function run(string $cmd, string $cwd = ''): array {
    $out = []; $code = 0;
    $prefix = $cwd ? "cd " . escapeshellarg($cwd) . " && " : '';
    exec($prefix . $cmd . ' 2>&1', $out, $code);
    return [
        'cmd'  => $cmd,
        'out'  => implode("\n", $out),
        'code' => $code,
    ];
}

function checkPath(string $label, string $path): string {
    $exists = file_exists($path);
    $type   = is_link($path) ? 'symlink→' . readlink($path)
            : (is_dir($path) ? 'pasta' : (is_file($path) ? 'ficheiro' : 'NÃO EXISTE'));
    $perms  = $exists ? substr(sprintf('%o', fileperms($path)), -4) : '----';
    $icon   = $exists ? '✓' : '✗';
    return "$icon  $label\n    $path\n    $type · $perms\n";
}

// ── ACÇÕES ──────────────────────────────────────────────────
$action  = $_GET['action'] ?? 'info';
$results = [];

// ── 1. MIGRATE + SEED ───────────────────────────────────────
if ($action === 'migrate') {
    $results[] = run('php artisan migrate --force', $base);
    $results[] = run('php artisan db:seed --force', $base);
    $results[] = run('php artisan storage:link --force', $base);
    $results[] = run('php artisan optimize:clear', $base);
    $results[] = run('php artisan config:cache', $base);
    $results[] = run('php artisan route:cache', $base);
    $results[] = run('php artisan view:cache', $base);
}

// ── 2. MIGRATE FRESH (apaga tudo!) ──────────────────────────
if ($action === 'fresh') {
    $results[] = run('php artisan migrate:fresh --seed --force', $base);
    $results[] = run('php artisan storage:link --force', $base);
    $results[] = run('php artisan optimize:clear', $base);
    $results[] = run('php artisan config:cache', $base);
}

// ── 3. LIMPAR CACHE ─────────────────────────────────────────
if ($action === 'clear') {
    $results[] = run('php artisan optimize:clear', $base);
    $results[] = run('php artisan config:clear', $base);
    $results[] = run('php artisan route:clear', $base);
    $results[] = run('php artisan view:clear', $base);
    $results[] = run('php artisan cache:clear', $base);
}

// ── 4. RECONSTRUIR CACHE ────────────────────────────────────
if ($action === 'cache') {
    $results[] = run('php artisan config:cache', $base);
    $results[] = run('php artisan route:cache', $base);
    $results[] = run('php artisan view:cache', $base);
}

// ── 5. LOGS ─────────────────────────────────────────────────
if ($action === 'logs') {
    $log = $base . '/storage/logs/laravel.log';
    if (file_exists($log)) {
        $lines = array_slice(file($log), -100);
        $results[] = [
            'cmd'  => 'tail -100 storage/logs/laravel.log',
            'out'  => implode('', $lines),
            'code' => 0,
        ];
    } else {
        $results[] = ['cmd' => 'logs', 'out' => 'Ficheiro de log não encontrado em ' . $log, 'code' => 1];
    }
}

// ── 6. VER .ENV ─────────────────────────────────────────────
if ($action === 'env') {
    $envFile = $base . '/.env';
    if (file_exists($envFile)) {
        $content = file_get_contents($envFile);
        $content = preg_replace('/(DB_PASSWORD\s*=\s*).*/m', '$1*****', $content);
        $content = preg_replace('/(APP_KEY\s*=\s*).*/m',     '$1*****', $content);
        $results[] = ['cmd' => 'cat .env (passwords ocultas)', 'out' => $content, 'code' => 0];
    } else {
        $results[] = ['cmd' => 'cat .env', 'out' => '.env não encontrado!', 'code' => 1];
    }
}

// ── 6.5 CONFIGURAR MAIL ─────────────────────────────────────
if ($action === 'config_mail') {
    $envFile = $base . '/.env';
    if (file_exists($envFile)) {
        $content = file_get_contents($envFile);
        
        // Configurar SMTP Real
        $content = preg_replace('/^MAIL_MAILER=.*$/m', 'MAIL_MAILER=smtp', $content);
        $content = preg_replace('/^MAIL_HOST=.*$/m', 'MAIL_HOST=mail.ineds.org', $content);
        $content = preg_replace('/^MAIL_PORT=.*$/m', 'MAIL_PORT=465', $content);
        $content = preg_replace('/^MAIL_USERNAME=.*$/m', 'MAIL_USERNAME=alphabilhetes@ineds.org', $content);
        $content = preg_replace('/^MAIL_PASSWORD=.*$/m', 'MAIL_PASSWORD="Ivetefilip&1"', $content);
        $content = preg_replace('/^MAIL_ENCRYPTION=.*$/m', 'MAIL_ENCRYPTION=ssl', $content);
        
        $content = preg_replace('/^MAIL_FROM_ADDRESS=.*$/m', 'MAIL_FROM_ADDRESS=alphabilhetes@ineds.org', $content);
        $content = preg_replace('/^MAIL_FROM_NAME=.*$/m', 'MAIL_FROM_NAME="Bilheteira Digital Alpha"', $content);
        
        // HostGator doesn't run workers, queue must be sync
        $content = preg_replace('/^QUEUE_CONNECTION=.*$/m', 'QUEUE_CONNECTION=sync', $content);
        
        file_put_contents($envFile, $content);
        
        $results[] = ['cmd' => 'config_mail', 'out' => "✅ Serviço SMTP configurado com alphabilhetes@ineds.org.", 'code' => 0];
        $results[] = run('php artisan config:clear', $base);
    } else {
        $results[] = ['cmd' => 'config_mail', 'out' => '.env não encontrado!', 'code' => 1];
    }
}

// ── 7. DIAGNÓSTICO COMPLETO ─────────────────────────────────
if ($action === 'diagnose') {
    $out = "═══ AMBIENTE ═══\n";
    $out .= "PHP: " . phpversion() . "\n";
    $out .= "Data/Hora: " . date('Y-m-d H:i:s') . "\n";
    $out .= "User: " . get_current_user() . "\n\n";

    $out .= "═══ ESTRUTURA DE FICHEIROS ═══\n";
    $paths = [
        '.env'                        => $base . '/.env',
        'artisan'                     => $base . '/artisan',
        'vendor/'                     => $base . '/vendor',
        'storage/app/public'          => $base . '/storage/app/public',
        'storage (gravável?)'         => $base . '/storage',
        '.git/'                       => $base . '/.git',
        'public/build/'               => $base . '/public/build',
        'public/build/manifest.json'  => $base . '/public/build/manifest.json',
        'public/images/'              => $base . '/public/images',
        'public/artists/'             => $base . '/public/artists',
        'public/alpha-logo-gold.png'  => $base . '/public/alpha-logo-gold.png',
        'public/favicon.ico'          => $base . '/public/favicon.ico',
        'public/storage (link)'       => $base . '/public/storage',
        '── alpha/bilhetes/ ──'       => $publicAlpha,
        'α index.php'                 => $publicAlpha . '/index.php',
        'α .htaccess'                 => $publicAlpha . '/.htaccess',
        'α build/'                    => $publicAlpha . '/build',
        'α build/manifest.json'       => $publicAlpha . '/build/manifest.json',
        'α images/'                   => $publicAlpha . '/images',
        'α artists/'                  => $publicAlpha . '/artists',
        'α alpha-logo-gold.png'       => $publicAlpha . '/alpha-logo-gold.png',
        'α favicon.ico'               => $publicAlpha . '/favicon.ico',
        'α storage'                   => $publicAlpha . '/storage',
    ];
    foreach ($paths as $label => $path) {
        $out .= checkPath($label, $path);
    }

    // Storage gravável
    $out .= "\n═══ PERMISSÕES CRÍTICAS ═══\n";
    $out .= "storage/ gravável: " . (is_writable($base . '/storage') ? '✓ SIM' : '✗ NÃO') . "\n";
    $out .= "bootstrap/cache/ gravável: " . (is_writable($base . '/bootstrap/cache') ? '✓ SIM' : '✗ NÃO') . "\n";

    // Manifest
    $manifest = $publicAlpha . '/build/manifest.json';
    if (file_exists($manifest)) {
        $out .= "\n═══ MANIFEST.JSON ═══\n";
        $data = json_decode(file_get_contents($manifest), true) ?? [];
        foreach (array_slice($data, 0, 10, true) as $k => $v) {
            $out .= "$k → " . ($v['file'] ?? '?') . "\n";
        }
    }

    // .env URLs
    $envFile = $base . '/.env';
    if (file_exists($envFile)) {
        $out .= "\n═══ .ENV (URLs e ambiente) ═══\n";
        foreach (file($envFile) as $line) {
            if (preg_match('/^(APP_URL|ASSET_URL|APP_ENV|APP_DEBUG|DB_CONNECTION|DB_DATABASE|DB_HOST)/i', $line)) {
                $out .= trim($line) . "\n";
            }
        }
    }

    $results[] = ['cmd' => 'diagnóstico', 'out' => $out, 'code' => 0];
}

// ── 8. FIX ASSETS ───────────────────────────────────────────
if ($action === 'fix_assets') {
    $out = "A corrigir assets em alpha/bilhetes/ ...\n\n";

    // Ficheiros e pastas a sincronizar de public/ → alpha/bilhetes/
    $items = ['build', 'images', 'artists', 'alpha-logo-gold.png', 'favicon.ico', 'robots.txt', 'sw.js', 'storage'];

    foreach ($items as $item) {
        $src  = $base . '/public/' . $item;
        $dest = $publicAlpha . '/' . $item;

        if (!file_exists($src)) {
            $out .= "⚠ Origem não existe: $src\n";
            continue;
        }

        // Remover destino existente para recriar
        if (is_link($dest)) {
            unlink($dest);
            $out .= "• Symlink antigo removido: $dest\n";
        } elseif (is_dir($dest)) {
            exec('rm -rf ' . escapeshellarg($dest));
            $out .= "• Pasta antiga removida: $dest\n";
        } elseif (is_file($dest)) {
            unlink($dest);
            $out .= "• Ficheiro antigo removido: $dest\n";
        }

        // Tentar symlink primeiro
        if (@symlink($src, $dest)) {
            $out .= "✓ Symlink: $item → $src\n";
        } else {
            // Fallback: cópia física
            if (is_dir($src)) {
                exec('cp -r ' . escapeshellarg($src) . ' ' . escapeshellarg($dest) . ' 2>&1', $o, $c);
            } else {
                copy($src, $dest);
                $c = 0; $o = [];
            }
            $out .= ($c === 0 ? "✓" : "✗") . " Copiado: $item" . ($o ? "\n  " . implode("\n  ", $o) : '') . "\n";
        }
    }

    // Corrigir ASSET_URL no .env
    $envFile    = $base . '/.env';
    $envContent = file_get_contents($envFile);

    // Garantir quebra de linha antes de DB_CONNECTION se colado na mesma linha
    $envContent = preg_replace('/(\S)(DB_CONNECTION)/m', "$1\nDB_CONNECTION", $envContent);

    if (str_contains($envContent, 'ASSET_URL=')) {
        $envContent = preg_replace('/^ASSET_URL=.*/m', 'ASSET_URL=https://ineds.org/alpha/bilhetes', $envContent);
        $out .= "\n✓ ASSET_URL actualizado no .env\n";
    } else {
        $envContent .= "\nASSET_URL=https://ineds.org/alpha/bilhetes\n";
        $out .= "\n✓ ASSET_URL adicionado ao .env\n";
    }

    // Garantir APP_ENV=production
    $envContent = preg_replace('/^APP_ENV=.*/m', 'APP_ENV=production', $envContent);
    $envContent = preg_replace('/^APP_DEBUG=.*/m', 'APP_DEBUG=false', $envContent);

    file_put_contents($envFile, $envContent);
    $out .= "✓ APP_ENV=production e APP_DEBUG=false\n";

    $results[] = ['cmd' => 'fix_assets', 'out' => $out, 'code' => 0];

    // Limpar e recriar cache
    $results[] = run('php artisan optimize:clear', $base);
    $results[] = run('php artisan config:cache', $base);
    $results[] = run('php artisan route:cache', $base);
}

// ── 9. GIT STATUS ───────────────────────────────────────────
if ($action === 'git_status') {
    $results[] = run('git status', $base);
    $results[] = run('git log --oneline -15', $base);
    $results[] = run('git remote -v', $base);
    $results[] = run('git branch -a', $base);
}

// ── 10. GIT INIT + CLONE ────────────────────────────────────
if ($action === 'git_init') {
    global $repo, $branch;

    // Verificar se já tem .git
    if (is_dir($base . '/.git')) {
        $results[] = ['cmd' => 'git check', 'out' => "Repositório Git já existe em $base/.git\nUsa Git Pull para actualizar.", 'code' => 0];
    } else {
        $results[] = run('git init', $base);
        $results[] = run("git remote add origin $repo", $base);
        $results[] = run("git fetch origin $branch", $base);
        $results[] = run("git checkout -f $branch", $base);
        $results[] = ['cmd' => 'git init concluído', 'out' => "Repositório ligado a:\n$repo\nBranch: $branch", 'code' => 0];
    }
}

// ── 11. GIT PULL + DEPLOY ───────────────────────────────────
if ($action === 'git_pull') {
    global $branch;

    $results[] = run('git fetch origin', $base);
    $results[] = run("git reset --hard origin/$branch", $base);
    $results[] = run('composer install --no-dev --optimize-autoloader', $base);
    $results[] = run('php artisan migrate --force', $base);
    $results[] = run('php artisan storage:link --force', $base);
    $results[] = run('php artisan optimize:clear', $base);
    $results[] = run('php artisan config:cache', $base);
    $results[] = run('php artisan route:cache', $base);
    $results[] = run('php artisan view:cache', $base);

    // Re-sincronizar assets após pull
    $items = ['build', 'images', 'artists', 'alpha-logo-gold.png', 'favicon.ico', 'robots.txt', 'sw.js'];
    $assetOut = "Assets sincronizados após pull:\n";
    foreach ($items as $item) {
        $src  = $base . '/public/' . $item;
        $dest = $publicAlpha . '/' . $item;
        if (!file_exists($src)) continue;
        if (is_link($dest) || is_dir($dest) || is_file($dest)) {
            is_link($dest) ? unlink($dest) : (is_dir($dest) ? exec('rm -rf ' . escapeshellarg($dest)) : unlink($dest));
        }
        if (@symlink($src, $dest)) {
            $assetOut .= "✓ $item\n";
        } else {
            is_dir($src)
                ? exec('cp -r ' . escapeshellarg($src) . ' ' . escapeshellarg($dest))
                : copy($src, $dest);
            $assetOut .= "• $item (copiado)\n";
        }
    }
    $results[] = ['cmd' => 'sync assets', 'out' => $assetOut, 'code' => 0];
    $results[] = run('git log --oneline -5', $base);
}

// ── 12. PERMISSÕES ──────────────────────────────────────────
if ($action === 'permissions') {
    $results[] = run('chmod -R 775 storage', $base);
    $results[] = run('chmod -R 775 bootstrap/cache', $base);
    $results[] = run('find storage -type f -exec chmod 664 {} \;', $base);
    $results[] = run('find storage -type d -exec chmod 775 {} \;', $base);
    $results[] = ['cmd' => 'permissões', 'out' => "✓ storage/ e bootstrap/cache/ com permissões 775", 'code' => 0];
}

// ── 14. FIX LIVEWIRE SUBDIRECTORY ───────────────────────────
if ($action === 'fix_livewire') {
    $out = "═══ FIX LIVEWIRE SUBDIRECTORY ═══\n\n";

    // 1. Show current server vars
    $out .= "Current SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
    $out .= "Current SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
    $out .= "Current REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
    $out .= "Current PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'N/A') . "\n\n";

    // 2. Show current index.php
    $indexPath = $publicAlpha . '/index.php';
    $out .= "═══ CURRENT INDEX.PHP ═══\n";
    $out .= file_get_contents($indexPath) . "\n\n";

    // 3. Write patched index.php that forces SCRIPT_NAME
    $newIndex = <<<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ── Subdirectory fix ──────────────────────────────────────────
// Force SCRIPT_NAME to include the subdirectory so that Laravel's
// URL generator produces correct URLs including for Livewire.
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
PHP;

    file_put_contents($indexPath, $newIndex);
    $out .= "✓ index.php actualizado com SCRIPT_NAME fix\n\n";

    // 4. Write simplified AppServiceProvider that only forces HTTPS
    $providerPath = $base . '/app/Providers/AppServiceProvider.php';
    $newProvider = <<<'PHP'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
PHP;

    file_put_contents($providerPath, $newProvider);
    $out .= "✓ AppServiceProvider.php simplificado (apenas forceScheme)\n\n";

    // 5. Clear all caches
    $results[] = ['cmd' => 'fix_livewire', 'out' => $out, 'code' => 0];
    $results[] = run('php artisan optimize:clear', $base);
    $results[] = run('php artisan config:clear', $base);
    $results[] = run('php artisan route:clear', $base);
    $results[] = run('php artisan view:clear', $base);

    // 6. Show new index.php
    $results[] = ['cmd' => 'new index.php', 'out' => file_get_contents($indexPath), 'code' => 0];
    $results[] = ['cmd' => 'new AppServiceProvider', 'out' => file_get_contents($providerPath), 'code' => 0];
}

// ── 13. INFO DO SERVIDOR ────────────────────────────────────
if ($action === 'info') {
    $out  = "PHP: " . phpversion() . "\n";
    $out .= "Laravel: ";
    ob_start();
    run('php artisan --version', $base);
    $out .= run('php artisan --version', $base)['out'] . "\n";
    $out .= "Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'desconhecido') . "\n";
    $out .= "Hora: " . date('Y-m-d H:i:s T') . "\n";
    $out .= "Memória: " . ini_get('memory_limit') . "\n";
    $out .= "Max execução: " . ini_get('max_execution_time') . "s\n";
    $out .= "Upload max: " . ini_get('upload_max_filesize') . "\n\n";
    $out .= "Extensões PHP necessárias:\n";
    $exts = ['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'curl', 'zip'];
    foreach ($exts as $ext) {
        $out .= (extension_loaded($ext) ? "✓" : "✗") . " $ext\n";
    }
    $out .= "\nGit disponível: " . (shell_exec('which git') ? "✓ " . trim(shell_exec('which git')) : "✗ não encontrado") . "\n";
    $out .= "Composer: " . trim(shell_exec('which composer') ?? 'não encontrado') . "\n";
    $results[] = ['cmd' => 'info do servidor', 'out' => $out, 'code' => 0];
}

?><!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Setup · Bilheteira Digital</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background: #0D0B07;
    color: #F0E8D5;
    font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
    padding: 24px 20px;
    min-height: 100vh;
  }
  .header { margin-bottom: 20px; }
  .header h1 {
    font-family: Georgia, serif;
    font-size: 22px;
    color: #D4A017;
    letter-spacing: .04em;
    margin-bottom: 4px;
  }
  .header p { font-size: 11px; color: rgba(240,232,213,.4); }
  .warn {
    background: rgba(224,84,84,.1);
    border: 1px solid rgba(224,84,84,.4);
    border-left: 3px solid #E05454;
    padding: 9px 14px;
    border-radius: 4px;
    color: #E05454;
    margin-bottom: 20px;
    font-size: 11px;
  }
  /* GRUPOS DE BOTÕES */
  .btn-groups { display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px; }
  .btn-group-label {
    font-size: 9px;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgba(212,160,23,.5);
    margin-bottom: 6px;
  }
  .btn-row { display: flex; flex-wrap: wrap; gap: 8px; }
  a.btn {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    cursor: pointer;
    transition: opacity .15s;
  }
  a.btn:hover { opacity: .85; }
  .btn-gold   { background: #D4A017; color: #0D0B07; }
  .btn-green  { background: rgba(61,186,124,.15); color: #3DBA7C; border: 1px solid rgba(61,186,124,.4); }
  .btn-blue   { background: rgba(74,158,224,.15); color: #4A9EE0; border: 1px solid rgba(74,158,224,.4); }
  .btn-orange { background: rgba(224,138,58,.15); color: #E08A3A; border: 1px solid rgba(224,138,58,.4); }
  .btn-red    { background: rgba(224,84,84,.12); color: #E05454; border: 1px solid rgba(224,84,84,.4); }
  .btn-gray   { background: rgba(255,255,255,.05); color: rgba(240,232,213,.7); border: 1px solid rgba(255,255,255,.1); }
  /* DIVIDER */
  .divider {
    height: 1px;
    background: rgba(212,160,23,.1);
    margin: 24px 0;
  }
  /* RESULTADOS */
  .result-block { margin-bottom: 20px; }
  .result-label {
    font-size: 10px;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #D4A017;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .result-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(212,160,23,.1);
  }
  pre {
    background: #111009;
    border: 1px solid rgba(212,160,23,.1);
    border-radius: 4px;
    padding: 14px;
    font-size: 11px;
    line-height: 1.75;
    white-space: pre-wrap;
    word-break: break-all;
    overflow-x: auto;
    max-height: 500px;
    overflow-y: auto;
  }
  pre.ok  { border-color: rgba(61,186,124,.3); }
  pre.err { border-color: rgba(224,84,84,.4); color: #fca5a5; }
  /* STATUS BAR */
  .status-bar {
    position: sticky;
    bottom: 0;
    background: #111009;
    border-top: 1px solid rgba(212,160,23,.15);
    padding: 8px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 10px;
    color: rgba(240,232,213,.4);
    margin: 0 -20px -24px;
  }
  .status-bar .repo { color: rgba(212,160,23,.6); }
  .empty {
    background: rgba(212,160,23,.05);
    border: 1px solid rgba(212,160,23,.1);
    border-radius: 4px;
    padding: 24px;
    text-align: center;
    color: rgba(240,232,213,.4);
    font-size: 12px;
  }
</style>
</head>
<body>

<div class="header">
  <h1>⚙ Bilheteira Digital — Setup & Deploy</h1>
  <p>Concerto Renúncia 2026 · <?= htmlspecialchars($base) ?> · PHP <?= phpversion() ?></p>
</div>

<div class="warn">
  ⚠ SEGURANÇA: Apagar este ficheiro imediatamente após o setup!
  Caminho: <?= htmlspecialchars($publicAlpha) ?>/setup.php
</div>

<div class="btn-groups">

  <div>
    <div class="btn-group-label">// Base de dados</div>
    <div class="btn-row">
      <a class="btn btn-gold"
         href="?key=renuncia2026&action=migrate">▶ Migrate + Seed</a>
      <a class="btn btn-red"
         href="?key=renuncia2026&action=fresh"
         onclick="return confirm('⚠ APAGA TODOS OS DADOS!\nTens a certeza absoluta?')">⚠ Migrate Fresh</a>
    </div>
  </div>

  <div>
    <div class="btn-group-label">// Assets & Ficheiros</div>
    <div class="btn-row">
      <a class="btn btn-blue"  href="?key=renuncia2026&action=fix_assets">🔧 Fix Assets Auto</a>
      <a class="btn btn-green" href="?key=renuncia2026&action=permissions">🔒 Fix Permissões</a>
      <a class="btn btn-orange" href="?key=renuncia2026&action=fix_livewire">⚡ Fix Livewire Subdirectory</a>
    </div>
  </div>

  <div>
    <div class="btn-group-label">// Cache</div>
    <div class="btn-row">
      <a class="btn btn-orange" href="?key=renuncia2026&action=clear">🧹 Limpar Cache</a>
      <a class="btn btn-green"  href="?key=renuncia2026&action=cache">⚡ Recriar Cache</a>
    </div>
  </div>

  <div>
    <div class="btn-group-label">// Git · git@github.com:filipeive/bilheteira_digital.git</div>
    <div class="btn-row">
      <a class="btn btn-gray"   href="?key=renuncia2026&action=git_status">📊 Git Status</a>
      <a class="btn btn-blue"
         href="?key=renuncia2026&action=git_pull"
         onclick="return confirm('Fazer git pull da branch main e deploy?\nIsto irá actualizar todos os ficheiros.')">⬇ Git Pull + Deploy</a>
      <a class="btn btn-gray"
         href="?key=renuncia2026&action=git_init"
         onclick="return confirm('Inicializar repositório Git?\nApenas necessário na primeira vez.')">🔗 Git Init (1ª vez)</a>
    </div>
  </div>

  <div>
    <div class="btn-group-label">// Diagnóstico e Configuração</div>
    <div class="btn-row">
      <a class="btn btn-gold"  href="?key=renuncia2026&action=diagnose">🔍 Diagnóstico Completo</a>
      <a class="btn btn-gray"  href="?key=renuncia2026&action=logs">📋 Logs de Erro</a>
      <a class="btn btn-gray"  href="?key=renuncia2026&action=env">🔐 Ver .env</a>
      <a class="btn btn-gray"  href="?key=renuncia2026&action=info">ℹ Info Servidor</a>
      <a class="btn btn-blue"  href="?key=renuncia2026&action=config_mail" onclick="return confirm('Configurar env para enviar email via sendmail?')">📧 Config Email</a>
      <a class="btn btn-green" href="https://ineds.org/alpha/bilhetes" target="_blank">↗ Abrir Site</a>
    </div>
  </div>

</div>

<div class="divider"></div>

<?php if (!empty($results)): ?>
  <?php foreach ($results as $r): ?>
    <div class="result-block">
      <div class="result-label">$ <?= htmlspecialchars($r['cmd']) ?></div>
      <pre class="<?= $r['code'] === 0 ? 'ok' : 'err' ?>"><?= htmlspecialchars($r['out']) ?></pre>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <div class="empty">
    Escolhe uma acção acima para começar.<br>
    Começa por <strong>🔍 Diagnóstico Completo</strong> para ver o estado actual.
  </div>
<?php endif; ?>

<div class="status-bar">
  <span>Acção actual: <strong><?= htmlspecialchars($action) ?></strong></span>
  <span class="repo">git@github.com:filipeive/bilheteira_digital.git · branch: main</span>
  <span><?= date('H:i:s') ?></span>
</div>

</body>
</html>
