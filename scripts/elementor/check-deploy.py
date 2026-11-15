"""Workflow warmup and exact release verification, via HTTP or a real browser."""
from dataclasses import dataclass
from pathlib import Path
from urllib.error import HTTPError
from urllib.parse import urlsplit
from urllib.request import Request, urlopen
import argparse
import json
import re
import sys
import time


class ResponseError(Exception):
    """A response is not yet usable; retry without accepting a false success."""


class BrowserRequired(ResponseError):
    pass


@dataclass
class Payload:
    url: str
    status: int
    content_type: str
    body: bytes

    def description(self):
        return f'{urlsplit(self.url).path}: HTTP {self.status}, {self.content_type or "unknown content type"}'


def same_origin(url, origin):
    def parts(value):
        parsed = urlsplit(value)
        return parsed.scheme.lower(), parsed.hostname, parsed.port or (443 if parsed.scheme == 'https' else 80)
    return parts(url) == parts(origin)


def browser_challenge(body):
    value = body.lower()
    return b'/aes.js' in value and (b'slowaes' in value or b'this site requires javascript' in value)


def validate_response(payload, origin):
    if not same_origin(payload.url, origin):
        raise ResponseError('The website redirected outside its configured origin.')
    if not 200 <= payload.status < 300:
        raise ResponseError(payload.description())
    if browser_challenge(payload.body):
        raise BrowserRequired('Hosting returned a JavaScript browser check; use --browser with the Playwright dependencies installed.')
    return payload


def parse_status(payload):
    try:
        state = json.loads(payload.body.decode('utf-8-sig'))
    except (ValueError, UnicodeError):
        kind = 'HTML' if payload.body.lstrip().startswith(b'<') else 'empty or invalid JSON'
        raise ResponseError(f'Expected JSON from {payload.description()}; received {kind}. Check the uploaded path and PHP errors.') from None
    if not isinstance(state, dict):
        raise ResponseError(f'Expected a JSON status object from {payload.description()}.')
    return state


class HttpClient:
    def __init__(self, origin):
        self.origin = origin

    def __enter__(self):
        return self

    def __exit__(self, *args):
        pass

    def read(self, url, timeout):
        try:
            request = Request(url, headers={'User-Agent': 'TienHai-Layout-Deployment', 'Cache-Control': 'no-cache'})
            with urlopen(request, timeout=timeout) as response:
                payload = Payload(response.url, response.status, response.headers.get('Content-Type', ''), response.read())
        except HTTPError as error:
            raise ResponseError(f'{urlsplit(url).path}: HTTP {error.code}') from None
        return validate_response(payload, self.origin)


class BrowserClient:
    """Let Chromium run the host's normal JS/cookie check in one browser session."""
    def __init__(self, origin, channel=None):
        self.origin = origin
        self.channel = channel
        self.driver = self.browser = None
        self.navigation = None

    def __enter__(self):
        try:
            from playwright.sync_api import sync_playwright
        except ImportError:
            raise ResponseError('Install scripts/elementor/requirements-browser.txt and run python -m playwright install chromium.') from None
        try:
            self.driver = sync_playwright().start()
            options = {'headless': True}
            if self.channel:
                options['channel'] = self.channel
            self.browser = self.driver.chromium.launch(**options)
            self.context = self.browser.new_context(
                extra_http_headers={'Cache-Control': 'no-cache'}, service_workers='block')
            # Only this site's requests are needed for warmup and static status.
            self.context.route('**/*', self.route)
            self.page = self.context.new_page()
            self.page.on('response', self.response)
            return self
        except Exception as error:
            self.__exit__()
            raise ResponseError('Browser startup failed: ' + str(error).splitlines()[0]) from None

    def __exit__(self, *args):
        try:
            if self.browser:
                self.browser.close()
        finally:
            if self.driver:
                self.driver.stop()

    def route(self, route):
        if same_origin(route.request.url, self.origin):
            route.continue_()
        else:
            route.abort()

    def response(self, response):
        if response.request.is_navigation_request() and response.frame == self.page.main_frame:
            self.navigation = response

    def read(self, url, timeout):
        if not same_origin(url, self.origin):
            raise ResponseError('Browser request is outside the configured origin.')
        self.navigation = None
        deadline = time.monotonic() + timeout
        try:
            self.page.goto(url, wait_until='domcontentloaded', timeout=timeout * 1000)
            # The initial 200 may be a JS redirect. Wait for its replacement,
            # then read the final network response, not Chromium's JSON viewer.
            self.page.wait_for_function("""() => {
                const markup = (document.documentElement?.outerHTML || '').toLowerCase();
                return !(markup.includes('/aes.js') &&
                    (markup.includes('slowaes') || markup.includes('this site requires javascript')));
            }""", timeout=max(1, int((deadline - time.monotonic()) * 1000)))
            response = self.navigation
            if response is None:
                raise ResponseError('Browser returned no navigation response.')
            payload = Payload(response.url, response.status, response.headers.get('content-type', ''), response.body())
        except ResponseError:
            raise
        except Exception as error:
            raise ResponseError(f'Browser request failed for {urlsplit(url).path}: {str(error).splitlines()[0]}') from None
        return validate_response(payload, self.origin)


