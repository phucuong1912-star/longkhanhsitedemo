"""Exercise the recovery plugin against local WP, using disposable plugin files.

Real account fixtures are rolled back by the existing transactional test suite.
No production requests, accounts or emails are sent.
"""
from pathlib import Path
import shutil
import subprocess
import tempfile

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / '.codex-tools/account-recovery-2026-11-25'
OUT.mkdir(parents=True, exist_ok=True)
plugin = ROOT / 'wp-content/plugins/long-khanh-directory'
test = (ROOT / 'scripts/test-account-links.php').read_text(encoding='utf-8')
start = test.index("add_filter('page_link',$rename,999,2);")
end = test.index("remove_filter('page_link',$rename,999);", start) + len("remove_filter('page_link',$rename,999);")
test = test[:start] + """
 account_check(lk_resolve_action_url(lk_link('new-review'))===lk_link('new-review')&&lk_resolve_action_url(lk_link('explore').'?lk_feed=following#feed')===lk_link('explore').'?lk_feed=following#feed','Recovery leaves community URLs unchanged');
 account_check(!lk_action_link_nodes(array(array('settings'=>array('link'=>array('url'=>lk_link('new-review')))))),'Recovery does not change the cache policy for community-only links');
 account_check(lk_resolve_action_url(lk_link('profile'))===lk_auth_url(),'Recovery repairs the legacy account link');
""" + test[end:]
test = test.replace("require dirname(__DIR__).'/wp-load.php';", "require '" + ROOT.as_posix() + "/wp-load.php';")
test = test.replace("$ids[]=$id;", """$ids[]=$id;
 $signed=wp_authenticate(get_userdata($id)->user_login,$password);
 account_check(!is_wp_error($signed)&&$signed->ID===$id,'Recovered account signs in with its valid password');
 $signed=wp_authenticate($email,$password);
 account_check(!is_wp_error($signed)&&$signed->ID===$id,'Recovered account also signs in by email');
 account_check(is_wp_error(wp_authenticate($email,'wrong-password')),'Incorrect login password is rejected');
 $die_filter=function(){return function(){throw new RuntimeException('owned-invalid-nonce');};};
 add_filter('wp_die_handler',$die_filter,999);$_POST=array('lk_nonce'=>'invalid');$blocked=false;
 try{lk_handle_login();}catch(RuntimeException $error){$blocked=$error->getMessage()==='owned-invalid-nonce';}
 finally{remove_filter('wp_die_handler',$die_filter,999);$_POST=array();}
 account_check($blocked,'Login POST without a valid nonce is blocked before authentication');
""")
snapshot = """
$preserved_tables=array($wpdb->posts,$wpdb->postmeta,$wpdb->comments,$wpdb->commentmeta,$wpdb->users,$wpdb->usermeta,$wpdb->terms,$wpdb->term_taxonomy,$wpdb->term_relationships,$wpdb->options);
$fingerprint=function()use($wpdb,$preserved_tables){$result=array();foreach($preserved_tables as $table)$result[$table]=hash('sha256',serialize($wpdb->get_results('SELECT * FROM `'.$table.'` ORDER BY 1,2',ARRAY_A)));return $result;};
$rows_before=$fingerprint();
"""
test = test.replace("$wpdb->query('START TRANSACTION');ob_start();", snapshot + "\n$wpdb->query('START TRANSACTION');ob_start();")
test = test.replace("foreach($checks as $label)echo", "account_check($rows_before===$fingerprint(),'Posts, listings, reviews, users, metadata, taxonomy and options are unchanged after recovery tests');\nforeach($checks as $label)echo")
with tempfile.TemporaryDirectory(prefix='lk-account-review-', dir=plugin.parent) as folder:
    fixture = Path(folder)
    shutil.copytree(plugin, fixture, dirs_exist_ok=True)
    payload = ROOT / '.github/account-recovery/files/wp-content/plugins/long-khanh-directory'
    shutil.copytree(payload, fixture, dirs_exist_ok=True)
    community = subprocess.check_output(['git', 'show', '7fcd371f:wp-content/plugins/long-khanh-directory/includes/community.php'], cwd=ROOT)
    (fixture / 'includes/community.php').write_bytes(community)
    entry = fixture.name + '/long-khanh-directory.php'
    bootstrap = "<?php\nrequire_once '" + ROOT.as_posix() + "/wp-includes/plugin.php';\n"
    bootstrap += "add_filter('pre_http_request',function(){return new WP_Error('local-test-network-disabled','No outbound HTTP in local recovery tests');},PHP_INT_MAX);\n"
    bootstrap += "add_filter('option_active_plugins',function($plugins){$key=array_search('long-khanh-directory/long-khanh-directory.php',$plugins,true);if($key===false)throw new RuntimeException('Expected local plugin is not active');$plugins[$key]='" + entry + "';return $plugins;});\n?>\n"
    runner = OUT / 'test-recovery-local.php'
    runner.write_text(bootstrap + test, encoding='utf-8')
    try:
        result = subprocess.run(['php', str(runner)], cwd=ROOT, capture_output=True, text=True, encoding='utf-8', errors='replace', timeout=90)
    except subprocess.TimeoutExpired as error:
        print((error.stderr or b'').decode('utf-8', errors='replace') if isinstance(error.stderr, bytes) else error.stderr)
        raise
    (OUT / 'test-results.txt').write_text(result.stdout + result.stderr, encoding='utf-8')
    print(result.stdout, end='')
    print(result.stderr, end='')
    if result.returncode:
        raise SystemExit(result.returncode)
