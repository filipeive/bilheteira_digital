#!/usr/bin/env python3
"""
Deploy script — Bilhetes App → HostGator (ineds.org)
Envia apenas ficheiros alterados via FTP.

Uso:
    python3 deploy.py                  # Apenas ficheiros alterados
    python3 deploy.py --views          # Só views Blade
    python3 deploy.py --assets         # Só assets compilados (CSS/JS)
    python3 deploy.py --livewire       # Só Livewire components
    python3 deploy.py --routes         # Só routes
    python3 deploy.py --controllers    # Só controllers
    python3 deploy.py --models         # Só models + services
    python3 deploy.py --jobs           # Só jobs + mail
    python3 deploy.py --database       # Só migrations + seeders
    python3 deploy.py --config         # Só config/
    python3 deploy.py --pdf            # Só templates PDF
    python3 deploy.py --emails         # Só templates de email
    python3 deploy.py --full           # Tudo (forçar)
    python3 deploy.py --file path      # Ficheiro específico
    python3 deploy.py --status         # Ver o que mudou sem enviar
    python3 deploy.py --clear-cache    # Limpar cache local de hashes
"""

import ftplib
import os
import sys
import hashlib
import json
import time
from pathlib import Path
from datetime import datetime

# ─── CONFIGURAÇÃO ────────────────────────────────────────────
# ⚠️  Mover para .env ou variáveis de ambiente em produção
FTP_HOST    = os.environ.get("FTP_HOST",    "ftp.ineds.org")
FTP_PORT    = int(os.environ.get("FTP_PORT", 21))
FTP_USER    = os.environ.get("FTP_USER",    "alphabilhetes@ineds.org")
FTP_PASS    = os.environ.get("FTP_PASS",    "")          # ← definir via env
REMOTE_BASE = "/public_html"

LOCAL_BASE  = os.path.dirname(os.path.abspath(__file__))
CACHE_FILE  = os.path.join(LOCAL_BASE, ".deploy-cache.json")
LOG_FILE    = os.path.join(LOCAL_BASE, ".deploy-log.txt")

# ─── GRUPOS DE DEPLOY ────────────────────────────────────────
DEPLOY_GROUPS = {
    "views": [
        "resources/views/",
    ],
    "assets": [
        "public/build/",
        "public/images/",
        "public/artists/",
        "public/alpha-logo-gold.png",
        "public/favicon.ico",
        "public/sw.js",
        "public/manifest.json",
        "public/robots.txt",
    ],
    "routes": [
        "routes/web.php",
        "routes/api.php",
        "routes/auth.php",
        "routes/console.php",
    ],
    "controllers": [
        "app/Http/Controllers/",
        "app/Http/Middleware/",
        "app/Http/Requests/",
    ],
    "livewire": [
        "app/Livewire/",
    ],
    "mail": [
        "app/Mail/",
    ],
    "jobs": [
        "app/Jobs/",
    ],
    "models": [
        "app/Models/",
        "app/Services/",
        "app/Policies/",
    ],
    "config": [
        "config/",
    ],
    "database": [
        "database/migrations/",
        "database/seeders/",
        "database/factories/",
    ],
    "pdf": [
        "resources/views/pdf/",
    ],
    "emails": [
        "resources/views/emails/",
    ],
    "console": [
        "app/Console/",
    ],
}

# ─── PADRÕES A IGNORAR SEMPRE ────────────────────────────────
SKIP_PATTERNS = [
    ".git",
    "node_modules",
    ".env",
    ".env.example",
    "vendor",
    "storage/logs",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
    "storage/app/public",
    ".deploy-cache.json",
    ".deploy-log.txt",
    "deploy.py",
    "__pycache__",
    ".DS_Store",
    "Thumbs.db",
    "tests/",
    "phpunit.xml",
    ".phpunit",
    "*.swp",
    "*.swo",
]

# Extensões de ficheiros a ignorar
SKIP_EXTENSIONS = {".pyc", ".pyo", ".log", ".cache"}

