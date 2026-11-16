<?php
/** Read-only local integration checks; no posts, layouts or selections are saved. */
if(PHP_SAPI!=='cli')exit(1);
require dirname(__DIR__).'/wp-load.php';
if(!in_array(wp_parse_url(home_url(),PHP_URL_HOST),array('localhost','127.0.0.1','::1'),true))throw new RuntimeException('Local only.');
function selection_check($ok,$message){if(!$ok)throw new RuntimeException($message);echo 'PASS '.$message."\n";}
$before=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');
$home=(int)((get_option('lk_page_map')['home'])??0);$raw=get_post_meta($home,'_elementor_data',true);
$posts=lk_posts();$category=null;$members=array();
foreach(lk_directory_terms('listing-category') as $term){$found=array_values(array_filter($posts,function($p)use($term){return has_term($term->term_id,'listing-category',$p);}));if(count($found)>=2){$category=$term;$members=$found;break;}}
selection_check((bool)$category,'Local category has two published fixtures');
$slugs=array($members[1]->post_name,$members[0]->post_name);
$settings=array('listing_selection'=>'curated','tabs'=>'yes','limit'=>8,'category_groups'=>array(array('tab_category'=>$category->slug,'selection_mode'=>'manual','listing_slugs'=>array_merge($slugs,array($slugs[0],'missing-deleted-place')))));
$groups=lk_curated_listing_groups($settings);
selection_check(wp_list_pluck($groups[0]['posts'],'post_name')===$slugs,'Manual order preserved; duplicates and missing/deleted selections excluded');
$outsider=null;foreach($posts as $post)if(!has_term($category->term_id,'listing-category',$post)){$outsider=$post;break;}
if($outsider){$settings['category_groups'][0]['listing_slugs'][]=$outsider->post_name;selection_check(count(lk_curated_listing_groups($settings)[0]['posts'])===2,'A selection from another category is rejected');}
$settings['category_groups'][0]['listing_slugs']=array();selection_check(!lk_curated_listing_groups($settings)[0]['posts'],'Manual empty selection never falls back to all posts');
$settings['category_groups'][0]['selection_mode']='auto';$settings['limit']=1;selection_check(count(lk_curated_listing_groups($settings)[0]['posts'])===1,'Automatic selection stays in category and respects limit');
$settings['category_groups'][0]['tab_category']='missing-deleted-category';selection_check(lk_curated_listing_groups($settings)===array(),'Deleted/unknown category never exposes all listings');
ob_start();lk_listing_grid(array('limit'=>2,'tabs'=>'yes'));$legacy=ob_get_clean();selection_check(str_contains($legacy,'data-category="an-uong"')&&!str_contains($legacy,'data-lk-curated-list'),'Absent new settings retain legacy renderer and tabs');
$settings['limit']=8;$settings['category_groups']=array(array('tab_category'=>$category->slug,'selection_mode'=>'manual','listing_slugs'=>$slugs,'tab_title'=>'Ăn uống đã chọn'),array('tab_category'=>$category->slug,'selection_mode'=>'manual','listing_slugs'=>array(),'tab_title'=>'Chưa chọn'));
ob_start();lk_render_curated_listings($settings);$html=ob_get_clean();
selection_check(str_contains($html,'role="tabpanel"')&&str_contains($html,' hidden')&&str_contains($html,'Chưa có địa điểm phù hợp'),'Curated tabs render accessible scoped panels and explicit empty state');
$settings['tabs']='';ob_start();lk_render_curated_listings($settings);$grouped=ob_get_clean();
selection_check(!str_contains($grouped,'role="tablist"')&&substr_count($grouped,'class="lk-curated-group-title"')===2,'Turning tabs off renders selected category groups in configured order');
$settings['tabs']='yes';
selection_check($before===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json')&&$raw===get_post_meta($home,'_elementor_data',true),'Widget reads do not rewrite home Elementor or versioned package');
if(in_array('--fixture',$argv,true)){
 $id='';$walk=function($nodes)use(&$walk,&$id){foreach($nodes as $n){if(($n['widgetType']??'')==='lk-listings')$id=$n['id'];if(!empty($n['elements']))$walk($n['elements']);}};$walk(json_decode($raw,true));
 file_put_contents(sys_get_temp_dir().'/lk-selection-fixture.json',wp_json_encode(array('home'=>$home,'widget'=>$id,'category'=>$category->slug,'slugs'=>$slugs,'titles'=>array(get_the_title($members[1]),get_the_title($members[0])),'settings'=>$settings,'html'=>$html)));
}
