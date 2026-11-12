<?php
/*------------------------------------------------------*/
/* Pricing plans
/*------------------------------------------------------*/
vc_map(array(
	"name"                      => esc_html__("Pricing Plans", "js_composer"),
	"base"                      => 'listingpro_pricing',
	"category"                  => esc_html__('Listingpro', 'js_composer'),
	"description"               => '',
	"icon" => get_template_directory_uri() . "/assets/images/vcicon.png",
	"params"                    => array(
		array(
			"type"			=> "checkbox",
			"class"			=> "",
			"heading"		=> esc_html__("Show Title And Subtitle", "js_composer"),
			"param_name"	=> "title_subtitle_show",
			"value" => array(
				'' => 'show_hide',
			),
			'save_always' => true,
			"description" =>  esc_html__("Check the checkbox", "js_composer"),
		),
		array(
			"type"			=> "textfield",
			"class"			=> "",
			"heading"		=> esc_html__("Title", "js_composer"),
			"param_name"	=> "title",
			"value"			=> "",
			"dependency"  => array(
				"element" => "title_subtitle_show",
				"value"   => "show_hide"
			),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__('Subtitle', 'js_composer'),
			'param_name'  => 'subtitle',
			'value'       => "",
			"dependency"  => array(
				"element" => "title_subtitle_show",
				"value"   => "show_hide"
			),
		),
		array(
			"type"        => "dropdown",
			"class"       => "",
			"heading"     => esc_html__("Pricing plans views", "js_composer"),
			"param_name"  => "pricing_views",
			'value' => array(
				esc_html__('Horizontal View', 'js_composer') => 'horizontal_view',
				esc_html__('Vertical View', 'js_composer') => 'vertical_view',
			),
			'save_always' => true,
			"description" => "",
		),
		array(
			"type"        => "dropdown",
			"class"       => "",
			"heading"     => esc_html__("Horizontal Views", "js_composer"),
			"param_name"  => "pricing_horizontal_view",
			'value' => array(
				esc_html__('Horizontal View 1', 'js_composer') => 'horizontal_view_1',
				esc_html__('Horizontal View 2', 'js_composer') => 'horizontal_view_2',
			),
			'save_always' => true,
			'dependency'  => array(
				'element' => 'pricing_views',
				'value'   => array('horizontal_view')
			),
			"description" => "",
			'default'	  => 'default',
		),
		array(
			"type"        => "dropdown",
			"class"       => "",
			"heading"     => esc_html__("Vertical Views", "js_composer"),
			"param_name"  => "pricing_vertical_view",
			'value' => array(
				esc_html__('Vertical View 1', 'js_composer') => 'vertical_view_1',
				esc_html__('Vertical View 2', 'js_composer') => 'vertical_view_2',
				/* esc_html__( 'Vertical View 3', 'js_composer' ) => 'vertical_view_3',
				esc_html__( 'Vertical View 4', 'js_composer' ) => 'vertical_view_4', */
				esc_html__('Vertical View 3', 'js_composer') => 'vertical_view_5',
				/* esc_html__( 'Vertical View 6', 'js_composer' ) => 'vertical_view_6',
				esc_html__( 'Vertical View 7', 'js_composer' ) => 'vertical_view_7',
				esc_html__( 'Vertical View 8', 'js_composer' ) => 'vertical_view_8', */
				esc_html__('Vertical View 4', 'js_composer') => 'vertical_view_9',
			),
			'save_always' => true,
			'dependency'  => array(
				'element' => 'pricing_views',
				'value'   => array('vertical_view')
			),
			"description" => ""
		),
	),
));
if (!function_exists('listingpro_shortcode_pricing')) {
	function listingpro_shortcode_pricing($atts, $content = null)
	{
		extract(shortcode_atts(array(
			'title_subtitle_show'		=> '',
			'title'   			=> '',
			'subtitle'   		=> '',
			'pricing_views'   	=> 'horizontal_view',
			'pricing_horizontal_view'  => 'horizontal_view_1',
			'pricing_vertical_view'  => 'vertical_view_1',
			'plan_status'  => '',
		), $atts));
		$output = null;
		global $listingpro_options;
		//set_query_var('pricing_plan_style', $pricing_views);
		$pricing_views = isset($pricing_views) ? sanitize_file_name($pricing_views) : '';
		$allowed_statuses = array('horizontal_view', 'vertical_view');
		$GLOBALS['pricing_views'] = $pricing_views;
		$GLOBALS['pricing_horizontal_view'] = $pricing_horizontal_view;
		$GLOBALS['pricing_vertical_view'] = $pricing_vertical_view;
		$lp_plans_cats = lp_theme_option('listingpro_plans_cats');
		$lp_plans_cats_position = lp_theme_option('listingpro_plans_cats');
		$lp_listing_paid_claim_switchh = lp_theme_option('lp_listing_paid_claim_switch');
		$output .= '<div class="col-md-10 col-md-offset-1 padding-bottom-40 lp-margin-top-case ' . $pricing_views . '">';
		//Title and subtitle field optional
		if ($title_subtitle_show == 'show_hide') {
			$output .= '<div class="page-header">
						<h3>' . $title . '</h3>
						<p>' . $subtitle . '</p>
			</div>';
		} elseif ($lp_plans_cats == 'no') {
			$output .= '<div class="lp-no-title-subtitle">
            </div>';
		}
		if ($lp_listing_paid_claim_switchh == 'yes' && !is_front_page()) {
			$output .= '<div class="lp-no-title-subtitleeeeeeeee">
                ' . esc_html__("Choose a Plan to Claim Your Business", "listingpro-plugin") . '
         </div>';
		}
		if ($plan_status != 'claim') {
			if ($lp_plans_cats == 'yes') {
				ob_start();
				include_once(LISTINGPRO_PLUGIN_PATH . 'templates/pricing/by_category.php');
				$output .= ob_get_contents();
				ob_end_clean();
				ob_flush();
				if (in_array($pricing_views, $allowed_statuses, true)) {
					ob_start();
					include_once(LISTINGPRO_PLUGIN_PATH . "templates/pricing/" . $pricing_views . '.php');
					$output .= ob_get_contents();
					ob_end_clean();
					ob_flush();
				}
			} else {
				if (in_array($pricing_views, $allowed_statuses, true)) {
					ob_start();
					include_once(LISTINGPRO_PLUGIN_PATH . "templates/pricing/" . $pricing_views . '.php');
					$output .= ob_get_contents();
					ob_end_clean();
					ob_flush();
				}
			}
		} else {
			ob_start();
			include_once(LISTINGPRO_PLUGIN_PATH . 'templates/pricing/loop/claim_plans.php');
			$output .= ob_get_contents();
			ob_end_clean();
			ob_flush();
		}
		$output .= '	</div>';
		return $output;
	}
}
add_shortcode('listingpro_pricing', 'listingpro_shortcode_pricing');