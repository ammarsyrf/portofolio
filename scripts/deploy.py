import os
import sys
import pathlib

# Ensure UTF-8 output
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

# Import SSH runner from zenerie scripts
ZENERIE_SCRIPTS = r"c:\laravel10\zenerie\scripts"
if ZENERIE_SCRIPTS not in sys.path:
    sys.path.insert(0, ZENERIE_SCRIPTS)

import ssh_runner

ROOT_DIR = str(pathlib.Path(__file__).resolve().parent.parent)
REMOTE_ROOT = "/home/zeneriem/zen.zenerie.my.id"

IGNORE_DIRS = {'.git', '.gemini', '.agents', '.codex', '.system_generated', 'scratch', 'scripts', '__pycache__', 'docs', 'database'}
IGNORE_FILES = {'TASK.md', 'COLLABORATION.md', 'AGENTS.md', 'README.md', '.gitignore', 'connect_ssh.bat', 'error.log', 'error_log'}

def get_files():
    files = []
    for dirpath, dirnames, filenames in os.walk(ROOT_DIR):
        dirnames[:] = [d for d in dirnames if d not in IGNORE_DIRS]
        for f in filenames:
            if f in IGNORE_FILES:
                continue
            local_path = os.path.join(dirpath, f)
            rel_path = os.path.relpath(local_path, ROOT_DIR)
            remote_path = (pathlib.Path(REMOTE_ROOT) / pathlib.Path(rel_path)).as_posix()
            files.append((local_path, remote_path, rel_path))
    return files

def ensure_remote_dir(sftp, remote_path):
    dirs = []
    parent = os.path.dirname(remote_path)
    while parent and parent != '/':
        dirs.append(parent)
        parent = os.path.dirname(parent)
    dirs.reverse()
    for d in dirs:
        try:
            sftp.stat(d)
        except IOError:
            try:
                sftp.mkdir(d)
            except Exception:
                pass

def deploy(specific_files=None):
    print(f"[*] Connecting via SSH to {ssh_runner.HOST}:{ssh_runner.PORT}...")
    client = ssh_runner.get_ssh_client()
    sftp = client.open_sftp()
    print("[+] SFTP session opened successfully.")

    all_files = get_files()
    if specific_files:
        targets = [f for f in all_files if any(pattern.replace('\\', '/') in f[2].replace('\\', '/') for pattern in specific_files)]
    else:
        targets = all_files

    print(f"[*] Uploading {len(targets)} files to {REMOTE_ROOT}...")
    count = 0
    for local_path, remote_path, rel_path in targets:
        ensure_remote_dir(sftp, remote_path)
        sftp.put(local_path, remote_path)
        count += 1
        print(f"  [{count}/{len(targets)}] {rel_path} -> {remote_path}")

    sftp.close()
    client.close()
    print(f"[DONE] Deployment finished! {count} files synchronized to live server.")

if __name__ == '__main__':
    args = sys.argv[1:]
    deploy(args if args else None)
