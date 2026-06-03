#!/usr/bin/env python3
"""
Deploy script for Bilhetes App → HostGator (ineds.org)
Uploads only changed files via FTP.

Usage:
    python3 deploy.py              # Deploy all changed files
    python3 deploy.py --views      # Deploy only blade views
    python3 deploy.py --assets     # Deploy only built assets (CSS/JS)
    python3 deploy.py --routes     # Deploy routes
    python3 deploy.py --full       # Full deploy (all files)
    python3 deploy.py --file path  # Deploy a specific file
"""

import ftplib
import os
import sys
import hashlib
import json
import time
from pathlib import Path

# ─── Configuration ───────────────────────────────────────────
FTP_HOST = "ftp.ineds.org"
FTP_PORT = 21
FTP_USER = "alphabilhetes@ineds.org"
FTP_PASS = "Ivetefilip&1"
REMOTE_BASE = "/public_html"

LOCAL_BASE = os.path.dirname(os.path.abspath(__file__))
CACHE_FILE = os.path.join(LOCAL_BASE, ".deploy-cache.json")

# Directories/files to deploy by category
DEPLOY_GROUPS = {
    "views": [
        "resources/views/",
    ],
    "assets": [
        "public/build/",
        "public/images/",
        "public/artists/",
        "public/alpha-logo-gold.png",
        "public/sw.js",
        "public/manifest.json",
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
    ],
    "livewire": [
        "app/Livewire/",
    ],
    "mail": ["app/Mail/"],
    "jobs": ["app/Jobs/"],
    "models": [
        "app/Models/",
        "app/Services/",
    ],
    "config": [
        "config/",
    ],
    "database": [
        "database/migrations/",
        "database/seeders/",
    ],
    "pdf": [
        "resources/views/pdf/",
    ],
}

# Files/dirs to always skip
SKIP_PATTERNS = [
    ".git", "node_modules", ".env", "vendor", "storage/logs",
    "storage/framework/cache", "storage/framework/sessions",
    "storage/framework/views", ".deploy-cache.json", "deploy.py",
    "__pycache__", ".DS_Store", "tests/", "phpunit.xml",
]

def should_skip(rel_path):
    for pattern in SKIP_PATTERNS:
        if pattern in rel_path:
            return True
    return False

