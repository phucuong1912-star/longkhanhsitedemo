<?php
if(!defined('ABSPATH'))exit;

function lk_listing_picker_title($post){return wp_strip_all_tags(html_entity_decode(get_the_title($post),ENT_QUOTES|ENT_HTML5,'UTF-8'));}

// Slugs are portable references: resolve against published data on this site.
// Never migrate or rewrite a page/template when adding these widget controls.
function lk_curated_listing_groups($settings){
 $posts=lk_posts();$by_slug=array();foreach($posts as $post)$by_slug[$post->post_name]=$post;
 $limit=max(1,min(50,(int)($settings['limit']??8)));$result=array();
 $rows=$settings['category_groups']??array();if(!is_array($rows))return $result;
 foreach($rows as $row){
  if(!is_array($row)||!is_scalar($row['tab_category']??null))continue;
  $slug=sanitize_title($row['tab_category']);if(!$slug)continue;
  $term=get_term_by('slug',$slug,'listing-category');if(!$term)continue;
  $eligible=function($post)use($term){return has_term($term->term_id,'listing-category',$post);};
  $selected=array();
  if(($row['selection_mode']??'auto')==='manual'){
   $slugs=$row['listing_slugs']??array();if(!is_array($slugs))$slugs=array();
   foreach($slugs as $value){
    if(!is_scalar($value))continue;$value=sanitize_title($value);
    if(isset($by_slug[$value])&&$eligible($by_slug[$value]))$selected[$value]=$by_slug[$value];
   }
  }else foreach($posts as $post)if($eligible($post))$selected[$post->post_name]=$post;
  $title=is_scalar($row['tab_title']??null)?sanitize_text_field($row['tab_title']):'';
  $result[]=array('category'=>$term->slug,'title'=>$title?:$term->name,'posts'=>array_slice(array_values($selected),0,$limit));
 }
 return $result;
}
function lk_render_curated_listings($settings){
 $groups=lk_curated_listing_groups($settings);
 if(!$groups){echo '<p class="lk-empty">Chưa chọn danh mục hiển thị.</p>';return;}
 $tabs=!empty($settings['tabs']);$uid=wp_unique_id('lk-curated-'.sanitize_html_class($settings['lk_widget_id']??'list').'-');$columns=max(2,min(4,(int)($settings['columns']??4)));
 if($tabs){$recent=lk_posts();usort($recent,function($a,$b){return strcmp($b->post_date,$a->post_date)?:($b->ID<=>$a->ID);});array_unshift($groups,array('category'=>'','title'=>'Mới','posts'=>array_slice($recent,0,max(1,min(50,(int)($settings['limit']??8))))));}
 echo '<div class="lk-curated-list'.($tabs?'':' lk-curated-list--groups').'" data-lk-curated-list>';
 if($tabs){echo '<div class="lk-tabs" role="tablist" aria-label="Danh mục địa điểm">';foreach($groups as $i=>$group)echo '<button type="button" role="tab" id="'.$uid.'tab-'.$i.'" aria-controls="'.$uid.'panel-'.$i.'" aria-selected="'.($i===0?'true':'false').'" tabindex="'.($i===0?'0':'-1').'" data-lk-curated-tab>'.esc_html($group['title']).'</button>';echo '</div>';}
 foreach($groups as $i=>$group){
  echo '<div class="lk-curated-panel" id="'.$uid.'panel-'.$i.'"'.($tabs?' role="tabpanel" aria-labelledby="'.$uid.'tab-'.$i.'" tabindex="0"':'').($tabs&&$i!==0?' hidden':'').'>';
  if(!$tabs)echo '<h3 class="lk-curated-group-title">'.esc_html($group['title']).'</h3>';
  echo '<div class="lk-place-grid" style="--lk-columns:'.$columns.'">';
  foreach($group['posts'] as $post)lk_card($post);
  echo '</div>';
  if(!$group['posts'])echo '<p class="lk-empty">Chưa có địa điểm phù hợp trong danh mục này.</p>';
  if(!empty($settings['more']))echo '<div class="lk-center"><a class="lk-button lk-outline" href="'.esc_url($group['category']?add_query_arg('categories',array($group['category']),lk_link('search')):lk_link('search')).'">'.esc_html($settings['more']).'</a></div>';
  echo '</div>';
 }
 echo '</div>';
}
add_action('init',function(){
 wp_register_script('lk-listing-selection',LK_URL.'assets/listing-selection.js',array(),filemtime(LK_DIR.'assets/listing-selection.js'),true);
 wp_register_style('lk-listing-selection',LK_URL.'assets/listing-selection.css',array(),filemtime(LK_DIR.'assets/listing-selection.css'));
});
add_action('elementor/editor/after_enqueue_scripts',function(){
 $places=array();foreach(lk_posts() as $post){$terms=wp_get_post_terms($post->ID,'listing-category',array('fields'=>'slugs'));$places[$post->post_name]=array('title'=>lk_listing_picker_title($post),'categories'=>is_wp_error($terms)?array():$terms);}
 wp_enqueue_script('lk-listing-selection-editor',LK_URL.'assets/listing-selection-editor.js',array('jquery','elementor-editor'),filemtime(LK_DIR.'assets/listing-selection-editor.js'),true);
 wp_localize_script('lk-listing-selection-editor','LK_LISTING_PICKER',array('places'=>$places));
});
