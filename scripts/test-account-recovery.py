"""Offline recovery tests: disposable filesystem/FTP fixtures, never a server."""
from contextlib import redirect_stdout, contextmanager
import ftplib
import importlib.util
import io
import json
import os
from pathlib import Path
import shutil
import subprocess
import tempfile
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


if __name__ == '__main__':
    unittest.main(verbosity=2)
