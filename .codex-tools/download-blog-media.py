from concurrent.futures import ThreadPoolExecutor, as_completed
from pathlib import Path
from urllib.request import Request, urlopen
from urllib.parse import urlparse
import json
import re

folder = Path(__file__).resolve().parents[1] / 'wp-content/plugins/long-khanh-directory/assets/images'
reference = json.loads((folder / 'reference-content.json').read_text(encoding='utf-8'))
manifest = json.loads((folder / 'manifest.json').read_text(encoding='utf-8'))
jobs = []
for slug, record in reference['blogDetails'].items():
    if not re.fullmatch(r'[a-z0-9-]+', slug):
        continue
    sections = re.split(r'^## +', record['blog']['content'], flags=re.M)[1:]
    for index, section in enumerate(sections, 1):
        images = re.findall(r'!\[[^\]]*\]\(([^)]+)\)', section)
        if images:
            jobs.append((f'blog-section-{slug}-{index}', images[0]))

def download(job):
    key, url = job
    if key in manifest and (folder / manifest[key]['filename']).exists():
        return key, manifest[key]
    parsed = urlparse(url)
    if parsed.scheme != 'https' or parsed.hostname != 'diadiemlongkhanh.cdn.vccloud.vn':
        raise ValueError(f'Unexpected image host for {key}')
    with urlopen(Request(url, headers={'User-Agent': 'LongKhanhLocalReference/1.0'}), timeout=35) as response:
        content_type = response.headers.get_content_type()
        extension = {'image/jpeg': 'jpeg', 'image/png': 'png', 'image/webp': 'webp'}.get(content_type)
        data = response.read(8 * 1024 * 1024 + 1)
        if not extension:
            if data.startswith(b'\xff\xd8\xff'):
                extension = 'jpeg'
            elif data.startswith(b'\x89PNG\r\n\x1a\n'):
                extension = 'png'
            elif data.startswith(b'RIFF') and data[8:12] == b'WEBP':
                extension = 'webp'
        if not extension:
            raise ValueError(f'Unsupported image type for {key}: {content_type}')
    if len(data) > 8 * 1024 * 1024:
        raise ValueError(f'Image exceeds 8 MB: {key}')
    filename = f'{key}.{extension}'
    (folder / filename).write_bytes(data)
    return key, {'filename': filename, 'source': url}

failures = []
with ThreadPoolExecutor(max_workers=4) as pool:
    for result in as_completed([pool.submit(download, job) for job in jobs]):
        try:
            key, entry = result.result()
            manifest[key] = entry
            print(key, flush=True)
        except Exception as exc:
            failures.append(str(exc))
(folder / 'manifest.json').write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding='utf-8')
print(json.dumps({'images': len(jobs), 'failures': failures}))
