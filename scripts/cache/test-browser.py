"""Exercise a disposable LOCAL OpenLiteSpeed WordPress stack, never production."""
import argparse,json,os,pathlib,sys,time
from urllib.parse import urlsplit

ROOT=pathlib.Path(__file__).resolve().parents[2]
sys.path.insert(0,str(ROOT/'.codex-tools/browser-deps'))
from playwright.sync_api import sync_playwright

parser=argparse.ArgumentParser()
parser.add_argument('--state',required=True,help='Disposable local fixture JSON; never a production account')
parser.add_argument('--browser-executable',required=True)
args=parser.parse_args()
state_path=pathlib.Path(args.state).resolve();data=json.loads(state_path.read_text(encoding='utf-8'))
base=data['base'];assert urlsplit(base).hostname in ['localhost','127.0.0.1'],'Local URLs only'
out=state_path.parent/'browser-results';out.mkdir(exist_ok=True)
sys.stdout.reconfigure(encoding='utf-8')

def check(ok,label):
    if not ok:raise AssertionError(label)
    print('PASS '+label,flush=True)
def no_cache(response,label):
    check(response.headers.get('x-litespeed-cache')!='hit',label+' is not a cache HIT')
    check('no-cache' in response.headers.get('x-litespeed-cache-control',''),label+' emits LiteSpeed no-cache')
def go(page,url):
    response=page.goto(url,wait_until='domcontentloaded',timeout=60000)
    check(response.status==200,'HTTP 200 for local page')
    page.wait_for_function('window.LK_SITE',timeout=30000)
    return response

