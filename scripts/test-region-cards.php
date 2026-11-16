<?php
/** Read-only local checks; optional rendered fixtures never save Elementor data. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);
require dirname(__DIR__).'/wp-load.php';
if(!in_array(wp_parse_url(home_url(),PHP_URL_HOST),array('localhost','127.0.0.1','::1'),true))throw new RuntimeException('Local only.');
function region_check($ok,$label){if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
function region_cards_in($nodes){$cards=array();foreach($nodes as $node){$classes=preg_split('/\s+/',trim(($node['settings']['css_classes']??'').' '.($node['settings']['_css_classes']??'')));if(in_array('lk-region-card',$classes,true))$cards[]=$node;else $cards=array_merge($cards,region_cards_in($node['elements']??array()));}return $cards;}
function region_count_nodes($nodes){$result=array();foreach($nodes as $node){if(($node['settings']['lk_binding']??'')==='region-count')$result[]=$node;$result=array_merge($result,region_count_nodes($node['elements']??array()));}return $result;}
$home=(int)(get_option('lk_page_map',array())['home']??0);
$raw=get_post_meta($home,'_elementor_data',true);$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');
$nodes=json_decode($raw,true);$cards=region_cards_in($nodes);
region_check(count($cards)>=5,'All existing local homepage region cards are available');
$document=\Elementor\Plugin::$instance->documents->get($home);
\Elementor\Plugin::$instance->documents->switch_to_document($document);
try{region_check(lk_region_card_cache_ttl('24')==='disable','Homepage region cards cannot reuse stale Elementor document HTML');}
finally{\Elementor\Plugin::$instance->documents->restore_document();}
$about=(int)(get_option('lk_page_map',array())['about']??0);
if($about){
 \Elementor\Plugin::$instance->documents->switch_to_document(\Elementor\Plugin::$instance->documents->get($about));
 try{region_check(lk_region_card_cache_ttl('24')==='24','Unrelated Elementor documents keep their cache policy');}
 finally{\Elementor\Plugin::$instance->documents->restore_document();}
}
$old_get=$_GET;$_GET=array('q'=>'unrelated-page-query','regions'=>array('not-the-card-region'));
$query=lk_region_card_query(add_query_arg('regions','example-region',lk_link('search')));
region_check($query===array('regions'=>array('example-region')),'Destination scalar region is parsed independently of the current request');
$query=lk_region_card_query(lk_link('search').'?regions%5B%5D=one&regions%5B%5D=two&amp;categories%5B%5D=food');
region_check($query===array('regions'=>array('one','two'),'categories'=>array('food')),'Encoded array filters and HTML entities are supported');
region_check(lk_region_card_query(wp_parse_url(lk_link('search'),PHP_URL_PATH).'?regions=one')===array('regions'=>array('one')),'Site-relative search links work in a subdirectory installation');
foreach(array('https://outside.invalid/search/?regions=one','javascript:alert(1)',lk_link('about').'?regions=one',lk_link('search'),lk_link('search').'?regions=') as $url)region_check(lk_region_card_query($url)===null,'Unrelated or invalid destination is ignored: '.$url);
$bound=lk_bind_region_card_nodes($nodes);
foreach(region_cards_in($bound) as $card){
 $q=lk_region_card_heading_query($card['elements']);$expected=lk_region_card_count($q);$counts=region_count_nodes($card['elements']);
 region_check((bool)$counts,'Card '.$card['id'].' retains its native count widget');
 foreach($counts as $count)region_check($count['settings']['editor']==='<p>'.$expected.' địa điểm</p>','Card '.$card['id'].' agrees with its search destination');
}
region_check($_GET===array('q'=>'unrelated-page-query','regions'=>array('not-the-card-region')),'Counting restores the original request filters');$_GET=$old_get;
$slugs=array_values(array_unique(array_filter(array_column(lk_directory_index(),'regionSlug'))));
if(count($slugs)<2)throw new RuntimeException('Need local listings in two regions.');
$standalone=lk_resolve_listing_settings('text-editor',array('lk_binding'=>'region-count','lk_region'=>$slugs[0]),0);
region_check($standalone['editor']==='<p>'.lk_region_card_count(array('regions'=>array($slugs[0]))).' địa điểm</p>','Standalone region counts retain their selected region');
$fixtures=array();$expected=array();
for($i=0;$i<8;$i++){
 $card=$cards[$i%count($cards)];$card['id']=dechex(0xa00000+$i);$slug=$i===7?'missing-removed-region':$slugs[($i+1)%count($slugs)];
 $url=add_query_arg('regions',$slug,lk_link('search'));$changed=false;
 $change=function(&$children)use(&$change,&$changed,$url,$i){foreach($children as &$child){$child['id']=substr(hash('sha256',$i.'-'.$child['id']),0,8);if(!$changed&&($child['widgetType']??'')==='heading'){$child['settings']['link']['url']=$url;$changed=true;}if(!empty($child['elements']))$change($child['elements']);}};
 $change($card['elements']);$fixtures[]=$card;$expected[]=array('id'=>$card['id'],'url'=>$url,'count'=>lk_region_card_count(lk_region_card_query($url)));
}
// Deliberately keep all old count-region settings, as in the edited prod layout.
$fixture_nodes=array(array('id'=>'aabb001','elType'=>'section','settings'=>array('css_classes'=>'lk-section lk-carousel lk-region-row'),'elements'=>$fixtures));
$fixture_filter=function($data,$id)use($home,$fixture_nodes){return (int)$id===$home?$fixture_nodes:$data;};
add_filter('elementor/frontend/builder_content_data',$fixture_filter,10,2);
try{$html=\Elementor\Plugin::$instance->frontend->get_builder_content_for_display($home,true);}
finally{remove_filter('elementor/frontend/builder_content_data',$fixture_filter,10);}
if(in_array('--fixture',$argv,true))file_put_contents(dirname(__DIR__).'/.codex-tools/region-cards-render.html',$html);
$dom=new DOMDocument();$prior=libxml_use_internal_errors(true);$dom->loadHTML('<?xml encoding="UTF-8">'.$html);libxml_clear_errors();libxml_use_internal_errors($prior);$xpath=new DOMXPath($dom);
foreach($expected as $item){$matches=$xpath->query('//*[@data-id="'.$item['id'].'"]//*[contains(concat(" ",normalize-space(@class)," ")," elementor-widget-text-editor ")]');$text=$matches->length?trim($matches->item(0)->textContent):'missing';region_check($matches->length===1&&$text===$item['count'].' địa điểm','Rendered copied card '.$item['id'].' follows the changed link (expected '.$item['count'].', actual '.$text.')');}
if(in_array('--fixture',$argv,true)){
 $head='<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
 $head.='<link rel="stylesheet" href="'.esc_url(plugins_url('elementor/assets/css/frontend.min.css')).'">';
 foreach(array('site.css','motion.css','feedback2.css') as $asset)$head.='<link rel="stylesheet" href="'.esc_url(get_stylesheet_directory_uri().'/assets/'.$asset).'">';
 $head.='<link rel="stylesheet" href="'.esc_url(LK_URL.'assets/region-cards.css').'">';
 $scripts='<script src="'.esc_url(get_stylesheet_directory_uri().'/assets/motion.js').'"></script><script src="'.esc_url(LK_URL.'assets/region-cards.js').'"></script>';
 $file=dirname(__DIR__).'/.codex-tools/region-cards-fixture.json';
 file_put_contents($file,wp_json_encode(array('expected'=>$expected,'html'=>'<!doctype html><html lang="vi"><head>'.$head.'</head><body class="lk-site lk-home">'.$html.$scripts.'</body></html>')));
 echo 'Fixture: '.$file."\n";
}
region_check(get_post_meta($home,'_elementor_data',true)===$raw&&hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json')===$manifest,'Homepage Elementor data and versioned package remain unchanged');
