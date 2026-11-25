"""Offline recovery tests: disposable filesystem/FTP fixtures, never a server."""
from contextlib import redirect_stdout, contextmanager
import ftplib
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
import importlib.util
import io
import json
import os
from pathlib import Path
import shutil
import subprocess
import sys
import tempfile
import threading
from types import SimpleNamespace
import unittest
from unittest.mock import patch

ROOT = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location('account_recovery', ROOT / '.github/scripts/recover-account.py')
recovery = importlib.util.module_from_spec(spec)
spec.loader.exec_module(recovery)
PACKAGE = recovery.PACKAGE
UPDATER = ROOT / '.github/scripts/recover-account-update.php'
MANIFEST = recovery.validate_package()
BROWSER_TESTS = '--browser' in sys.argv
if BROWSER_TESTS:
    sys.argv.remove('--browser')


@contextmanager
def recovery_env(values):
    # Replacing only the Python mapping avoids Windows dropping inherited empty
    # Git config values when patch.dict restores the process environment.
    with patch.object(recovery.os, 'environ', dict(os.environ, **values)):
        yield


def git_bytes(commit, name):
    return subprocess.check_output(['git', 'show', commit + ':' + name], cwd=ROOT)


class FakeFTP:
    def __init__(self, files, fail_rename=0):
        self.files = files
        self.fail_rename = fail_rename
        self.renames = 0

    def connect(self, *args, **kwargs):
        pass

    def login(self, *args):
        pass

    def retrbinary(self, command, callback):
        name = command[5:]
        if name not in self.files:
            raise ftplib.error_perm('550 missing')
        callback(self.files[name])

    def nlst(self, parent):
        return [p for p in self.files if str(Path(p).parent).replace('\\', '/') == parent]

    def storbinary(self, command, source):
        self.files[command[5:]] = source.read()

    def rename(self, before, after):
        self.renames += 1
        if self.renames == self.fail_rename:
            raise ftplib.error_temp('450 injected publish failure')
        self.files[after] = self.files.pop(before)

    def delete(self, name):
        if name not in self.files:
            raise ftplib.error_perm('550 missing')
        del self.files[name]

    def close(self):
        pass