def file_hash(filepath):
    h = hashlib.md5()
    with open(filepath, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            h.update(chunk)
    return h.hexdigest()

def load_cache():
    if os.path.exists(CACHE_FILE):
        with open(CACHE_FILE, "r") as f:
            return json.load(f)
    return {}

def save_cache(cache):
    with open(CACHE_FILE, "w") as f:
        json.dump(cache, f, indent=2)

def get_files_for_group(group_name):
    """Get all files for a deploy group."""
    files = []
    if group_name not in DEPLOY_GROUPS:
        print(f"Unknown group: {group_name}")
        return files
    
    for path in DEPLOY_GROUPS[group_name]:
        full_path = os.path.join(LOCAL_BASE, path)
        if os.path.isfile(full_path):
            files.append(path)
        elif os.path.isdir(full_path):
            for root, dirs, filenames in os.walk(full_path):
                for filename in filenames:
                    abs_path = os.path.join(root, filename)
                    rel_path = os.path.relpath(abs_path, LOCAL_BASE)
                    if not should_skip(rel_path):
                        files.append(rel_path)
    return files

def get_all_deployable_files():
    """Get all files across all groups."""
    files = []
    for group in DEPLOY_GROUPS:
        files.extend(get_files_for_group(group))
    return list(set(files))

def get_changed_files(file_list, cache):
    """Filter to only files that have changed since last deploy."""
    changed = []
    for rel_path in file_list:
        abs_path = os.path.join(LOCAL_BASE, rel_path)
        if not os.path.exists(abs_path):
            continue
        current_hash = file_hash(abs_path)
        if cache.get(rel_path) != current_hash:
            changed.append(rel_path)
    return changed

def ensure_remote_dir(ftp, remote_dir):
    """Create remote directory tree if it doesn't exist."""
    parts = remote_dir.strip("/").split("/")
    current = ""
    for part in parts:
        current += f"/{part}"
        try:
            ftp.cwd(current)
        except ftplib.error_perm:
            try:
                ftp.mkd(current)
            except ftplib.error_perm:
                pass

def upload_file(ftp, local_path, remote_path):
    """Upload a single file."""
    remote_dir = os.path.dirname(remote_path)
    ensure_remote_dir(ftp, remote_dir)
    
    with open(local_path, "rb") as f:
        ftp.storbinary(f"STOR {remote_path}", f)

def deploy(file_list, force=False):
    """Deploy files to the server."""
    cache = load_cache()
    
    if force:
        to_upload = file_list
    else:
        to_upload = get_changed_files(file_list, cache)
    
    if not to_upload:
        print("✅ Nenhum ficheiro alterado. Tudo actualizado!")
        return
    
    print(f"\n📦 {len(to_upload)} ficheiro(s) para enviar:\n")
    for f in to_upload:
        print(f"  → {f}")
    print()
    
    # Connect
    print("🔌 A conectar ao servidor FTP...")
    ftp = ftplib.FTP()
    ftp.connect(FTP_HOST, FTP_PORT, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.encoding = "utf-8"
    print("✅ Conectado!\n")
    
    success = 0
    errors = 0
    
    for rel_path in to_upload:
        abs_path = os.path.join(LOCAL_BASE, rel_path)
        remote_path = f"{REMOTE_BASE}/{rel_path}"
        
        try:
            upload_file(ftp, abs_path, remote_path)
            cache[rel_path] = file_hash(abs_path)
            size = os.path.getsize(abs_path)
            print(f"  ✅ {rel_path} ({size:,} bytes)")
            success += 1
        except Exception as e:
            print(f"  ❌ {rel_path} - Erro: {e}")
            errors += 1
    
    save_cache(cache)
    ftp.quit()
    
    print(f"\n{'='*50}")
    print(f"📊 Resultado: {success} enviado(s), {errors} erro(s)")
    print(f"{'='*50}\n")

def main():
    args = sys.argv[1:]
    
    if not args:
        # Deploy all changed files
        print("🚀 Deploy: ficheiros alterados")
        files = get_all_deployable_files()
        deploy(files)
    
    elif "--full" in args:
        print("🚀 Deploy COMPLETO (todos os ficheiros)")
        files = get_all_deployable_files()
        deploy(files, force=True)
    
    elif "--views" in args:
        print("🚀 Deploy: Views (Blade templates)")
        files = get_files_for_group("views")
        deploy(files, force=True)
    
    elif "--assets" in args:
        print("🚀 Deploy: Assets (CSS/JS)")
        files = get_files_for_group("assets")
        deploy(files, force=True)
    
    elif "--routes" in args:
        print("🚀 Deploy: Routes")
        files = get_files_for_group("routes")
        deploy(files, force=True)
    
    elif "--controllers" in args:
        print("🚀 Deploy: Controllers")
        files = get_files_for_group("controllers")
        deploy(files, force=True)
    
    elif "--livewire" in args:
        print("🚀 Deploy: Livewire Components")
        files = get_files_for_group("livewire")
        deploy(files, force=True)
    
    elif "--pdf" in args:
        print("🚀 Deploy: PDF Templates")
        files = get_files_for_group("pdf")
        deploy(files, force=True)
    
    elif "--file" in args:
        idx = args.index("--file")
        if idx + 1 < len(args):
            filepath = args[idx + 1]
            # Convert absolute to relative if needed
            if os.path.isabs(filepath):
                filepath = os.path.relpath(filepath, LOCAL_BASE)
            print(f"🚀 Deploy: ficheiro único → {filepath}")
            deploy([filepath], force=True)
        else:
            print("❌ Especifique o ficheiro: --file path/to/file")
    
    else:
        # Deploy specific groups
        for arg in args:
            group = arg.lstrip("-")
            if group in DEPLOY_GROUPS:
                print(f"🚀 Deploy: {group}")
                files = get_files_for_group(group)
                deploy(files, force=True)
            else:
                print(f"❌ Grupo desconhecido: {group}")
                print(f"   Grupos disponíveis: {', '.join(DEPLOY_GROUPS.keys())}")

if __name__ == "__main__":
    main()
