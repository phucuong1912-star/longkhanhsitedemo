<?php
if (!defined('ABSPATH')) exit;

// Accept old single-value links as well as the checkbox groups in the filter.
function lk_filter_values($name) {
 $raw = $_GET[$name] ?? array();
 return array_values(array_unique(array_filter(array_map('sanitize_title', array_filter((array) wp_unslash($raw),'is_scalar')))));
}

function lk_price_ceiling() {
 $ceiling=300000;foreach(lk_posts() as $post){$data=lk_place_data($post);$ceiling=max($ceiling,(int)ceil(max($data['min'],$data['max'])/10000)*10000);}return $ceiling;
}

function lk_search_state() {
 $min = max(0, (int) ($_GET['min_price'] ?? 0));
 $max = max(0, (int) ($_GET['max_price'] ?? lk_price_ceiling()));
 if ($min > $max) { $swap = $min; $min = $max; $max = $swap; }
 $sort = sanitize_key($_GET['sort'] ?? 'relevance');
 if (!in_array($sort, array('relevance', 'rating', 'price', 'nearest'), true)) $sort = 'relevance';
 $nearby=is_scalar($_GET['nearby']??'')?sanitize_title(wp_unslash($_GET['nearby']??'')):'';$origin=null;
 if($nearby&&$nearby!=='me'){$source=get_page_by_path($nearby,OBJECT,'listing');if($source&&$source->post_status==='publish'&&!$source->post_password&&lk_is_directory_listing($source->ID)){$data=lk_place_data($source);if($data['lat']!==null&&$data['lng']!==null)$origin=array('id'=>$source->ID,'lat'=>$data['lat'],'lng'=>$data['lng'],'title'=>$data['title'],'slug'=>$source->post_name);}}
 if($origin&&!isset($_GET['sort']))$sort='nearest';
 return array('q'=>sanitize_text_field(wp_unslash($_GET['q'] ?? '')), 'categories'=>lk_filter_values('categories'), 'regions'=>lk_filter_values('regions'), 'collections'=>lk_filter_values('collections'), 'features'=>lk_filter_values('features'), 'price_status'=>array_values(array_intersect(lk_filter_values('price_status'),array_keys(lk_price_status_labels()))), 'open'=>!empty($_GET['open']), 'min'=>$min, 'max'=>$max, 'sort'=>$sort, 'nearby'=>$origin!==null||$nearby==='me'||$sort==='nearest','origin'=>$origin);
}

function lk_filtered_posts($state) {
 $needle = lk_normalize_search($state['q']);
 $posts = array_values(array_filter(lk_posts(), function($post) use ($state, $needle) {
  $data = lk_place_data($post);
  if(!empty($state['origin'])&&$post->ID===$state['origin']['id'])return false;
  if(!empty($state['collections'])&&!has_term($state['collections'],'lk_collection',$post))return false;
  $haystack = lk_normalize_search($data['title'].' '.$data['address'].' '.$data['region'].' '.$data['categoryNames'].' '.wp_strip_all_tags($post->post_content));
  if ($needle !== '' && mb_strpos($haystack, $needle) === false) return false;
  if ($state['categories'] && !array_intersect($state['categories'], $data['categories'])) return false;
  if ($state['regions'] && !in_array($data['regionSlug'], $state['regions'], true)) return false;
  // Every selected amenity must be present on the listing.
  if (array_diff($state['features'], $data['features'])) return false;
  if ($state['open'] && !$data['opened']) return false;
  if ($state['price_status'] && !in_array($data['priceStatus']?:'notsay',$state['price_status'],true)) return false;
  if ($state['min'] > 0 || $state['max'] < lk_price_ceiling()) {
   if (!$data['min'] && !$data['max']) return false;
   $low = $data['min'] ?: $data['max'];
   $high = max($low, $data['max']);
   if ($high < $state['min'] || $low > $state['max']) return false;
  }
  return true;
 }));
 if ($state['sort'] === 'rating') usort($posts, function($a, $b) { return lk_place_data($b)['rating'] <=> lk_place_data($a)['rating']; });
 if ($state['sort'] === 'price') usort($posts, function($a, $b) { return (lk_place_data($a)['min'] ?: PHP_INT_MAX) <=> (lk_place_data($b)['min'] ?: PHP_INT_MAX); });
 return $posts;
}

