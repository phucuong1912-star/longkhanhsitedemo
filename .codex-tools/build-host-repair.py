import hashlib,json,zipfile
from pathlib import Path

root=Path(__file__).resolve().parent.parent
out=Path('C:/Users/ITCK/.codex/backups/long-khanh-host-repair.zip')
base='long-khanh-host-repair/'
groups={'wordpress':root/'wp-includes/js/dist/vendor','elementor':root/'wp-content/plugins/elementor/vendor'}
manifest={'versions':{'wordpress':'7.1.4','elementor':'4.4.0'},'files':[]}
with zipfile.ZipFile(out,'w',zipfile.ZIP_DEFLATED) as archive:
 archive.write(root/'wp-content/plugins/long-khanh-host-repair/long-khanh-host-repair.php',base+'long-khanh-host-repair.php')
 for group,folder in groups.items():
  for path in sorted(folder.rglob('*')):
   if not path.is_file():continue
   relative=path.relative_to(folder).as_posix()
   manifest['files'].append({'group':group,'relative':relative,'sha256':hashlib.sha256(path.read_bytes()).hexdigest()})
   archive.write(path,base+'payload/'+group+'/'+relative)
 archive.writestr(base+'payload-manifest.json',json.dumps(manifest,ensure_ascii=False))
print(json.dumps({'zip':str(out),'bytes':out.stat().st_size,'files':len(manifest['files'])}))
