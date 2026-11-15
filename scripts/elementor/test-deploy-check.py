"""Offline contract tests: the workflow must confirm the exact applied release."""
from contextlib import redirect_stdout
from io import BytesIO, StringIO
from pathlib import Path
from unittest.mock import patch
from urllib.error import URLError
import json
import runpy
import unittest

SCRIPT = Path(__file__).with_name('check-deploy.py')
ROOT = SCRIPT.parents[2]
PACKAGE = json.loads((ROOT / 'wp-content/lk-layouts/manifest.json').read_text(encoding='utf-8'))
COMMIT = 'a' * 40


class DeploymentCheckTest(unittest.TestCase):
    def status(self, **changes):
        state = dict(status='applied', release=PACKAGE['release'], commit=COMMIT, environment='prod')
        state.update(changes)
        return state

    def run_check(self, statuses):
        pending = iter(statuses)

        def respond(request, **kwargs):
            if '/wp-login.php?' in request.full_url:
                return BytesIO(b'<html>WordPress booted</html>')
            state = next(pending)
            if isinstance(state, Exception):
                raise state
            return BytesIO(json.dumps(state).encode())

        argv = [str(SCRIPT), '--environment', 'prod', '--commit', COMMIT,
                '--attempts', str(len(statuses)), '--interval', '0']
        with patch('sys.argv', argv), patch('urllib.request.urlopen', side_effect=respond), redirect_stdout(StringIO()):
            with self.assertRaises(SystemExit) as result:
                runpy.run_path(str(SCRIPT), run_name='__main__')
        return result.exception.code

    def test_exact_release_commit_environment_succeeds(self):
        self.assertEqual(self.run_check([self.status()]), 0)

    def test_stale_release_fails(self):
        self.assertIn('not verified', self.run_check([self.status(release='b' * 64)]))

    def test_stale_commit_fails(self):
        self.assertIn('not verified', self.run_check([self.status(commit='b' * 40)]))

    def test_wrong_environment_fails(self):
        self.assertIn('not verified', self.run_check([self.status(environment='master')]))

    def test_import_error_fails_the_job(self):
        self.assertIn('refused', self.run_check([self.status(status='error')]))

    def test_transient_http_error_retries(self):
        self.assertEqual(self.run_check([URLError('temporary'), self.status()]), 0)


if __name__ == '__main__':
    unittest.main()
