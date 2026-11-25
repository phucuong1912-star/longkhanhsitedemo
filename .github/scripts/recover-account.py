"""One reviewed account recovery, executed exclusively by GitHub Actions.

No core transfer, WordPress bootstrap, layout marker or database import.
"""
import argparse
from collections import Counter
import ftplib
import hashlib
import io
import json
import os
from pathlib import Path, PurePosixPath
import re
import shlex
import subprocess
import tarfile
import tempfile
import urllib.request
import urllib.parse

ROOT = Path(__file__).resolve().parents[2]
PACKAGE = ROOT / '.github/account-recovery'
PREFIX = 'recover-account-only:'
PATHS = [
    'wp-content/plugins/long-khanh-directory/includes/account.php',
    'wp-content/plugins/long-khanh-directory/templates/account.php',
    'wp-content/plugins/long-khanh-directory/assets/account.css',
    'wp-content/plugins/long-khanh-directory/long-khanh-directory.php',
    'wp-content/plugins/long-khanh-directory/includes/forms.php',
    'wp-content/themes/listingpro-longkhanh-child/assets/feedback2.js',
    'wp-content/themes/listingpro-longkhanh-child/includes/presentation.php',
]


def digest(data):
    return hashlib.sha256(data).hexdigest() if data is not None else None


def classify(data, row):
    sha = digest(data.replace(b'\r\n', b'\n')) if data is not None else None
    if sha in (row['after_sha256'], row['preserve_sha256']):
        return False
    if sha != row['before_sha256']:
        raise RuntimeError('Destination differs from reviewed versions: ' + row['path'])
    return True


def validate_package():
    manifest = json.loads((PACKAGE / 'manifest.json').read_text(encoding='utf-8'))
    if manifest.get('recovery_id') != 'account-2026-11-25' or [r['path'] for r in manifest['files']] != PATHS:
        raise RuntimeError('Unexpected recovery package or allowlist')
    for row in manifest['files']:
        for key in ('after_sha256', 'preserve_sha256'):
            if not re.fullmatch('[a-f0-9]{64}', row.get(key, '')):
                raise RuntimeError('Invalid package checksum')
        if row.get('before_sha256') is not None and not re.fullmatch('[a-f0-9]{64}', row['before_sha256']):
            raise RuntimeError('Invalid baseline checksum')
        if digest((PACKAGE / 'files' / row['path']).read_bytes()) != row['after_sha256']:
            raise RuntimeError('Payload integrity failed: ' + row['path'])
    return manifest


def check_commit_scope():
    message = subprocess.check_output(['git', 'log', '-1', '--format=%B'], cwd=ROOT, text=True)
    if not message.startswith(PREFIX):
        raise RuntimeError('This workflow only accepts the explicit recovery commit prefix')
    changed = subprocess.check_output(['git', 'diff', '--name-only', 'HEAD^', 'HEAD'], cwd=ROOT, text=True).splitlines()
    exact = {'.github/workflows/deploy.yml', '.github/workflows/deploy-prod.yml',
             '.github/workflows/recover-account.yml', '.github/scripts/recover-account.py',
             '.github/scripts/recover-account-update.php', 'scripts/test-account-recovery.py',
             'scripts/test-account-recovery-wordpress.py',
             'docs/recovery-account-2026-11-25.md', 'docs/deployment-spec.md'}
    if not changed or any(p not in exact and not p.startswith('.github/account-recovery/') for p in changed):
        raise RuntimeError('Recovery commit includes changes outside the reviewed recovery infrastructure')


def successful_master_required():
    if subprocess.run(['git', 'merge-base', '--is-ancestor', 'origin/master', 'HEAD'], cwd=ROOT).returncode:
        raise RuntimeError('Merge current master into prod before recovering prod')
    repository = os.environ['GITHUB_REPOSITORY']
    request = urllib.request.Request(
        f'https://api.github.com/repos/{repository}/actions/workflows/recover-account.yml/runs?branch=master&status=success&per_page=30',
        headers={'Authorization': 'Bearer ' + os.environ['GH_TOKEN'], 'Accept': 'application/vnd.github+json'})
    with urllib.request.urlopen(request, timeout=30) as response:
        runs = json.load(response)['workflow_runs']
    expected = (PACKAGE / 'manifest.json').read_bytes()
    for run in runs:
        sha = run['head_sha']
        if not re.fullmatch('[a-f0-9]{40}', sha):
            continue
        if subprocess.run(['git', 'merge-base', '--is-ancestor', sha, 'HEAD'], cwd=ROOT).returncode:
            continue
        candidate = subprocess.run(['git', 'show', sha + ':.github/account-recovery/manifest.json'], cwd=ROOT, capture_output=True)
        if candidate.returncode == 0 and candidate.stdout == expected:
            print('Verified successful master account recovery: ' + sha)
            return
    raise RuntimeError('Prod requires a successful master recovery of the identical package, merged into prod')


