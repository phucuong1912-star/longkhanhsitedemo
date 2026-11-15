"""Real-browser integration against a local JS/cookie fixture; no remote site or WP DB."""
from contextlib import redirect_stdout
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from importlib.util import module_from_spec, spec_from_file_location
from io import StringIO
from pathlib import Path
from types import SimpleNamespace
import json
import os
import sys
import threading
import unittest

SCRIPT = Path(__file__).with_name('check-deploy.py')
SPEC = spec_from_file_location('layout_deploy_check', SCRIPT)
CHECK = module_from_spec(SPEC)
sys.modules[SPEC.name] = CHECK
SPEC.loader.exec_module(CHECK)
PACKAGE = {'release': 'c' * 64}
COMMIT = 'a' * 40


class Fixture(BaseHTTPRequestHandler):
    def log_message(self, *args):
        pass

    def send(self, body, kind='text/html', status=200):
        self.send_response(status)
        self.send_header('Content-Type', kind)
        self.end_headers()
        self.wfile.write(body.encode())

    def challenge(self, cookie):
        self.send('<script src="/aes.js"></script><script>const slowAES = {}; '
                  f'document.cookie="{cookie}=ready; path=/"; location.replace(location.href);</script>')

    def do_GET(self):
        cookie = self.headers.get('Cookie', '')
        self.server.requests.append((self.path, cookie))
        if self.path == '/aes.js':
            self.send('// Local fixture for the JS loader', 'text/javascript')
        elif 'layout_fixture=ready' not in cookie:
            self.challenge('layout_fixture')
        elif self.path.startswith('/wp-login.php'):
            self.send('<html><body>Fixture WordPress booted</body></html>')
        elif self.path.startswith('/?lk_deploy='):
            self.send('<html><body>Fixture public home page</body></html>', status=self.server.homepage_status)
        elif self.path.startswith('/wp-content/lk-layouts/applied.json'):
            if self.server.challenge_status and 'layout_status_fixture=ready' not in cookie:
                self.challenge('layout_status_fixture')
            elif self.server.html_status:
                self.send('<html>Fixture server error</html>')
            else:
                self.send(json.dumps(self.server.state), 'application/json', self.server.status_code)
        else:
            self.send('Missing', 'text/plain', 404)


class BrowserVerificationTest(unittest.TestCase):
    def setUp(self):
        self.server = ThreadingHTTPServer(('127.0.0.1', 0), Fixture)
        self.server.requests = []
        self.server.state = dict(status='applied', release=PACKAGE['release'], commit=COMMIT, environment='master')
        self.server.challenge_status = False
        self.server.html_status = False
        self.server.status_code = 200
        self.server.homepage_status = 200
        self.thread = threading.Thread(target=self.server.serve_forever, daemon=True)
        self.thread.start()
        self.origin = 'http://127.0.0.1:' + str(self.server.server_port)
        self.args = SimpleNamespace(environment='master', commit=COMMIT, attempts=1, interval=0)

    def tearDown(self):
        self.server.shutdown()
        self.server.server_close()
        self.thread.join()

    def verify(self):
        channel = os.environ.get('LAYOUT_TEST_BROWSER_CHANNEL')
        with CHECK.BrowserClient(self.origin, channel) as client, redirect_stdout(StringIO()):
            return CHECK.verify(client, self.origin, PACKAGE, self.args)

    def test_plain_http_reproduces_the_host_challenge(self):
        with CHECK.HttpClient(self.origin) as client:
            with self.assertRaises(CHECK.BrowserRequired):
                client.read(self.origin + '/wp-login.php', timeout=5)

    def test_browser_executes_javascript_and_keeps_cookie_for_status(self):
        self.assertEqual(self.verify(), 0)
        status_requests = [cookie for path, cookie in self.server.requests if 'applied.json' in path]
        self.assertTrue(status_requests)
        self.assertTrue(all('layout_fixture=ready' in cookie for cookie in status_requests))

    def test_challenge_on_json_endpoint_is_executed_before_parsing(self):
        self.server.challenge_status = True
        self.assertEqual(self.verify(), 0)
        self.assertTrue(any('layout_status_fixture=ready' in cookie for path, cookie in self.server.requests if 'applied.json' in path))

    def test_browser_does_not_accept_a_stale_commit(self):
        self.server.state['commit'] = 'b' * 40
        with self.assertRaisesRegex(SystemExit, 'not verified'):
            self.verify()

    def test_html_does_not_pass_as_json_in_browser_mode(self):
        self.server.html_status = True
        with self.assertRaisesRegex(SystemExit, 'received HTML'):
            self.verify()

    def test_http_error_does_not_pass_with_a_matching_json_body(self):
        self.server.status_code = 404
        with self.assertRaisesRegex(SystemExit, 'HTTP 404'):
            self.verify()

    def test_successful_import_with_homepage_500_is_refused(self):
        self.server.homepage_status = 500
        with self.assertRaisesRegex(SystemExit, 'HTTP 500'):
            self.verify()


if __name__ == '__main__':
    unittest.main()