# ─── CORES NO TERMINAL ───────────────────────────────────────
class C:
    RESET  = "\033[0m"
    BOLD   = "\033[1m"
    GREEN  = "\033[92m"
    YELLOW = "\033[93m"
    RED    = "\033[91m"
    BLUE   = "\033[94m"
    GOLD   = "\033[33m"
    DIM    = "\033[2m"

def ok(msg):    print(f"  {C.GREEN}✓{C.RESET}  {msg}")
def err(msg):   print(f"  {C.RED}✗{C.RESET}  {msg}")
def info(msg):  print(f"  {C.BLUE}→{C.RESET}  {msg}")
def warn(msg):  print(f"  {C.YELLOW}⚠{C.RESET}  {msg}")
def title(msg): print(f"\n{C.BOLD}{C.GOLD}{msg}{C.RESET}")

# ─── HELPERS ─────────────────────────────────────────────────
def should_skip(rel_path: str) -> bool:
    rel_path = rel_path.replace("\\", "/")
    _, ext = os.path.splitext(rel_path)
    if ext.lower() in SKIP_EXTENSIONS:
        return True
    for pattern in SKIP_PATTERNS:
        if pattern in rel_path:
            return True
    return False

def file_hash(filepath: str) -> str:
    h = hashlib.md5()
    with open(filepath, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            h.update(chunk)
    return h.hexdigest()

def load_cache() -> dict:
    if os.path.exists(CACHE_FILE):
        try:
            with open(CACHE_FILE, "r") as f:
                return json.load(f)
        except (json.JSONDecodeError, IOError):
            return {}
    return {}

def save_cache(cache: dict):
    with open(CACHE_FILE, "w") as f:
        json.dump(cache, f, indent=2, sort_keys=True)

def log_deploy(files: list, success: int, errors: int):
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with open(LOG_FILE, "a") as f:
        f.write(f"\n[{timestamp}] Deploy: {success} ok, {errors} erros\n")
        for file in files:
            f.write(f"  {file}\n")

def format_size(size: int) -> str:
    if size < 1024:      return f"{size}B"
    if size < 1048576:   return f"{size/1024:.1f}KB"
    return f"{size/1048576:.1f}MB"

# ─── RECOLHA DE FICHEIROS ────────────────────────────────────
def get_files_for_group(group_name: str) -> list:
    files = []
    if group_name not in DEPLOY_GROUPS:
        err(f"Grupo desconhecido: {group_name}")
        print(f"     Grupos: {', '.join(DEPLOY_GROUPS.keys())}")
        return files

    for path in DEPLOY_GROUPS[group_name]:
        full_path = os.path.join(LOCAL_BASE, path)

        if os.path.isfile(full_path):
            rel = os.path.relpath(full_path, LOCAL_BASE)
            if not should_skip(rel):
                files.append(rel)

        elif os.path.isdir(full_path):
            for root, dirs, filenames in os.walk(full_path):
                # Ignorar pastas na travessia
                dirs[:] = [
                    d for d in dirs
                    if not should_skip(os.path.relpath(
                        os.path.join(root, d), LOCAL_BASE
                    ))
                ]
                for filename in filenames:
                    abs_path = os.path.join(root, filename)
                    rel      = os.path.relpath(abs_path, LOCAL_BASE)
                    if not should_skip(rel):
                        files.append(rel)
        else:
            # Ficheiro não existe localmente — ignorar silenciosamente
            pass

    return list(set(files))

def get_all_deployable_files() -> list:
    files = []
    for group in DEPLOY_GROUPS:
        files.extend(get_files_for_group(group))
    return list(set(files))

def get_changed_files(file_list: list, cache: dict) -> list:
    changed = []
    for rel_path in file_list:
        abs_path = os.path.join(LOCAL_BASE, rel_path)
        if not os.path.exists(abs_path):
            continue
        current_hash = file_hash(abs_path)
        if cache.get(rel_path) != current_hash:
            changed.append(rel_path)
    return changed

# ─── FTP ─────────────────────────────────────────────────────
def connect_ftp() -> ftplib.FTP:
    ftp = ftplib.FTP()
    ftp.connect(FTP_HOST, FTP_PORT, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.encoding = "utf-8"
    try:
        ftp.set_pasv(True)  # Modo passivo — mais compatível com firewalls
    except Exception:
        pass
    return ftp

def ensure_remote_dir(ftp: ftplib.FTP, remote_dir: str):
    """Criar directório remoto recursivamente se não existir."""
    parts = [p for p in remote_dir.strip("/").split("/") if p]
    current = ""
    for part in parts:
        current += f"/{part}"
        try:
            ftp.cwd(current)
        except ftplib.error_perm:
            try:
                ftp.mkd(current)
                ftp.cwd(current)
            except ftplib.error_perm:
                pass  # Já existe — ok

def upload_file(ftp: ftplib.FTP, local_path: str, remote_path: str):
    """Fazer upload de um ficheiro."""
    remote_dir  = os.path.dirname(remote_path).replace("\\", "/")
    ensure_remote_dir(ftp, remote_dir)

    with open(local_path, "rb") as f:
        ftp.storbinary(f"STOR {remote_path}", f)

# ─── DEPLOY PRINCIPAL ────────────────────────────────────────
def deploy(file_list: list, force: bool = False, dry_run: bool = False):
    cache = load_cache()

    if force:
        to_upload = [
            f for f in file_list
            if os.path.exists(os.path.join(LOCAL_BASE, f))
        ]
    else:
        to_upload = get_changed_files(file_list, cache)

    if not to_upload:
        ok("Nenhum ficheiro alterado. Tudo actualizado!")
        return

    # Ordenar: assets e routes primeiro, views depois
    priority = ["routes/", "app/", "config/", "database/"]
    def sort_key(f):
        for i, p in enumerate(priority):
            if f.startswith(p): return (i, f)
        return (len(priority), f)
    to_upload.sort(key=sort_key)

    # Mostrar lista
    title(f"{'[DRY RUN] ' if dry_run else ''}Ficheiros a enviar ({len(to_upload)})")
    total_size = 0
    for f in to_upload:
        abs_f = os.path.join(LOCAL_BASE, f)
        size  = os.path.getsize(abs_f)
        total_size += size
        print(f"  {C.DIM}{f}{C.RESET}  {C.YELLOW}{format_size(size)}{C.RESET}")
    print(f"\n  Total: {C.BOLD}{format_size(total_size)}{C.RESET}\n")

    if dry_run:
        warn("Dry run — nenhum ficheiro enviado.")
        return

    # Confirmação para deploys grandes
    if len(to_upload) > 20:
        resp = input(f"  {C.YELLOW}Enviar {len(to_upload)} ficheiros? (s/n): {C.RESET}")
        if resp.strip().lower() not in ("s", "sim", "y", "yes"):
            warn("Deploy cancelado.")
            return

    # Conectar
    title("A conectar ao servidor FTP...")
    try:
        ftp = connect_ftp()
    except Exception as e:
        err(f"Não foi possível conectar: {e}")
        sys.exit(1)
    ok(f"Conectado a {FTP_HOST}")
    print()

    success_list = []
    error_list   = []
    start_time   = time.time()

    for rel_path in to_upload:
        abs_path    = os.path.join(LOCAL_BASE, rel_path)
        remote_path = f"{REMOTE_BASE}/{rel_path}".replace("\\", "/")
        # Normalizar separadores
        remote_path = remote_path.replace("//", "/")

        try:
            upload_file(ftp, abs_path, remote_path)
            size = os.path.getsize(abs_path)
            cache[rel_path] = file_hash(abs_path)
            ok(f"{rel_path}  {C.DIM}{format_size(size)}{C.RESET}")
            success_list.append(rel_path)
        except Exception as e:
            err(f"{rel_path}  →  {e}")
            error_list.append(rel_path)

    save_cache(cache)
    log_deploy(to_upload, len(success_list), len(error_list))

    try:
        ftp.quit()
    except Exception:
        pass

    elapsed = time.time() - start_time

    # Resumo
    print(f"\n{'─'*50}")
    ok(f"{len(success_list)} ficheiro(s) enviado(s)")
    if error_list:
        err(f"{len(error_list)} erro(s):")
        for f in error_list: print(f"     {f}")
    print(f"  {C.DIM}Tempo: {elapsed:.1f}s{C.RESET}")
    print(f"{'─'*50}\n")

    # Lembrete pós-deploy
    if success_list:
        print(f"{C.YELLOW}Lembrar:{C.RESET}")
        print(f"  Se alteraste routes/migrations, corre no servidor:")
        print(f"  {C.DIM}https://ineds.org/public/setup.php?key=renuncia2026&action=clear{C.RESET}")
        print(f"  {C.DIM}https://ineds.org/public/setup.php?key=renuncia2026&action=migrate{C.RESET} (se necessário)\n")

# ─── STATUS — ver o que mudou sem enviar ─────────────────────
def show_status():
    cache = load_cache()
    all_files = get_all_deployable_files()
    changed   = get_changed_files(all_files, cache)
    new_files = [
        f for f in changed
        if f not in cache
    ]
    modified = [f for f in changed if f in cache]

    title("Estado actual do projecto")

    if not changed:
        ok("Nenhuma alteração pendente.")
        return

    if new_files:
        print(f"\n  {C.GREEN}Novos ({len(new_files)}):{C.RESET}")
        for f in sorted(new_files):
            print(f"    + {f}")

    if modified:
        print(f"\n  {C.YELLOW}Modificados ({len(modified)}):{C.RESET}")
        for f in sorted(modified):
            print(f"    ~ {f}")

    print(f"\n  Total: {len(changed)} ficheiro(s) por enviar\n")
    print(f"  Corre {C.BOLD}python3 deploy.py{C.RESET} para enviar.")

# ─── MAIN ─────────────────────────────────────────────────────
def main():
    args = sys.argv[1:]

    # Verificar password
    global FTP_PASS
    if not FTP_PASS:
        # Tentar ler de ficheiro .ftp-credentials
        creds_file = os.path.join(LOCAL_BASE, ".ftp-credentials")
        if os.path.exists(creds_file):
            with open(creds_file) as f:
                for line in f:
                    if line.startswith("FTP_PASS="):
                        FTP_PASS = line.split("=", 1)[1].strip()
                        break
        if not FTP_PASS:
            # Pedir interactivamente
            import getpass
            FTP_PASS = getpass.getpass("Password FTP: ")

    # ── Comandos ──────────────────────────────────────────────
    if not args:
        title("Deploy: ficheiros alterados")
        deploy(get_all_deployable_files())

    elif "--status" in args:
        show_status()

    elif "--clear-cache" in args:
        if os.path.exists(CACHE_FILE):
            os.remove(CACHE_FILE)
            ok("Cache de deploy limpo. Próximo deploy enviará tudo.")
        else:
            info("Cache já estava vazio.")

    elif "--full" in args:
        title("Deploy COMPLETO (todos os ficheiros)")
        dry = "--dry" in args
        deploy(get_all_deployable_files(), force=True, dry_run=dry)

    elif "--dry" in args:
        title("Dry run — ver o que seria enviado")
        deploy(get_all_deployable_files(), dry_run=True)

    elif "--file" in args:
        idx = args.index("--file")
        if idx + 1 >= len(args):
            err("Especifique o ficheiro: --file path/to/file")
            sys.exit(1)
        filepath = args[idx + 1]
        if os.path.isabs(filepath):
            filepath = os.path.relpath(filepath, LOCAL_BASE)
        filepath = filepath.replace("\\", "/")
        title(f"Deploy: {filepath}")
        deploy([filepath], force=True)

    else:
        # Grupos específicos
        deployed_any = False
        for arg in args:
            group = arg.lstrip("-")
            if group in DEPLOY_GROUPS:
                title(f"Deploy: {group}")
                deploy(get_files_for_group(group), force=True)
                deployed_any = True
            elif group not in ("dry", "force"):
                err(f"Argumento desconhecido: {arg}")
                print(f"\n  Uso: python3 deploy.py [opção]")
                print(f"  Grupos: {', '.join(f'--{g}' for g in DEPLOY_GROUPS)}")
                print(f"  Outros: --full, --status, --dry, --clear-cache, --file path\n")

        if not deployed_any:
            title("Deploy: ficheiros alterados")
            deploy(get_all_deployable_files())

if __name__ == "__main__":
    main()