def require_public_url(actual, expected, allow_host_challenge=False):
    current, target = urllib.parse.urlsplit(actual), urllib.parse.urlsplit(expected)
    if (current.scheme, current.netloc, current.path, current.fragment) != (target.scheme, target.netloc, target.path, target.fragment):
        raise RuntimeError('Unexpected public redirect (origin, path or fragment changed)')
    current_query = Counter(urllib.parse.parse_qsl(current.query, keep_blank_values=True))
    target_query = Counter(urllib.parse.parse_qsl(target.query, keep_blank_values=True))
    if current_query == target_query:
        return
    # The master's observed JS/cookie challenge appends exactly one i=1.
    # Preserve every functional query parameter and reject any other addition.
    if allow_host_challenge and not any(key == 'i' for key, value in target_query):
        if current_query == target_query + Counter({('i', '1'): 1}):
            return
    raise RuntimeError('Unexpected public redirect (functional query parameters changed)')


def verify_http(origin, browser=False, allow_host_challenge=False):
    def check(html, mode):
        if any(s in html.lower() for s in ('there has been a critical error', 'fatal error:', 'error establishing a database connection')):
            raise RuntimeError('Public page contains a WordPress error')
        if mode and ('lk-auth-page' not in html or f'value="lk_{mode}"' not in html):
            raise RuntimeError('Expected frontend account form is missing: ' + mode)
        if not mode and 'lk-site' not in html:
            raise RuntimeError('Homepage is missing the expected site shell')

    if browser:
        from playwright.sync_api import sync_playwright
        with sync_playwright() as playwright:
            chromium = playwright.chromium.launch(headless=True)
            try:
                page = chromium.new_page()
                documents = []
                page.on('response', lambda response: documents.append(response) if response.request.is_navigation_request() and response.frame == page.main_frame else None)
                for mode in ('', 'login', 'register', 'lost'):
                    documents.clear()
                    url = origin + ('/?lk_account=' + mode if mode else '/')
                    page.goto(url, wait_until='domcontentloaded', timeout=60000)
                    page.wait_for_selector('body.lk-auth-page' if mode else 'body.lk-site', timeout=60000)
                    if not documents or documents[-1].status != 200 or 'text/html' not in documents[-1].headers.get('content-type', ''):
                        raise RuntimeError('Unexpected public browser HTTP response')
                    require_public_url(page.url, url, allow_host_challenge=allow_host_challenge)
                    check(page.content(), mode)
            finally:
                chromium.close()
    else:
        for mode in ('', 'login', 'register', 'lost'):
            url = origin + ('/?lk_account=' + mode if mode else '/')
            with urllib.request.urlopen(urllib.request.Request(url, headers={'User-Agent': 'LK-Account-Recovery-Check'}), timeout=30) as response:
                if response.status != 200 or response.headers.get_content_type() != 'text/html' or response.url != url:
                    raise RuntimeError('Unexpected public HTTP response')
                check(response.read().decode('utf-8'), mode)
    print('Public homepage, login, registration and password-help forms verified (read-only GETs).')


def ftp_read(ftp, name, missing=False):
    data = io.BytesIO()
    try:
        ftp.retrbinary('RETR ' + name, data.write)
    except ftplib.error_perm as error:
        if missing and str(error).startswith('550'):
            # Distinguish an absent new file from a denied read of an existing file.
            parent, filename = str(PurePosixPath(name).parent), PurePosixPath(name).name
            if filename not in [PurePosixPath(p).name for p in ftp.nlst(parent)]:
                return None
        raise
    return data.getvalue()


