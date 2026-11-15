"""Workflow HTTP warmup triggers the gated layout importer, then checks its status."""
from pathlib import Path
from urllib.request import Request, urlopen
import argparse, json, sys, time

parser=argparse.ArgumentParser()
parser.add_argument('--environment', choices=['master','prod'], required=True)
parser.add_argument('--commit', required=True)
parser.add_argument('--attempts', type=int, default=30)
parser.add_argument('--interval', type=float, default=3)
args=parser.parse_args()
root=Path(__file__).resolve().parents[2]
policy=json.loads((root/'wp-content/mu-plugins/lk-layout-sync/scopes.json').read_text(encoding='utf-8'))
package=json.loads((root/'wp-content/lk-layouts/manifest.json').read_text(encoding='utf-8'))
origin=policy['production_origin' if args.environment=='prod' else 'master_origin'].rstrip('/')
headers={'User-Agent':'LongKhanh-Layout-Deployment','Cache-Control':'no-cache'}
last='No response'
for attempt in range(args.attempts):
    try:
        # wp-login.php boots WordPress and bypasses normal page caches. No login,
        # password or authenticated request is used, and no content is posted.
        with urlopen(Request(origin+'/wp-login.php?lk_layout_warmup='+args.commit,headers=headers),timeout=40) as response:
            response.read()
        with urlopen(Request(origin+'/wp-content/lk-layouts/applied.json?commit='+args.commit,headers=headers),timeout=20) as response:
            state=json.load(response)
        if state.get('release')==package['release'] and state.get('commit')==args.commit and state.get('environment')==args.environment:
            if state.get('status')=='error':
                raise SystemExit('Layout import refused. Inspect WordPress Tools > Layout Sync; do not bypass conflicts.')
            if state.get('status')=='applied':
                print(f"Verified {args.environment}: layout {package['release']} applied at commit {args.commit}.")
                sys.exit(0)
        last='Waiting for the exact release/commit marker'
    except (OSError,ValueError) as error:
        last=str(error)
    if attempt+1<args.attempts:time.sleep(args.interval)
raise SystemExit('Layout deployment was not verified: '+last)