def verify(client, origin, package, args):
    last = 'No response'
    for attempt in range(args.attempts):
        try:
            # wp-login.php boots WP without logging in and avoids page caches.
            client.read(origin + '/wp-login.php?lk_layout_warmup=' + args.commit, timeout=40)
            state = parse_status(client.read(origin + '/wp-content/lk-layouts/applied.json?commit=' + args.commit, timeout=20))
            exact = (state.get('release') == package['release'] and state.get('commit') == args.commit
                     and state.get('environment') == args.environment)
            if exact and state.get('status') == 'error':
                raise SystemExit('Layout import refused. Inspect WordPress Tools > Layout Sync; do not bypass conflicts.')
            if exact and state.get('status') == 'applied':
                print(f"Verified {args.environment}: layout {package['release']} applied at commit {args.commit}.")
                return 0
            last = 'Waiting for status=applied with the exact release, commit and environment.'
        except BrowserRequired as error:
            raise SystemExit('Layout deployment was not verified: ' + str(error)) from None
        except (OSError, ResponseError) as error:
            last = str(error)
        print(f'Attempt {attempt + 1}/{args.attempts}: {last}', flush=True)
        if attempt + 1 < args.attempts:
            time.sleep(args.interval)
    raise SystemExit('Layout deployment was not verified: ' + last)


def main(argv=None):
    parser = argparse.ArgumentParser()
    parser.add_argument('--environment', choices=['master', 'prod'], required=True)
    parser.add_argument('--commit', required=True)
    parser.add_argument('--attempts', type=int, default=30)
    parser.add_argument('--interval', type=float, default=3)
    parser.add_argument('--browser', action='store_true', help='Use Chromium for hosts requiring JavaScript and cookies.')
    parser.add_argument('--browser-channel', help='Optional installed browser channel for local fixture tests, e.g. msedge.')
    args = parser.parse_args(argv)
    if not re.fullmatch(r'[a-f0-9]{40}', args.commit) or args.attempts < 1 or args.interval < 0:
        parser.error('Expected a full Git SHA, positive attempts and a nonnegative interval.')
    if args.browser_channel and not args.browser:
        parser.error('--browser-channel requires --browser.')
    root = Path(__file__).resolve().parents[2]
    policy = json.loads((root / 'wp-content/mu-plugins/lk-layout-sync/scopes.json').read_text(encoding='utf-8'))
    package = json.loads((root / 'wp-content/lk-layouts/manifest.json').read_text(encoding='utf-8'))
    origin = policy['production_origin' if args.environment == 'prod' else 'master_origin'].rstrip('/')
    client = BrowserClient(origin, args.browser_channel) if args.browser else HttpClient(origin)
    print(f'Verifying {args.environment} at {origin} using {"Chromium" if args.browser else "HTTP"}; commit {args.commit}.', flush=True)
    try:
        with client:
            return verify(client, origin, package, args)
    except ResponseError as error:
        raise SystemExit('Layout deployment was not verified: ' + str(error)) from None


if __name__ == '__main__':
    raise SystemExit(main())
