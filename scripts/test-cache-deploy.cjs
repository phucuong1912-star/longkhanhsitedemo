// Offline transfer-filter regression: no FTP/SSH, WordPress boot or database writes.
// Usage: node scripts/test-cache-deploy.cjs <multimatch-5-module> [--ftp-only]
const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const multimatch = require(path.resolve(process.argv[2]));
const root = path.resolve(__dirname, '..');
const prefix = 'wp-content/plugins/litespeed-cache/src/data_structure/';
const names = ['avatar', 'crawler', 'crawler_blacklist', 'img_optming', 'url', 'url_file'];
const schemas = names.map(name => prefix + name + '.sql');
const workflow = fs.readFileSync(path.join(root, '.github/workflows/deploy.yml'), 'utf8');
const block = workflow.match(/^          exclude: \|\r?\n((?:            .*\r?\n)+)/m);
assert.ok(block, 'FTP exclude block must be present');
const filters = block[1].trim().split(/\r?\n/).map(line => line.trim());
assert.deepEqual(filters.filter(rule => rule.startsWith('!')).sort(), schemas.map(p => '!' + p).sort());
const diskSchemas = fs.readdirSync(path.join(root, prefix)).filter(name => name.endsWith('.sql')).sort();
assert.deepEqual(diskSchemas, names.map(name => name + '.sql').sort(), 'Review new upstream schema files explicitly');
for (const schema of schemas) {
  const content = fs.readFileSync(path.join(root, schema), 'utf8');
  assert.match(content, /`id`/);
  assert.match(content, /PRIMARY KEY/);
  assert.doesNotMatch(content, /\b(?:INSERT\s+INTO|DROP\s+TABLE)\b/i);
}
const blocked = [
  'database.sql', 'database.sql.gz', 'backup/site.sql',
  prefix + 'database.sql', prefix + 'url.sql.gz', prefix + 'backup/url.sql',
  'other/' + prefix + 'url.sql',
  'wp-content/plugins/other/schema.sql',
  'wp-content/uploads/url.sql', 'wp-content/uploads/' + prefix + 'url.sql',
  '.codex-tools/url.sql', '.git/config', '.env',
  'wp-config.php', '.htaccess', '.htaccess.bk',
  'wp-content/cache/page.html', 'wp-content/litespeed/debug.log',
  'wp-content/.litespeed_conf.php', 'wp-content/.litespeed_conf.dat',
  'wp-content/object-cache.php', 'wp-content/advanced-cache.php',
];
const allowed = [...schemas, 'wp-content/plugins/litespeed-cache/src/data.cls.php'];
function ftpAllows(file) {
  const parts = file.split('/');
  const entries = parts.slice(0, -1).map((_, i) => parts.slice(0, i + 1).join('/') + '/');
  entries.push(file);
  return entries.every(entry => multimatch(entry, filters, { matchBase: true, dot: true }).length === 0);
}
for (const file of allowed) assert.equal(ftpAllows(file), true, 'FTP must transfer ' + file);
for (const file of blocked) assert.equal(ftpAllows(file), false, 'FTP must exclude ' + file);
console.log(`PASS FTP filters: ${allowed.length} required files included, ${blocked.length} private/dump files excluded`);
if (!process.argv.includes('--ftp-only')) {
  const fixture = fs.mkdtempSync(path.join(os.tmpdir(), 'lk-cache-deploy-'));
  try {
    const src = path.join(fixture, 'source');
    const dst = path.join(fixture, 'destination');
    fs.mkdirSync(dst, { recursive: true });
    for (const file of [...allowed, ...blocked]) {
      const target = path.join(src, file);
      fs.mkdirSync(path.dirname(target), { recursive: true });
      fs.writeFileSync(target, schemas.includes(file) ? fs.readFileSync(path.join(root, file)) : 'fixture');
    }
    // Dry run between temporary local directories only. Never invoke deploy-prod.sh.
    const result = spawnSync('rsync', [
      '-arn', '--out-format=%n', '--exclude-from=' + path.join(root, '.github/prod-excludes.txt'),
      src + '/', dst + '/',
    ], { encoding: 'utf8' });
    assert.equal(result.status, 0, result.stderr || String(result.error));
    const files = result.stdout.trim().split(/\r?\n/).filter(file => file && !file.endsWith('/'));
    assert.deepEqual(files.sort(), allowed.slice().sort(), 'rsync must transfer only required source files');
    assert.deepEqual(fs.readdirSync(dst), [], 'Dry run must not write destination files');
    console.log(`PASS rsync filters: ${allowed.length} required files included, ${blocked.length} private/dump files excluded`);
  } finally {
    fs.rmSync(fixture, { recursive: true, force: true });
  }
}