function lk_filter_terms($taxonomy, $state) {
 // Only offer amenities attached to published directory listings in these categories.
 if($taxonomy==='lk_collection')return get_terms(array('taxonomy'=>$taxonomy,'hide_empty'=>false));
 if ($taxonomy !== 'features') return lk_directory_terms($taxonomy);
 $slugs = $state['features'];
 foreach (lk_posts() as $post) {
  $data = lk_place_data($post);
  if(!empty($state['collections'])&&!has_term($state['collections'],'lk_collection',$post))continue;
  if (!$state['categories'] || array_intersect($state['categories'], $data['categories'])) $slugs = array_merge($slugs, $data['features']);
 }
 return $slugs ? get_terms(array('taxonomy'=>'features', 'hide_empty'=>false, 'slug'=>array_unique($slugs))) : array();
}

function lk_render_results($settings = array()) {
 $state = lk_search_state(); $posts = lk_filtered_posts($state); $count = count($posts);
 $page = max(1, absint($_GET['pg'] ?? 1)); $per = 10; $page = min($page, max(1, (int) ceil($count / $per)));
 $reset = add_query_arg('q', $state['q'], lk_link('search'));
 $active = $state['collections'] || $state['categories'] || $state['regions'] || $state['features'] || $state['price_status'] || $state['open'] || $state['min'] > 0 || $state['max'] < lk_price_ceiling() || $state['nearby'];
 $ceiling = max(lk_price_ceiling(), $state['max']);
 echo '<div class="lk-results-layout" data-result-ids="'.esc_attr(wp_json_encode(wp_list_pluck($posts, 'ID'))).'" data-origin-id="'.(int)($state['origin']['id']??0).'" data-nearby="'.($state['nearby'] ? '1' : '0').'"><aside class="lk-filter-wrap"><button class="lk-map-preview" data-map>Xem bản đồ</button><form class="lk-filter" method="get" action="'.esc_url(lk_link('search')).'" id="lk-filter"><div class="lk-filter-heading"><h2><span class="lk-filter-title-desktop">'.esc_html($settings['filter_title'] ?? 'Lọc kết quả').'</span><span class="lk-filter-title-mobile">'.esc_html($settings['mobile_filter_title']??'Bộ lọc').'</span></h2><button type="button" class="lk-filter-close" aria-label="Đóng bộ lọc">&times;</button></div><input type="hidden" name="q" value="'.esc_attr($state['q']).'"><input type="hidden" name="nearby" value="'.($state['origin']['slug']??($state['nearby']?'me':'')).'">';
 echo '<fieldset class="lk-filter-group lk-filter-hours"><legend>Giờ mở cửa</legend><div class="lk-filter-options"><label><input type="radio" name="open" value="0" '.checked(!$state['open'], true, false).'> Tất cả</label><label><input type="radio" name="open" value="1" '.checked($state['open'], true, false).'> Đang mở cửa</label></div></fieldset>';
 foreach (array('categories'=>array($settings['categories_title']??'Danh mục','listing-category'), 'regions'=>array($settings['regions_title']??'Khu vực','location'), 'collections'=>array($settings['collections_title']??'Bộ sưu tập','lk_collection'), 'features'=>array($settings['features_title']??'Tiện ích','features')) as $name=>$config) {
  if ($name === 'features') {
   echo '<fieldset class="lk-filter-group"><legend>'.esc_html($settings['price_tier_title']??'Mức giá').'</legend><div class="lk-filter-options">';foreach(lk_price_status_labels() as $key=>$label)echo '<label><input type="checkbox" name="price_status[]" value="'.$key.'" '.checked(in_array($key,$state['price_status'],true),true,false).'><span>'.esc_html($label).'</span></label>';echo '</div></fieldset>';
   echo '<fieldset class="lk-price-filter lk-filter-group lk-filter-range"><legend>'.esc_html($settings['price_range_title']??'Khoảng giá').'</legend><div class="lk-filter-options"><output class="lk-price-output">'.esc_html(lk_money($state['min']).' – '.lk_money($state['max'])).'</output><div class="lk-price-track"><input id="lk-min-price" aria-label="Giá thấp nhất" type="range" name="min_price" min="0" max="'.$ceiling.'" step="10000" value="'.$state['min'].'"><input id="lk-max-price" aria-label="Giá cao nhất" type="range" name="max_price" min="0" max="'.$ceiling.'" step="10000" value="'.$state['max'].'"></div></div></fieldset>';
  }
  echo '<fieldset class="lk-filter-group"><legend>'.esc_html($config[0]).'</legend><div class="lk-filter-options">';
  $terms = lk_filter_terms($config[1], $state);
  if (is_wp_error($terms)) $terms = array();
  foreach ($terms as $term) {
   $icon = $name === 'features' ? get_term_meta($term->term_id, '_lk_icon', true) : '';
   if (!$icon && $name === 'features' && function_exists('listingpro_get_term_meta')) $icon = preg_replace('/^(?:fa[srlb]?\s+)?fa-/', '', (string) listingpro_get_term_meta($term->term_id, 'lp_features_icon'));
   echo '<label><input type="checkbox" name="'.esc_attr($name).'[]" value="'.esc_attr($term->slug).'" '.checked(in_array($term->slug, $state[$name], true), true, false).'>'.($name === 'features' ? lk_icon($icon ?: 'check') : '').'<span>'.esc_html($term->name).'</span></label>';
  }
  if (!$terms) echo '<p class="lk-filter-empty">Chưa có tiện ích cho danh mục này.</p>';
  echo '</div></fieldset>';
 }
 echo '<div class="lk-filter-actions"><button class="lk-button" type="submit">Áp dụng</button><a class="lk-button lk-outline" href="'.esc_url($reset).'">Đặt lại</a></div></form></aside><div class="lk-results"><div class="lk-results-top"><div><p class="lk-result-count"><strong>'.$count.'</strong> địa điểm'.($state['q'] ? ' cho <strong>“'.esc_html($state['q']).'”</strong>' : ' khớp với tìm kiếm của bạn').'</p>'.($active ? '<a class="lk-clear-filters" href="'.esc_url($reset).'">Xóa tất cả bộ lọc</a>' : '').'</div><label class="lk-sort">Sắp xếp theo: <select name="sort" form="lk-filter" aria-label="Sắp xếp kết quả">';
 foreach (array('relevance'=>'Đúng nhất', 'rating'=>'Điểm đánh giá', 'nearest'=>'Gần tôi nhất', 'price'=>'Giá tăng dần') as $value=>$label) echo '<option value="'.$value.'" '.selected($state['sort'], $value, false).'>'.$label.'</option>';
 echo '</select></label></div><p class="lk-location-status" role="status" hidden></p><div class="lk-active-filters">';
 foreach (array('categories'=>'listing-category','regions'=>'location','collections'=>'lk_collection','features'=>'features') as $name=>$taxonomy) foreach ($state[$name] as $slug) {
  $term = get_term_by('slug', $slug, $taxonomy); if (!$term) continue;
  $args = $_GET; unset($args['pg']); $args[$name] = array_values(array_diff($state[$name], array($slug))); if (!$args[$name]) unset($args[$name]);
  echo '<a href="'.esc_url(add_query_arg($args, lk_link('search'))).'" aria-label="'.esc_attr('Bỏ lọc '.$term->name).'">'.esc_html($term->name).' <span aria-hidden="true">×</span></a>';
 }
 foreach($state['price_status'] as $value){$label=lk_price_status_labels()[$value];$args=$_GET;unset($args['pg']);$args['price_status']=array_values(array_diff($state['price_status'],array($value)));if(!$args['price_status'])unset($args['price_status']);echo '<a href="'.esc_url(add_query_arg($args,lk_link('search'))).'" aria-label="'.esc_attr('Bỏ lọc '.$label).'">'.esc_html($label).' <span aria-hidden="true">×</span></a>';}
 if ($state['nearby']) echo '<span class="lk-nearby-chip">'.lk_icon('location-arrow').' '.esc_html($state['origin']?'Gần '.$state['origin']['title']:'Gần tôi').'</span>';
 echo '</div><div class="lk-results-cards">';
 if (!$count) echo '<div class="lk-empty"><i class="fas fa-search" aria-hidden="true"></i><h2>Chưa tìm thấy địa điểm phù hợp</h2><p>Thử đổi từ khóa hoặc bỏ bớt bộ lọc để xem thêm địa điểm.</p><a class="lk-button" href="'.esc_url(lk_link('search')).'">Xem tất cả địa điểm</a></div>';
 foreach (array_slice($posts, ($page - 1) * $per, $per) as $post) lk_card($post, 'list');
 echo '</div><nav class="lk-pagination" aria-label="Trang kết quả"'.($count <= $per ? ' hidden' : '').'>';
 for ($i = 1; $i <= ceil($count / $per); $i++) echo '<a '.($i === $page ? 'aria-current="page"' : '').' href="'.esc_url(add_query_arg('pg', $i)).'">'.$i.'</a>';
 echo '</nav></div></div><div class="lk-result-tools"><button class="lk-filter-open">'.lk_icon('sliders-h').' Bộ lọc'.($active ? ' <span class="lk-filter-dot"></span>' : '').'</button><button data-map>'.lk_icon('map-marked-alt').' Bản đồ</button></div>';
}
