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

    def run_check(self, statuses, warmup_challenge=False):
        pending = iter(statuses)

        def response(url, body, content_type='application/json', status=200):
            result = BytesIO(body)
            result.url, result.status = url, status
            result.headers = {'Content-Type': content_type}
            return result

        def respond(request, **kwargs):
            if '/wp-login.php?' in request.full_url:
                if warmup_challenge:
                    return response(request.full_url, b'<script src="/aes.js"></script><script>slowAES.decrypt()</script>', 'text/html')
                return response(request.full_url, b'<html>WordPress booted</html>', 'text/html')
            state = next(pending)
            if isinstance(state, Exception):
                raise state
            if isinstance(state, bytes):
                return response(request.full_url, state, 'text/html')
            return response(request.full_url, json.dumps(state).encode())

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

    def test_import_error_includes_safe_code_and_asset_identifier(self):
        error = self.run_check([self.status(status='error', error_code='asset_integrity', asset='b' * 64)])
        self.assertIn('error_code=asset_integrity', error)
        self.assertIn('asset=' + 'b' * 64, error)

    def test_private_exception_message_is_not_echoed_to_actions(self):
        error = self.run_check([self.status(status='error', message='password=SECRET; /private/database.sql', error_code='bad\nSECRET')])
        self.assertNotIn('SECRET', error)
        self.assertNotIn('database.sql', error)

    def test_transient_http_error_retries(self):
        self.assertEqual(self.run_check([URLError('temporary'), self.status()]), 0)

    def test_host_javascript_challenge_reports_the_actual_problem(self):
        error = self.run_check([b'<script src="/aes.js"></script><script>slowAES.decrypt()</script>'])
        self.assertIn('JavaScript browser check', error)
        self.assertIn('--browser', error)

    def test_challenge_on_warmup_is_not_silently_ignored(self):
        self.assertIn('JavaScript browser check', self.run_check([self.status()], warmup_challenge=True))

    def test_html_response_is_not_a_successful_deployment(self):
        error = self.run_check([b'<html>PHP error page</html>'])
        self.assertIn('received HTML', error)
        self.assertNotIn('line 1 column 1', error)

    def test_empty_response_has_context(self):
        error = self.run_check([b''])
        self.assertIn('/wp-content/lk-layouts/applied.json', error)
        self.assertIn('empty or invalid JSON', error)

    def test_json_array_cannot_pass_as_a_status_object(self):
        self.assertIn('JSON status object', self.run_check([[]]))

    def test_utf8_bom_json_is_accepted(self):
        self.assertEqual(self.run_check([b'\xef\xbb\xbf' + json.dumps(self.status()).encode()]), 0)

    def test_pending_import_never_reports_success(self):
        self.assertIn('not verified', self.run_check([self.status(status='pending')]))


if __name__ == '__main__':
    unittest.main()
