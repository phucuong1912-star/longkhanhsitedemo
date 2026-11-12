<?php
/* ============== Breadcrumb ============ */
	if(!function_exists('listingpro_breadcrumbs')){
		function listingpro_breadcrumbs() {
		 
		  $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
		  $delimiter = '&raquo;'; // delimiter between crumbs
		  $home = esc_html__('Home', 'listingpro'); // text for the 'Home' link
		  $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		  $before = '<li><span>'; // tag before the current crumb
		  $after = '</span></li>'; // tag after the current crumb
		 
		  global $post;
		  $homeLink = esc_url(home_url('/'));
		 

		 
			echo '<ul class="breadcrumbs"><li><a href="' . $homeLink . '">' . $home . '</a></li> ';
		 
			if ( is_category() ) {
			  $thisCat = get_category(get_query_var('cat'), false);
			  if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ');
			  echo wp_kses_post($before) . single_cat_title('', false) . $after;
		 
			} elseif ( is_search() ) {
				$tag = '';
				$tagName = 'Null';
				if(!empty($_GET['lp_s_tag']) || !empty($_GET['lp_s_cat'])){
					if(isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])){
						$tag = esc_html($_GET['lp_s_tag']);
						$termo = get_term_by('id', $tag, 'list-tags');
						$tagName = $termo->name;
					}elseif(isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])){
						$tag = esc_html($_GET['lp_s_cat']);
						$termo = get_term_by('id', $tag, 'listing-category');
						$tagName = $termo->name;
					}
					$tagtitle = esc_html__('Search Tag', 'listingpro');
					echo wp_kses_post($before) .$tagtitle." ".$tagName ." ".$after;
				}
				else if(empty($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && !empty($_GET['select']) ){
					$tagtitle = esc_html__('Search ', 'listingpro');
					echo wp_kses_post($before) .$tagtitle." ".sanitize_text_field($_GET['select']) ." ".$after;
				}
				
			  
		 
			} elseif ( is_day() ) {
			  echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			  echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
			  echo wp_kses_post($before) . get_the_time('d') . $after;
		 
			} elseif ( is_month() ) {
			  echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			  echo wp_kses_post($before) . get_the_time('F') . $after;
		 
			} elseif ( is_year() ) {
			  echo wp_kses_post($before) . get_the_time('Y') . $after;
		 
			} elseif ( is_single() && !is_attachment() ) {
			  if ( get_post_type() != 'post' ) {
				$category = get_the_terms( $post->ID, 'listing-category' );				
					if ($category) {
						echo '<li><a href="' . get_term_link( $category[0]->term_id, 'listing-category' ) . '">' . $category[0]->name.'</a> </li>';
										}
				if ($showCurrent == 1) echo wp_kses_post($before) . get_the_title() . $after;
			  } else {
				$cat = get_the_category(); $cat = $cat[0];
				$cats = get_category_parents($cat, TRUE, ' ');
				if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
				echo wp_kses_post($before) .$cats. $after;
				if ($showCurrent == 1) echo wp_kses_post($before) . get_the_title() . $after;
			  }
		 
			}elseif(is_tax('location') || is_tax('listing-category') || is_tax('features')){
				
				  $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
					echo wp_kses_post($before) . wp_kses_post($term->name). wp_kses_post($after); 
			
		 
			}  elseif ( is_attachment() ) {
			  $parent = get_post($post->post_parent);
			  if(isset($parent)&& !empty($parent)){
				  $cat = get_the_category($parent->ID); 
				  if(!empty($cat)){
					$cat = $cat[0];
					echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
					echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
				  }
			  }
			  if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
		 
			} elseif ( is_page() && !$post->post_parent ) {
			  if ($showCurrent == 1) echo wp_kses_post($before) . get_the_title() . $after;
		 
			} elseif ( is_page() && $post->post_parent ) {
			  $parent_id  = $post->post_parent;
			  $breadcrumbs = array();
			  while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
				$parent_id  = $page->post_parent;
			  }
			  $breadcrumbs = array_reverse($breadcrumbs);
			  for ($i = 0; $i < count($breadcrumbs); $i++) {
				echo wp_kses_post($breadcrumbs[$i]);
				if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
			  }
			  if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
		 
			} elseif ( is_tag() ) {
			  echo wp_kses_post($before) . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
		 
			} elseif ( is_author() ) {
			   global $author;
			  $userdata = get_userdata($author);
			  echo wp_kses_post($before) . 'Articles posted by ' . $userdata->display_name . $after;
		 
			} elseif ( is_404() ) {
			  echo wp_kses_post($before) . 'Error 404' . $after;
			}
		 
			if ( get_query_var('paged') ) {
			  if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
			  echo __('Page','listingpro') . ' ' . get_query_var('paged');
			  if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
			}
		 
			echo '</ul>';

		} // end
	}