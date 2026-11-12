<?php
if ( ! function_exists( 'listingpro_shortcode_claimpricing' ) ) {
	function listingpro_shortcode_claimpricing() {
		global $listingpro_options;
		global $wpdb;

		$lp_social_show;
		$lp_social_show = $listingpro_options['listin_social_switch'];
		$dbprefix       = '';
		$dbprefix       = $wpdb->prefix;
		$user_ID        = '';
		$user_ID        = get_current_user_id();
		$output1        = null;
		$results        = null;
		$table_name     = $dbprefix . 'listing_orders';
		$limitLefts     = '';
		$taxOn          = $listingpro_options['lp_tax_swtich'];
		$withtaxprice   = false;
		if ( $taxOn == "1" ) {
			$showtaxwithprice = $listingpro_options['lp_tax_with_plan_swtich'];
			if ( $showtaxwithprice == "1" ) {
				$withtaxprice = true;
			}
		}

		$hide_plan_class = 'lp_hide_general_plans';

		/* horizontal view */

		$output1 .= '
				<div class="page-inner-container ' . $hide_plan_class . ' lp-horizontial-specific lp-plan-paid-claim">';
		$args    = array(
			'post_type'      => 'price_plan',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key'     => 'lp_listingpro_options',
						'value'   => 'claimonly',
						'compare' => 'LIKE'
					),
					array(
						'key'     => 'lp_listingpro_options',
						'value'   => 'listingandclaim',
						'compare' => 'LIKE'
					)
				),
				array(
					'key'   => 'plan_package_type',
					'value' => 'Pay Per Listing',

				),
			),
		);
		$query   = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				global $post;

				$lp_social_show;
				$lp_social_show = $listingpro_options['listin_social_switch'];
				$dbprefix       = '';
				$dbprefix       = $wpdb->prefix;
				$user_ID        = '';
				$user_ID        = get_current_user_id();
				$outputHorz     = null;
				$results        = null;
				$table_name     = $dbprefix . 'listing_orders';
				$limitLefts     = '';
				$taxOn          = $listingpro_options['lp_tax_swtich'];
				$withtaxprice   = false;
				if ( $taxOn == "1" ) {
					$showtaxwithprice = $listingpro_options['lp_tax_with_plan_swtich'];
					if ( $showtaxwithprice == "1" ) {
						$withtaxprice = true;
					}
				}


				$showCatsBoxes = false;

				/* horizontal view */

				global $post;
				$plan_package_type     = get_post_meta( get_the_ID(), 'plan_package_type', true );
				$post_price            = get_post_meta( get_the_ID(), 'plan_price', true );
				$plan_time             = '';
				$plan_time             = get_post_meta( get_the_ID(), 'plan_time', true );
				$posts_allowed_in_plan = '';
				$posts_allowed_in_plan = get_post_meta( get_the_ID(), 'plan_text', true );
				$plan_type             = $plan_package_type;

				if ( ! empty( $plan_package_type ) && $plan_package_type == "Package" ) {
					if ( is_numeric( $posts_allowed_in_plan ) ) {
						$posts_allowed_in_plan = $posts_allowed_in_plan;
					} else {
						$posts_allowed_in_plan = esc_html__( 'unlimited', 'listingpro-plugin' );
					}
				}


				$contact_show = get_post_meta( get_the_ID(), 'contact_show', true );
				if ( $contact_show == 'true' ) {
					$contact_checked = 'checked';
				} else {
					$contact_checked = 'unchecked';
				}

				$map_show = get_post_meta( get_the_ID(), 'map_show', true );
				if ( $map_show == 'true' ) {
					$map_checked = 'checked';
				} else {
					$map_checked = 'unchecked';
				}

				$video_show = get_post_meta( get_the_ID(), 'video_show', true );
				if ( $video_show == 'true' ) {
					$video_checked = 'checked';
				} else {
					$video_checked = 'unchecked';
				}

				$gallery_show = get_post_meta( get_the_ID(), 'gallery_show', true );
				if ( $gallery_show == 'true' ) {
					$gallery_checked = 'checked';
				} else {
					$gallery_checked = 'unchecked';
				}

				$listingproc_tagline = get_post_meta( get_the_ID(), 'listingproc_tagline', true );
				if ( $listingproc_tagline == 'true' ) {
					$tagline_checked = 'checked';
				} else {
					$tagline_checked = 'unchecked';
				}

				$listingproc_location = get_post_meta( get_the_ID(), 'listingproc_location', true );
				if ( $listingproc_location == 'true' ) {
					$location_checked = 'checked';
				} else {
					$location_checked = 'unchecked';
				}

				$listingproc_website = get_post_meta( get_the_ID(), 'listingproc_website', true );
				if ( $listingproc_website == 'true' ) {
					$website_checked = 'checked';
				} else {
					$website_checked = 'unchecked';
				}

				$listingproc_social = get_post_meta( get_the_ID(), 'listingproc_social', true );
				if ( $listingproc_social == 'true' ) {
					$social_checked = 'checked';
				} else {
					$social_checked = 'unchecked';
				}

				$listingproc_faq = get_post_meta( get_the_ID(), 'listingproc_faq', true );
				if ( $listingproc_faq == 'true' ) {
					$faq_checked = 'checked';
				} else {
					$faq_checked = 'unchecked';
				}

				$listingproc_price = get_post_meta( get_the_ID(), 'listingproc_price', true );
				if ( $listingproc_price == 'true' ) {
					$price_checked = 'checked';
				} else {
					$price_checked = 'unchecked';
				}

				$listingproc_tag_key = get_post_meta( get_the_ID(), 'listingproc_tag_key', true );
				if ( $listingproc_tag_key == 'true' ) {
					$tag_key_checked = 'checked';
				} else {
					$tag_key_checked = 'unchecked';
				}

				$listingproc_bhours = get_post_meta( get_the_ID(), 'listingproc_bhours', true );
				if ( $listingproc_bhours == 'true' ) {
					$bhours_checked = 'checked';
				} else {
					$bhours_checked = 'unchecked';
				}

				/* new options  */
				$resurva_show = get_post_meta( get_the_ID(), 'listingproc_plan_reservera', true );
				if ( $resurva_show == "true" ) {
					$resurva_show = 'checked';
				} else {
					$resurva_show = 'unchecked';
				}

				$timekit_show = get_post_meta( get_the_ID(), 'listingproc_plan_timekit', true );
				if ( $timekit_show == "true" ) {
					$timekit_show = 'checked';
				} else {
					$timekit_show = 'unchecked';
				}

				$menu_show = get_post_meta( get_the_ID(), 'listingproc_plan_menu', true );
				if ( $menu_show == "true" ) {
					$menu_show = 'checked';
				} else {
					$menu_show = 'unchecked';
				}

				$announcment_show = get_post_meta( get_the_ID(), 'listingproc_plan_announcment', true );
				if ( $announcment_show == "true" ) {
					$announcment_show = 'checked';
				} else {
					$announcment_show = 'unchecked';
				}

				$deals_show = get_post_meta( get_the_ID(), 'listingproc_plan_deals', true );
				if ( $deals_show == "true" ) {
					$deals_show = 'checked';
				} else {
					$deals_show = 'unchecked';
				}

				$competitor_show = get_post_meta( get_the_ID(), 'listingproc_plan_campaigns', true );
				if ( $competitor_show == "true" ) {
					$competitor_show = 'checked';
				} else {
					$competitor_show = 'unchecked';
				}


				$featured_show = get_post_meta( get_the_ID(), 'lp_featured_imageplan', true );
				if ( $featured_show == "true" ) {
					$featured_show = 'checked';
				} else {
					$featured_show = 'unchecked';
				}


				$event_show = get_post_meta( get_the_ID(), 'lp_eventsplan', true );
				if ( $event_show == "true" ) {
					$event_show = 'checked';
				} else {
					$event_show = 'unchecked';
				}
				/* new options ends  */


				$plan_hot = '';
				$plan_hot = get_post_meta( get_the_ID(), 'plan_hot', true );


				$plan_type_name = '';
				if ( $plan_package_type == "Pay Per Listing" ) {
					$plan_type_name = esc_html__( "Per Listing", 'listingpro-plugin' );
				} else {
					$plan_type_name = esc_html__( "Per Package", 'listingpro-plugin' );
				}


				$plan_text       = '';
				$used            = '';
				$plan_limit_left = '';
				$limitLefts      = null;
				$currentPlanID   = get_the_ID();

				if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
					$results = $wpdb->get_results( "SELECT * FROM " . $dbprefix . "listing_orders WHERE user_id ='$user_ID' AND plan_id ='$currentPlanID' AND status = 'success' AND plan_type='$plan_package_type'" );
				}

				$used = '';
				if ( ! empty( $plan_package_type ) && $plan_package_type == "Package" ) {
					$plan_text       = esc_html__( 'Per Package ', 'listingpro-plugin' );
					$plan_limit_left = $posts_allowed_in_plan;
				} else if ( ! empty( $plan_package_type ) && $plan_package_type == "Pay Per Listing" ) {
					$plan_text = esc_html__( 'Per Listing ', 'listingpro-plugin' );
				}
				if ( ! empty( $results ) && count( $results ) > 0 ) {
					if ( ! empty( $plan_package_type ) && $plan_package_type == "Package" ) {

						/* package details */
						/* foreach ( $results as $info ) {
							$used = $info->used;
						} */

						$used = $results[0]->used;

						if ( is_numeric( $posts_allowed_in_plan ) ) {
							$plan_limit_left = (int) $posts_allowed_in_plan - (int) $used;
						} else {
							$plan_limit_left = $posts_allowed_in_plan;
						}

						$plan_text = esc_html__( 'Per Package ', 'listingpro-plugin' );
					} else if ( ! empty( $plan_package_type ) && $plan_package_type == "Pay Per Listing" ) {
						$plan_text = esc_html__( 'Per Listing ', 'listingpro-plugin' );
					}


				}

				$plan_title_color = '';
				$plan_title_img   = '';
				$plan_title_bg    = '';

				$plan_title_img = listing_get_metabox_by_ID( 'lp_price_plan_bg', get_the_ID() );

				$plan_title_color = get_post_meta( get_the_ID(), 'plan_title_color', true );
				$classForBg       = 'price-plan-box-upper';
				if ( isset( $plan_title_img ) && $plan_title_img != '' ) {
					$plan_title_bg = "background: url($plan_title_img); background-size:cover;";
					$classForBg    .= ' lp-overlay-pricing';
				} else {
					$plan_title_bg = "background-color: $plan_title_color;";
				}

				$hotClass = '';
				if ( ! empty( $plan_hot ) && $plan_hot == "true" ) {
					$hotClass = 'featured-plan';
				} else {
					$hotClass = '';
				}

				//class="featured-active-plan" for active plans
				$output1 .= '
															<div class="clearfix lp_hori_view_plan_left_section ' . get_the_ID() . ' ' . $hotClass . '">
															<div class="lp-active-badge-on-plan">
																<img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAABdCAYAAAACA/BSAAAACXBIWXMAABYlAAAWJQFJUiTwAAAEcklEQVR4nO3cv0+TQQDG8QestA3oK9GUNoGGEOtkYusKjazGhcHFDXTVGCcGFx1I3DBxcCOwMDDpgP4BNC6SoNGtmCgaCyWKr9RAa20d2oNS2r53fe/XS++bsEBf7vjkerw9GrpGn3yaBDCJDmn/54++zTepm99WHnxmuc4HYBjANRGT0rJyGQBeAIizXNYtZDL6d2UwOfuU5YJOhQKA+4PJ2QnaB3cyFADMDyZnh2ke2OlQFir7lWOdDgVQ7lcGqpLjfmWgDmu5Xxmow1ruVwbqaE33KwN1vIb7lYFq3LH9ykA17th+ZaCad2S/MlCtO9ivDJRz84PJ2XMGyjkLwAufqtEjlg8Zuyh93MD5C/i28qCL9TplK+rhjRAS0aCq4ZlTApWIBhEfCuDOWL+K4dtKCRQBig8FPLOqpEOR1UTyyqqSDlUP45VVJRWqfjWRvLCqpEI1A/HCqpIG1Ww1kXRfVdKgnCB0X1Xc78xjoR70BU4hEQ3gjL8bsQE/wpYP4bPOQz27FcG7r/vY3f+HdLaATbuIjF1EeiuPXL7Ee6pMuYKKhXqQvNSLq9Eg+vzduBjqcT0h8vRMxnqPfD6XL2E9W0DG/ov0VgFLq7brsVhy9dRLZwuIWD7EhwJckFrV5+9GfCiAZKwXaxt7QsdqlOs9amZ5G68/7vKYi2O5fAn3Fr8jnS1IGa82Lpu5DCyVSADH33oisVQjAZxvD0Rg6YAECLiP4omlCxIg6IaTB5ZOSIDAO/OZ5W1X1y+t2togAQKhYi7vq9xezzthUGHrtKvrIy6v5524FTXgbkWIvtNnTdunHq/vwSuBK8rf8uvr2YLjiYDbpy/PhP0BtNmxyubvIuZSO3j1YRcRy4fbY/24fvlMw8fGBnqwkv4jaopMCYFqdABXC0TK2EXMLG9jLrXTEOxqNIg57IiYInNCoCLW4bfN5UuYS+20PD+qBXt4I3RwJhW2lP3F/1hCZhK2fMjlS1hatbH01qY+nczYRdxd/I5ENIg7Y/0tz9hlJwQqvVXAzecbbR/frm3s4e7iHhLRoLI3c9QnBIrXBqziJLNZ5v1RlBkoygwUZQaKMgNFmYGizEBRZqAoM1CUGSjKDBRlBooylVBfFI7NnCqo96j8E5kpReMzpwLqPYDx1PTIr9T0yDw8giUb6gCJfKKK9VjyPJiTCWWjDomUmh55BGBB4lyYkwXVFImUmh6ZhMZYMqAI0junB+qMJRqKGomkK5ZIKGYkko5YoqDaRiLphiUCyjUSSScs3lDckEi6YPGE4o5E0gGLF5QwJJJqLF5QQpFIKrF4QE3JQCKpwnILNVV9USu1KtZLmWO6gVKCVNMkKqcRUmoXSjUSqi+wxyEJqx0o5UgkmVisUNogkWRhsUBph0SSgUULpS0SSTQWDZT2SCSRWE5QnkEiicJqBbXgNSSSCKxmUAvVu1/PxhurEZTnkUg8seqhTgwSiRdWLdSJQyLVYLX9xhACdWKRSFWsCVQOGdkbffJpuFwuo1M+2v15/wPdmpDUltAwdgAAAABJRU5ErkJggg==">
															</div>
															
															
																<ul class="horizontal_view_list col-md-7">';
				if ( ! empty( $plan_hot ) && $plan_hot == "true" ) {
					//echo '<div class="lp-hot">'.esc_html__('Hot','listingpro-plugin').'</div>';
					$title = '';
					if ( ! empty( get_the_title() ) ) : $title = '<span>' . get_the_title() . '</span>'; endif;

					$output1 .= '<div class="lp-hot">
																					<img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAABdCAYAAAACA/BSAAAACXBIWXMAABYlAAAWJQFJUiTwAAAEcklEQVR4nO3cv0+TQQDG8QestA3oK9GUNoGGEOtkYusKjazGhcHFDXTVGCcGFx1I3DBxcCOwMDDpgP4BNC6SoNGtmCgaCyWKr9RAa20d2oNS2r53fe/XS++bsEBf7vjkerw9GrpGn3yaBDCJDmn/54++zTepm99WHnxmuc4HYBjANRGT0rJyGQBeAIizXNYtZDL6d2UwOfuU5YJOhQKA+4PJ2QnaB3cyFADMDyZnh2ke2OlQFir7lWOdDgVQ7lcGqpLjfmWgDmu5Xxmow1ruVwbqaE33KwN1vIb7lYFq3LH9ykA17th+ZaCad2S/MlCtO9ivDJRz84PJ2XMGyjkLwAufqtEjlg8Zuyh93MD5C/i28qCL9TplK+rhjRAS0aCq4ZlTApWIBhEfCuDOWL+K4dtKCRQBig8FPLOqpEOR1UTyyqqSDlUP45VVJRWqfjWRvLCqpEI1A/HCqpIG1Ww1kXRfVdKgnCB0X1Xc78xjoR70BU4hEQ3gjL8bsQE/wpYP4bPOQz27FcG7r/vY3f+HdLaATbuIjF1EeiuPXL7Ee6pMuYKKhXqQvNSLq9Eg+vzduBjqcT0h8vRMxnqPfD6XL2E9W0DG/ov0VgFLq7brsVhy9dRLZwuIWD7EhwJckFrV5+9GfCiAZKwXaxt7QsdqlOs9amZ5G68/7vKYi2O5fAn3Fr8jnS1IGa82Lpu5DCyVSADH33oisVQjAZxvD0Rg6YAECLiP4omlCxIg6IaTB5ZOSIDAO/OZ5W1X1y+t2togAQKhYi7vq9xezzthUGHrtKvrIy6v5524FTXgbkWIvtNnTdunHq/vwSuBK8rf8uvr2YLjiYDbpy/PhP0BtNmxyubvIuZSO3j1YRcRy4fbY/24fvlMw8fGBnqwkv4jaopMCYFqdABXC0TK2EXMLG9jLrXTEOxqNIg57IiYInNCoCLW4bfN5UuYS+20PD+qBXt4I3RwJhW2lP3F/1hCZhK2fMjlS1hatbH01qY+nczYRdxd/I5ENIg7Y/0tz9hlJwQqvVXAzecbbR/frm3s4e7iHhLRoLI3c9QnBIrXBqziJLNZ5v1RlBkoygwUZQaKMgNFmYGizEBRZqAoM1CUGSjKDBRlBooylVBfFI7NnCqo96j8E5kpReMzpwLqPYDx1PTIr9T0yDw8giUb6gCJfKKK9VjyPJiTCWWjDomUmh55BGBB4lyYkwXVFImUmh6ZhMZYMqAI0junB+qMJRqKGomkK5ZIKGYkko5YoqDaRiLphiUCyjUSSScs3lDckEi6YPGE4o5E0gGLF5QwJJJqLF5QQpFIKrF4QE3JQCKpwnILNVV9USu1KtZLmWO6gVKCVNMkKqcRUmoXSjUSqi+wxyEJqx0o5UgkmVisUNogkWRhsUBph0SSgUULpS0SSTQWDZT2SCSRWE5QnkEiicJqBbXgNSSSCKxmUAvVu1/PxhurEZTnkUg8seqhTgwSiRdWLdSJQyLVYLX9xhACdWKRSFWsCVQOGdkbffJpuFwuo1M+2v15/wPdmpDUltAwdgAAAABJRU5ErkJggg==">
																					</div>';
				}
				$title = '';
				if ( ! empty( get_the_title() ) ) : $title = '<span>' . get_the_title() . '</span>'; endif;
				$output1 .= '<li>
																<label>
																<input type="radio" name="plan_id" value="' . get_the_ID() . '" />
																' . $title . '
																</label>
																	
																	<div class="per_user_per_listing_price">';

				if ( ! empty( $post_price ) ) {
					if ( $withtaxprice == "1" ) {
						$taxrate    = $listingpro_options['lp_tax_amount'];
						$taxprice   = (float) ( ( $taxrate / 100 ) * $post_price );
						$post_price = (float) $post_price + (float) $taxprice;
					}
					$post_price           = number_format( (float) $post_price, 2, '.', '' );
					$lp_currency_position = $listingpro_options['pricingplan_currency_position'];
					if ( isset( $lp_currency_position ) && $lp_currency_position == "left" ) {
						$output1 .= '<span>' . listingpro_currency_sign() . $post_price . '</span>';
					} else {
						$output1 .= '<span>' . $post_price . listingpro_currency_sign() . '</span>';
					}
				} else {
					$output1 .= '<span>' . esc_html__( "Free", "listingpro-plugin" ) . '</span>';
				}

				$output1 .= '<p>/ ' . $plan_text . '</p>';

				$output1 .= '</div>
																	</li>';


				$output1 .= '</ul>';


				$output1 .= '<div class="col-md-5 lp-list-form-outer-postion"><ul class="lp-listprc">
																			<li>
																				<span class="icon-text">
																					<i class="awesome_plan_icon_check fa fa-check-circle"></i>
																				</span>';
				$output1 .= '<span>';
				if ( ! empty( $plan_time ) ) {
					$output1 .= esc_html__( 'Duration', 'listingpro-plugin' ) . ' : ' . $plan_time . ' ' . esc_html__( 'days', 'listingpro-plugin' );
				} else {
					$output1 .= esc_html__( 'Duration', 'listingpro-plugin' );
					$output1 .= esc_html__( ' : Unlimited days', 'listingpro-plugin' );
				}
				$output1 .= '</span>';
				$output1 .= '<div class="tooltip_price_features">
																							<span><i class="fa fa-question"></i></span>
																							<p class="lp_tooltip_text">' . esc_html__( 'Lorem ipsum dolor sit amet, lorem sit.', 'listingpro-plugin' ) . '</p>
																							</div>';
				$output1 .= '</li>';

				if ( ! empty( $posts_allowed_in_plan ) && $plan_type == "Package" ) {
					$output1 .= '<li>';
					$output1 .= '<span class="icon-text">' . listingpro_fontawesome_icon( 'checked' ) . '</span>';
					$output1 .= '<span>' . esc_html__( 'Max. Listings : ', 'listingpro-plugin' ) . $posts_allowed_in_plan . '</span>';
					$output1 .= '</li>';
				}

				if ( empty( $posts_allowed_in_plan ) && $plan_type == "Package" ) {
					$output1 .= '<li>';
					$output1 .= '<span class="icon-text">' . listingpro_fontawesome_icon( 'checked' ) . '</span>';
					$output1 .= '<span>' . esc_html__( 'Max. Listings : Unlimited', 'listingpro-plugin' ) . '</span>';
					$output1 .= '</li>';
				}

				if ( $listingpro_options['lp_showhide_address'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'map_show_hide', true ) == '' ) {
						$output1 .= '
																					<li>
																						<span class="icon icons8-Cancel">' . listingpro_fontawesome_icon( $map_checked ) . '</span>
																						<span>' . esc_html__( 'Map Display', 'listingpro-plugin' ) . '</span>
																					</li>';
					}
				}
				if ( $listingpro_options['phone_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'contact_show_hide', true ) == '' ) {
						$output1 .= '
																							<li>
																								<span class="icon icons8-Cancel">' . listingpro_fontawesome_icon( $contact_checked ) . '</span>
																								<span>' . esc_html__( 'Contact Display', 'listingpro-plugin' ) . '</span>
																							</li>
																							';
					}
				}
				if ( $listingpro_options['file_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'gall_show_hide', true ) == '' ) {
						$output1 .= '
																						<li>
																							<span class="icon icons8-Cancel">' . listingpro_fontawesome_icon( $gallery_checked ) . '</span>
																							<span>' . esc_html__( 'Image Gallery', 'listingpro-plugin' ) . '</span>
																						</li>
																						';
					}
				}
				if ( $listingpro_options['vdo_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'video_show_hide', true ) == '' ) {
						$output1 .= '
																						<li>
																							<span class="icon icons8-Cancel">' . listingpro_fontawesome_icon( $video_checked ) . '</span>
																							<span>' . esc_html__( 'Video', 'listingpro-plugin' ) . '</span>
																						</li>
																						';
					}
				}
				if ( get_post_meta( get_the_ID(), 'tagline_show_hide', true ) == '' ) {
					$output1 .= '
																				<li>
																					<span class="icon-text">' . listingpro_fontawesome_icon( $tagline_checked ) . '</span>
																					<span>' . esc_html__( 'Business Tagline', 'listingpro-plugin' ) . '</span>
																				</li>
																				';
				}
				if ( $listingpro_options['location_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'location_show_hide', true ) == '' ) {
						$output1 .= '
																						<li>
																							<span class="icon-text">' . listingpro_fontawesome_icon( $location_checked ) . '</span>
																							<span>' . esc_html__( 'Location', 'listingpro-plugin' ) . '</span>
																						</li>';
					}
				}
				if ( $listingpro_options['web_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'website_show_hide', true ) == '' ) {
						$output1 .= '
																					<li>
																						<span class="icon-text">' . listingpro_fontawesome_icon( $website_checked ) . '</span>
																						<span>' . esc_html__( 'Website', 'listingpro-plugin' ) . '</span>
																					</li>';
					}

				}

				if ( $listingpro_options['listin_social_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'social_show_hide', true ) == '' ) {
						$output1 .= '
																					<li>
																						<span class="icon-text">' . listingpro_fontawesome_icon( $social_checked ) . '</span>
																						<span>' . esc_html__( 'Social Links', 'listingpro-plugin' ) . '</span>
																					</li>
																					';
					}
				}
				if ( $listingpro_options['faq_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'faqs_show_hide', true ) == '' ) {
						$output1 .= '
																						<li>
																							<span class="icon-text">' . listingpro_fontawesome_icon( $faq_checked ) . '</span>
																							<span>' . esc_html__( 'FAQ', 'listingpro-plugin' ) . '</span>
																						</li>
																						';
					}
				}
				if ( $listingpro_options['currency_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'price_show_hide', true ) == '' ) {
						$output1 .= '
																						<li>
																							<span class="icon-text">' . listingpro_fontawesome_icon( $price_checked ) . '</span>
																							<span>' . esc_html__( 'Price Range', 'listingpro-plugin' ) . '</span>
																						</li>
																						';
					}
				}

				if ( $listingpro_options['tags_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'tags_show_hide', true ) == '' ) {
						$output1 .= '
																						<li>
																							<span class="icon-text">' . listingpro_fontawesome_icon( $tag_key_checked ) . '</span>
																							<span>' . esc_html__( 'Tags/Keywords', 'listingpro-plugin' ) . '</span>
																						</li>
																						';
					}
				}
				if ( $listingpro_options['oph_switch'] == "1" ) {
					if ( get_post_meta( get_the_ID(), 'bhours_show_hide', true ) == '' ) {
						$output1 .= '		
																					<li>
																						<span class="icon-text">' . listingpro_fontawesome_icon( $bhours_checked ) . '</span>
																						<span>' . esc_html__( 'Business Hours', 'listingpro-plugin' ) . '</span>
																					</li>
																					';
					}
				}
				/* new option */
				if ( lp_theme_option( 'lp_featured_file_switch' ) ) {
					if ( get_post_meta( get_the_ID(), 'reserva_show_hide', true ) == '' ) {
						$output1 .= '
																						<li>
																							<span class="icon-text">' . listingpro_fontawesome_icon( $resurva_show ) . '</span>
																							<span>' . esc_html__( 'Resurva', 'listingpro-plugin' ) . '</span>
																						</li>
																						';
					}
				}
				if ( get_post_meta( get_the_ID(), 'timekit_show_hide', true ) == '' ) {
					$output1 .= '
																					<li>
																						<span class="icon-text">' . listingpro_fontawesome_icon( $timekit_show ) . '</span>
																						<span>' . esc_html__( 'Timekit', 'listingpro-plugin' ) . '</span>
																					</li>
																					';
				}
				if ( get_post_meta( get_the_ID(), 'menu_show_hide', true ) == '' ) {

					$output1 .= '
																					<li>
																						<span class="icon-text">' . listingpro_fontawesome_icon( $menu_show ) . '</span>
																						<span>' . esc_html__( 'Menu', 'listingpro-plugin' ) . '</span>
																					</li>
																					';
				}
				if ( get_post_meta( get_the_ID(), 'announcment_show_hide', true ) == '' ) {
					$output1 .= '
																				<li>
																					<span class="icon-text">' . listingpro_fontawesome_icon( $announcment_show ) . '</span>
																					<span>' . esc_html__( 'Announcement', 'listingpro-plugin' ) . '</span>
																				</li>
																				';
				}
				if ( get_post_meta( get_the_ID(), 'deals_show_hide', true ) == '' ) {
					$output1 .= '
																				<li>
																					<span class="icon-text">' . listingpro_fontawesome_icon( $deals_show ) . '</span>
																					<span>' . esc_html__( 'Deals-Offers-Discounts', 'listingpro-plugin' ) . '</span>
																				</li>
																				';
				}
				if ( get_post_meta( get_the_ID(), 'metacampaign_show_hide', true ) == '' ) {
					$output1 .= '
																				<li>
																					<span class="icon-text">' . listingpro_fontawesome_icon( $competitor_show ) . '</span>
																					<span>' . esc_html__( 'Hide competitors Ads', 'listingpro-plugin' ) . '</span>
																				</li>
																				';
				}
				if ( get_post_meta( get_the_ID(), 'events_show_hide', true ) == '' ) {
					$output1 .= '
																				<li>
																					<span class="icon-text">' . listingpro_fontawesome_icon( $event_show ) . '</span>
																					<span>' . esc_html__( 'Events', 'listingpro-plugin' ) . '</span>
																				</li>
																				';
				}
				$listingproc_leadform = get_post_meta( get_the_ID(), 'listingproc_leadform', true );
				if ( $listingproc_leadform == "true" ) {
					$listingproc_leadform = 'checked';
				} else {
					$listingproc_leadform = 'unchecked';
				}
				if ( get_post_meta( get_the_ID(), 'leadform_show_hide', true ) == '' ) {
					$output1 .= '
                                    <li>
                                        <span class="icon-text">' . listingpro_fontawesome_icon( $listingproc_leadform ) . '</span>
                                        <span>' . esc_html__( 'Lead Form', 'listingpro-plugin' ) . '</span>
                                    </li>
                                    ';
				}
				/* new option emd */

				$lp_plan_more_fields = listing_get_metabox_by_ID( 'lp_price_plan_addmore', get_the_ID() );
				if ( ! empty( $lp_plan_more_fields ) ) {
					foreach ( $lp_plan_more_fields as $morefield ) {
						if ( ! empty( $morefield ) ) {
							$output1 .= '<li>
																							<span class="icon-text">' . listingpro_fontawesome_icon( 'checked' ) . '</span>
																							<span>' . $morefield . '</span>
																						</li>';
						}
					}
				}
				$output1 .= '
																		</ul>

																		
																	</div>';


				$output1 .= '</div>';


			}/* END WHILE */
			wp_reset_postdata();
		} else {
			echo '<p class="text-center">' . esc_html__( 'There is no Plan available right now.', 'listingpro-plugin' ) . '</p>';
		}

		$output1  .= '
            </div>';
		$srtingss = esc_html__( 'By checking this box you agree that the listing claim process is not completed until the admin approves the request, and then an email is sent to you to process the payment.', 'listingpro-plugin' );
		if ( $query->have_posts() ) {
			$output1 .= '
			<div class="checkbox singincheckbox">
                <div class="form-group lp-claim-form-check-circle lp-claim-form-check-circle-new">
                    <label><input type="checkbox" id="" name="lp-claim-form-check-circle" value=""><span class="lp-new-checkbox-style"></span><span class="lp-new-checkbox-style2">' . $srtingss . '</span></label>
                </div>
            </div>
            <div class="clearfix"></div>
                <form  enctype="multipart/form-data" method="post" action="#" class="price-plan-button horizontial_view_width">
                        <input type="hidden" name="plan_id" value="' . get_the_ID() . '"/>';

			if ( ! empty( $plan_package_type ) && $plan_package_type == "Package" ) {
				$plan_price = get_post_meta( get_the_ID(), 'plan_price', true );
				if ( ! empty( $plan_price ) ) {

					if ( ! empty( $plan_limit_left ) ) {


						$output1 .= '<input id="submit' . get_the_ID() . '" class="lp-secondary-btn btn-second-hover lp-secondary-choose" type="submit" value="' . esc_html__( 'CHOOSE PLAN', 'listingpro-plugin' ) . '" name="submit">';
					} else {
						$output1 .= '<input id="submit' . get_the_ID() . '" class="lp-secondary-btn btn-second-hover lp-secondary-choose" type="submit" value="' . esc_html__( 'CHOOSE PLAN', 'listingpro-plugin' ) . '" name="submit">';
					}
				} else {
					$output1 .= '<p>A <strong>' . esc_html__( "Package", 'listingpro-plugin' ) . '</strong>' . esc_html__( " should have a price ", 'listingpro-plugin' ) . '</p>';
				}
			} else {
				$output1 .= '<input id="submit' . get_the_ID() . '" class="lp-secondary-btn btn-second-hover lp-secondary-choose" type="submit" value="' . esc_html__( 'CHOOSE PLAN', 'listingpro-plugin' ) . '" name="submit">';
			}

			$output1 .= wp_nonce_field( 'price_nonce', 'price_nonce_field' . get_the_ID(), true, false );
			$dsjfdk  = esc_html__( 'Claim request is processed after verification..', 'listingpro' );
			$output1 .= '
                    </form>
                <input class="lp-secondary-btn btn-second-hover lp-secondary-choose lp-claim-plan-btn" type="submit" value="' . esc_html__( 'CHOOSE PLAN', 'listingpro-plugin' ) . '" name="submit" style="display:none;">
                <p class="claim_shield text-center"><i class="fa fa-shield" aria-hidden="true"></i> ' . $dsjfdk . '</p>';
		}
		echo $output1;
	}
}
add_shortcode( 'listingpro_claim_pricing', 'listingpro_shortcode_claimpricing' );