def recover_master(manifest):
    backup = ROOT / '.account-recovery-backup'
    backup.mkdir(exist_ok=False)
    ftp = ftplib.FTP()
    ftp.connect(os.environ['FTP_SERVER'], 21, timeout=60)
    ftp.login(os.environ['FTP_USERNAME'], os.environ['FTP_PASSWORD'])
    originals, plan, changed, temps = {}, [], [], []
    nonce = os.environ['GITHUB_RUN_ID'] + '-' + os.environ['GITHUB_RUN_ATTEMPT']
    if not re.fullmatch('[0-9]+-[0-9]+', nonce):
        raise RuntimeError('Invalid Actions run identifiers')
    core_path = '/htdocs/wp-includes/version.php'
    try:
        core = digest(ftp_read(ftp, core_path))
        for row in manifest['files']:
            name = '/htdocs/' + row['path']
            data = ftp_read(ftp, name, missing=row['before_sha256'] is None)
            originals[name] = data
            if classify(data, row):
                plan.append((name, row))
            if data is not None:
                path = backup / row['path']
                path.parent.mkdir(parents=True, exist_ok=True)
                path.write_bytes(data)
        (backup / 'manifest.json').write_text(json.dumps(manifest, indent=2), encoding='utf-8')
        for name, row in plan:
            temp = name + '.lk-account-' + nonce
            temps.append(temp)
            ftp.storbinary('STOR ' + temp, io.BytesIO((PACKAGE / 'files' / row['path']).read_bytes()))
            if digest(ftp_read(ftp, temp)) != row['after_sha256']:
                raise RuntimeError('FTP staging integrity failed')
        for name, row in plan:
            if digest(ftp_read(ftp, name, missing=originals[name] is None)) != digest(originals[name]):
                raise RuntimeError('Destination changed during staging; no files activated')
        for name, row in plan:
            if digest(ftp_read(ftp, name, missing=originals[name] is None)) != digest(originals[name]):
                raise RuntimeError('Destination changed before activation')
            ftp.rename(name + '.lk-account-' + nonce, name)
            changed.append((name, row))
        for name, row in plan:
            if digest(ftp_read(ftp, name)) != row['after_sha256']:
                raise RuntimeError('FTP activation integrity failed')
        if digest(ftp_read(ftp, core_path)) != core:
            raise RuntimeError('WordPress core version file changed during recovery')
    except Exception:
        for name, row in reversed(changed):
            if digest(ftp_read(ftp, name)) != row['after_sha256']:
                raise RuntimeError('Concurrent edit prevents FTP rollback; backup is retained in Actions')
            if originals[name] is None:
                ftp.delete(name)
            else:
                ftp.storbinary('STOR ' + name, io.BytesIO(originals[name]))
        raise
    finally:
        for name in temps:
            try:
                ftp.delete(name)
            except ftplib.error_perm:
                pass
        ftp.close()
    print(f'Master: {len(changed)} account files changed; existing reviewed newer files preserved.')