class RecoveryTests(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory(prefix='lk-account-offline-')
        self.addCleanup(self.temp.cleanup)
        self.directory = Path(self.temp.name)
        self.web = self.directory / 'web'
        self.web.mkdir()
        self.stage = self.directory / 'stage'
        shutil.copytree(PACKAGE, self.stage)
        for row in MANIFEST['files']:
            path = self.web / row['path']
            path.parent.mkdir(parents=True, exist_ok=True)
            if row['before_sha256'] is not None:
                path.write_bytes(git_bytes(MANIFEST['base_commit'], row['path']))
        for name, data in {'wp-includes/version.php': b'preserved core',
                           'wp-config.php': b'private local fixture',
                           'wp-content/uploads/keep.jpg': b'preserved media',
                           'wp-content/lk-layouts/manifest.json': b'preserved Elementor',
                           'wp-content/lk-layouts/ready.json': b'preserved readiness',
                           'wp-content/lk-layouts/applied.json': b'preserved layout status',
                           'wp-content/plugins/long-khanh-directory/includes/community.php': b'preserved community'}.items():
            path = self.web / name
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_bytes(data)

    def snapshot(self):
        return {p.relative_to(self.web).as_posix(): p.read_bytes() for p in self.web.rglob('*') if p.is_file()}

    def php(self, mode, ok=True, updater=UPDATER):
        result = subprocess.run(['php', str(updater), mode, str(self.web.resolve()), str(self.stage.resolve())], capture_output=True, text=True)
        if ok:
            self.assertEqual(result.returncode, 0, result.stderr)
        else:
            self.assertNotEqual(result.returncode, 0, 'Expected refusal')
        if mode == 'check' and result.returncode == 0:
            (self.stage / 'plan.json').write_text(result.stdout, encoding='utf-8')
        return result

    def test_exact_payload_and_auth_only_scope(self):
        self.assertEqual([r['path'] for r in MANIFEST['files']], recovery.PATHS)
        account = (PACKAGE / 'files' / recovery.PATHS[0]).read_text(encoding='utf-8')
        self.assertIn("foreach(array('profile') as $candidate)", account)
        self.assertNotIn('profile|new-review|explore', account)

    def test_php_restores_only_seven_files_and_preserves_every_other_byte(self):
        before = self.snapshot()
        self.php('check')
        self.assertEqual(self.snapshot(), before)
        self.php('apply')
        after = self.snapshot()
        self.assertEqual({p for p in before.keys() | after.keys() if before.get(p) != after.get(p)}, set(recovery.PATHS))
        for row in MANIFEST['files']:
            self.assertEqual(recovery.digest(after[row['path']]), row['after_sha256'])

    def test_php_preserves_existing_complete_21_november_fix(self):
        for row in MANIFEST['files']:
            (self.web / row['path']).write_bytes(git_bytes(MANIFEST['source_commit'], row['path']))
        before = self.snapshot()
        self.php('check')
        self.php('apply')
        self.assertEqual(self.snapshot(), before)

    def test_php_repeat_is_idempotent(self):
        self.php('check')
        self.php('apply')
        before = self.snapshot()
        self.php('check')
        self.php('apply')
        self.assertEqual(self.snapshot(), before)

    def test_php_refuses_destination_edit_without_any_writes(self):
        (self.web / recovery.PATHS[4]).write_bytes(b'owner changes')
        before = self.snapshot()
        self.php('check', ok=False)
        self.assertEqual(self.snapshot(), before)

    def test_php_refuses_concurrent_edit_after_preflight(self):
        self.php('check')
        (self.web / recovery.PATHS[4]).write_bytes(b'concurrent owner changes')
        before = self.snapshot()
        self.php('apply', ok=False)
        self.assertEqual(self.snapshot(), before)

    def test_php_refuses_tampered_payload(self):
        (self.stage / 'files' / recovery.PATHS[0]).write_bytes(b'bad payload')
        before = self.snapshot()
        self.php('check', ok=False)
        self.assertEqual(self.snapshot(), before)

    def test_php_publish_failure_restores_all_original_bytes(self):
        before = self.snapshot()
        self.php('check')
        failing = self.directory / 'failing-updater.php'
        source = UPDATER.read_text(encoding='utf-8')
        injection = r'''<?php
namespace LK_Recovery_Fault_Test;
use \RuntimeException;
use \Throwable;
function rename($before,$after){static $count=0;if(++$count===6)return false;return \rename($before,$after);}
'''
        failing.write_text(injection + source.removeprefix('<?php'), encoding='utf-8')
        self.php('apply', ok=False, updater=failing)
        self.assertEqual(self.snapshot(), before)

    def test_php_refuses_allowlist_expansion(self):
        manifest = json.loads((self.stage / 'manifest.json').read_text(encoding='utf-8'))
        manifest['files'][0]['path'] = 'wp-config.php'
        (self.stage / 'manifest.json').write_text(json.dumps(manifest), encoding='utf-8')
        before = self.snapshot()
        self.php('check', ok=False)
        self.assertEqual(self.snapshot(), before)

    @unittest.skipIf(os.name == 'nt', 'Symlink creation may require Windows administrator rights')
    def test_php_refuses_symlink_destination(self):
        outside = self.directory / 'outside'
        outside.write_bytes(b'outside data')
        target = self.web / recovery.PATHS[4]
        target.unlink()
        target.symlink_to(outside)
        self.php('check', ok=False)
        self.assertEqual(outside.read_bytes(), b'outside data')

    def ftp_fixture(self, fail_rename=0):
        files = {'/htdocs/' + p: b for p, b in self.snapshot().items()}
        return FakeFTP(files, fail_rename)

    def run_ftp(self, ftp):
        with patch.object(recovery, 'ROOT', self.directory), patch.object(recovery.ftplib, 'FTP', return_value=ftp), recovery_env({
            'FTP_SERVER': 'fixture.invalid', 'FTP_USERNAME': 'fixture', 'FTP_PASSWORD': 'fixture',
            'GITHUB_RUN_ID': '1', 'GITHUB_RUN_ATTEMPT': '1'}), redirect_stdout(io.StringIO()):
            recovery.recover_master(MANIFEST)

    def test_ftp_changes_only_seven_paths_after_backing_up_originals(self):
        ftp = self.ftp_fixture()
        before = dict(ftp.files)
        self.run_ftp(ftp)
        changed = {p for p in before.keys() | ftp.files.keys() if before.get(p) != ftp.files.get(p)}
        self.assertEqual(changed, {'/htdocs/' + p for p in recovery.PATHS})
        for row in MANIFEST['files']:
            if row['before_sha256'] is not None:
                self.assertEqual((self.directory / '.account-recovery-backup' / row['path']).read_bytes(), before['/htdocs/' + row['path']])

    def test_ftp_publish_failure_restores_original_bytes(self):
        ftp = self.ftp_fixture(fail_rename=6)
        before = dict(ftp.files)
        with self.assertRaises(ftplib.error_temp):
            self.run_ftp(ftp)
        self.assertEqual(ftp.files, before)

    def test_ftp_owner_edit_stops_before_any_upload(self):
        ftp = self.ftp_fixture()
        ftp.files['/htdocs/' + recovery.PATHS[4]] = b'owner edit'
        before = dict(ftp.files)
        with self.assertRaisesRegex(RuntimeError, 'differs'):
            self.run_ftp(ftp)
        self.assertEqual(ftp.files, before)
        self.assertEqual(ftp.renames, 0)

    def test_prod_gate_requires_successful_master_for_identical_manifest(self):
        response = io.BytesIO(json.dumps({'workflow_runs': []}).encode())
        with recovery_env({'GITHUB_REPOSITORY': 'fixture/repo', 'GH_TOKEN': 'fixture'}), patch.object(recovery.urllib.request, 'urlopen', return_value=response):
            with self.assertRaisesRegex(RuntimeError, 'requires a successful master'):
                recovery.successful_master_required()

    def test_prod_gate_accepts_only_ancestor_with_same_package(self):
        sha = '1' * 40
        response = io.BytesIO(json.dumps({'workflow_runs': [{'head_sha': sha}]}).encode())
        manifest = (PACKAGE / 'manifest.json').read_bytes()
        def git_result(args, **kwargs):
            return SimpleNamespace(returncode=0, stdout=manifest if args[1] == 'show' else b'')
        with recovery_env({'GITHUB_REPOSITORY': 'fixture/repo', 'GH_TOKEN': 'fixture'}), patch.object(recovery.urllib.request, 'urlopen', return_value=response), patch.object(recovery.subprocess, 'run', side_effect=git_result), redirect_stdout(io.StringIO()):
            recovery.successful_master_required()

    def test_workflow_routes_recovery_away_from_full_deploys(self):
        for name in ('deploy.yml', 'deploy-prod.yml'):
            workflow = (ROOT / '.github/workflows' / name).read_text(encoding='utf-8')
            self.assertIn("!startsWith(github.event.head_commit.message, 'recover-account-only:')", workflow)
            self.assertIn('--guard-full-deploy', workflow)
        scoped = (ROOT / '.github/workflows/recover-account.yml').read_text(encoding='utf-8')
        self.assertIn('environment: prod', scoped)
        self.assertIn('actions: read', scoped)
        self.assertIn('include-hidden-files: true', scoped)

    def test_public_url_accepts_only_the_observed_master_challenge_marker(self):
        for query in ('', '?lk_account=login', '?lk_account=register', '?lk_account=lost'):
            expected = 'https://fixture.invalid/' + query
            actual = expected + ('&i=1' if query else '?i=1')
            with self.subTest(query=query):
                recovery.require_public_url(actual, expected, allow_host_challenge=True)
                with self.assertRaisesRegex(RuntimeError, 'query parameters changed'):
                    recovery.require_public_url(actual, expected)

    def test_public_url_rejects_lost_mode_extra_parameters_and_other_destinations(self):
        expected = 'https://fixture.invalid/?lk_account=register'
        invalid = (
            'https://fixture.invalid/?i=1',
            'https://fixture.invalid/?lk_account=login&i=1',
            expected + '&i=2',
            expected + '&i=1&i=1',
            expected + '&i=1&redirect_to=elsewhere',
            expected + '&i=1&lk_account=login',
            expected + '#another-page',
            'https://other.invalid/?lk_account=register&i=1',
            'http://fixture.invalid/?lk_account=register&i=1',
            'https://fixture.invalid/profile/?lk_account=register&i=1',
        )
        for actual in invalid:
            with self.subTest(actual=actual), self.assertRaisesRegex(RuntimeError, 'Unexpected public redirect'):
                recovery.require_public_url(actual, expected, allow_host_challenge=True)


class RecoveryBrowserFixture(BaseHTTPRequestHandler):
    def log_message(self, *args):
        pass

    def do_GET(self):
        self.server.requests.append((self.path, self.headers.get('Cookie', '')))
        parts = recovery.urllib.parse.urlsplit(self.path)
        query = dict(recovery.urllib.parse.parse_qsl(parts.query))
        self.send_response(self.server.status_code if query.get('i') == '1' else 200)
        self.send_header('Content-Type', 'text/html; charset=utf-8')
        self.end_headers()
        if query.get('i') != '1':
            # Reproduce the host by executing JS and setting the cookie normally.
            target = self.path + ('&i=1' if parts.query else '?i=1')
            if self.server.drop_mode and 'lk_account' in query:
                target = '/?i=1'
            html = '<script>document.cookie="recovery_fixture=ready; path=/"; location.replace(' + json.dumps(target) + ');</script>'
        elif 'lk_account' in query or self.server.drop_mode:
            mode = query.get('lk_account', 'login')
            body = 'Fixture missing form' if self.server.missing_form else '<form><input name="action" value="lk_' + mode + '"></form>'
            html = '<html><body class="lk-site lk-auth-page">' + body + '</body></html>'
        else:
            html = '<html><body class="lk-site">Fixture homepage</body></html>'
        self.wfile.write(html.encode())


@unittest.skipUnless(BROWSER_TESTS, 'Run --browser RecoveryBrowserTests after installing Playwright')
class RecoveryBrowserTests(unittest.TestCase):
    def setUp(self):
        self.server = ThreadingHTTPServer(('127.0.0.1', 0), RecoveryBrowserFixture)
        self.server.requests = []
        self.server.drop_mode = False
        self.server.missing_form = False
        self.server.status_code = 200
        self.thread = threading.Thread(target=self.server.serve_forever, daemon=True)
        self.thread.start()
        self.origin = 'http://127.0.0.1:' + str(self.server.server_port)
        self.addCleanup(self.stop_server)

    def stop_server(self):
        self.server.shutdown()
        self.server.server_close()
        self.thread.join()

    def verify(self):
        with redirect_stdout(io.StringIO()):
            recovery.verify_http(self.origin, browser=True, allow_host_challenge=True)

    def test_js_challenge_retains_account_modes_and_session_cookie(self):
        self.verify()
        visited = [path for path, cookie in self.server.requests if 'i=1' in path and 'recovery_fixture=ready' in cookie]
        for mode in ('login', 'register', 'lost'):
            self.assertIn('/?lk_account=' + mode + '&i=1', visited)

    def test_a_redirect_dropping_account_mode_still_fails(self):
        self.server.drop_mode = True
        with self.assertRaisesRegex(RuntimeError, 'functional query parameters changed'):
            self.verify()

    def test_missing_form_still_fails_after_a_valid_host_challenge(self):
        self.server.missing_form = True
        with self.assertRaisesRegex(RuntimeError, 'form is missing'):
            self.verify()

    def test_http_500_still_fails_with_valid_site_markup(self):
        self.server.status_code = 500
        with self.assertRaisesRegex(RuntimeError, 'HTTP response'):
            self.verify()


if __name__ == '__main__':
    unittest.main(verbosity=2)