with sync_playwright() as playwright:
    browser=playwright.chromium.launch(headless=True,executable_path=args.browser_executable,args=['--no-proxy-server'])
    contexts=[];errors=[]
    def context():
        ctx=browser.new_context(viewport={'width':1280,'height':900});contexts.append(ctx)
        ctx.route('**/*',lambda route:route.continue_() if urlsplit(route.request.url).hostname in ['localhost','127.0.0.1',None] else route.abort())
        return ctx
    def page_for(ctx):
        page=ctx.new_page();page.on('pageerror',lambda error:errors.append(str(error)));return page
    try:
        guest=context();a=context();b=context()
        initial=guest.request.get(base+'?lk_account=login')
        check(initial.headers.get('x-lk-cache-qa')=='disposable','Server identifies an isolated local QA database before writes')
        no_cache(initial,'Guest login form')
        first=guest.request.get(data['listing_url']);second=guest.request.get(data['listing_url'])
        check(first.status==200 and second.status==200,'Public listing returns HTTP 200')
        check(second.headers.get('x-litespeed-cache')=='hit','Public anonymous listing is served by the actual OpenLiteSpeed cache')
        for path in ['?lk_account=register','?lk_account=lost','?lk_account=reset','?lk_saved=1','?lk_feed=following']:
            no_cache(guest.request.get(base+path),'Guest '+path)
        no_cache(guest.request.get(data['review_url']),'Mapped review composer')
        gp=page_for(guest);go(gp,base)
        check(not gp.evaluate('window.LK_SITE.loggedIn'),'Guest browser remains signed out with a cached homepage')
        gp.locator('.lk-login-open').first.click()
        check(gp.locator('#lk-login').is_visible(),'Login popup opens from cached HTML')
        gp.locator('#lk-login [data-close]').first.click()

        pages={}
        for key,ctx in [('a',a),('b',b)]:
            page=page_for(ctx);pages[key]=page;go(page,base+'?lk_account=login')
            page.locator('.lk-auth-card input[name=username]').fill(data['users'][key]['login'])
            page.locator('.lk-auth-card input[name=password]').fill(data['users'][key]['password'])
            with page.expect_navigation(wait_until='domcontentloaded'):
                page.locator('.lk-auth-card button[type=submit]').click()
            page.wait_for_function('window.LK_SITE && window.LK_SITE.loggedIn===true',timeout=30000)
            check(True,'Real login succeeds for owned user '+key.upper())
            response=go(page,base+'?lk_account=login');no_cache(response,'Account '+key.upper())
            text=page.locator('.lk-auth-card').inner_text()
            check('Owned Cache User '+key.upper() in text,'Account shows its own identity '+key.upper())
            check('Owned Cache User '+('B' if key=='a' else 'A') not in text,'Account does not leak the other session')
            no_cache(ctx.request.get(base),'Authenticated homepage '+key.upper())

        saved_a=pages['a'].evaluate('window.LK_SITE.saved');saved_b=pages['b'].evaluate('window.LK_SITE.saved')
        result=a.request.post(saved_a['url'],form={'action':'lk_saved_places','nonce':saved_a['nonce'],'operation':'save','ids':json.dumps([data['listing_id']])})
        no_cache(result,'Saved-place POST')
        if not result.json()['success']:raise AssertionError('Saved-place request failed: '+str(result.status)+' '+result.text()[:260])
        check(True,'User A can save the owned listing')
        result=b.request.post(saved_b['url'],form={'action':'lk_saved_places','nonce':saved_b['nonce'],'operation':'list'})
        check(result.json()['success'] and data['listing_id'] not in result.json()['data']['ids'],'Saved places stay isolated for user B')
        rejected=guest.request.post(base+'wp-admin/admin-post.php',form={'action':'lk_login','username':data['users']['a']['login'],'password':data['users']['a']['password']})
        check(rejected.status==403,'Login submission with no nonce is rejected');no_cache(rejected,'Invalid login POST')

        pa=pages['a'];response=go(pa,data['listing_url']);no_cache(response,'Signed-in review page')
        pa.locator('.lk-review-open').first.click();pa.locator('#lk-review-dialog').wait_for(state='visible')
        token='Owned browser cache review '+str(time.time_ns())
        form=pa.locator('#lk-review-form');form.locator('[name=review_title]').fill('Owned cache verification')
        form.locator('[name=review]').fill(token)
        with pa.expect_navigation(wait_until='domcontentloaded'):
            form.locator('button[type=submit]').click()
        check(token in pa.locator('.lk-reviews').inner_text(),'Real review form submission displays the new review')
        refreshed=guest.request.get(data['listing_url'])
        check(refreshed.headers.get('x-litespeed-cache')!='hit','Review publication invalidates the previously cached listing')
        check(token in refreshed.text(),'Guest immediately sees the newly published review')
        clean=context()
        # Use a fresh anonymous context and allow the engine to finish storing
        # a newly purged response before the next request races its disk write.
        for attempt in range(5):
            cached=clean.request.get(data['listing_url'])
            check(token in cached.text(),'Fresh anonymous response retains the newly published review')
            if cached.headers.get('x-litespeed-cache')=='hit':break
            check('public' in cached.headers.get('x-litespeed-cache-control',''),'Fresh anonymous review response remains publicly cacheable')
            time.sleep(0.2)
        check(cached.headers.get('x-litespeed-cache')=='hit','Updated public review page is cached again')

        register=page_for(guest);response=go(register,base+'?lk_account=register');no_cache(response,'Registration UI')
        login='lk_cache_ui_'+str(time.time_ns());password=data['users']['a']['password']
        register.locator('.lk-auth-card input[name=username]').fill(login)
        register.locator('.lk-auth-card input[name=email]').fill(login+'@example.invalid')
        register.locator('.lk-auth-card input[name=password]').fill(password)
        with register.expect_navigation(wait_until='domcontentloaded'):
            register.locator('.lk-auth-card button[type=submit]').click()
        check('registered=1' in register.url,'Real registration succeeds with the fresh form nonce')
        response=go(register,base+'?lk_account=lost');no_cache(response,'Password help UI')
        register.locator('.lk-auth-card input[name=identity]').fill(data['users']['a']['login'])
        with register.expect_navigation(wait_until='domcontentloaded'):
            register.locator('.lk-auth-card button[type=submit]').click()
        check('sent=1' in register.url,'Password help works with outbound email intercepted by the QA stack')
        for width in [390,1280]:
            register.set_viewport_size({'width':width,'height':900})
            check(register.evaluate('document.documentElement.scrollWidth<=innerWidth+1'),'Account form fits '+str(width)+'px')
            register.screenshot(path=str(out/('account-'+str(width)+'.png')))
        go(pa,base+'?lk_account=login')
        with pa.expect_navigation(wait_until='domcontentloaded'):
            pa.locator('.lk-auth-card a',has_text='Đăng xuất').click()
        pa.wait_for_function('window.LK_SITE && window.LK_SITE.loggedIn===false',timeout=30000)
        check(True,'Logout returns to an unauthenticated page')
        check(not errors,'Account and review flows have no uncaught JavaScript errors: '+str(errors[:3]))
    finally:
        for ctx in contexts:ctx.close()
        browser.close()
print('All local OpenLiteSpeed cache/browser flow checks passed.',flush=True)