def recover_prod(manifest):
    host = os.environ.get('PROD_SSH_HOST', '45.76.161.17')
    port = os.environ.get('PROD_SSH_PORT', '22')
    user = os.environ.get('PROD_SSH_USER', 'root')
    root = os.environ.get('PROD_WEB_ROOT', '/var/www/html')
    site = 'https://thodiatienhai.com'
    if not re.fullmatch('[a-zA-Z0-9.-]+', host) or not port.isdigit() or not 0 < int(port) < 65536 or not re.fullmatch('[a-z_][a-z0-9_-]*', user):
        raise RuntimeError('Invalid prod SSH destination')
    if not re.fullmatch('/[a-zA-Z0-9_./-]+', root) or '..' in root or root.endswith('/') or root.count('/') < 3:
        raise RuntimeError('Invalid prod web root')
    run = os.environ['GITHUB_RUN_ID'] + '-' + os.environ['GITHUB_RUN_ATTEMPT']
    if not re.fullmatch('[0-9]+-[0-9]+', run):
        raise RuntimeError('Invalid Actions run identifiers')
    stage = '/var/tmp/lk-account-' + run
    data = io.BytesIO()
    with tarfile.open(fileobj=data, mode='w:gz') as archive:
        for row in manifest['files']:
            archive.add(PACKAGE / 'files' / row['path'], arcname='files/' + row['path'], recursive=False)
        archive.add(PACKAGE / 'manifest.json', arcname='manifest.json')
        archive.add(ROOT / '.github/scripts/recover-account-update.php', arcname='update.php')
    with tempfile.TemporaryDirectory(prefix='lk-account-ssh-', dir=os.environ['RUNNER_TEMP']) as temporary:
        temp = Path(temporary)
        for name, key in [('key', 'PROD_SSH_PRIVATE_KEY'), ('known_hosts', 'PROD_SSH_KNOWN_HOSTS')]:
            path = temp / name
            path.write_text(os.environ[key].replace('\r', '') + '\n', encoding='utf-8')
            path.chmod(0o600)
        config = temp / 'config'
        config.write_text(f'Host recovery\n HostName {host}\n Port {port}\n User {user}\n IdentityFile {temp / "key"}\n UserKnownHostsFile {temp / "known_hosts"}\n IdentitiesOnly yes\n BatchMode yes\n StrictHostKeyChecking yes\n ConnectTimeout 20\n', encoding='utf-8')
        config.chmod(0o600)
        ssh = ['ssh', '-T', '-F', str(config), 'recovery']
        command = f'umask 077; test ! -e {shlex.quote(stage)} && mkdir -m 700 {shlex.quote(stage)} && tar -xz -C {shlex.quote(stage)}'
        subprocess.run(ssh + [command], input=data.getvalue(), check=True)
        shell = r'''set -Eeuo pipefail
umask 077
root=$1; stage=$2; site=$3; sha=$4
test "$(readlink -f "$root")" = "$root"
test "$(head -n 1 "$root/.lk-prod-deploy")" = "$site"
test -f "$root/wp-config.php"
for tool in php wp tar gzip sha256sum; do command -v "$tool" >/dev/null; done
core_before=$(sha256sum "$root/wp-includes/version.php")
php "$stage/update.php" check "$root" "$stage" > "$stage/plan.json"
backup="/root/longkhanh-prod-backups/account-$(date -u +%Y%m%dT%H%M%SZ)-${sha:0:12}"
test ! -e "$backup"
install -d -m 700 "$backup"
wp --allow-root --path="$root" db export "$backup/database.sql" --add-drop-table
gzip "$backup/database.sql"
tar --exclude='./wp-content/uploads' --exclude='./wp-content/cache' --exclude='./wp-content/debug.log' -czf "$backup/code.tar.gz" -C "$root" .
printf 'Backup: %s\n' "$backup"
php "$stage/update.php" apply "$root" "$stage"
test "$(sha256sum "$root/wp-includes/version.php")" = "$core_before"
# No Elementor flush, importer, marker, migration or database import.
wp --allow-root --path="$root" --url="$site" --skip-plugins=nextend-facebook-connect,nextend-social-login-pro cache flush
case "$stage" in /var/tmp/lk-account-[0-9]*-[0-9]*) rm -rf -- "$stage";; *) exit 1;; esac
'''
        command = 'bash -s -- ' + ' '.join(shlex.quote(v) for v in (root, stage, site, os.environ['GITHUB_SHA']))
        subprocess.run(ssh + [command], input=shell.encode(), check=True)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--environment', choices=['master', 'prod'])
    parser.add_argument('--guard-full-deploy', action='store_true')
    args = parser.parse_args()
    if args.guard_full_deploy:
        message = subprocess.check_output(['git', 'log', '-1', '--format=%B'], cwd=ROOT, text=True)
        changed = subprocess.check_output(['git', 'diff', '--name-only', 'HEAD^', 'HEAD'], cwd=ROOT, text=True).splitlines()
        if message.startswith(PREFIX) or any(p.startswith(('.github/account-recovery/', '.github/scripts/recover-account')) or p == '.github/workflows/recover-account.yml' for p in changed):
            raise RuntimeError('Recovery changes require the recover-account-only: commit prefix and the scoped workflow; full deploy refused')
        return
    if args.environment is None:
        parser.error('--environment is required for the scoped recovery')
    if os.environ.get('GITHUB_ACTIONS') != 'true' or os.environ.get('GITHUB_REF') != 'refs/heads/' + args.environment:
        raise RuntimeError('Remote recovery is restricted to the matching GitHub Actions branch')
    if not re.fullmatch('[a-f0-9]{40}', os.environ.get('GITHUB_SHA', '')):
        raise RuntimeError('Missing Actions commit SHA')
    check_commit_scope()
    manifest = validate_package()
    if args.environment == 'master':
        recover_master(manifest)
        verify_http('https://longkhanhsites.site.je', browser=True, allow_host_challenge=True)
    else:
        successful_master_required()
        recover_prod(manifest)
        verify_http('https://thodiatienhai.com')


if __name__ == '__main__':
    try:
        main()
    except Exception as error:
        # Never emit raw FTP/SSH/network exceptions which may include private details.
        safe = str(error) if isinstance(error, RuntimeError) else type(error).__name__
        raise SystemExit('Account recovery stopped: ' + safe)
