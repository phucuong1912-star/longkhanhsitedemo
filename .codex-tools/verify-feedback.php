<?php
if (PHP_SAPI !== 'cli') exit;
define('WP_HTTP_BLOCK_EXTERNAL', true);
require dirname(__DIR__).'/wp-load.php';
$checks = array(); $fixtures = array(); $reviews = array();
$baseline = count(lk_posts());
function feedback_check($name, $ok) { global $checks; $checks[$name] = (bool) $ok; }
$terms = get_terms(array('taxonomy'=>'features','hide_empty'=>false,'number'=>2));
$categories = lk_directory_terms('listing-category');
try {
 foreach (array(array(20000,50000),array(80000,150000),array(0,0)) as $i=>$prices) {
  $id = wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Feedback verification '.($i+1),'post_author'=>1));
  $fixtures[] = $id; update_post_meta($id, '_lk_managed', 1);
  update_post_meta($id, 'lp_listingpro_options', array('list_price'=>$prices[0],'list_price_to'=>$prices[1]) + ($i===0 ? array('latitude'=>'10.94','longitude'=>'107.24') : array()));
  wp_set_object_terms($id, array($terms[0]->term_id), 'features');
  if ($i===0) wp_set_object_terms($id, array($terms[1]->term_id), 'features', true);
  if ($categories) wp_set_object_terms($id,array($categories[0]->term_id),'listing-category');
 }
 $_GET = array('q'=>'Feedback verification','features'=>wp_list_pluck($terms,'slug'));
 feedback_check('all_selected_features_required', wp_list_pluck(lk_filtered_posts(lk_search_state()),'ID') === array($fixtures[0]));
 $_GET = array('q'=>'Feedback verification','min_price'=>60000,'max_price'=>100000);
 feedback_check('price_interval_overlap', wp_list_pluck(lk_filtered_posts(lk_search_state()),'ID') === array($fixtures[1]));
 $_GET = array('q'=>'Feedback verification','min_price'=>0,'max_price'=>10000);
 feedback_check('unknown_price_excluded_when_filtered', count(lk_filtered_posts(lk_search_state())) === 0);
 feedback_check('missing_coordinates_not_fabricated', lk_place_data($fixtures[1])['lat'] === null && lk_place_data($fixtures[1])['lng'] === null);
 feedback_check('valid_coordinates_preserved', lk_place_data($fixtures[0])['lat'] === 10.94);
 if ($categories) {
  $_GET = array('q'=>'Feedback verification','categories'=>$categories[0]->slug);
  feedback_check('old_category_links_supported', count(lk_filtered_posts(lk_search_state())) === 3);
  $_GET['categories'] = array($categories[0]->slug);
  feedback_check('checkbox_category_links_supported', count(lk_filtered_posts(lk_search_state())) === 3);
 }
 $_GET = array();
 $review = wp_insert_comment(array('comment_post_ID'=>$fixtures[0],'comment_type'=>'lk_review','comment_approved'=>1,'comment_content'=>'Feedback precision verification','comment_author'=>'Verification'));
 $reviews[] = $review; update_comment_meta($review,'lk_rating',3.8);
 feedback_check('fractional_rating_summary', (float)get_post_meta($fixtures[0],'_lk_rating',true) === 3.8);
 wp_set_current_user(1);
 ob_start(); lk_review_composer(array('fixed_place'=>$fixtures[0])); $form = ob_get_clean();
 feedback_check('detail_form_stars', substr_count($form,'type="radio"') === 25 && !str_contains($form,'<select'));
 feedback_check('detail_form_photos', str_contains($form,'multipart/form-data') && str_contains($form,'name="review_photos[]"'));
 feedback_check('detail_form_fixed_place', str_contains($form,'type="hidden" name="place_id" value="'.$fixtures[0].'"'));
 $_GET = array('features'=>array($terms[0]->slug), 'min_price'=>10000,'max_price'=>150000);
 ob_start(); lk_results(); $results = ob_get_clean();
 feedback_check('filter_controls', str_contains($results,'name="features[]"') && str_contains($results,'id="lk-min-price"') && str_contains($results,'value="nearest"'));
} finally {
 foreach ($reviews as $id) wp_trash_comment($id);
 foreach ($fixtures as $id) wp_trash_post($id);
 $_GET = array();
 feedback_check('user_listings_preserved', count(lk_posts()) === $baseline);
}
echo wp_json_encode(array('checks'=>$checks,'passed'=>!in_array(false,$checks,true),'listings'=>count(lk_posts())),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),PHP_EOL;
exit(in_array(false,$checks,true) ? 1 : 0);
