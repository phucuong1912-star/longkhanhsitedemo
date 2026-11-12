<?php
/*------------------------------------------------------*/
/* ListingPro Columns Element
/*------------------------------------------------------*/
vc_add_shortcode_param('dropdown_multi', 'dropdown_multi_settings_field');
function dropdown_multi_settings_field($param, $value)
{

    $param_line = '';
    $param_line .= '<select multiple name="' . esc_attr($param['param_name']) . '" class="wpb_vc_param_value wpb-input wpb-select ' . esc_attr($param['param_name']) . ' ' . esc_attr($param['type']) . '">';
    foreach ($param['value'] as $text_val => $val) {
        if (is_numeric($text_val) && (is_string($val) || is_numeric($val))) {
            $text_val = $val;
        }
        $text_val = __($text_val, "js_composer");
        $selected = '';

        if (!is_array($value)) {
            $param_value_arr = explode(',', $value);
        } else {
            $param_value_arr = $value;
        }

        if ($value !== '' && in_array($val, $param_value_arr)) {
            $selected = ' selected="selected"';
        }
        $param_line .= '<option class="' . $val . '" value="' . $val . '"' . $selected . '>' . $text_val . '</option>';
    }
    $param_line .= '</select>';

    return  $param_line;
}

$loc = array();
$argsloc = array(
    'order' => 'ASC',
    'hide_empty' => false,
    'fields' => 'id=>name',
);
$loc = get_terms('location', $argsloc);
$loc = array_flip($loc);

$cats = array();
$argscat = array(
    'order' => 'ASC',
    'hide_empty' => false,
    'fields' => 'id=>name',
);
$cats = get_terms('listing-category', $argscat);
$cats = array_flip($cats);

vc_map(array(
    "name"                      => esc_html__("LP Columns Element", "js_composer"),
    "base"                      => 'listingpro_columns',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "attach_image",
            "class"       => "",

            "heading"     => esc_html__("Column Left Image", "js_composer"),
            "param_name"  => "listing_cols_left_img",
            "value"       => get_template_directory_uri() . "/assets/images/columns.png",
            "description" => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("First Column Title", "js_composer"),
            "param_name"    => "listing_first_col_title",
            "value"            => "1- Claimed"
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('First Column Description', 'js_composer'),
            'param_name'  => 'listing_first_col_desc',
            'value'       => 'Best way to start managing your business listing is by claiming it so you can update.'
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Second Column Title", "js_composer"),
            "param_name"    => "listing_second_col_title",
            "value"            => "2- Promote"
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('Second Column Description', 'js_composer'),
            'param_name'  => 'listing_second_col_desc',
            'value'       => 'Promote your business to target customers who need your services or products.'
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Third Column Title", "js_composer"),
            "param_name"    => "listing_third_col_title",
            "value"            => "3- Convert"
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('Third Column Description', 'js_composer'),
            'param_name'  => 'listing_third_col_desc',
            'value'       => 'Turn your visitors into paying customers with exciting offers and services on your page.'
        ),
    ),
));
function listingpro_shortcode_columns($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_cols_left_img'      => get_template_directory_uri() . "/assets/images/columns.png",
        'listing_first_col_title'    => 'Claimed',
        'listing_first_col_desc'     => 'Best way to start managing your business listing is by claiming it so you can update.',
        'listing_second_col_title'      => 'Promote',
        'listing_second_col_desc'      => 'Promote your business to target customers who need your services or products.',
        'listing_third_col_title'      => 'Convert',
        'listing_third_col_desc'      => 'Turn your visitors into paying customers with exciting offers and services on your page.',
    ), $atts));

    $output = null;

    $leftImg = '';
    if (!empty($listing_cols_left_img)) {
        if (is_array($listing_cols_left_img)) {
            $listing_cols_left_img  =   $listing_cols_left_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($listing_cols_left_img, 'full');
        if (isset($bgImage[0])) {
            $leftImg = '<img src="' . $bgImage[0] . '" alt="image">';
        }
    }

    $output .= '
	<div class="promotional-element listingpro-columns">
		<div class="listingpro-row padding-top-60 padding-bottom-60">
			<div class="promotiona-col-left">
				' . $leftImg . '
			</div>
			<div class="promotiona-col-right">
				<article>
					<h3><i class="fa-solid fa-1"></i>' . $listing_first_col_title . '</h3>
					<p>' . $listing_first_col_desc . '</p>
				</article>
				<article>
					<h3><i class="fa-solid fa-2"></i>' . $listing_second_col_title . '</h3>
					<p>' . $listing_second_col_desc . '</p>
				</article>
				<article>
					<h3><i class="fa-solid fa-3"></i>' . $listing_third_col_title . '</h3>
					<p>' . $listing_third_col_desc . '</p>
				</article>
			</div>
		</div>
	</div>';

    return $output;
}
add_shortcode('listingpro_columns', 'Listingpro_shortcode_columns');
/*------------------------------------------------------*/
/* Promotional Element
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => esc_html__("LP Promotional Element", "js_composer"),
    "base"                      => 'listingpro_promotional',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Banner Left Image", "js_composer"),
            "param_name"  => "listing_element_left_img",
            "value"       => get_template_directory_uri() . "/assets/images/adss.png",
            "description" => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Element Title", "js_composer"),
            "param_name"    => "listing_element_title",
            "value"            => ""
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('Element Description', 'js_composer'),
            'param_name'  => 'element_desc',
            'value'       => ''
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Element Link Title', 'js_composer'),
            'param_name'  => 'element_link_title',
            'description' => esc_html__('Add Link Title', 'js_composer'),
            'default'      => '',
            'value'       => '',
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Element Link URL', 'js_composer'),
            'param_name'  => 'element_link_url',
            'description' => esc_html__('Add URL here', 'js_composer'),
            'default'      => '',
            'value'       => '',
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Element Phone Number', 'js_composer'),
            'param_name'  => 'element_phone_number',
            'description' => '',
            'default'      => '',
            'value'       => '',
        ),
    ),
));
function listingpro_shortcode_promotion($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_element_left_img'      => get_template_directory_uri() . "/assets/images/adss.png",
        'listing_element_title'         => '',
        'element_desc'                     => '',
        'element_link_title'               => '',
        'element_link_url'                   => '',
        'element_phone_number'               => '',
    ), $atts));

    $output = null;

    $leftImg = '';
    if (!empty($listing_element_left_img)) {
        if (is_array($listing_element_left_img)) {
            $listing_element_left_img   =   $listing_element_left_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($listing_element_left_img, 'full');
        if (isset($bgImage[0])) {
            $leftImg = '<img src="' . $bgImage[0] . '" alt="image">';
        }
    } else {
        $leftImg = '';
    }

    $output .= '
	<div class="promotional-element">
		<div class="promotional-row">
			<div class="promotiona-col-left">
				' . $leftImg . '
			</div>
			<div class="promotiona-col-right">
				<h3>' . $listing_element_title . '</h3>
				<p>' . $element_desc . '</p>
				<a href="' . $element_link_url . '" class="lp-sent-btn">' . $element_link_title . '</a>
				<p class="phone_content">' . $element_phone_number . '</p>
			</div>
		</div>
	</div>';

    return $output;
}
add_shortcode('listingpro_promotional', 'Listingpro_shortcode_promotion');


/*------------------------------------------------------*/
/* Promotional Element Services
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => esc_html__("LP Promotional Services", "js_composer"),
    "base"                      => 'listingpro_pro_services',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Banner Left Image", "js_composer"),
            "param_name"  => "listing_pro_services_img",
            "value"       => get_template_directory_uri() . "/assets/images/servcs1.png",
            "description" => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Element Title", "js_composer"),
            "param_name"    => "listing_pro_services_title",
            "value"            => ""
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('Element Description', 'js_composer'),
            'param_name'  => 'pro_services_desc',
            'value'       => ''
        ),
    ),
));
function listingpro_shortcode_pro_services($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_pro_services_img'      => get_template_directory_uri() . "/assets/images/servcs1.png",
        'listing_pro_services_title'         => '',
        'pro_services_desc'                     => '',
    ), $atts));

    $output = null;

    $thumbImg = '';
    if (!empty($listing_pro_services_img)) {
        if (is_array($listing_pro_services_img)) {
            $listing_pro_services_img   =   $listing_pro_services_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($listing_pro_services_img, 'full');
        $thumbImg = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $thumbImg = '';
    }

    $output .= '
	<div class="promotional-service">
		<div class="promotiona-thumb">
			' . $thumbImg . '
		</div>
		<div class="promotiona-text-details">
			<h3>' . $listing_pro_services_title . '</h3>
			<p>' . $pro_services_desc . '</p>
		</div>
	</div>';

    return $output;
}
add_shortcode('listingpro_pro_services', 'Listingpro_shortcode_pro_services');


/*------------------------------------------------------*/
/* Promotional Element Timeline
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => esc_html__("LP Promotional Timeline", "js_composer"),
    "base"                      => 'listingpro_pro_timeline',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Timeline Title", "js_composer"),
            "param_name"    => "listing_pro_timeline_title",
            "value"            => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Timeline Short Description", "js_composer"),
            "param_name"    => "pro_timeline_short_desc",
            "value"            => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Timeline First Title", "js_composer"),
            "param_name"    => "pro_timeline_title_first",
            "value"            => ""
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('Timeline First Description', 'js_composer'),
            'param_name'  => 'pro_timeline_desc_first',
            'value'       => ''
        ),
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Timeline Right Image", "js_composer"),
            "param_name"  => "pro_timeline_first_img",
            "value"       => get_template_directory_uri() . "/assets/images/time1.png",
            "description" => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Timeline Second Title", "js_composer"),
            "param_name"    => "pro_timeline_title_second",
            "value"            => ""
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('Timeline Second Description', 'js_composer'),
            'param_name'  => 'pro_timeline_desc_second',
            'value'       => ''
        ),
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Timeline Left Image", "js_composer"),
            "param_name"  => "pro_timeline_second_img",
            "value"       => get_template_directory_uri() . "/assets/images/time2.png",
            "description" => ""
        ),
    ),
));
function listingpro_shortcode_pro_timeline($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_pro_timeline_title'      => '',
        'pro_timeline_short_desc'         => '',
        'pro_timeline_title_first'           => '',
        'pro_timeline_desc_first'           => '',
        'pro_timeline_first_img'           => get_template_directory_uri() . "/assets/images/time1.png",
        'pro_timeline_title_second'           => '',
        'pro_timeline_desc_second'           => '',
        'pro_timeline_second_img'           => get_template_directory_uri() . "/assets/images/time2.png",
    ), $atts));

    $output = null;

    $timelilneImg1 = '';
    if (!empty($pro_timeline_first_img)) {
        $bgImage = wp_get_attachment_image_src($pro_timeline_first_img, 'full');
        $timelilneImg1 = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $timelilneImg1 = '';
    }

    $timelilneImg2 = '';
    if (!empty($pro_timeline_second_img)) {
        $bgImage = wp_get_attachment_image_src($pro_timeline_second_img, 'full');
        $timelilneImg2 = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $timelilneImg2 = '';
    }

    $output .= '
	<div class="promotional-timeline">
		<div class="top-desc">
			<h2>' . $listing_pro_timeline_title . '</h2>
			<p>' . $pro_timeline_short_desc . '</p>
		</div>
		<div class="timeline-section">
			<div class="promotional-text-details">
				<h3>' . $pro_timeline_title_second . '</h3>
				<p>' . $pro_timeline_desc_second . '</p>
			</div>
			<div class="promotional-thumb">
				' . $timelilneImg1 . '
			</div>
		</div>
		<div class="timeline-section">
			<div class="promotional-thumb">
				' . $timelilneImg2 . '
			</div>
			<div class="promotional-text-details">
				<h3>' . $pro_timeline_title_first . '</h3>
				<p>' . $pro_timeline_desc_first . '</p>
			</div>
		</div>
	</div>';

    return $output;
}
add_shortcode('listingpro_pro_timeline', 'Listingpro_shortcode_pro_timeline');


/*------------------------------------------------------*/
/* Promotional Element Presentaion
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => esc_html__("LP Promotional Presentaion", "js_composer"),
    "base"                      => 'listingpro_pro_presentation',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Presentaion Title", "js_composer"),
            "param_name"    => "presentation_title",
            "value"            => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Presentaion Short Description", "js_composer"),
            "param_name"    => "presentation_short_desc",
            "value"            => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Presentaion First Title", "js_composer"),
            "param_name"    => "presentation_title_first",
            "value"            => ""
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Presentaion First Designation', 'js_composer'),
            'param_name'  => 'presentation_designation_first',
            'value'       => ''
        ),
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Presentaion First Image", "js_composer"),
            "param_name"  => "presentation_first_img",
            "value"       => get_template_directory_uri() . "/assets/images/presentation.png",
            "description" => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Presentaion Second Title", "js_composer"),
            "param_name"    => "presentation_title_second",
            "value"            => ""
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Presentaion Second Designation', 'js_composer'),
            'param_name'  => 'presentation_designation_second',
            'value'       => ''
        ),
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Presentaion Left Image", "js_composer"),
            "param_name"  => "presentation_second_img",
            "value"       => get_template_directory_uri() . "/assets/images/presentation2.png",
            "description" => ""
        ),
    ),
));
function listingpro_shortcode_presentation($atts, $content = null)
{
    extract(shortcode_atts(array(
        'presentation_title'                      => '',
        'presentation_short_desc'                 => '',
        'presentation_title_first'                   => '',
        'presentation_designation_first'         => '',
        'presentation_first_img'                   => get_template_directory_uri() . "/assets/images/presentation.png",
        'presentation_title_second'               => '',
        'presentation_designation_second'         => '',
        'presentation_second_img'                   => get_template_directory_uri() . "/assets/images/presentation2.png",
    ), $atts));

    $output = null;

    $presentationImg1 = '';
    if (!empty($presentation_first_img)) {
        if (is_array($presentation_first_img)) {
            $presentation_first_img =   $presentation_first_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($presentation_first_img, 'full');
        $presentationImg1 = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $presentationImg1 = '';
    }

    $presentationImg2 = '';
    if (!empty($presentation_second_img)) {
        if (is_array($presentation_second_img)) {
            $presentation_second_img    =   $presentation_second_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($presentation_second_img, 'full');
        $presentationImg2 = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $presentationImg2 = '';
    }

    $output .= '
	<div class="promotional-presentation">
		<div class="top-desc">
			<h2>' . $presentation_title . '</h2>
			<p>' . $presentation_short_desc . '</p>
		</div>
		<div class="presentation-section">
			<div class="presentation-text-details">
				<h3>' . $presentation_title_first . '</h3>
				<p>' . $presentation_designation_first . '</p>
			</div>
			<div class="presentation-thumb">
				' . $presentationImg1 . '
			</div>
		</div>
		<div class="presentation-section">
			<div class="presentation-text-details">
				<h3>' . $presentation_title_second . '</h3>
				<p>' . $presentation_designation_second . '</p>
			</div>
			<div class="presentation-thumb">
				' . $presentationImg2 . '
			</div>
		</div>
	</div>';

    return $output;
}
add_shortcode('listingpro_pro_presentation', 'listingpro_shortcode_presentation');


/*------------------------------------------------------*/
/* Promotional Element Support
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => esc_html__("LP Promotional Support", "js_composer"),
    "base"                      => 'listingpro_pro_support',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Support Background Image", "js_composer"),
            "param_name"  => "support_bg_img",
            "value"       => get_template_directory_uri() . "/assets/images/support.jpg",
            "description" => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Title", "js_composer"),
            "param_name"    => "support_title",
            "value"            => "John Doe"
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Designation", "js_composer"),
            "param_name"    => "support_designation",
            "value"            => "John Doe, CEO Abc Organisation"
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Description", "js_composer"),
            "param_name"    => "support_short_desc",
            "value"            => "Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua  eiusmod tempor incididunt ut labore et dolore magna aliqua."
        ),
    ),
));
function listingpro_shortcode_support($atts, $content = null)
{
    extract(shortcode_atts(array(
        'support_bg_img'          => get_template_directory_uri() . "/assets/images/support.jpg",
        'support_title'         => 'John Doe',
        'support_designation'     => 'John Doe, CEO Abc Organisation',
        'support_short_desc'     => 'Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua  eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    ), $atts));

    $output = null;

    $supportImg = '';
    if (!empty($support_bg_img)) {
        if (is_array($support_bg_img)) {
            $support_bg_img =   $support_bg_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($support_bg_img, 'full');
        $supportImg = 'style="background-image: url(' . $bgImage[0] . ');"';
    } else {
        $supportImg = '';
    }

    $output .= '
	<div class="promotional-support" ' . $supportImg . '>
		<div class="support-section">
			<div class="support-text-details">
				<div class="testi-detail">
					<p>' . $support_short_desc . '</p>
				</div>
				<h3>' . $support_title . '</h3>
				<p>' . $support_designation . '</p>
			</div>
		</div>
	</div>';

    return $output;
}
add_shortcode('listingpro_pro_support', 'listingpro_shortcode_support');


/*------------------------------------------------------*/
/* Promotional Element Call to Action
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => esc_html__("LP Call to Action", "js_composer"),
    "base"                      => 'listingpro_calltoaction',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Select Listingpro Call to Action Style", "js_composer"),
            "param_name"  => "listingpro_calltoaction_style",
            'value' => array(
                __('Call to Action with Button ', 'js_composer') => 'style1',
                __('Call to Action Style 3', 'js_composer') => 'style3',
                __('Call to Action without Button ', 'js_composer') => 'style2',

            ),
            'save_always' => true,
            "description" => "Select Call Out Style"
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Call to Action Title", "js_composer"),
            "param_name"    => "calltoaction_title",
            'dependency'  => array(
                'element' => 'listingpro_calltoaction_style',
                'value'   => array('style1', 'style2')
            ),
            "value"            => "Reach customers with confidence."
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Short Description", "js_composer"),
            "param_name"    => "calltoaction_desc",
            'dependency'  => array(
                'element' => 'listingpro_calltoaction_style',
                'value'   => array('style1', 'style2')
            ),
            "value"            => "Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore"
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Line 1", "js_composer"),
            "param_name"    => "style3_line1",
            "value"            => "daily news &amp; tips",
            'dependency'  => array(
                'element' => 'listingpro_calltoaction_style',
                'value'   => 'style3'
            ),
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Line 2", "js_composer"),
            "param_name"    => "style3_line2",
            "value"            => "Read Stories",
            'dependency'  => array(
                'element' => 'listingpro_calltoaction_style',
                'value'   => 'style3'
            ),
        ),
        array(
            "type"        => "attach_images",
            "class"       => "",
            "heading"     => __("Background Image", "js_composer"),
            "param_name"  => "style3_line2_bg",
            "value"       => "",
            'dependency'  => array(
                'element' => 'listingpro_calltoaction_style',
                'value'   => array('style3')
            ),

        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Button Text", "js_composer"),
            "param_name"    => "calltoaction_button",
            "value"            => "Let's Promote Now",
            'dependency'  => array(
                'element' => 'listingpro_calltoaction_style',
                'value'   => array('style1', 'style3')
            ),
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Button Link", "js_composer"),
            "param_name"    => "calltoaction_button_link",
            "value"            => "#",
            'dependency'  => array(
                'element' => 'listingpro_calltoaction_style',
                'value'   => array('style1', 'style3')
            ),
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Phone Number", "js_composer"),
            "param_name"    => "calltoaction_phone",
            "value"            => "or, Call 1800-ListingPro",
            'dependency'  => array(
                'element' => 'listingpro_calltoaction_style',
                'value'   => 'style1'
            ),
        ),
    ),
));
function listingpro_shortcode_calltoaction($atts, $content = null)
{
    extract(shortcode_atts(array(

        'listingpro_calltoaction_style'          => 'style1',
        'calltoaction_title'          => "Reach customers with confidence.",
        'calltoaction_desc'         => "Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore",
        'calltoaction_button'         => "Let's Promote Now",
        'calltoaction_button_link'     => "#",
        'calltoaction_phone'         => "or, Call 1800-ListingPro",
        'style3_line1'         => "daily news &amp; tips",
        'style3_line2'         => "Read Stories",
        'style3_line2_bg' => ''
    ), $atts));

    $output = null;
    if ($listingpro_calltoaction_style == 'style3') {
        if (!empty($style3_line2_bg)) {
            if (is_array($style3_line2_bg)) {
                $style3_line2_bg    =   $style3_line2_bg['id'];
            }
            $style3_line2_bg    =   wp_get_attachment_url($style3_line2_bg);
        }
        $output .=  '<div class="lp-read-news-overlay"></div>';
        $output .=  '<div class="lp-read-news">';
        if (!empty($style3_line1))   $output .=  '<p>' . $style3_line1 . '</p>';
        if (!empty($style3_line2))   $output .=  '<p class="large-size">' . $style3_line2 . '</p>';
        if (!empty($calltoaction_button)) $output .=  '<a href="' . $calltoaction_button_link . '">' . $calltoaction_button . '</a>';
        $output .=  '</div>';
    } else if ($listingpro_calltoaction_style == 'style1') {
        $output .= '
	
	<div class="call-to-action">
		<div class="calltoaction-left-panel">
			<h3>' . $calltoaction_title . '</h3>
			<p>' . $calltoaction_desc . '</p>
		</div>
		<div class="calltoaction-right-panel">
			<a href="' . $calltoaction_button_link . '">' . $calltoaction_button . '</a>
			<p>' . $calltoaction_phone . '</p>
		</div>
	</div>';
    } else {
        $output .= '
	
		<div class="call-to-action2 text-center">
			<div class="calltoaction-left-panel2">
				<h3>' . $calltoaction_title . '</h3>
				<h1>' . $calltoaction_desc . '</h1>
				<img src="' . get_template_directory_uri() . '/assets/images/banner-arrow.png" alt="banner-arrow" class="banner-arrow">
			</div>
			
		</div>';
    }
    return $output;
}
add_shortcode('listingpro_calltoaction', 'listingpro_shortcode_calltoaction');


/*------------------------------------------------------*/
/* Promotional Element Thank you
/*------------------------------------------------------*/

$args = array(
    'sort_order' => 'asc',
    'sort_column' => 'post_title',
    'post_type' => 'page',
    'post_status' => 'publish'
);
$pages = get_pages($args);
$thnxPage = array();
foreach ($pages as $p) {
    $thnxPage[$p->post_title] = $p->ID;
}
vc_map(array(
    "name"                      => esc_html__("LP Notification", "js_composer"),
    "base"                      => 'listingpro_thankyou',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Notification Image", "js_composer"),
            "param_name"  => "thankyou_img",
            "value"       => get_template_directory_uri() . "/assets/images/thankyou.jpg",
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Notification Title", "js_composer"),
            "param_name"    => "thankyou_title",
            "value"            => ""
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Select Notice", "js_composer"),
            "param_name"  => "listingpro_notice",
            'value' => array(
                esc_html__('Success', 'js_composer') => 'success',
                esc_html__('Failed', 'js_composer') => 'failed',
            ),
            'save_always' => true,
            "description" => "Select notice that you want to show"
        ),
        array(
            "type"            => "textarea",
            "class"            => "",
            "heading"        => esc_html__("Success Description", "js_composer"),
            "param_name"    => "success_text",
            'dependency' => array(
                'element' => 'listingpro_notice',
                'value' => 'success'
            ),
            "value"            => esc_html__('An email receipt with detials about your order has been sent to email address provided.please keep it for your record', 'js_composer'),
        ),
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("Icon with description", "js_composer"),
            "param_name"  => "success_txt_img",
            'dependency' => array(
                'element' => 'listingpro_notice',
                'value' => 'success'
            ),
            "value"       => get_template_directory_uri() . "/assets/images/email.jpg",
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Notification Description", "js_composer"),
            "param_name"    => "thankyou_desc",
            "value"            => ""
        ),
        array(
            'type'        => 'dropdown',
            'heading'     => esc_html__('Redirect to Page', 'js_composer'),
            'param_name'  => 'thankyou_goto_page',
            'description' => '',
            'default'      => 'default',
            'value'       => $thnxPage
        ),
    ),
));
function listingpro_shortcode_thankyou($atts, $content = null)
{
    extract(shortcode_atts(array(
        'thankyou_img'          => get_template_directory_uri() . "/assets/images/thankyou.jpg",
        'thankyou_title'         => '',
        'listingpro_notice'     => '',
        'success_text'             => esc_html__('An email receipt with detials about your order has been sent to email address provided.please keep it for your record', 'js_composer'),
        'success_txt_img'         => get_template_directory_uri() . "/assets/images/email.jpg",
        'thankyou_desc'         => '',
        'thankyou_goto_page'     => '',
    ), $atts));

    $output = null;

    $thnkImg = '';
    if (!empty($thankyou_img)) {
        if (is_array($thankyou_img)) {
            $thankyou_img   =   $thankyou_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($thankyou_img, 'full');
        $thnkImg = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $thnkImg = '';
    }

    $thnkIcon = '';
    if (!empty($success_txt_img)) {
        if (is_array($success_txt_img)) {
            $success_txt_img    =   $success_txt_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($success_txt_img, 'full');
        $thnkIcon = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $thnkIcon = '';
    }

    $output .= '
	<div class="thankyou-page">
		<div class="thankyou-icon">
			' . $thnkImg . '
		</div>
		<div class="thankyou-panel">
			<h3>' . $thankyou_title . '</h3>';
    if ($listingpro_notice == 'success') {
        $output .= '
				<div class="success-txt">';
        if (!empty($thnkIcon)) {
            $output .= '
						<span>' . $thnkIcon . '</span>';
        }
        $output .= '
					<p>' . $success_text . '</p>
				</div>';
    }
    $output .= '
			<p>' . $thankyou_desc . '</p>
			<ul>
				<li>
					<a href="' . get_the_permalink($thankyou_goto_page) . '">' . get_the_title($thankyou_goto_page) . '</a>
				</li>
				<li>
					<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'listingpro-plugin') . '</a>
				</li>
			</ul>
		</div>
	</div>';

    return $output;
}
add_shortcode('listingpro_thankyou', 'listingpro_shortcode_thankyou');

/*------------------------------------------------------*/
/* Listings Tabs
/*------------------------------------------------------*/
vc_map(array(
    "name"                      => __("LP Tabs", "js_composer"),
    "base"                      => 'listing_tabs',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Options", "js_composer"),
            "param_name"  => "listing_multi_options",
            'value' => array(
                esc_html__('By Category', 'js_composer') => 'cat_view',
                esc_html__('By Location', 'js_composer') => 'location_view',
            ),
            'save_always' => true,
            "description" => "Select number of posts you want to show"
        ),
        array(
            "type"            => "dropdown_multi",
            "class"           => "",
            "heading"         => esc_html__("Select Location", "js_composer"),
            "param_name"      => "listing_loc",
            'value'           => $loc,
            'save_always'     => true,
            "dependency"     => array(
                "element"     => "listing_multi_options",
                "value"     => array("location_view")
            ),
            "description" => ""
        ),
        array(
            "type"        => "dropdown_multi",
            "class"       => "",
            "heading"     => esc_html__("Select Category", "js_composer"),
            "param_name"  => "listing_cat",
            'value'       => $cats,
            "dependency"     => array(
                "element"     => "listing_multi_options",
                "value"     => array("cat_view")
            ),
            'save_always' => true,
            "description" => ""
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Listing per page", "js_composer"),
            "param_name"  => "listing_per_page",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Layout", "js_composer"),
            "param_name"  => "listing_layout",
            'value' => array(
                esc_html__('List View', 'js_composer') => 'list_view',
                esc_html__('Grid View', 'js_composer') => 'grid_view',
            ),
            'save_always' => true,
            "description" => "Select lists layout"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_grid_style",
            'value' => array(
                esc_html__('Grid Style 1', 'js_composer') => 'grid_view1',
                esc_html__('Grid Style 2', 'js_composer') => 'grid_view2',
                esc_html__('Grid Style 3', 'js_composer') => 'grid_view3',
                esc_html__('Grid Style 4', 'js_composer') => 'grid_view4',
                esc_html__('Grid Style 5', 'js_composer') => 'grid_view5',
                esc_html__('Grid Style 6', 'js_composer') => 'grid_view6',
                esc_html__('Grid Classic Style', 'js_composer') => 'grid_view_classic',

            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "grid_view"
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_list_style",
            'value' => array(
                esc_html__('List Style 1', 'js_composer') => 'listing_views_1',
                esc_html__('List Style 2', 'js_composer') => 'list_view_v2',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "list_view"
            ),
            'save_always' => true,
        ),
    ),
));
function listingpro_shortcode_listing_tabs($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_multi_options'       => 'cat_view',
        'listing_loc'               => '',
        'listing_cat'               => '',
        'listing_per_page'           => '3',
        'listing_layout'           => 'grid_view',
        'listing_grid_style'   => 'grid_view1',
        'listing_list_style'   => 'listing_views_1',
    ), $atts));

    $output =   listing_elements_loop_cb('listing_tabs', $atts);

    return $output;
}
add_shortcode('listing_tabs', 'listingpro_shortcode_listing_tabs');

/*------------------------------------------------------*/
/* Listings
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => __("LP Entries", "js_composer"),
    "base"                      => 'listing_entries',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Posts per page", "js_composer"),
            "param_name"  => "number_posts",
            'value' => array(
                esc_html__('3 Posts', 'js_composer') => '3',
                esc_html__('6 Posts', 'js_composer') => '6',
                esc_html__('9 Posts', 'js_composer') => '9',
                esc_html__('12 Posts', 'js_composer') => '12',
                esc_html__('15 Posts', 'js_composer') => '15',
            ),
            'save_always' => true,
            "description" => "Select number of posts you want to show"
        ),
        //new code 2.6.15
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listings Order By", "js_composer"),
            "param_name"  => "listing_orders",
            'value' => array(
                esc_html__('Date', 'js_composer') => 'listing_order_date',
                esc_html__('Random', 'js_composer') => 'listing_order_ran',
            ),
            'save_always' => true,
            "description" => "Select listing order that you want to show"
        ),
        //end new code 2.6.15
    ),
));
function listingpro_shortcode_listing_entries($atts, $content = null)
{
    extract(shortcode_atts(array(
        'number_posts'   => '3',
        'listing_orders' => 'listing_order_date'
    ), $atts));

    $output = null;
    $type = 'listing';

    //new code 2.6.15
    $order_listings = '';
    $order_listings = '';

    if ($listing_orders == 'listing_order_ran') {
        $order_listings = 'rand';
    } else if ($listing_orders == 'listing_order_date') {
        $order_listings = 'date';
    }

    $args = array(
        'post_type' => $type,
        'post_status' => 'publish',
        'posts_per_page' => $number_posts,
        'orderby' => $order_listings,
    );
    //end new code 2.6.15

    $listingcurrency = '';
    $listingprice = '';
    $listing_query = null;
    $listing_query = new WP_Query($args);

    global $listingpro_options;
    $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
    $img_url    =     $listingpro_options['lp_def_featured_image']['url'];
    if ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
        ob_start();
        if ($listing_query->have_posts()) {
            $listing_entries_counter    =   1;
            while ($listing_query->have_posts()) : $listing_query->the_post();
                if ($listing_entries_counter == 1) {
                    echo '<div class="app-view2-first-recent">';
                    get_template_part('mobile/listing-loop-app-view-adds');
                    echo '</div>';
                } else {
                    get_template_part('mobile/listing-loop-app-view-new');
                }
                $listing_entries_counter++;
            endwhile;
        } else {
            echo 'no listings found';
        }
        $output .= ob_get_contents();
        ob_end_clean();
        ob_flush();
    } else {
        $post_count = 1;
        $output .= '
	<div class="listing-second-view paid-listing lp-section-content-container lp-list-page-grid">
		<div class="listing-post">
			<div class="row">';
        if ($listing_query->have_posts()) {
            while ($listing_query->have_posts()) : $listing_query->the_post();
                $phone = listing_get_metabox('phone');
                $website = listing_get_metabox('website');
                $email = listing_get_metabox('email');
                $latitude = listing_get_metabox('latitude');
                $longitude = listing_get_metabox('longitude');
                $gAddress = listing_get_metabox('gAddress');
                $priceRange = listing_get_metabox('price_status');
                $listingpTo = listing_get_metabox('list_price_to');
                $listingprice = listing_get_metabox('list_price');
                $isfavouriteicon = listingpro_is_favourite_grids(get_the_ID(), $onlyicon = true);
                $isfavouritetext = listingpro_is_favourite_grids(get_the_ID(), $onlyicon = false);
                $claimed_section = listing_get_metabox('claimed_section');
                $rating = get_post_meta(get_the_ID(), 'listing_rate', true);
                $rate = $rating;

                $output .= '
						<div class="col-md-4 col-sm-4 col-xs-12">
							<article>
								<figure>';
                if (has_post_thumbnail()) {
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');
                    if (!empty($image[0])) {
                        $output .= '
												<a href="' . get_the_permalink() . '" >
													<img src="' . $image[0] . '" alt="image" />
												</a>';
                    } else {
                        $output .= '
												<a href="' . get_the_permalink() . '" >
													<img src="' . esc_html__('https://placehold.co/372x240', 'listingpro-plugin') . '" alt="image">
												</a>';
                    }
                } else {
                    $output .= '
										<a href="' . get_the_permalink() . '" >
											<img src="' . $img_url . '" alt="image">
										</a>';
                }
                $output .= '
									<figcaption>';
                if (!empty($listingprice)) {
                    $output .= '
											<div class="listing-price">';
                    $output .= esc_html($listingprice);
                    if (!empty($listingpTo)) {
                        $output .= ' - ';
                        $output .= esc_html($listingpTo);
                    }
                    $output .= '
											</div>';
                }
                $output .= '
										<div class="bottom-area">
											<div class="listing-cats">';
                $cats = get_the_terms(get_the_ID(), 'listing-category');
                if (!empty($cats)) {
                    foreach ($cats as $cat) {
                        $term_link = get_term_link($cat);
                        $output .= '
														<a href="' . $term_link . '">
															' . $cat->name . '
														</a>';
                    }
                }
                $output .= '
											</div>';
                if (!empty($rate)) {
                    $output .= '
												<span class="rate">' . $rate . '<sup>/5</sup></span>';
                }
                $output .= '
											<h4>
												<a href="' . get_the_permalink() . '">
													' . mb_substr(get_the_title(), 0, 40) . '
												</a>
											</h4>';
                if (!empty($gAddress)) {
                    $output .= '
												<div class="listing-location">
													<p>' . $gAddress . '</p>
												</div>';
                }
                $output .= '
										</div>
									</figcaption>
								</figure>
							</article>
						</div>';
                if ($post_count == 3) {
                    $output .= '<div class="clearfix"></div>';
                    $post_count = 1;
                } else {
                    $post_count++;
                }
            endwhile;
        }
        $output .= '
			</div>
		</div>
	</div>';
    }



    return $output;
}
add_shortcode('listing_entries', 'listingpro_shortcode_listing_entries');


// End Harry Elements ====================================================================================== //
/*------------------------------------------------------*/
/* CLIENT TESTIMONIALS
/*------------------------------------------------------*/
vc_map(array(
    "name"                      => __("LP Client Testimonial", "js_composer"),
    "base"                      => 'listingpro_testimonial',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(

            'type'        => 'dropdown',
            'heading'     => __('Testimonial Style', 'js_composer'),
            'param_name'  => 'style',
            'description' => __('Choose your testimonial style', 'js_composer'),
            'default'      => 'default',
            'value'       => array(
                __("Default, on a white background color", "js_composer") => "default",
                __("Inverted, on a dark background color", "js_composer") => "inverted"
            )
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => __("Client Name", "js_composer"),
            "param_name"    => "name",
            "value"            => ""
        ),
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Client Avatar", "js_composer"),
            "param_name"  => "avatar",
            "value"       => "",
            "description" => "Client image, the size should be smaller than 200 x 200px"
        ),
        array(
            'type'        => 'textarea',
            'heading'     => __('Testimonial Content', 'js_composer'),
            'param_name'  => 'testimonial_content',
            'value'       => ''
        ),
        array(

            'type'        => 'textfield',
            'heading'     => __('Designation', 'js_composer'),
            'param_name'  => 'designation',
            'description' => __('Add designation', 'js_composer'),
            'default'      => 'Manager',
            'value'       => '',
        ),
    ),
));
function listingpro_shortcode_testimonial($atts, $content = null)
{
    extract(shortcode_atts(array(
        'style'               => '',
        'name'                => '',
        'avatar'              => '',
        'testimonial_content' => '',
        'designation'           => '',
    ), $atts));

    $output = null;
    $style_class = null;

    if ($style == 'inverted') $style_class = ' inverted';
    if (is_array($avatar)) {
        $avatar =   $avatar['id'];
    }

    $output .= '
	<div class="testimonial-box testimonial' . $style_class . '">';
    $output .= '<div class="testimonial-image">
									<img src="' . wp_get_attachment_url($avatar) . '" alt="image">
				</div>';
    $output .= '<div class="testimonial-msg triangle-isosceles top">
									<div class="testimonial-tit"> 
										<h3>' . esc_attr($name) . '</h3>
										<div class="testimonial-rating">';
    $output .= esc_attr($designation);


    $output .= '						</div>
									</div>
									<div class="testimonial-des">';

    if ($testimonial_content) {
        $output .= '
																	<p>' . wp_kses_post($testimonial_content) . '</p>';
    }
    $output .= '					</div>
					</div>';



    $output .= '</div>';


    return $output;
}
add_shortcode('listingpro_testimonial', 'Listingpro_shortcode_testimonial');

/*------------------------------------------------------*/
/* Locations
/*------------------------------------------------------*/
$categories = get_terms('location', array('hide_empty' => false));
$locations = array();
foreach ($categories as $category) {
    $locations[$category->name] = $category->term_id;
}
vc_map(array(
    "name"                      => __("LP Locations", "js_composer"),
    "base"                      => 'locations',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(

        array(
            'type'        => 'dropdown',
            'heading'     => __('Category Styles', 'js_composer'),
            'param_name'  => 'locstyles',
            'description' => __('Choose your Listing style', 'js_composer'),
            'value'       => array(
                __("Abstracted View", "js_composer") => "loc_abstracted",
                __("Boxed View", "js_composer") => "loc_boxed",
                __("Boxed View 2", "js_composer") => "loc_boxed_2",
                __("Abstracted Classic View", "js_composer") => "loc_abstracted_classic",
                __("Grid View", "js_composer") => "grid_abstracted"
            ),
            'save_always' => true,

        ),
       array(
            'type' => 'checkbox',
            'heading' => __('Select location', 'js_composer'),
            'param_name' => 'location_ids',
            'description' => __('Check the checkbox'),
            'value' => $locations
        ),
        array(
            'type'        => 'dropdown',
            'heading'     => __('Order', 'js_composer'),
            'param_name'  => 'location_order',
            'description' => __('Order of locations', 'js_composer'),
            'default'      => 'default',
            'value'       => array(
                __("ASC", "js_composer") => "ASC",
                __("DESC", "js_composer") => "DESC"
            ),
            'save_always' => true,
        ),

    ),
));
function listingpro_shortcode_locations($atts, $content = null)
{
    extract(shortcode_atts(array(
        'location_ids'   => '',
        'location_order'   => 'ASC',
        'locstyles'    => 'loc_abstracted',
    ), $atts));
    require_once(THEME_PATH . "/include/aq_resizer.php");
    $output = null;
    global $listingpro_options;
    $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
    if ($locstyles == 'loc_boxed_2') {
        $Locations = $location_ids;
        $ucat = array(
            'post_type' => 'listing',
            'hide_empty' => false,
            'order' => $location_order,
            'include' => $Locations
        );
        $allLocations = get_terms('location', $ucat);

        $output .= '<div class="lp-section-content-container"> <div class="lp-locations">';
        $output .= '   <div class="lp-locations-slider"> ';
        foreach ($allLocations as $location) {

            $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing', 'location', $location->term_id);
            $location_image = listing_get_tax_meta($location->term_id, 'location', 'image');
            $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
            if (!empty($location_image_id)) {
                $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_400', true);
                $imgurl = $thumbnail_url[0];
                $imgurl = aq_resize($imgurl, '185', '175', true, true, true);
            } else {
                $imgurl = aq_resize($location_image, '185', '175', true, true, true);
                if (empty($imgurl)) {
                    $imgurl = 'https://placehold.co/185x175';
                }
            }

            $output .= '<div class="col-md-2 col-xs-6">
                        <div class="lp-location-box">
                            <div class="lp-location-thumb">
                                <a href="' . esc_url(get_term_link($location->term_id, 'location')) . '"><img src="' . $imgurl . '" alt="' . esc_attr($location->name) . '"></a>
                            </div>
                            <div class="lp-location-bottom">
                                <a href="' . esc_url(get_term_link($location->term_id, 'location')) . '"><span class="lp-cat-name">' . esc_attr($location->name) . '</span> <span class="lp-cat-list-count">' . esc_attr($totalListinginLoc) . ' ' . esc_html__('Listings', 'listingpro-plugin') . '</span></a>
                            </div>
                        </div>
                    </div>';
        }

        $output .= '   </div>';
        $output .= '</div></div>';
    } else {
        if (($listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2') && wp_is_mobile()) {


            $app_view2_location_class   =   '';
            if ($listing_mobile_view == 'app_view2') {
                $app_view2_location_class   =   'app-view2-location-container';
            }

            $output .= '<div class="lp-section-content-container lp-location-slider clearfix ' . $app_view2_location_class . '">';

            $Locations = $location_ids;
            $ucat = array(
                'post_type' => 'listing',
                'hide_empty' => false,
                'order' => $location_order,
                'include' => $Locations
            );
            $allLocations = get_terms('location', $ucat);


            foreach ($allLocations as $location) {
                $location_image = listing_get_tax_meta($location->term_id, 'location', 'image');

                $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing', 'location', $location->term_id);

                $gridStyle = 'col-md-3 col-sm-3 col-xs-12 cities-app-view';

                $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                $image_alt = "";
                if (!empty($location_image_id)) {
                    $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_400', true);
                    $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                    $imgurl = $thumbnail_url[0];
                } else {
                    $imgurl = aq_resize($location_image, '270', '400', true, true, true);
                    if (empty($imgurl)) {
                        $imgurl = 'https://placehold.co/270x400';
                    }
                }



                $output .= '<div class="' . $gridStyle . '">
										<div class="city-girds lp-border-radius-8 location-girds4">
											<div class="city-thumb">
												<img src="' . $imgurl . '" alt="' . $image_alt . '" />
											</div>
											<div class="city-title text-center">
												<h3 class="lp-h3">
													<a href="' . esc_url(get_term_link($location->term_id, 'location')) . '">' . esc_attr($location->name) . '</a>
												</h3>
												<label class="lp-listing-quantity">' . esc_attr($totalListinginLoc) . ' ' . esc_html__('Listings', 'listingpro-plugin') . '</label>
											</div>
											<a href="' . esc_url(get_term_link($location)) . '" class="overlay-link"></a>
										</div>
									</div>';
            }


            $output .= '</div>';
        } else {
            if ($locstyles == "loc_abstracted") {
                $output .= '<div class="lp-section-content-container row">';

                $Locations = $location_ids;
                $ucat = array(
                    'post_type' => 'listing',
                    'hide_empty' => false,
                    'order' => $location_order,
                    'include' => $Locations
                );
                $allLocations = get_terms('location', $ucat);

                $grid = 0;


                foreach ($allLocations as $location) {
                    $location_image = listing_get_tax_meta($location->term_id, 'location', 'image');

                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing', 'location', $location->term_id);

                    if ($grid == 0) {
                        $gridStyle = 'col-md-6 col-sm-6  col-xs-12 cities-app-view';

                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location570_455', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '570', '455', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/570x455';
                            }
                        }
                    } elseif ($grid == 1) {
                        $gridStyle = 'col-md-6 col-sm-6  col-xs-12 cities-app-view';

                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location570_228', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '570', '228', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/570x228';
                            }
                        }
                    } else {
                        $gridStyle = 'col-md-3 col-sm-3 col-xs-12 cities-app-view';

                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_197', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '270', '197', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/270x197';
                            }
                        }
                    }


                    $output .= '<div class="' . $gridStyle . '">
										<div class="city-girds lp-border-radius-8">
											<div class="city-thumb">
												<img src="' . $imgurl . '" alt="' . $image_alt . '" />
											</div>
											<div class="city-title text-center">
												<h3 class="lp-h3">
													<a href="' . esc_url(get_term_link($location->term_id, 'location')) . '">' . esc_attr($location->name) . '</a>
												</h3>
												<label class="lp-listing-quantity">' . esc_attr($totalListinginLoc) . ' ' . esc_html__('Listings', 'listingpro-plugin') . '</label>
											</div>
											<a href="' . esc_url(get_term_link($location)) . '" class="overlay-link"></a>
										</div>
									</div>';
                    $grid++;
                }
                $output .= '</div>';
            } elseif ($locstyles == "loc_abstracted_classic") {
                wp_enqueue_style('lp-classic-location-grid');
                $output .= '<div class="lp-section-content-container classic-locations row">';

                $Locations = $location_ids;
                $ucat = array(
                    'post_type' => 'listing',
                    'hide_empty' => false,
                    'order' => $location_order,
                    'include' => $Locations
                );
                $allLocations = get_terms('location', $ucat);


                $grid = 0;




                foreach ($allLocations as $location) {
                    $location_image = listing_get_tax_meta($location->term_id, 'location', 'image');


                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing', 'location', $location->term_id);

                    if ($grid == 0) {
                        $gridStyle = 'col-md-4 col-sm-4  col-xs-12 cities-app-view height-normal-grids-first';


                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location339_395', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '339', '395', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/339x395';
                            }
                        }
                    } elseif ($grid == 1) {
                        $gridStyle = 'col-md-4 col-sm-4  col-xs-12 cities-app-view height-normal-grids';


                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location341_228', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '341', '196', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/341x196';
                            }
                        }
                    } elseif ($grid == 2) {
                        $gridStyle = 'col-md-4 col-sm-4 col-xs-12 cities-app-view height-normal-grids';


                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location341_196', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '341', '196', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/341x196';
                            }
                        }
                    } elseif ($grid == 3) {


                        $gridStyle = 'col-md-4 col-sm-4 col-xs-12 cities-app-view height-normal-grids';


                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location414_197', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '414', '196', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/414x196';
                            }
                        }
                    } elseif ($grid == 4) {


                        $gridStyle = 'col-md-2 col-sm-2 col-xs-12 cities-app-view  height-normal-grids-last';


                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location198_200', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '198', '200', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/198x200';
                            }
                        }
                    } else {


                        $gridStyle = 'col-md-2 col-sm-2 col-xs-12 cities-app-view height-normal-grids-last';


                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                        if (!empty($location_image_id)) {
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location198_200', true);
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        } else {
                            $imgurl = aq_resize($location_image, '198', '200', true, true, true);
                            if (empty($imgurl)) {
                                $imgurl = 'https://placehold.co/198x200';
                            }
                        }
                    }
                    if ($totalListinginLoc == 0) {
                        $location_list_text = esc_html__('Listing', 'listingpro-plugin');
                    } else {
                        $location_list_text = esc_html__('Listings', 'listingpro-plugin');
                    }
                    $output .= '<div class="' . $gridStyle . '">
                                        <div class="city-girds lp-border-radius-8">
                                            <div class="city-thumb">
                                                <img src="' . $imgurl . '" alt="' . $image_alt . '" />
                                            </div>
                                            <div class="city-title text-center">
                                                <h3 class="lp-h3">
                                                    <a href="' . esc_url(get_term_link($location->term_id, 'location')) . '">' . esc_attr($location->name) . '</a>
                                                </h3>
                                            </div>
                                            <a href="' . esc_url(get_term_link($location)) . '" class="overlay-link">
											   <label class="lp-listing-quantity"><span>' . esc_attr($totalListinginLoc) . '</span></br>' . $location_list_text . '</label>
											</a>
                                        </div>
                                    </div>';
                    $grid++;
                }




                $output .= '</div>';
            } elseif ($locstyles == "loc_boxed") {
                $output .= '<div class="lp-section-content-container row">';

                $Locations = $location_ids;
                $ucat = array(
                    'post_type' => 'listing',
                    'hide_empty' => false,
                    'order' => $location_order,
                    'include' => $Locations,
                );
                $allLocations = get_terms('location', $ucat);


                foreach ($allLocations as $location) {
                    $location_image = listing_get_tax_meta($location->term_id, 'location', 'image');


                    $gridStyle = 'col-md-3 col-sm-3 col-xs-12 cities-app-view';

                    $image_alt = "";
                    $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                    if (!empty($location_image_id)) {
                        $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_197', true);
                        $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                        $imgurl = $thumbnail_url[0];
                    } else {
                        $imgurl = aq_resize($location_image, '270', '197', true, true, true);
                        if (empty($imgurl)) {
                            $imgurl = 'https://placehold.co/270x197';
                        }
                    }

                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing', 'location', $location->term_id);
                    $output .= '<div class="' . $gridStyle . '">
										<div class="city-girds lp-border-radius-8">
											<div class="city-thumb">
												<img src="' . $imgurl . '" alt="' . $image_alt . '" />
											</div>
											<div class="city-title text-center">
												<h3 class="lp-h3">
													<a href="' . esc_url(get_term_link($location->term_id, 'location')) . '">' . esc_attr($location->name) . '</a>
												</h3>
												<label class="lp-listing-quantity">' . esc_attr($totalListinginLoc) . ' ' . esc_html__('Listings', 'listingpro-plugin') . '</label>
											</div>
											<a href="' . esc_url(get_term_link($location)) . '" class="overlay-link"></a>
										</div>
									</div>';
                }


                $output .= '</div>';
            } else {
                $output .= '<div class="lp-section-content-container row">';

                $Locations = $location_ids;
                $ucat = array(
                    'post_type' => 'listing',
                    'hide_empty' => false,
                    'order' => $location_order,
                    'include' => $Locations
                );
                $allLocations = get_terms('location', $ucat);


                foreach ($allLocations as $location) {
                    $location_image = listing_get_tax_meta($location->term_id, 'location', 'image');
                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing', 'location', $location->term_id);

                    $gridStyle = 'col-md-3 col-sm-3 col-xs-12 cities-app-view';

                    $image_alt = "";
                    $location_image_id = listing_get_tax_meta($location->term_id, 'location', 'image_id');
                    if (!empty($location_image_id)) {
                        $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_400', true);
                        $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                        $imgurl = $thumbnail_url[0];
                    } else {
                        $imgurl = aq_resize($location_image, '270', '400', true, true, true);
                        if (empty($imgurl)) {
                            $imgurl = 'https://placehold.co/270x400';
                        }
                    }


                    $output .= '<div class="' . $gridStyle . '">
										<div class="city-girds lp-border-radius-8 location-girds4">
											<div class="city-thumb">
												<img src="' . $imgurl . '" alt="' . $image_alt . '" />
											</div>
											<div class="city-title text-center">
												<h3 class="lp-h3">
													<a href="' . esc_url(get_term_link($location->term_id, 'location')) . '">' . esc_attr($location->name) . '</a>
												</h3>
												<label class="lp-listing-quantity">' . esc_attr($totalListinginLoc) . ' ' . esc_html__('Listings', 'listingpro-plugin') . '</label>
											</div>
											<a href="' . esc_url(get_term_link($location)) . '" class="overlay-link"></a>
										</div>
									</div>';
                }


                $output .= '</div>';
            }
        }
    }
    return $output;
}
add_shortcode('locations', 'listingpro_shortcode_locations');

/*------------------------------------------------------*/
/* feature box
/*------------------------------------------------------*/
vc_map(array(
    "name"                      => __("LP Feature Box", "js_composer"),
    "base"                      => 'feature_box',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(

        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Feature Image", "js_composer"),
            "param_name"  => "box_style",
            'value' => array(
                __('Style 1', 'js_composer') => 'style1',
                __('Style 2', 'js_composer') => 'style2',
            ),
            'save_always' => true,
            "description" => "Put here feature image, Use Perfect size for better output."
        ),
        array(
            "type" => "textfield",
            "heading" => "Title for Description",
            "param_name" => "style_2_title",
            "value" => "",
            "dependency" => array(
                "element" => "box_style",
                "value" => "style2"
            )
        ),

        array(
            "type" => "textfield",
            "heading" => "Sub Title for Description",
            "param_name" => "style_2_stitle",
            "value" => "",
            "dependency" => array(
                "element" => "box_style",
                "value" => "style2"
            )
        ),

        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Feature Image", "js_composer"),
            "param_name"  => "feature_image",
            "value"       => "",
            "description" => "Put here feature image, Use Perfect size for better output."
        ),

        array(
            "type"        => "textarea",
            "class"       => "",
            "heading"     => __("Description about this element", "js_composer"),
            "param_name"  => "fbox_desc",
            "value"       => "",
            "description" => "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusa ntium dolore mque<br> laud antium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi <br>
											arc hitecto beatae vitae dicta sunt explicabo."
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Button Link (1)", "js_composer"),
            "param_name"  => "botton_link1",
            "value"       => "",
            "description" => ""
        ),

        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Button BG Image (1)", "js_composer"),
            "param_name"  => "botton_image1",
            "value"       => "",
            "description" => "Please Use one either color or Bg image "
        ),


        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Button Link (2)", "js_composer"),
            "param_name"  => "botton_link2",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Button BG Image (2)", "js_composer"),
            "param_name"  => "botton_image2",
            "value"       => "",
            "description" => "Please Use one either color or Bg image "
        ),
    ),
));
function listingpro_shortcode_feature_box($atts, $content = null)
{
    extract(shortcode_atts(array(
        'box_style'   => 'style1',
        'feature_image'   => '',
        'style_2_title'   => '',
        'style_2_stitle'   => '',
        'fbox_desc'   => '',
        'botton_link1'   => '',
        'botton_image1'   => '',
        'botton_link2'   => '',
        'botton_image2'   => '',
    ), $atts));

    $output = null;
    $FimageURL = null;
    $bottonImage1 = null;
    $bottonImage2 = null;

    if ($feature_image) {
        if (is_array($feature_image)) {
            $feature_image  =   $feature_image['id'];
        }
        $imgurl = wp_get_attachment_image_src($feature_image, 'full');
        $FimageURL = '<img src="' . $imgurl[0] . '" alt="image">';
    }
    if ($botton_image1) {
        if (is_array($botton_image1)) {
            $botton_image1  =   $botton_image1['id'];
        }
        $imgurl = wp_get_attachment_image_src($botton_image1, 'full');
        $bottonImage1 = '<img src="' . $imgurl[0] . '" alt="image">';
    }
    if ($botton_image2) {
        if (is_array($botton_image2)) {
            $botton_image2  =   $botton_image2['id'];
        }
        $imgurl = wp_get_attachment_image_src($botton_image2, 'full');
        $bottonImage2 = '<img src="' . $imgurl[0] . '" alt="image">';
    }

    if ($box_style == 'style1') {

        $output .= '<div class="lp-section-content-container row">';
        $output .= '<div class="col-md-12 text-center">
						<div class="nearby-banner">
							' . $FimageURL . '
						</div>
						<div class="nearby-description">
							<p>
								' . $fbox_desc . '

							</p>
						</div>';
        if (!empty($bottonImage1) || !empty($bottonImage2)) {
            $output .= '
							<ul class="nearby-download nearby-download-about nearby-download-top">
								<li>
									<a href="' . $botton_link1 . '">
										' . $bottonImage1 . '
									</a>
								</li>
								<li>
									<a href="' . $botton_link2 . '">
										' . $bottonImage2 . '
									</a>
								</li>
							</ul>';
        }
        $output .= '
					</div>';
        $output .= '</div>';
    } elseif ($box_style == 'style2') {

        $output .= '<div class="lp-section-content-container row">';

        $output .= '	<div class="col-md-6">
						<div class="">
							<Div class="lp-about-section-best-header">
								<h3 class="margin-top-0">' . $style_2_title . '</h3>
								<p>' . $style_2_stitle . '</p>
							</div>
							<div class="lp-about-section-best-description margin-top-45 ">
								<p class="paragraph-small">
								' . $fbox_desc . '
								</p>';
        if (!empty($bottonImage1) || !empty($bottonImage2)) {
            $output .= '
									<ul class="nearby-download nearby-download-about nearby-download-top">
										<li>
											<a href="' . $botton_link1 . '">
												' . $bottonImage1 . '
											</a>
										</li>
										<li>
											<a href="' . $botton_link2 . '">
												' . $bottonImage2 . '
											</a>
										</li>
									</ul>';
        }
        $output .= '
							</div>
						</div><!-- ../section-content-container-->
					</div>
					<div class="col-md-6">
						<div class="">
							' . $FimageURL . '
						</div><!-- ../section-content-container-->
					</div>';
        $output .= '</div>';
    }

    return $output;
}
add_shortcode('feature_box', 'listingpro_shortcode_feature_box');

/*------------------------------------------------------*/
/* Listings with Multi Options
/*------------------------------------------------------*/

$loc = array();
$argsloc = array(
    'order' => 'ASC',
    'hide_empty' => false,
    'fields' => 'id=>name',
);
$loc = get_terms('location', $argsloc);
$loc = array_flip($loc);

$cats = array();
$argscat = array(
    'order' => 'ASC',
    'hide_empty' => false,
    'fields' => 'id=>name',
);
$cats = get_terms('listing-category', $argscat);
$cats = array_flip($cats);
vc_map(array(
    "name"                      => __("LP Listings By (location/category)", "js_composer"),
    "base"                      => 'listing_options',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Options", "js_composer"),
            "param_name"  => "listing_multi_options",
            'value' => array(
                esc_html__('By Category', 'js_composer') => 'cat_view',
                esc_html__('By Location', 'js_composer') => 'location_view',
                esc_html__('By Location and Category', 'js_composer') => 'location_cat_view',
                esc_html__('Recent', 'js_composer') => 'recent_view',
            ),
            'save_always' => true,
            "description" => "Select number of posts you want to show"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Include Children (Category/Location)", "js_composer"),
            "param_name"  => "listing_include",
            'value' => array(
                esc_html__('Include', 'js_composer') => 1,
                esc_html__('Exclude', 'js_composer') => 0,
            ),
            "dependency" => array(
                "element" => "listing_multi_options",
                "value" => array("location_cat_view", "location_view", "cat_view"),
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Relation Between Category And Location", "js_composer"),
            "param_name"  => "listing_opr",
            'value' => array(
                esc_html__('AND', 'js_composer') => 'AND',
                esc_html__('OR', 'js_composer') => 'OR',
            ),
            "dependency" => array(
                "element" => "listing_multi_options",
                "value" => "location_cat_view",
            ),
            'save_always' => true,
        ),
        array(
            "type"          => "checkbox",
            "class"         => "",
            "heading"       => esc_html__("Select Location", "js_composer"),
            "param_name"    => "listing_loc",
            'value'         => $loc,
            'save_always'   => true,
            "dependency"    => array(
                "element"   => "listing_multi_options",
                "value"     => array("location_view", "location_cat_view")
            ),
            "description" => ""
        ),
        array(
            "type"        => "checkbox",
            "class"       => "",
            "heading"     => esc_html__("Select Category", "js_composer"),
            "param_name"  => "listing_cat",
            'value'       => $cats,
            "dependency"    => array(
                "element"   => "listing_multi_options",
                "value"     => array("cat_view", "location_cat_view")
            ),
            'save_always' => true,
            "description" => ""
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Listing per page", "js_composer"),
            "param_name"  => "listing_per_page",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Layout", "js_composer"),
            "param_name"  => "listing_layout",
            'value' => array(
                esc_html__('List View', 'js_composer') => 'list_view',
                esc_html__('Grid View', 'js_composer') => 'grid_view',
            ),
            'save_always' => true,
            "description" => "Select lists layout"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_grid_style",
            'value' => array(
                esc_html__('Grid Style 1', 'js_composer') => 'grid_view1',
                esc_html__('Grid Style 2', 'js_composer') => 'grid_view2',
                esc_html__('Grid Style 3', 'js_composer') => 'grid_view3',
                esc_html__('Grid Style 4', 'js_composer') => 'grid_view4',
                esc_html__('Grid Style 5', 'js_composer') => 'grid_view5',
                esc_html__('Grid Classic Style', 'js_composer') => 'grid_view_classic',
                esc_html__('Grid Style 6', 'js_composer') => 'grid_view6',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "grid_view"
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_list_style",
            'value' => array(
                esc_html__('List Style 1', 'js_composer') => 'listing_views_1',
                esc_html__('List Style 2', 'js_composer') => 'list_view_v2',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "list_view"
            ),
            'save_always' => true,
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Text", "js_composer"),
            "param_name" => "grid3_button_text",
            "description" => __("Button for grid style 3, Leave empty to hide.", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Link", "js_composer"),
            "param_name" => "grid3_button_link",
            "description" => __("Button link for grid style 3", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
    ),
));
function listingpro_shortcode_listing_options($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_multi_options'     => 'cat_view',
        'listing_include'           => '1',
        'listing_opr'               => 'OR',
        'listing_loc'               => '',
        'listing_cat'               => '',
        'listing_per_page'          => '3',
        'listing_layout'        => 'grid_view',
        'listing_grid_style'   => 'grid_view1',
        'listing_list_style'   => 'listing_views_1',
        'grid3_button_text'   => '',
        'grid3_button_link'   => '',
    ), $atts));
    $output =   listing_elements_loop_cb('listing_options', $atts);
    return $output;
}
add_shortcode('listing_options', 'listingpro_shortcode_listing_options');

/*------------------------------------------------------*/
/* Listings
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => __("LP Listing Posts", "js_composer"),
    "base"                      => 'listing_grids',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(

        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Layout", "js_composer"),
            "param_name"  => "listing_layout",
            'value' => array(
                esc_html__('List View', 'js_composer') => 'list_view',
                esc_html__('Grid View', 'js_composer') => 'grid_view',
            ),
            'save_always' => true,
            "description" => "Select lists layout"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_grid_style",
            'value' => array(
                esc_html__('Grid Style 1', 'js_composer') => 'grid_view1',
                esc_html__('Grid Style 2', 'js_composer') => 'grid_view2',
                esc_html__('Grid Style 3', 'js_composer') => 'grid_view3',
                esc_html__('Grid Style 4', 'js_composer') => 'grid_view4',
                esc_html__('Grid Style 5', 'js_composer') => 'grid_view5',
                esc_html__('Grid Style 6', 'js_composer') => 'grid_view6',
                esc_html__('Grid Classic Style', 'js_composer') => 'grid_view_classic',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "grid_view"
            ),
            'save_always' => true,
            "description" => "Select number of posts you want to show"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_list_style",
            'value' => array(
                esc_html__('List Style 1', 'js_composer') => 'listing_views_1',
                esc_html__('List Style 2', 'js_composer') => 'list_view_v2',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "list_view"
            ),
            'save_always' => true,
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Text", "js_composer"),
            "param_name" => "grid3_button_text",
            "description" => __("Button for grid style 3, Leave empty to hide.", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Link", "js_composer"),
            "param_name" => "grid3_button_link",
            "description" => __("Button link for grid style 3", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Posts per page", "js_composer"),
            "param_name"  => "number_posts",
            'value' => array(
                esc_html__('3 Posts', 'js_composer') => '3',
                esc_html__('6 Posts', 'js_composer') => '6',
                esc_html__('9 Posts', 'js_composer') => '9',
                esc_html__('12 Posts', 'js_composer') => '12',
                esc_html__('15 Posts', 'js_composer') => '15',
            ),
            'save_always' => true,
            "description" => "Select number of posts you want to show"
        ),
    ),
));
function listingpro_shortcode_listing_grids($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_grid_style'   => 'grid_view1',
        'listing_list_style'   => 'listing_views_1',
        'number_posts'   => '3',
        'listing_layout'   => 'grid_view',
        'grid3_button_text'   => '',
        'grid3_button_link'   => '',
    ), $atts));

    $output =  listing_elements_loop_cb('listing_grids', $atts);
    return $output;
}
add_shortcode('listing_grids', 'listingpro_shortcode_listing_grids');

/*------------------------------------------------------*/
/* Listings with coupons
/*------------------------------------------------------*/
vc_map(array(
    "name"                      => __("LP Listings With Coupons", "js_composer"),
    "base"                      => 'listing_grids_with_coupons',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Layout", "js_composer"),
            "param_name"  => "listing_layout",
            'value' => array(
                esc_html__('List View', 'js_composer') => 'list_view',
                esc_html__('Grid View', 'js_composer') => 'grid_view',
            ),
            'save_always' => true,
            "description" => "Select lists layout"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_grid_style",
            'value' => array(
                esc_html__('Grid Style 1', 'js_composer') => 'grid_view1',
                esc_html__('Grid Style 2', 'js_composer') => 'grid_view2',
                esc_html__('Grid Style 3', 'js_composer') => 'grid_view3',
                esc_html__('Grid Style 4', 'js_composer') => 'grid_view4',
                esc_html__('Grid Style 5', 'js_composer') => 'grid_view5',
                esc_html__('Grid Classic Style', 'js_composer') => 'grid_view_classic',

            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "grid_view"
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_list_style",
            'value' => array(
                esc_html__('List Style 1', 'js_composer') => 'listing_views_1',
                esc_html__('List Style 2', 'js_composer') => 'list_view_v2',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "list_view"
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Posts per page", "js_composer"),
            "param_name"  => "number_posts",
            'value' => array(
                esc_html__('3 Posts', 'js_composer') => '3',
                esc_html__('6 Posts', 'js_composer') => '6',
                esc_html__('9 Posts', 'js_composer') => '9',
                esc_html__('12 Posts', 'js_composer') => '12',
                esc_html__('15 Posts', 'js_composer') => '15',
            ),
            'save_always' => true,
            "description" => "Select number of posts you want to show"
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Text", "js_composer"),
            "param_name" => "grid3_button_text",
            "description" => __("Button for grid style 3, Leave empty to hide.", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Link", "js_composer"),
            "param_name" => "grid3_button_link",
            "description" => __("Button link for grid style 3", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
    ),
));
function listingpro_shortcode_listing_grids_with_coupons($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_grid_style'   => 'grid_view1',
        'listing_list_style'   => 'listing_views_1',
        'number_posts'   => '3',
        'listing_layout'   => 'grid_view',
        'grid3_button_text'   => '',
        'grid3_button_link'   => '',
        'number_posts'   => '',
    ), $atts));

    $output =  listing_elements_loop_cb('listing_grids_with_coupons', $atts);
    return $output;
}
add_shortcode('listing_grids_with_coupons', 'listingpro_shortcode_listing_grids_with_coupons');


vc_map(array(
    "name"                      => __("LP Listings by ID", "js_composer"),
    "base"                      => 'listing_grids_by_id',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Layout", "js_composer"),
            "param_name"  => "listing_layout",
            'value' => array(
                esc_html__('List View', 'js_composer') => 'list_view',
                esc_html__('Grid View', 'js_composer') => 'grid_view',
            ),
            'save_always' => true,
            "description" => "Select lists layout"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_grid_style",
            'value' => array(
                esc_html__('Grid Style 1', 'js_composer') => 'grid_view1',
                esc_html__('Grid Style 2', 'js_composer') => 'grid_view2',
                esc_html__('Grid Style 3', 'js_composer') => 'grid_view3',
                esc_html__('Grid Style 4', 'js_composer') => 'grid_view4',
                esc_html__('Grid Style 5', 'js_composer') => 'grid_view5',
                esc_html__('Grid Style 6', 'js_composer') => 'grid_view6',
                esc_html__('Grid Classic Style', 'js_composer') => 'grid_view_classic',

            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "grid_view"
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_list_style",
            'value' => array(
                esc_html__('List Style 1', 'js_composer') => 'listing_views_1',
                esc_html__('List Style 2', 'js_composer') => 'list_view_v2',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "list_view"
            ),
            'save_always' => true,
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("IDs", "js_composer"),
            "param_name" => "posts_ids",
            "description" => __("comma separated ids", "js_composer"),
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Text", "js_composer"),
            "param_name" => "grid3_button_text",
            "description" => __("Button for grid style 3, Leave empty to hide.", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Link", "js_composer"),
            "param_name" => "grid3_button_link",
            "description" => __("Button link for grid style 3", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
    ),
));
function listingpro_shortcode_listing_grids_by_id($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_grid_style'   => 'grid_view1',
        'listing_list_style'   => 'listing_views_1',
        'number_posts'   => '3',
        'listing_layout'   => 'grid_view',
        'grid3_button_text'   => '',
        'grid3_button_link'   => '',
        'posts_ids'   => '',
    ), $atts));

    $output =  listing_elements_loop_cb('listing_grids_by_id', $atts);
    return $output;
}
add_shortcode('listing_grids_by_id', 'listingpro_shortcode_listing_grids_by_id');

/*------------------------------------------------------*/
/* claimed Listings
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => __("LP Claimed Listings", "js_composer"),
    "base"                      => 'claimed_listings_grids',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Posts per page", "js_composer"),
            "param_name"  => "number_posts",
            'value' => array(
                esc_html__('3 Posts', 'js_composer') => '3',
                esc_html__('6 Posts', 'js_composer') => '6',
                esc_html__('9 Posts', 'js_composer') => '9',
                esc_html__('12 Posts', 'js_composer') => '12',
                esc_html__('15 Posts', 'js_composer') => '15',
            ),
            'save_always' => true,
            "description" => "Select number of posts you want to show"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Layout", "js_composer"),
            "param_name"  => "listing_layout",
            'value' => array(
                esc_html__('List View', 'js_composer') => 'list_view',
                esc_html__('Grid View', 'js_composer') => 'grid_view',
            ),
            'save_always' => true,
            "description" => "Select lists layout"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_grid_style",
            'value' => array(
                esc_html__('Grid Style 1', 'js_composer') => 'grid_view1',
                esc_html__('Grid Style 2', 'js_composer') => 'grid_view2',
                esc_html__('Grid Style 3', 'js_composer') => 'grid_view3',
                esc_html__('Grid Style 4', 'js_composer') => 'grid_view4',
                esc_html__('Grid Style 5', 'js_composer') => 'grid_view5',
                esc_html__('Grid Classic Style', 'js_composer') => 'grid_view_classic',

            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "grid_view"
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_list_style",
            'value' => array(
                esc_html__('List Style 1', 'js_composer') => 'listing_views_1',
                esc_html__('List Style 2', 'js_composer') => 'list_view_v2',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "list_view"
            ),
            'save_always' => true,
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Text", "js_composer"),
            "param_name" => "grid3_button_text",
            "description" => __("Button for grid style 3, Leave empty to hide.", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Button Link", "js_composer"),
            "param_name" => "grid3_button_link",
            "description" => __("Button link for grid style 3", "js_composer"),
            "dependency" => array(
                "element" => "listing_grid_style",
                "value" => "grid_view3"
            ),
        ),
    ),
));
function listingpro_shortcode_claimed_listings_grids($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_grid_style'   => 'grid_view1',
        'listing_list_style'   => 'listing_views_1',
        'number_posts'   => '3',
        'listing_layout'   => 'grid_view',
        'grid3_button_text'   => '',
        'grid3_button_link'   => '',
    ), $atts));

    $output =  listing_elements_loop_cb('claimed_listings_grids', $atts);
    return $output;
}
add_shortcode('claimed_listings_grids', 'listingpro_shortcode_claimed_listings_grids');

/*------------------------------------------------------*/
/* Image gallery
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => __("LP Image Gallery", "js_composer"),
    "base"                      => 'image_gallery',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            'type' => 'dropdown',
            'heading' => __('Image gallery styles', 'js_composer'),
            'param_name' => 'imagegallerystyle',
            'description' => __('Choose your Gallery style', 'js_composer'),
            'value' => array(
                __("Old Style", "js_composer") => "old_style",
                __("New Style", "js_composer") => "new_style"
            ),
            'save_always' => true
        ),
        array(
            "type"        => "attach_images",
            "class"       => "",
            "heading"     => __("Image for gallery", "js_composer"),
            "param_name"  => "gallery_images",
            "value"       => "",
            "description" => "Upload image for gallery."
        ),
    ),
));

function listingpro_shortcode_gallery($atts, $content = null)
{
    extract(shortcode_atts(array(
        'imagegallerystyle' => '',
        'gallery_images' => ''
    ), $atts));

    $output = null;
    $imgIDs = null;
    $screenImage = null;
    $IDs = $gallery_images;


    if (is_array($IDs)) {
        $imgIDs =   array();
        foreach ($IDs as $img_arr) {
            $imgIDs[]   =   $img_arr['id'];
        }
    } else {
        $imgIDs = explode(',', $IDs);
    }

    $count = 1;
    if ($imagegallerystyle == "old_style") {
        $output .= '<div class="about-gallery  lp-section-content-container popup-gallery clearfix">';

        if (!empty($IDs)) {

            foreach ($imgIDs as $imgID) {

                if ($count == 1) {
                    $imgurl = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                    $imgFull = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';

                    $output .= '	<div class="col-md-5 col-sm-5 about-gallery-box">
							<a href="' . $imgFull[0] . '" class="image-popup">
								' . $screenImage . '
							</a>
						</div>';
                } elseif ($count == 2) {
                    $imgurl = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb2');
                    $imgFull = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';

                    $output .= '	<div class="col-md-4 col-sm-4 about-gallery-box">
							<a href="' . $imgFull[0] . '" class="image-popup">
								' . $screenImage . '
							</a>
						</div>';
                } elseif ($count == 3) {
                    $imgurl = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb3');
                    $imgFull = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';

                    $output .= '	<div class="col-md-3 col-sm-3 about-gallery-box">
							<a href="' . $imgFull[0] . '" class="image-popup">
								' . $screenImage . '
							</a>
						</div>';
                } elseif ($count == 4) {
                    $imgurl = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb4');
                    $imgFull = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';
                    $output .= '	<div class="col-md-7 col-sm-7 about-gallery-box">
							<a href="' . $imgFull[0] . '" class="image-popup">
								' . $screenImage . '
							</a>
						</div>';
                } else {
                    $imgurl = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb2');
                    $imgFull = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';
                    $output .= '    <div class="col-md-4 col-sm-4 about-gallery-box">                            <a href="' . $imgFull[0] . '" class="image-popup">                                ' . $screenImage . '                            </a>                        </div>';
                }

                $count++;
            }
        }

        $output .= '</div>';
    } else {
        $output .= '<div class="about-gallery  popup-gallery clearfix about-gallery-style2">';
        if (!empty($IDs)) {
            foreach ($imgIDs as $imgID) {
                if ($count == 1) {
                    $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                    $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';
                    $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                } elseif ($count == 2) {
                    $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                    $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';
                    $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                } elseif ($count == 3) {
                    $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                    $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';
                    $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                } elseif ($count == 4) {
                    $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                    $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';
                    $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                } else {
                    $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                    $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                    $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';
                    $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                }

                $count++;
            }
        }

        $output .= '</div>';
    }

    return $output;
}
add_shortcode('image_gallery', 'listingpro_shortcode_gallery');

/*------------------------------------------------------*/
/* Video testimonials
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => __("LP Video Testimonials", "js_composer"),
    "base"                      => 'video_testimonials',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(


        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Video preview Image", "js_composer"),
            "param_name"  => "screen_image",
            "value"       => "",
            "description" => "Please upload preview Image Size(580x386)"
        ),

        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Video URL", "js_composer"),
            "param_name"  => "video_url",
            "value"       => "",
            "description" => "You can use direct URL from youtube, vimeo, dailymotion or any other wordpress supported website"
        ),

        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Testimonial Title", "js_composer"),
            "param_name"  => "testi_title",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Author name", "js_composer"),
            "param_name"  => "author_name",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Author Company", "js_composer"),
            "param_name"  => "author_company",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Author Avatar", "js_composer"),
            "param_name"  => "author_image",
            "value"       => "",
            "description" => "Please upload preview Image Size(60x60)"
        ),
        array(
            "type"        => "textarea",
            "class"       => "",
            "heading"     => __("Testimonial Content", "js_composer"),
            "param_name"  => "testi_content",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "style_testimonial",
            'value' => array(
                esc_html__('Style 1', 'js_composer') => 'classic',
                esc_html__('Style 2', 'js_composer') => 'classic_new',
            ),
            'save_always' => true,
        )
    ),
));
function listingpro_shortcode_video_box($atts, $content = null)
{
    extract(shortcode_atts(array(
        'screen_image'   => '',
        'video_url'   => '',
        'testi_title'   => '',
        'author_name'   => '',
        'author_company'   => '',
        'author_image'   => '',
        'testi_content'   => '',
        'style_testimonial'   => '',

    ), $atts));

    $output = null;
    $screenImage = null;
    $authorImage = null;


    if ($screen_image) {
        if (is_array($screen_image)) {
            $screen_image   =   $screen_image['id'];
        }
        $imgurl = wp_get_attachment_image_src($screen_image, 'full');
        if (isset($imgurl[0])) {
            $screenImage = '<img src="' . $imgurl[0] . '" alt="image">';
        }
    }
    if ($author_image) {
        if (is_array($author_image)) {
            $author_image   =   $author_image['id'];
        }
        $imgurl = wp_get_attachment_image_src($author_image, 'listingpro-author-thumb');
        if (isset($imgurl[0])) {
            $authorImage = '<img src="' . $imgurl[0] . '" alt="image">';
        }
    }


    if ($style_testimonial == 'classic_new') {
        $output .= '<div class="testimonial classic-testimonial lp-section-content-container row">';

        $output .= '<div class="col-md-5">
						<div class="video-thumb">
								' . $screenImage . '
							<a href="' . $video_url . '" class="overlay-video-thumb popup-vimeo">
								<i class="fa fa-play-circle-o"></i>
							</a>
						</div><!-- ../video-thumb -->
					</div>';

        $output .= '<div class="col-md-7">
						<div class="testimonial-inner-box">
							<h3 class="margin-top-0">' . $testi_title . '</h3>
							<div class="testimonial-description lp-border-radius-5">
								<p>' . esc_attr($testi_content) . '	</p>
							</div><!-- ../testimonial-description -->
							<div class="testimonial-user-info user-info">
								<div class="testimonial-user-thumb user-thumb">
									' . $authorImage . '
								</div>
								<div class="testimonial-user-txt user-text">
									<label class="testimonial-user-name user-name">' . $author_name . '</label><br>
									<label class="testimonial-user-position user-position">' . $author_company . '</label>
								</div>
							</div><!-- ../testimonial-user-info -->
						</div><!-- ../testimonial-inner-box -->
					</div>';

        $output .= '</div>';
    } else {
        $output .= '<div class="testimonial lp-section-content-container row">';

        $output .= '<div class="col-md-6">
						<div class="video-thumb">
								' . $screenImage . '
							<a href="' . $video_url . '" class="overlay-video-thumb popup-vimeo">
								<i class="fa fa-play-circle-o"></i>
							</a>
						</div><!-- ../video-thumb -->
					</div>';

        $output .= '<div class="col-md-6">
						<div class="testimonial-inner-box">
							<h3 class="margin-top-0">' . $testi_title . '</h3>
							<div class="testimonial-description lp-border-radius-5">
								<p>' . esc_attr($testi_content) . '	</p>
							</div><!-- ../testimonial-description -->
							<div class="testimonial-user-info user-info">
								<div class="testimonial-user-thumb user-thumb">
									' . $authorImage . '
								</div>
								<div class="testimonial-user-txt user-text">
									<label class="testimonial-user-name user-name">' . $author_name . '</label><br>
									<label class="testimonial-user-position user-position">' . $author_company . '</label>
								</div>
							</div><!-- ../testimonial-user-info -->
						</div><!-- ../testimonial-inner-box -->
					</div>';

        $output .= '</div>';
    }


    return $output;
}
add_shortcode('video_testimonials', 'listingpro_shortcode_video_box');

/*------------------------------------------------------*/
/* Listings
/*------------------------------------------------------*/
function blog_category_field($settings, $value)
{
    $categories = get_terms('category');
    //$dependency = vc_generate_dependencies_attributes($settings);
    $dependency = (function_exists('vc_generate_dependencies_attributes'))
        ? vc_generate_dependencies_attributes($settings) : '';
    $data = '<select name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '">';

    $selected = '';
    foreach ($categories as $category) {
        $selected = '';
        if ($value !== '' && $category->term_id == $value) {
            $selected = ' selected="selected"';
        }
        $data .= '<option class="' . $category->slug . '" value="' . $category->term_id . '"' . $selected . '>' . $category->name . ' (' . $category->count . ' Posts)</option>';
    }
    $selectedd = '';
    if (empty($value)) {
        $selectedd = ' selected="selected"';
    }
    $data .= '<option class="" value="" ' . $selectedd . '>' . esc_html__('All Categories', 'listingpro-plugin') . '</option>';
    $data .= '</select>';
    return $data;
}
vc_add_shortcode_param('blog_category', 'blog_category_field');

vc_map(array(
    "name"                      => __("LP Blog Grids", "js_composer"),
    "base"                      => 'blog_grids',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Select Blog Element Style", "js_composer"),
            "param_name"  => "blog_style",
            'value' => array(
                __('Blog Grid ', 'js_composer') => 'style1',
                __('Blog Masonery ', 'js_composer') => 'style2',
                __('Blog Circle ', 'js_composer') => 'style3',
                __('Classic New', 'js_composer') => 'style4',

            ),
            'save_always' => true,
            "description" => "Select Blog Style"
        ),
        array(
            "type" => "blog_category",
            "holder" => "div",
            "heading" => "Category",
            "param_name" => "category",
            "class"       => "hide_in_vc_editor",
            "admin_label"     => true,
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("No. of posts", "js_composer"),
            "param_name"  => "blog_per_page",
            "value"       => "3",
            "description" => ""
        ),
    ),
));
if (!function_exists('listingpro_shortcode_blog_grids')) {
    function listingpro_shortcode_blog_grids($atts, $content = null)
    {
        extract(shortcode_atts(array(

            'blog_style'   => 'style1',
            'category'   => '',
            'blog_per_page' => '3',
        ), $atts));

        $output = null;
        $post_count = 1;
        if ($blog_style == 'style1') {
            $output .= '<div class="lp-section-content-container lp-blog-grid-container row">';

            $type = 'post';
            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => "$blog_per_page",
                'cat' => $category,
            );

            $my_query = null;
            $my_query = new WP_Query($args);
            if ($my_query->have_posts()) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    $imgurl = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');
                    if ($imgurl) {
                        $thumbnail = $imgurl[0];
                    } else {
                        $thumbnail = 'https://placehold.co/372x240';
                    }
                    $categories = get_the_category(get_the_ID());
                    $separator = ', ';
                    $catoutput = '';
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $catoutput .= '<a href="' . esc_url(get_category_link($category->term_id)) . '" >' . esc_html($category->name) . '</a>' . $separator;
                        }
                    }

                    $author_avatar_url = get_user_meta(get_the_author_meta('ID'), "listingpro_author_img_url", true);
                    $avatar = '';
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url(get_the_author_meta('ID'), $size = '51');
                        $avatar =  $avatar_url;
                    }
                    $output .= '<div class="col-md-4 col-sm-4 lp-blog-grid-box">
                                    <div class="lp-blog-grid-box-container lp-border lp-border-radius-8">
                                        <div class="lp-blog-grid-box-thumb">
                                            <a href="' . get_the_permalink() . '">
                                                <img src="' . $thumbnail . '" alt="blog-grid-1-410x308" />
                                            </a>
                                        </div>
                                        <div class="lp-blog-grid-box-description text-center">
                                                <div class="lp-blog-user-thumb margin-top-subtract-25">
                                                    <img class="avatar" src="' . esc_url($avatar) . '" alt="image">
                                                </div>
                                                <div class="lp-blog-grid-category">
                                                    ' . trim($catoutput, $separator) . '
                                                </div>
                                                <div class="lp-blog-grid-title">
                                                    <h4 class="lp-h4">
                                                        <a href="' . get_the_permalink() . '">' . get_the_title() . '</a>
                                                    </h4>
                                                </div>
                                                <ul class="lp-blog-grid-author">
                                                    <li>
                                                        <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">
                                                            <i class="fa fa-user"></i>
                                                            <span>' . get_the_author() . '</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-calendar"></i>
                                                        <span>' . get_the_date(get_option('date_format')) . '</span>
                                                    </li>
                                                </ul><!-- ../lp-blog-grid-author -->
                                        </div>
                                    </div>
                                </div>';

                    if ($post_count == 3) {
                        $output .= '<div class="clearfix"></div>';
                        $post_count = 1;
                    } else {
                        $post_count++;
                    }
                endwhile;
            }



            $output .= '</div>';
        } elseif ($blog_style == 'style2') {

            $output .= '<div class="lp-section-content-container lp-blog-grid-container row lp-blog-grid-container-style2">';

            $type = 'post';
            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => "$blog_per_page",
                'cat' => $category,
            );
            $gridNumber = 1;
            $gridNumber2 = 1;
            $my_query = null;
            $my_query = new WP_Query($args);
            if ($my_query->have_posts()) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    if ($gridNumber == 1) {
                        $imgurl = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid3');
                        if ($imgurl) {
                            $thumbnail = $imgurl[0];
                        } else {
                            $thumbnail = 'https://placehold.co/672x430';
                        }
                    } else {
                        $imgurl = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');
                        if ($imgurl) {
                            $thumbnail = $imgurl[0];
                        } else {
                            $thumbnail = 'https://placehold.co/372x240';
                        }
                    }
                    $categories = get_the_category(get_the_ID());
                    $separator = ' ';
                    $catoutput = '';
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $catoutput .= '<a href="' . esc_url(get_category_link($category->term_id)) . '" >' . esc_html($category->name) . '</a>' . $separator;
                        }
                    }

                    $author_avatar_url = get_user_meta(get_the_author_meta('ID'), "listingpro_author_img_url", true);
                    $avatar = '';
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url(get_the_author_meta('ID'), $size = '51');
                        $avatar =  $avatar_url;
                    }

                    if ($gridNumber == 1) {
                        $output .= '<div class="lp-blog-grid-box col-md-12">
                                    <div class="lp-blog-grid-box-container lp-blog-grid-box-container-first-post lp-border-radius-8 ">
                                            <div class="col-md-5 lp-blog-style2-outer padding-right-0 lp-border">
                                                <div class="lp-blog-style2-inner">
                                                    <div class="lp-blog-user-thumb2">
                                                        <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '"><img class="avatar" src="' . esc_url($avatar) . '" alt="image"></a>
                                                    </div>
                                                    
                                                    <div class="lp-blog-grid-title">
                                                        <h4 class="lp-h4">
                                                            <a href="' . get_the_permalink() . '">' . mb_substr(get_the_title(), 0, 29) . '</a>
                                                        </h4>
                                                        <p>' . mb_substr(strip_tags(get_the_content()), 0, 150) . '..</p>
                                                    </div>
                                                    <ul class="lp-blog-grid-author">
                                                        <li>
                                                            
                                                                <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                                <span>' . trim($catoutput, $separator) . '</span>
                                                            
                                                        </li>
                                                        <li>
                                                            <i class="fa fa-calendar"></i>
                                                            <span>' . get_the_date(get_option('date_format')) . '</span>
                                                        </li>
                                                    </ul><!-- ../lp-blog-grid-author -->
                                                    <div class="blog-read-more">
                                                        <a href="' . get_the_permalink() . '" class="watch-video">' . esc_html__('Read More', 'listingpro-plugin') . '</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lp-blog-grid-box-thumb col-md-7 padding-right-0 padding-left-0">
                                                <a href="' . get_the_permalink() . '">
                                                    <img src="' . $thumbnail . '" alt="blog-grid-1-410x308" />
                                                </a>
                                            </div>
                                        
                                        
                                    </div>
                                </div>';
                        if ($gridNumber == 1) {
                            $output .= '<div class="clearfix"></div>';
                        }
                    } else {

                        $output .= '<div class="col-md-4 col-sm-4 lp-blog-grid-box">
                                    <div class="lp-blog-grid-box-container lp-blog-grid-box-container-style2 lp-border-radius-8">
                                        <div class="lp-blog-grid-box-thumb">
                                            <a href="' . get_the_permalink() . '">
                                                <img src="' . $thumbnail . '" alt="blog-grid-1-410x308" />
                                            </a>
                                        </div>
                                        <div class="lp-blog-grid-box-description  lp-border lp-blog-grid-box-description2">
                                                <div class="lp-blog-user-thumb margin-top-subtract-25">
                                                    <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '"><img class="avatar" src="' . esc_url($avatar) . '" alt="image"></a>
                                                </div>
                                                
                                                <div class="lp-blog-grid-title">
                                                    <h4 class="lp-h4">
                                                        <a href="' . get_the_permalink() . '">' . mb_substr(get_the_title(), 0, 29) . '</a>
                                                    </h4>
                                                    <p>' . mb_substr(strip_tags(get_the_content()), 0, 80) . '..</p>
                                                </div>
                                                <ul class="lp-blog-grid-author lp-blog-grid-author2">
                                                    <li>
                                                        <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                        <span>' . trim($catoutput, $separator) . '</span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-calendar"></i>
                                                        <span>' . get_the_date(get_option('date_format')) . '</span>
                                                    </li>
                                                </ul><!-- ../lp-blog-grid-author -->
                                                <div class="blog-read-more">
                                                        <a href="' . get_the_permalink() . '" class="watch-video">' . esc_html__('Read More', 'listingpro-plugin') . '</a>
                                                </div>
                                        </div>
                                    </div>
                                </div>';
                        if ($gridNumber2 == 3) {
                            $output .= '<div class="clearfix"></div>';
                        }
                        $gridNumber2++;
                    }


                    $gridNumber++;
                endwhile;
            }



            $output .= '</div>';
        } elseif ($blog_style == 'style4') {
?>
    <?php
            wp_enqueue_style('lp-classic-blog-grid');
            $output .= '<div class="lp-section-content-container lp-blog-grid-container row">';


            $type = 'post';
            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => "$blog_per_page",
                'cat' => $category,
            );


            $my_query = null;
            $my_query = new \WP_Query($args);
            if ($my_query->have_posts()) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    $imgurl = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), '100');
                    if ($imgurl) {
                        $thumbnail = $imgurl[0];
                    } else {
                        $thumbnail = 'https://placehold.co/372x240';
                    }
                    $categories = get_the_category(get_the_ID());
                    $separator = ', ';
                    $catoutput = '';
                    if (!empty($categories)) {
                        foreach ($categories as $key => $category) {
                            if ($key == 0) {
                                $catoutput .= '<a href="' . esc_url(get_category_link($category->term_id)) . '" >' . esc_html($category->name) . '</a>' . $separator;
                            }
                        }
                    }


                    $author_avatar_url = get_user_meta(get_the_author_meta('ID'), "listingpro_author_img_url", true);
                    $avatar = '';
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url(get_the_author_meta('ID'), $size = '51');
                        $avatar =  $avatar_url;
                    }
                    $output .= '<div class="col-md-4 col-sm-4 lp-blog-grid-box">
                                    <div class="lp-blog-grid-box-container-classic-style">
                                    <div class="lp-blog-grid-box-thumb-posision">
                                    </div>
                                        <div class="lp-blog-grid-box-thumb">
                                            <a href="' . get_the_permalink() . '">
                                                <img src="' . $thumbnail . '"  />
                                            </a>
                                        </div>
                                       <div class="lp-blog-grid-box-dcontent">
                                        <div class="lp-blog-grid-box-description text-center">
                                                <div class="lp-blog-user-thumb margin-top-subtract-25">
                                                    <img class="avatar" src="' . esc_url($avatar) . '" alt="image">
                                                </div>
                                                <div class="lp-blog-grid-category">
                                                     <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '
                                                        </a>
                                                </div>
                                                <div class="lp-blog-grid-title">
                                                    <h4 class="lp-h4">
                                                        <a href="' . get_the_permalink() . '">' . get_the_title() . '</a>
                                                    </h4>
                                                </div>
                                        </div>
                                         <div class="lp-blog-grid-box-description-bottom">
                                                <ul class="lp-blog-grid-author">
                                                    <li>
                                                    	' . trim($catoutput, $separator) . '
                                                    </li>
                                                    <li>
                                                    <i class="fa-regular fa-calendar"></i>
                                                        <span>' . get_the_date(get_option('date_format')) . '</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>';


                    if ($post_count == 3) {
                        $output .= '<div class="clearfix"></div>';
                        $post_count = 1;
                    } else {
                        $post_count++;
                    }
                endwhile;
            }






            $output .= '</div>';
        } else {
            $output .= '<div class="lp-section-content-container lp-section-content-container-style3">';

            $type = 'post';
            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => "$blog_per_page",
                'cat' => $category,
            );

            $my_query = null;
            $my_query = new WP_Query($args);
            if ($my_query->have_posts()) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    $imgurl = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-thumb4');
                    if ($imgurl) {
                        $thumbnail = $imgurl[0];
                    } else {
                        $thumbnail = 'https://placehold.co/270x270';
                    }



                    $output .= '<div class="col-md-3 col-sm-3 lp-blog-style3">
                                    <div class="lp-blog-grid-box-container">
                                        <div class="lp-blog-grid-box-thumb">
                                            <a href="' . get_the_permalink() . '">
                                                <img src="' . $thumbnail . '" alt="blog-grid-1-270x270" />
                                            </a>
                                        </div>
                                        <div class="lp-blog-grid-box-description text-center">
                                                
                                                <div class="lp-blog-grid-title">
                                                    <h4 class="lp-h4">
                                                        <a href="' . get_the_permalink() . '">' . get_the_title() . '</a>
                                                    </h4>
                                                </div>
                                               
                                        </div>
                                    </div>
                                </div>';
                endwhile;
            }



            $output .= '<div class="clearfix"></div></div>';
        }
        return $output;
    }
}
add_shortcode('blog_grids', 'listingpro_shortcode_blog_grids');
/*------------------------------------------------------*/
/* Content boxes
/*------------------------------------------------------*/
vc_map(array(
    "name" => __("LP Content Boxes", "my-text-domain"),
    "base" => "content_boxes",
    "category"  => __('Listingpro', 'js_composer'),
    "as_parent" => array('only' => 'content_box'),
    "content_element" => true,
    "show_settings_on_create" => false,
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "is_container" => true,
    "params" => array(
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Element title", "js_composer"),
            "param_name" => "moderen_title",
            "value"       => "How Its Actually Work",
            'save_always' => true,
            "description" => "Enter Element Title"
        ),
    ),
    "js_view" => 'VcColumnView'
));
function listingpro_shortcode_content_box_container($atts, $content = null)
{
    extract(shortcode_atts(array(

        'moderen_title'   => '',
    ), $atts));
    $output = null;

    $output .= ' <div class="about-box-container">';
    $output .= '	<div class="lp-section-content-container clearfix">';

    $output .=                 do_shortcode($content);

    $output .= '	</div>';
    $output .= '</div>';



    return $output;
}
add_shortcode('content_boxes', 'listingpro_shortcode_content_box_container');


vc_map(array(
    "name"                      => __("LP Single Content Box", "js_composer"),
    "base"                      => 'content_box',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "content_element" => true,
    "as_child" => array('only' => 'content_boxes'),
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Select Single Content Box Sryle", "js_composer"),
            "param_name"  => "single_content_box_style",
            'value' => array(
                __('Old Style ', 'js_composer') => 'classic',
                __('New Style ', 'js_composer') => 'moderen',

            ),
            'save_always' => true,
            "description" => "Select Single Content Box Style"
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Title", "js_composer"),
            "param_name"  => "content_title",
            "value"       => "PLANNING",
            "description" => "Title fot content"
        ),
        array(
            "type"        => "textarea",
            "class"       => "",
            "heading"     => __("Content", "js_composer"),
            "param_name"  => "content_desc",
            "value"       => "Sed ut perspiciatis unde omnis iste natus error sit v oluptatem accusantium or sit v oluptatem accusantiumor sit v oluptatem ",
            "description" => "Some text"
        ),
        array(
            "type"        => "icon",
            "class"       => "",
            "heading"     => __("Content", "js_composer"),
            "param_name"  => "content_icon",
            "value"       => "",
            "description" => "Select Icon"
        ),


    ),
));
function listingpro_shortcode_content_box($atts, $content = null)
{
    extract(shortcode_atts(array(
        'single_content_box_style'   => 'classic',
        'content_title'   => 'PLANNING',
        'content_desc'   => 'Sed ut perspiciatis unde omnis iste natus error sit v oluptatem accusantium or sit v oluptatem accusantiumor sit v oluptatem',
        'content_icon'   => '',
    ), $atts));


    if (array_key_exists('from-elementor', $atts)) {
        $icon_class =   $content_icon;
    } else {
        $icon_class =   'fa fa-' . $content_icon;
    }

    $output = null;
    if ($single_content_box_style == 'classic') {
        $output .= '<div class="col-md-3 col-sm-6 about-box text-center">
						<div class="about-box-inner lp-border-radius-5 lp-border">
							<div class="about-box-slide">
								<div class="about-box-icon">
									<!-- Inspection icon by Icons8 -->
									<i class="' . $icon_class . '"></i>
								</div>
								<div class="about-box-title clearfix">
									<h4>' . $content_title . '</h4>
								</div>
								<div class="about-box-description">
									<p class="paragraph-small">
										' . $content_desc . '
									</p>
								</div>
							</div>
						</div>
					</div>';
    } else {

        $output .= '<div class="col-md-4 col-sm-6 about-box about-box-style2">
						<div class="about-box-inner lp-border-radius-5 lp-border">
							<div class="about-box-slide">
								<div class="about-box-icon-style2">
									<!-- Inspection icon by Icons8 -->
									<i class="' . $icon_class . '"></i>
								</div>
								<div class="about-box-title-style2 clearfix">
									<h4>' . $content_title . '</h4>
								</div>
								<div class="about-box-description-style2">
									<p class="paragraph-small">
										' . $content_desc . '
									</p>
								</div>
							</div>
						</div>
					</div>';
    }

    return $output;
}
add_shortcode('content_box', 'listingpro_shortcode_content_box');

/*------------------------------------------------------*/
/* Partners Logos
/*------------------------------------------------------*/
vc_map(array(
    "name" => __("LP Partners", "js_composer"),
    "base" => "listingpro_partners",
    "as_parent" => array('only' => 'listingpro_partner'),
    "content_element" => true,
    "show_settings_on_create" => false,
    "is_container" => true,
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params" => array(
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Element title", "js_composer"),
            "param_name" => "partner_title",
            "value"       => "10000+ clients trust us to electrify their events",
            'save_always' => true,
            "description" => "Enter Element Title"
        ),
    ),
    "js_view" => 'VcColumnView'
));
function listingpro_shortcode_listingpro_partners_container($atts, $content = null)
{
    extract(shortcode_atts(array(
        'partner_title'   => '',
    ), $atts));
    $output = null;

    $output .= ' <div class="travel-brands padding-bottom-30 padding-top-30">';
    $output .= '	<div class="row">';

    $output .=                 do_shortcode($content);

    $output .= '	</div>';
    $output .= '</div>';



    return $output;
}
add_shortcode('listingpro_partners', 'listingpro_shortcode_listingpro_partners_container');

vc_map(array(
    "name"                      => __("LP Single Partner Logo", "js_composer"),
    "base"                      => 'listingpro_partner',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "content_element" => true,
    "as_child" => array('only' => 'listingpro_partners'),
    "params"                    => array(
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Partner logo ", "js_composer"),
            "param_name"  => "p_image1",
            "value"       => "",
            "description" => "Put here Partner logo."
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Logo Url", "js_composer"),
            "param_name"  => "p_image1_url",
            "value"       => "#",
            "description" => ""
        ),
    ),
));
function listingpro_shortcode_listingpro_partner($atts, $content = null)
{
    extract(shortcode_atts(array(
        'p_image1'        => '',
        'p_image1_url'        => '',
    ), $atts));

    $output = null;

    $pimahe1 = '';
    if ($p_image1) {
        if (is_array($p_image1)) {
            $p_image1   =   $p_image1['id'];
        }
        $imgurl = wp_get_attachment_image_src($p_image1, 'full');

        if ($imgurl) {
            $thumbnail = $imgurl[0];
        } else {
            $thumbnail = 'https://placehold.co/570x228';
        }
    }
    $output .= '<div class="col-md-2 partner-box text-center">
					<div class="partner-box-inner">
						<div class="partner-image">
							<a href="' . $p_image1_url . '"><img src="' . $thumbnail . '" alt="image" /></a>
						</div>
					</div>
				</div>';
    return $output;
}
add_shortcode('listingpro_partner', 'listingpro_shortcode_listingpro_partner');


/*------------------------------------------------------*/
/* Activities
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => __("LP Activities", "js_composer"),
    "base"                      => 'lp_activities',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Style", "js_composer"),
            "param_name"  => "activity_style",
            'value' => array(
                esc_html__('Style 1', 'js_composer') => 'style1',
                esc_html__('Style 2', 'js_composer') => 'style2',
                esc_html__('Style 3', 'js_composer') => 'style3',
            ),
            'save_always' => true,
            "description" => "Select style of activities you want to show"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Number of Activities", "js_composer"),
            "param_name"  => "number_posts",
            'value' => array(
                esc_html__('3 Posts', 'js_composer') => '3',
                esc_html__('4 Posts', 'js_composer') => '4',
                esc_html__('5 Posts', 'js_composer') => '5',
            ),
            'save_always' => true,
            "description" => "Select number of activities you want to show"
        ),
    ),
));

function listingpro_shortcode_lp_activities($atts, $content = null)
{
    extract(shortcode_atts(array(
        'number_posts'   => '5',
        'activity_placeholder' => '',
        'activity_style' => ''
    ), $atts));
    require_once(THEME_PATH . "/include/aq_resizer.php");
    $output = null;

    $args   =   array(
        'post_type' => 'lp-reviews',
        'post_status' => 'publish',
        'posts_per_page' => $number_posts,
    );
    $activities  =   new WP_Query($args);
    $img_url    = '';
    $img_url2   = '';
    $img_url3   = '';
    $img_url4   = '';
    global $listingpro_options;
    $placeholder_img    =   '';
    $use_listing_img    =   $listingpro_options['lp_review_img_from_listing'];
    if ($use_listing_img == 'off') {
        $placeholder_img    =   $listingpro_options['lp_review_placeholder'];
        $placeholder_img    =   $placeholder_img['url'];
    }

    if ($activities->have_posts()) :
        $counter    =   1;
        if ($activity_style == 'style1') {
            $output .=  '<div class="lp-activities"><div class="lp-section-content-container"> ';
            $output .=  '    <div class="row">';
            while ($activities->have_posts()) : $activities->the_post();
                $img_url    = '';
                $img_url2   = '';
                $img_url3   = '';
                $img_url4   = '';
                global $post;
                $r_meta     =   get_post_meta(get_the_ID(), 'lp_listingpro_options', true);
                $LID        =   $r_meta['listing_id'];
                $rating     =   $r_meta['rating'];
                $rating     =   number_format($r_meta['rating'], 1, '.', '');
                $adStatus = apply_filters('lp_get_ad_status', '', $LID);

                $CHeckAd = '';
                $adClass = '';
                if ($adStatus == 'active') {
                    $CHeckAd = '<span>' . esc_html__('Ad', 'listingpro-plugin') . '</span>';
                    $adClass = 'promoted';
                }
                $author_avatar_url = get_user_meta($post->post_author, "listingpro_author_img_url", true);
                $avatar;
                if (!empty($author_avatar_url)) {
                    $avatar =  $author_avatar_url;
                } else {
                    $avatar_url = listingpro_get_avatar_url($post->post_author, $size = '55');
                    $avatar =  $avatar_url;
                }
                if (empty($avatar)) {
                    $avatar = 'https://www.gravatar.com/avatar/';
                }
                $interests = '';
                $Lols = '';
                $loves = '';
                $interVal = esc_html__('Interesting', 'listingpro-plugin');
                $lolVal = esc_html__('Lol', 'listingpro-plugin');
                $loveVal = esc_html__('Love', 'listingpro-plugin');

                $interests = listing_get_metabox_by_ID('review_' . $interVal . '', get_the_ID());
                $Lols = listing_get_metabox_by_ID('review_' . $lolVal . '', get_the_ID());
                $loves = listing_get_metabox_by_ID('review_' . $loveVal . '', get_the_ID());


                if (empty($interests)) {
                    $interests = 0;
                }
                if (empty($Lols)) {
                    $Lols = 0;
                }
                if (empty($loves)) {
                    $loves = 0;
                }

                $reacted_msg    =   esc_html__('You already reacted', 'listingpro-plugin');
                $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                if ($use_listing_img == 'off') {
                    $img_url    = aq_resize($placeholder_img, '360', '267', true, true, true);
                    $img_url2   = aq_resize($placeholder_img, '165', '97', true, true, true);
                    $img_url3   = aq_resize($placeholder_img, '500', '300', true, true, true);
                    $img_url4   = aq_resize($placeholder_img, '295', '150', true, true, true);
                } else {
                    if (has_post_thumbnail($LID)) {
                        $thumbnail_url  =   get_the_post_thumbnail_url($LID);
                        $img_url    = aq_resize($thumbnail_url, '360', '267', true, true, true);
                        $img_url2   = aq_resize($thumbnail_url, '165', '97', true, true, true);
                        $img_url3   = aq_resize($thumbnail_url, '500', '300', true, true, true);
                        $img_url4   = aq_resize($thumbnail_url, '295', '150', true, true, true);
                    } else {
                        $listing_gallery    =   get_post_meta($LID, 'gallery_image_ids', true);
                        $listing_imagearray = explode(',', $listing_gallery);
                        $listing_image      = wp_get_attachment_image_src($listing_imagearray[0], 'full');

                        if (!empty($listing_image[0])) {
                            $img_url    = aq_resize($listing_image[0], '360', '267', true, true, true);
                            $img_url2   = aq_resize($listing_image[0], '165', '97', true, true, true);
                            $img_url3   = aq_resize($listing_image[0], '500', '300', true, true, true);
                            $img_url4   = aq_resize($listing_image[0], '295', '150', true, true, true);
                        }
                    }
                }

                if (!empty($gallery)) {
                    $imagearray = explode(',', $gallery);
                    $image      = wp_get_attachment_image_src($imagearray[0], 'full');
                    $first_img  =   $imagearray[0];
                    if (!empty($image[0])) {
                        $img_url    = aq_resize($image[0], '360', '267', true, true, true);
                        $img_url2   = aq_resize($image[0], '165', '97', true, true, true);
                        $img_url3   = aq_resize($image[0], '500', '300', true, true, true);
                        $img_url4   = aq_resize($image[0], '295', '150', true, true, true);
                    }
                }
                if (empty($img_url)) {
                    if (empty($activity_placeholder)) {
                        $img_url    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url    =   aq_resize($element_placehilder_url, '360', '267', true, true, true);;
                    }
                }
                if (empty($img_url2)) {
                    if (empty($activity_placeholder)) {
                        $img_url2    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url2   = aq_resize($element_placehilder_url, '165', '97', true, true, true);
                    }
                }
                if (empty($img_url3)) {
                    if (empty($activity_placeholder)) {
                        $img_url3    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url3 = aq_resize($element_placehilder_url, '500', '300', true, true, true);
                    }
                }
                if (empty($img_url4)) {
                    if (empty($activity_placeholder)) {
                        $img_url4    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url4 = aq_resize($element_placehilder_url, '295', '150', true, true, true);
                    }
                }
                $lp_liting_title    =   get_the_title($LID);
                if (strlen($lp_liting_title) > 35) {
                    $lp_liting_title    =   mb_substr($lp_liting_title, 0, 35) . '...';
                }

                $rating_num_bg  =   '';
                $rating_num_clr  =   '';

                if ($rating < 2) {
                    $rating_num_bg  =   'num-level1';
                    $rating_num_clr  =   'level1';
                }
                if ($rating < 3) {
                    $rating_num_bg  =   'num-level2';
                    $rating_num_clr  =   'level2';
                }
                if ($rating < 4) {
                    $rating_num_bg  =   'num-level3';
                    $rating_num_clr  =   'level3';
                }
                if ($rating >= 4) {
                    $rating_num_bg  =   'num-level4';
                    $rating_num_clr  =   'level4';
                }
                if ($number_posts == 3) {
                    $output .=  '
                <div class="col-md-4"> 
                    <div class="lp-activity">
                        <div class="lp-activity-top">
                            <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" class="lp-activity-author-thumb"><img src="' . esc_attr($avatar) . '" alt="' . get_the_title() . '"></a>
                            <a href="' . get_permalink($LID) . '" class="lp-activity-thumb"><img class="hidden-md hidden-lg" src="' . $img_url3 . '" alt="' . get_the_title() . '"><img class="hidden-sm hidden-xs" src="' . $img_url . '" alt="' . get_the_title() . '"></a>
                        </div>
                        <div class="lp-activity-bottom">
                            <div class="lp-activity-review-writer">
                                <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a> 
                                <p>' . esc_html__('Wrote a review', 'listingpro-plugin') . '</p>
                            </div>
                            <div class="lp-listing-stars clearfix">
                           <div class="lp-rating-stars-outer">
                                <span class="lp-star-box ';
                    if ($rating > 0) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if ($rating > 1) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if ($rating > 2) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if ($rating > 3) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if ($rating > 4) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
</div>
                                <span class="lp-rating-num rating-with-colors ' . review_rating_color_class($rating) . '">' . $rating . '</span>
                            </div>
                            <h3><a href="' . get_permalink($LID) . '">' . $lp_liting_title . '</a></h3>
                            <strong>' . mb_substr(get_the_title(), 0, 35) . '</strong>
                            <div class="lp-activity-description">
                                <p>' . mb_substr($post->post_content, '0', '100') . ' <a href="' . get_permalink($LID) . '">' . esc_html__('Continue Reading', 'listingpro-plugin') . '</a></p>
                                <div class="activity-reactions">
                                    <a href="#" data-restype="' . $interVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($interests) . '" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span>' . esc_html__('Interesting', 'listingpro-plugin') . ' <span class="react-count">' . $interests . '</span></a>
                                    <a href="#" data-restype="' . $lolVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($Lols) . '" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span>' . esc_html__('LOL', 'listingpro-plugin') . ' <span class="react-count">' . $Lols . '</span></a>
                                    <a href="#" data-restype="' . $loveVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($loves) . '" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span>' . esc_html__('Love', 'listingpro-plugin') . ' <span class="react-count">' . $loves . '</span></a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }

                if ($counter == 1 && $number_posts == 5) {

                    $output .=  '
                <div class="col-md-4">
                    <div class="lp-activity">
                        <div class="lp-activity-top">
                            <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" class="lp-activity-author-thumb"><img src="' . esc_attr($avatar) . '" alt="' . get_the_title() . '"></a>
                            <a href="' . get_permalink($LID) . '" class="lp-activity-thumb"><img class="hidden-md hidden-lg" src="' . $img_url3 . '" alt="' . get_the_title() . '"><img class="hidden-sm hidden-xs" src="' . $img_url . '" alt="' . get_the_title() . '"></a>
                        </div>
                        <div class="lp-activity-bottom">
                            <div class="lp-activity-review-writer">
                                <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>
                                <p>' . esc_html__('Wrote a review', 'listingpro-plugin') . '</p>
                            </div>
                            <div class="lp-listing-stars clearfix">
                           <div class="lp-rating-stars-outer">
                                <span class="lp-star-box ';
                    if ($rating > 0) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if ($rating > 1) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if ($rating > 2) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if ($rating > 3) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if ($rating > 4) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
</div>
                                <span class="lp-rating-num rating-with-colors ' . review_rating_color_class($rating) . '">' . $rating . '</span>
                            </div>
                            <h3><a href="' . get_permalink($LID) . '">' . $lp_liting_title . '</a></h3>
                            <strong>' . mb_substr(get_the_title(), 0, 35) . '</strong>
                            <div class="lp-activity-description">
                                <p>' . mb_substr($post->post_content, '0', '130') . ' <a href="' . get_permalink($LID) . '">' . esc_html__('Continue Reading', 'listingpro-plugin') . '</a></p>
                                <div class="activity-reactions">
                                    <a href="#" data-restype="' . $interVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($interests) . '" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span>' . esc_html__('Interesting', 'listingpro-plugin') . ' <span class="react-count">' . $interests . '</span></a>
                                    <a href="#" data-restype="' . $lolVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($Lols) . '" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span>' . esc_html__('LOL', 'listingpro-plugin') . ' <span class="react-count">' . $Lols . '</span></a>
                                    <a href="#" data-restype="' . $loveVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($loves) . '" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span>' . esc_html__('Love', 'listingpro-plugin') . ' <span class="react-count">' . $loves . '</span></a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                if ($counter > 1 && $number_posts == 5) {
                    $bottom_class   =   '';
                    if ($counter == $number_posts - 1 || $counter == $number_posts) {
                        $bottom_class   =   'bottom0';
                    }
                    if ($counter == 2) {
                        $output .=  '<div class="col-md-8">';
                        $output .=  '    <div class="row">';
                    }
                    $output .=  '
                <div class="col-md-6">
                    <div class="lp-activity style2 ' . $bottom_class . '">
                        <div class="lp-activity-top">
                            <div class="row">
                                <div class="lp-activity-thumb col-md-6">
                                    <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" class="lp-activity-author-thumb"><img src="' . esc_attr($avatar) . '" alt="image"></a>
                                    <a href="' . get_permalink($LID) . '"><img class="hidden-xs hidden-sm" src="' . $img_url2 . '" alt="' . get_the_title() . '"><img class="hidden-md hidden-lg" src="' . $img_url3 . '" alt="' . get_the_title() . '"></a>
                                </div>
                                <div class="lp-activity-top-right col-md-6">
                                    <div class="lp-activity-review-writer">
                                        <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>
                                <p>' . esc_html__('Wrote a review', 'listingpro-plugin') . '</p>
                                    </div>
                                    <div class="lp-listing-stars clearfix">
                           <div class="lp-rating-stars-outer">
                                        <span class="lp-star-box ';
                    if ($rating > 0) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if ($rating > 1) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if ($rating > 2) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if ($rating > 3) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if ($rating > 4) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
</div>
                                        <span class="lp-rating-num rating-with-colors ' . review_rating_color_class($rating) . '">' . $rating;
                    if ($rating == '') {
                        $output   .=  0;
                    }
                    if ($rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5) {
                        $output .= '.0';
                    }
                    $output .= '</span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="lp-activity-bottom">
                            <h3><a href="' . get_permalink($LID) . '">' . $lp_liting_title . '</a></h3>
                            <strong>' . mb_substr(get_the_title(), 0, 35) . '</strong>
                        </div>

                        <div class="lp-activity-description">
                            <p>' . mb_substr($post->post_content, 0, 70) . ' <a href="' . get_permalink($LID) . '">' . esc_html__('Continue Reading', 'listingpro-plugin') . '</a></p>
                            <div class="activity-reactions small-btns">
                                <a href="#" data-restype="' . $interVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($interests) . '" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span>' . esc_html__('Interesting', 'listingpro-plugin') . ' <span class="react-count">' . $interests . '</span></a>
                                <a href="#" data-restype="' . $lolVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($Lols) . '" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span>' . esc_html__('LOL', 'listingpro-plugin') . ' <span class="react-count">' . $Lols . '</span></a>
                                <a href="#" data-restype="' . $loveVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($loves) . '" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span>' . esc_html__('Love', 'listingpro-plugin') . ' <span class="react-count">' . $loves . '</span></a>
                            </div>
                        </div>
                    </div>
                </div>';
                    if ($counter == $number_posts || $counter == $activities->found_posts) {
                        $output .=  '    </div>';
                        $output .=  '</div>';
                    }
                }
                if ($number_posts == 4) {
                    $output .=  '
                <div class="col-md-6">
                    <div class="lp-activity style2">
                        <div class="lp-activity-top">
                            <div class="row">
                                <div class="lp-activity-thumb col-md-6">
                                    <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" class="lp-activity-author-thumb"><img src="' . esc_attr($avatar) . '" alt="image"></a>
                                    <a href="' . get_permalink($LID) . '"><img class="hidden-xs hidden-sm" src="' . $img_url4 . '" alt="' . get_the_title() . '"><img class="hidden-md hidden-lg" src="' . $img_url3 . '" alt="' . get_the_title() . '"></a>
                                </div>
                                <div class="lp-activity-top-right col-md-6">
                                    <div class="lp-activity-review-writer">
                                        <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>
                                <p>' . esc_html__('Wrote a review', 'listingpro-plugin') . '</p>
                                    </div>
                                    <div class="lp-listing-stars clearfix">
                           <div class="lp-rating-stars-outer">
                                        <span class="lp-star-box ';
                    if ($rating > 0) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if ($rating > 1) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if ($rating > 2) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if ($rating > 3) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if ($rating > 4) {
                        $output .= 'filled' . ' ' . $rating_num_clr;
                    }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
</div>
                                        <span class="lp-rating-num rating-with-colors ' . review_rating_color_class($rating) . '">' . $rating;
                    if ($rating == '') {
                        $output   .=  0;
                    }
                    if ($rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5) {
                        $output .= '.0';
                    }
                    $output .= '</span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="lp-activity-bottom">
                            <h3><a href="' . get_permalink($LID) . '">' . $lp_liting_title . '</a></h3>
                            <strong>' . mb_substr(get_the_title(), 0, 35) . '</strong>
                        </div>
                        <div class="lp-activity-description">
                            <p>' . mb_substr($post->post_content, 0, 120) . ' <a href="' . get_permalink($LID) . '">' . esc_html__('Continue Reading', 'listingpro-plugin') . '</a></p>
                            <div class="activity-reactions small-btns">
                                <a href="#" data-restype="' . $interVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($interests) . '" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span>' . esc_html__('Interesting', 'listingpro-plugin') . ' <span class="react-count">' . $interests . '</span></a>
                                <a href="#" data-restype="' . $lolVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($Lols) . '" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span>' . esc_html__('LOL', 'listingpro-plugin') . ' <span class="react-count">' . $Lols . '</span></a>
                                <a href="#" data-restype="' . $loveVal . '" data-reacted ="' . $reacted_msg . '" data-id="' . get_the_ID() . '" data-score="' . esc_attr($loves) . '" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span>' . esc_html__('Love', 'listingpro-plugin') . ' <span class="react-count">' . $loves . '</span></a>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                $counter++;
            endwhile;
            wp_reset_postdata();
            $output .=  '   </div></div>';
            $output .=  '</div>';
        } elseif ($activity_style == 'style2') {
            wp_enqueue_style('lp-classic-reviews');

            $output .=  '<div class="lp-activities-classicx-style"><div class="lp-section-content-container"> ';
            $output .=  '    <div class="row lp-activities-classic-slider">';
            while ($activities->have_posts()) : $activities->the_post();
                $img_url    = '';
                $img_url2   = '';
                $img_url3   = '';
                $img_url4   = '';
                global $post;
                $r_meta     =   get_post_meta(get_the_ID(), 'lp_listingpro_options', true);
                $LID        =   $r_meta['listing_id'];
                $rating     =   $r_meta['rating'];

                $adStatus = apply_filters('lp_get_ad_status', '', $LID);
                $CHeckAd = '';
                $adClass = '';
                if ($adStatus == 'active') {
                    $CHeckAd = '<span>' . esc_html__('Ad', 'listingpro-plugin') . '</span>';
                    $adClass = 'promoted';
                }
                $author_avatar_url = get_user_meta($post->post_author, "listingpro_author_img_url", true);
                $avatar;
                if (!empty($author_avatar_url)) {
                    $avatar =  $author_avatar_url;
                } else {
                    $avatar_url = listingpro_get_avatar_url($post->post_author, $size = '55');
                    $avatar =  $avatar_url;
                }
                $interests = '';
                $Lols = '';
                $loves = '';
                $interVal = esc_html__('Interesting', 'listingpro-plugin');
                $lolVal = esc_html__('Lol', 'listingpro-plugin');
                $loveVal = esc_html__('Love', 'listingpro-plugin');

                $interests = listing_get_metabox_by_ID('review_' . $interVal . '', get_the_ID());
                $Lols = listing_get_metabox_by_ID('review_' . $lolVal . '', get_the_ID());
                $loves = listing_get_metabox_by_ID('review_' . $loveVal . '', get_the_ID());


                if (empty($interests)) {
                    $interests = 0;
                }
                if (empty($Lols)) {
                    $Lols = 0;
                }
                if (empty($loves)) {
                    $loves = 0;
                }

                $reacted_msg    =   esc_html__('You already reacted', 'listingpro-plugin');
                $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                if ($use_listing_img == 'off') {
                    $img_url    = aq_resize($placeholder_img, '360', '267', true, true, true);
                    $img_url2   = aq_resize($placeholder_img, '165', '97', true, true, true);
                    $img_url3   = aq_resize($placeholder_img, '500', '300', true, true, true);
                    $img_url4   = aq_resize($placeholder_img, '295', '150', true, true, true);
                } else {
                    if (has_post_thumbnail($LID)) {
                        $thumbnail_url  =   get_the_post_thumbnail_url($LID);
                        $img_url    = aq_resize($thumbnail_url, '360', '267', true, true, true);
                        $img_url2   = aq_resize($thumbnail_url, '165', '97', true, true, true);
                        $img_url3   = aq_resize($thumbnail_url, '500', '300', true, true, true);
                        $img_url4   = aq_resize($thumbnail_url, '295', '150', true, true, true);
                    } else {
                        $listing_gallery    =   get_post_meta($LID, 'gallery_image_ids', true);
                        $listing_imagearray = explode(',', $listing_gallery);
                        $listing_image      = wp_get_attachment_image_src($listing_imagearray[0], 'full');

                        if (!empty($listing_image[0])) {
                            $img_url    = aq_resize($listing_image[0], '360', '267', true, true, true);
                            $img_url2   = aq_resize($listing_image[0], '165', '97', true, true, true);
                            $img_url3   = aq_resize($listing_image[0], '500', '300', true, true, true);
                            $img_url4   = aq_resize($listing_image[0], '295', '150', true, true, true);
                        }
                    }
                }

                if (!empty($gallery)) {
                    $imagearray = explode(',', $gallery);
                    $image      = wp_get_attachment_image_src($imagearray[0], 'full');
                    $first_img  =   $imagearray[0];
                    if (!empty($image[0])) {
                        $img_url    = aq_resize($image[0], '360', '267', true, true, true);
                        $img_url2   = aq_resize($image[0], '165', '97', true, true, true);
                        $img_url3   = aq_resize($image[0], '500', '300', true, true, true);
                        $img_url4   = aq_resize($image[0], '295', '150', true, true, true);
                    }
                }
                if (empty($img_url)) {
                    if (empty($activity_placeholder)) {
                        $img_url    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url    =   aq_resize($element_placehilder_url, '360', '267', true, true, true);;
                    }
                }
                if (empty($img_url2)) {
                    if (empty($activity_placeholder)) {
                        $img_url2    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url2   = aq_resize($element_placehilder_url, '165', '97', true, true, true);
                    }
                }
                if (empty($img_url3)) {
                    if (empty($activity_placeholder)) {
                        $img_url3    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url3 = aq_resize($element_placehilder_url, '500', '300', true, true, true);
                    }
                }
                if (empty($img_url4)) {
                    if (empty($activity_placeholder)) {
                        $img_url4    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url4 = aq_resize($element_placehilder_url, '295', '150', true, true, true);
                    }
                }
                $lp_liting_title    =   get_the_title($LID);
                if (strlen($lp_liting_title) > 35) {
                    $lp_liting_title    =   substr($lp_liting_title, 0, 35) . '...';
                }

                $rating_num_bg  =   '';
                $rating_num_clr  =   '';

                if ($rating < 2) {
                    $rating_num_bg  =   'num-level1';
                    $rating_num_clr  =   'level1';
                }
                if ($rating < 3) {
                    $rating_num_bg  =   'num-level2';
                    $rating_num_clr  =   'level2';
                }
                if ($rating < 4) {
                    $rating_num_bg  =   'num-level3';
                    $rating_num_clr  =   'level3';
                }
                if ($rating >= 4) {
                    $rating_num_bg  =   'num-level4';

                    $rating_num_clr  =   'level4';
                }
                $get_userLocation =  get_user_meta(get_the_author_meta('ID'), 'country', true);
                $output .= '<div class="col-md-4"> 
                                 <div class="classic-reivew-grid-outer"> 
                                    <div class="classic-reivew-grid-thumbnail"> 
                                        <a href="' . get_permalink($LID) . '" class="lp-activity-thumb"><img class="hidden-md hidden-lg" src="' . $img_url3 . '" alt="' . get_the_title() . '"><img class="hidden-sm hidden-xs" src="' . $img_url . '" alt="' . get_the_title() . '"></a>
                                        <div class="classic-reivew-grid-listing-title">    <h3><a href="' . get_permalink($LID) . '">' . $lp_liting_title . '</a></h3></div>
                                    </div>
                                    <div class="classic-reivew-grid-content"> 
                                        <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" class="lp-activity-author-thumb"><img src="' . esc_attr($avatar) . '" alt="' . get_the_title() . '"></a>
                                        <div class="lp-activity-review-writer">
                                            <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>';
                if (!empty($get_userLocation)) {
                    $output .= '<p class="lp-classic-boxed-reviews-wrote">' . $get_userLocation . '</p>';
                }
                $output .= ' </div>
                                        <div class="lp-activity-review-writer-description">';
                $truncated_content = substr($post->post_content, 0, 115);

                $output .= '<p>' . $truncated_content;
                if (strlen($post->post_content) > 115) {
                    $output .= '...';
                }
                $output .= ' </p>

                                            <div class="lp-listing-stars clearfix">
                                                <div class="lp-rating-stars-outer">
                                                    <span class="lp-star-box ';
                if ($rating > 0) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                if ($rating > 1) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                if ($rating > 2) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                if ($rating > 3) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                if ($rating > 4) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                                <p class="reviews-percentage">';
                $output .= $rating;
                if ($rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5) {
                    $output .= '.0';
                }
                if ($rating == '') {
                    $output   .=  '0';
                }
                $output .= ' <span class="lp-rating-num rating-with-colors ' . review_rating_color_class($rating) . '"></span>';
                $output .= '
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';


            endwhile;
            wp_reset_postdata();
            $output .=  '   </div></div>';
            $output .=  '</div>';
        } elseif ($activity_style == 'style3') {
            wp_enqueue_style('lp-classic-reviews');
            $output .=  '<div class="lp-activities-classicx-style-boxed"><div class="lp-section-content-container-boxed"> ';
            $output .=  '    <div class="row lp-activities-classic-slider">';
            while ($activities->have_posts()) : $activities->the_post();
                $img_url    = '';
                $img_url2   = '';
                $img_url3   = '';
                $img_url4   = '';
                global $post;
                $r_meta     =   get_post_meta(get_the_ID(), 'lp_listingpro_options', true);
                $LID        =   $r_meta['listing_id'];
                $rating     =   $r_meta['rating'];

                $adStatus = apply_filters('lp_get_ad_status', '', $LID);
                $CHeckAd = '';
                $adClass = '';
                if ($adStatus == 'active') {
                    $CHeckAd = '<span>' . esc_html__('Ad', 'listingpro-plugin') . '</span>';
                    $adClass = 'promoted';
                }
                $author_avatar_url = get_user_meta($post->post_author, "listingpro_author_img_url", true);
                $avatar;
                if (!empty($author_avatar_url)) {
                    $avatar =  $author_avatar_url;
                } else {
                    $avatar_url = listingpro_get_avatar_url($post->post_author, $size = '55');
                    $avatar =  $avatar_url;
                }

                $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                if ($use_listing_img == 'off') {
                    $img_url    = aq_resize($placeholder_img, '360', '267', true, true, true);
                    $img_url2   = aq_resize($placeholder_img, '165', '97', true, true, true);
                    $img_url3   = aq_resize($placeholder_img, '500', '300', true, true, true);
                    $img_url4   = aq_resize($placeholder_img, '295', '150', true, true, true);
                } else {
                    if (has_post_thumbnail($LID)) {
                        $thumbnail_url  =   get_the_post_thumbnail_url($LID);
                        $img_url    = aq_resize($thumbnail_url, '360', '267', true, true, true);
                        $img_url2   = aq_resize($thumbnail_url, '165', '97', true, true, true);
                        $img_url3   = aq_resize($thumbnail_url, '500', '300', true, true, true);
                        $img_url4   = aq_resize($thumbnail_url, '295', '150', true, true, true);
                    } else {
                        $listing_gallery    =   get_post_meta($LID, 'gallery_image_ids', true);
                        $listing_imagearray = explode(',', $listing_gallery);
                        $listing_image      = wp_get_attachment_image_src($listing_imagearray[0], 'full');

                        if (!empty($listing_image[0])) {
                            $img_url    = aq_resize($listing_image[0], '360', '267', true, true, true);
                            $img_url2   = aq_resize($listing_image[0], '165', '97', true, true, true);
                            $img_url3   = aq_resize($listing_image[0], '500', '300', true, true, true);
                            $img_url4   = aq_resize($listing_image[0], '295', '150', true, true, true);
                        }
                    }
                }
                if (empty($img_url)) {
                    if (empty($activity_placeholder)) {
                        $img_url    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url    =   aq_resize($element_placehilder_url, '360', '267', true, true, true);;
                    }
                }
                if (!empty($gallery)) {
                    $imagearray = explode(',', $gallery);
                    $image      = wp_get_attachment_image_src($imagearray[0], 'full');
                    if (!empty($image[0])) {
                        $img_url3   = aq_resize($image[0], '500', '300', true, true, true);
                    }
                }
                if (empty($img_url3)) {
                    if (empty($activity_placeholder)) {
                        $img_url3    =     $listingpro_options['lp_def_featured_image']['url'];
                    } else {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url3 = aq_resize($element_placehilder_url, '500', '300', true, true, true);
                    }
                }
                $lp_liting_title    =   get_the_title($LID);
                if (strlen($lp_liting_title) > 35) {
                    $lp_liting_title    =   substr($lp_liting_title, 0, 35) . '...';
                }

                $rating_num_bg  =   '';
                $rating_num_clr  =   '';

                if ($rating < 2) {
                    $rating_num_bg  =   'num-level1';
                    $rating_num_clr  =   'level1';
                }
                if ($rating < 3) {
                    $rating_num_bg  =   'num-level2';
                    $rating_num_clr  =   'level2';
                }
                if ($rating < 4) {
                    $rating_num_bg  =   'num-level3';
                    $rating_num_clr  =   'level3';
                }
                if ($rating >= 4) {
                    $rating_num_bg  =   'num-level4';

                    $rating_num_clr  =   'level4';
                }
                $lp_liting_review_read = '';
                $get_userLocation =  get_user_meta(get_the_author_meta('ID'), 'country', true);

                if (strlen($post->post_content) > 300) {
                    $lp_liting_review_read    =   '... <a href="' . get_permalink($LID) . '">' . esc_html__('Read More', 'listingpro-plugin') . '</a>';
                }
                $output .= '<div class="col-md-4"> 
                                 <div class="classic-reivew-grid-outer-boxed"> 
                                 <div  class="lp-activity-thumb"><img class="hidden-md hidden-lg" src="' . $img_url3 . '" alt="' . get_the_title() . '"><img class="hidden-sm hidden-xs" src="' . $img_url . '" alt="' . get_the_title() . '"></div>
                                    <div class="classic-reivew-grid-content"> 
                                        <div class="lp-activity-review-writer-description">
                                        <i class="fa-solid fa-quote-left"></i>
                                        <div class="lp-activity-review-writer-content">
                                            <p class="lp-classic-boxed-reviews-content">' . substr($post->post_content, '0', '300') . ' ' . $lp_liting_review_read . '</p>
                                        </div>
                                        <div class="lp-activity-review-writer">
                                            <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" class="lp-activity-author-thumb"><img src="' . esc_attr($avatar) . '" alt="' . get_the_title() . '"></a>
                                                <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>';
                if (!empty($get_userLocation)) {
                    $output .= '<p class="lp-classic-boxed-reviews-wrote">' . $get_userLocation . '</p>';
                }
                $output .= '  </div>
                                            <div class="lp-listing-stars clearfix">
                                                <div class="lp-rating-stars-outer">
                                                    <span class="lp-star-box ';
                if ($rating > 0) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                if ($rating > 1) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                if ($rating > 2) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                if ($rating > 3) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                if ($rating > 4) {
                    $output .= 'filled' . ' ' . $rating_num_clr;
                }
                $output .=  '"><i class="fa fa-star" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                                <p class="reviews-percentage">';
                $output .= $rating;
                if ($rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5) {
                    $output .= '.0';
                }
                if ($rating == '') {
                    $output   .=  '0';
                }
                $output .= ' <span class="lp-rating-num rating-with-colors ' . review_rating_color_class($rating) . '"></span>';
                $output .= '
                                                </p>
                                            </div>
                                        </div>
                                        <div class="classic-reivew-grid-listing-title-boxed"> <h3><a href="' . get_permalink($LID) . '">' . $lp_liting_title . '</a></h3></div>
                                    </div>
                                </div>
                            </div>';


            endwhile;
            wp_reset_postdata();
            $output .=  '   </div></div>';
            $output .=  '</div>';
        }
    endif;

    return $output;
}

add_shortcode('lp_activities', 'listingpro_shortcode_lp_activities');

/*------------------------------------------------------*/
/* Events
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => __("LP Events", "js_composer"),
    "base"                      => 'lp_events',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(

        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Number of Activities", "js_composer"),
            "param_name"  => "number_events",
            'value' => array(
                esc_html__('3 Events', 'js_composer') => '3',
                esc_html__('4 Events', 'js_composer') => '4',
                esc_html__('5 Events', 'js_composer') => '5',
                esc_html__('6 Events', 'js_composer') => '6',
                esc_html__('7 Events', 'js_composer') => '7',
                esc_html__('All Events', 'js_composer') => '-1',
            ),
            'save_always' => true,
            "description" => "Select number of activities you want to show"
        ),
    ),
));

function listingpro_shortcode_lp_events($atts, $content = null)
{
    extract(shortcode_atts(array(
        'number_events'   => '3',
    ), $atts));
    $output = null;
    $time_now   =   strtotime("-1 day");
    $args   =   array(
        'post_type' => 'events',
        'posts_per_page' => $number_events,
        'meta_key'   => 'event-date',
        'orderby'    => 'meta_value_num',
        'order'      => 'ASC',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key'     => 'event-date',
                'value'   => $time_now,
                'compare' => '>',
            ),
            array(
                'key'     => 'event-date-e',
                'value'   => $time_now,
                'compare' => '>',
            ),
        ),
    );

    $lp_events  =   new WP_Query($args);
    ob_start();
    ?>
    <div class="lp-section-content-container listingcampaings">
        <div class="lp-listings grid-style">
            <div class="row">
                <?php
                if ($lp_events->have_posts()) :
                    $event_counter  =   0;
                ?>
                    <div class="events-element-content-area-wrap" data-num="<?php echo $lp_events->post_count; ?>">
                        <?php
                        while ($lp_events->have_posts()) : $lp_events->the_post();
                            $event_counter++;
                            get_template_part('templates/loop-events');
                            if ($event_counter % 3 == 0) {
                                echo '<div class="clearfix"></div>';
                            }
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                <?php
                else :
                    echo  '<p>' . esc_html__('No Events Found', 'listingpro-plugin') . '</p>';
                endif;
                ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('lp_events', 'listingpro_shortcode_lp_events');

/*------------------------------------------------------*/
/* Events Calander
/*------------------------------------------------------*/
vc_map(array(
    "name"                      => __("LP Events Calander", "js_composer"),
    "base"                      => 'lp_events_calander',
    "category"                  => __('Listingpro', 'js_composer'),
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "description"               => '',
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Events View", "js_composer"),
            "param_name"  => "events_view",
            'value' => array(
                esc_html__('Calender', 'js_composer') => 'calander',
                esc_html__('List', 'js_composer') => 'list',
            ),
            'save_always' => true,
            "description" => ""
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Events Calender Duration", "js_composer"),
            "param_name"  => "events_calander_duration",
            'value' => array(
                esc_html__('Monthly', 'js_composer') => 'monthly',
                esc_html__('Weekly', 'js_composer') => 'weekly',
            ),
            'dependency' => array(
                'element' => 'events_view',
                'value' => array('calander')
            ),
            'save_always' => true,
            "description" => ""
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Events Calender View", "js_composer"),
            "param_name"  => "events_calander_view",
            'value' => array(
                esc_html__('Classic', 'js_composer') => 'classic',
                esc_html__('Classic 2', 'js_composer') => 'classic2',
                esc_html__('Modern', 'js_composer') => 'modern',
            ),
            'dependency' => array(
                'element' => 'events_calander_duration',
                'value' => array('monthly')
            ),
            'save_always' => true,
            "description" => ""
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Events Per Page", "js_composer"),
            "param_name"  => "events_per_page",
            'value' => array(
                esc_html__('3', 'js_composer') => '3',
                esc_html__('4', 'js_composer') => '4',
                esc_html__('5', 'js_composer') => '5',
                esc_html__('6', 'js_composer') => '6',
                esc_html__('7', 'js_composer') => '7',
                esc_html__('8', 'js_composer') => '8',
                esc_html__('9', 'js_composer') => '9',
                esc_html__('10', 'js_composer') => '10',
                esc_html__('all', 'js_composer') => 'all',
            ),
            'dependency' => array(
                'element' => 'events_view',
                'value' => array('list')
            ),
            'save_always' => true,
            "description" => ""
        ),
    ),
));
function listingpro_shortcode_lp_events_calander($atts, $content = null)
{
    extract(shortcode_atts(array(
        'events_view'   => '',
        'events_calander_view'   => '',
        'events_per_page'   => '',
        'events_calander_duration' => ''
    ), $atts));
    $output = null;

    $GLOBALS['events_calander_duration']    =   $events_calander_duration;
    ob_start();

    if ($events_calander_duration == 'weekly') {
        get_template_part('templates/events-calender/event-calender-weekly');
    } else {
        if ($events_view == 'list') {
            $GLOBALS['events_per_page']  =   $events_per_page;
            get_template_part('templates/events-calender/event-list-view');
        } elseif ($events_calander_view == 'classic' || $events_calander_view == 'classic2') {
            $GLOBALS['calender_type']   =   $events_calander_view;
            get_template_part('templates/events-calender/event-calender-classic');
        } else {
            get_template_part('templates/events-calender/event-calender-modern');
        }
    }

    return ob_get_clean();
}
add_shortcode('lp_events_calander', 'listingpro_shortcode_lp_events_calander');

/*------------------------------------------------------*/
/* Coupons By Listings
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => __("LP Coupons", "js_composer"),
    "base"                      => 'lp_coupons',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Enter listings id (Comma Separated)", "js_composer"),
            "param_name"  => "listings",
            'value' => "",
            'save_always' => true
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Select Coupon Style", "js_composer"),
            "param_name"  => "style",
            'value' => array(
                __('Style 1', 'js_composer') => 'style_1',
                __('Style 2', 'js_composer') => 'style_2'

            ),
            'save_always' => true
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("No. of posts", "js_composer"),
            "param_name"  => "number_posts",
            "value"       => "3",
            "description" => "",
            'save_always' => true
        ),
    ),
));

if (!function_exists('listingpro_shortcode_listing_coupons')) {
    function listingpro_shortcode_listing_coupons($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'listings'   => 'listings',
            'number_posts'   => '3',
            'style'   => 'style_1',
        ), $atts));
        if (!is_array($listings)) {
            $listings = explode(',', $listings);
        }
        $coupon_query =  new \WP_Query(array(
            'post__in' => $listings,
            'post_type' => 'listing'
        ));
        global $coupon_count;
        $coupon_count = 0;
        require_once(THEME_PATH . "/include/aq_resizer.php");
        echo '<div class="row">';
        if ($coupon_query->have_posts()) {
            while ($coupon_query->have_posts()) : $coupon_query->the_post();
                $listing_discount_data = get_post_meta(get_the_ID(), 'listing_discount_data', true);
                lp_coupon_grid_style($listing_discount_data, $style, $number_posts);
            endwhile;
            echo '</div>';
        }
    }
}
add_shortcode('lp_coupons', 'listingpro_shortcode_listing_coupons');

if (!function_exists('lp_coupon_grid_style')) {
    function lp_coupon_grid_style($listing_discount_data, $style, $number_posts)
    {
        global $coupon_count;
        if ($style == 'style_1') {
            if (isset($listing_discount_data) && is_array($listing_discount_data) && !empty($listing_discount_data)) :
                $timezone_string = get_option('timezone_string');
                if (isset($timezone_string) && !empty($timezone_string)) {
                    date_default_timezone_set($timezone_string);
                }
                $strNow =  strtotime("NOW");
                $discount_counter   =   1;
                $nopadding  =   'no-padding-sidebar';
                $post_author_id = get_post_field('post_author', get_the_ID());
                $discount_displayin =   get_user_meta($post_author_id, 'discount_display_area', true);
                $col_class  =   'col-md-4';
                foreach ($listing_discount_data as $key => $discount_data) :
                    $couponExpiryE = null;
                    $couponExpiryS = null;
                    if ((isset($discount_data['disExpE']) && isset($discount_data['disTimeE'])) && (!empty($discount_data['disExpE']) && !empty($discount_data['disTimeE']))) {
                        $couponExpiryE = coupon_timestamp($discount_data['disExpE'], $discount_data['disTimeE']);
                        $couponExpiryS = coupon_timestamp($discount_data['disExpS'], $discount_data['disTimeS']);
                    }
                    if (($strNow < $couponExpiryE || empty($discount_data['disExpE'])) && ($strNow > $couponExpiryS || empty($discount_data['disExpS']))) :
                        $img_url    =   'https://placehold.co/100x100';
                        if (!empty($discount_data['disImg'])) {
                            $img_url  = aq_resize($discount_data['disImg'], '100', '100', true, true, true);
                        }
                        if ($coupon_count >= $number_posts) {
                            break;
                        }
    ?>
                        <div class="<?php echo esc_attr($col_class); ?>">
                            <div class="lp-widget lp-discount-widget">
                                <div class="lp-discount-top">
                                    <?php
                                    if ($discount_data['disOff']) echo '<span class="lp-discount-thumb-tagline">' . $discount_data['disOff'] . '</span>';
                                    ?>
                                    <div class="lp-discount-thumb">
                                        <img src="<?php echo esc_attr($img_url); ?>" alt="<?php echo esc_attr($discount_data['disHea']); ?>">
                                    </div>
                                </div>
                                <div class="lp-discount-bottom">
                                    <?php
                                    if (!empty($discount_data['disHea'])) {
                                        echo '<strong class="dishead">' . $discount_data['disHea'] . '</strong>';
                                    }
                                    ?>
                                    <?php
                                    if (!empty($discount_data['disDes'])) {
                                        echo html_entity_decode($discount_data['disDes']);
                                    }
                                    ?>
                                    <?php
                                    if (isset($discount_data['disExpE']) && isset($discount_data['disTimeE']) && !empty($discount_data['disExpE']) && !empty($discount_data['disTimeE'])) :
                                        $couponExpiry  = coupon_timestamp($discount_data['disExpE'], $discount_data['disTimeE']);
                                    ?>
                                        <p><strong><?php echo esc_html__('Validity:', 'listingpro'); ?></strong> <?php echo date_i18n(get_option('date_format'), $discount_data['disExpE']); ?></p>
                                        <div class="lp-discount-count-wrap">
                                            <div id="lp-discount-countdown-<?php echo esc_attr($discount_counter); ?>" class="lp-discount-countdown lp-countdown" data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>" data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>" data-label-mints="<?php echo esc_html__('min', 'listingpro') ?>" data-minute="<?php echo date('i', $couponExpiry); ?>" data-hour="<?php echo date('H', $couponExpiry); ?>" data-day="<?php echo date('d', $couponExpiry); ?>" data-month="<?php echo date('m', $couponExpiry) - 1; ?>" data-year="<?php echo date('Y', $couponExpiry); ?>"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                $bnt_text_def   =   esc_html__('Click Here', 'listingpro');
                                if (empty($discount_data['disBT'])) {
                                    $discount_data['disBT'] =   $bnt_text_def;
                                }
                                if (isset($discount_data['disBT']) && !empty($discount_data['disBT'])) :
                                ?>
                                    <a data-html="<?php echo esc_attr($discount_data['disBT']); ?>" data-target-code="dicount-copy-<?php echo esc_attr($discount_counter); ?>" <?php if (isset($discount_data['disBL']) && !empty($discount_data['disBL'])) : echo 'target="_blank" href="' . $discount_data['disBL'] . '"';
                                                                                                                                                                                    endif; ?> class="lp-discount-btn <?php if (empty($discount_data['disBL'])) {
                                                                                                                                                                                                                                                                                                                                                                            echo 'lp-copy-code';
                                                                                                                                                                                                                                                                                                                                                                        } ?>"><?php echo esc_attr($discount_data['disBT']); ?></a>
                                <?php endif; ?>
                                <?php
                                if (empty($discount_data['disBL'])) :
                                ?>
                                    <div class="dis-code-copy-pop extra-bottom dicount-copy-<?php echo esc_attr($discount_counter); ?>" id="dicount-copy-<?php echo esc_attr($discount_counter); ?>">
                                        <div class="dis-code-copy-pop-inner">
                                            <div class="dis-code-copy-pop-inner-cell">
                                                <p><?php echo esc_html__('Copy to clipboard', 'listingpro'); ?></p>
                                                <p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo esc_attr($discount_counter); ?>" type="text" value="<?php echo esc_attr($discount_data['disCod']); ?>"> <a data-target-code="dicount-copy-<?php echo esc_attr($discount_counter); ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__('Copied', 'listingpro'); ?>"><?php echo esc_html__('Copy', 'listingpro'); ?></a></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                        $coupon_count++;
                        $discount_counter++;
                    endif;
                endforeach;
            endif;
        } else {
            $listing_discount_data  =   get_post_meta(get_the_ID(), 'listing_discount_data', true);
            $listing_discount_data_final    =   array();
            $timezone_string = get_option('timezone_string');
            if (isset($timezone_string) && !empty($timezone_string)) {
                date_default_timezone_set($timezone_string);
            }
            $strNow =  strtotime("NOW");
            if (!empty($listing_discount_data)) :
                foreach ($listing_discount_data as $key => $val) {
                    if (isset($val['disExpE']) && isset($val['disTimeE']) && isset($val['disTimeS'])) :

                        $couponExpiryE  = coupon_timestamp($val['disExpE'], $val['disTimeE']);

                        $couponExpiryS  = coupon_timestamp($val['disExpS'], $val['disTimeS']);

                        if (($strNow < $couponExpiryE || empty($discount_data['disExpE'])) && ($strNow > $couponExpiryS || empty($discount_data['disExpS']))) :
                            $listing_discount_data_final[]  =   $val;
                        endif;
                    endif;
                }
                if (is_array($listing_discount_data_final) && !empty($listing_discount_data_final)) :
                    require_once(THEME_PATH . "/include/aq_resizer.php");
                    ?>
                    <?php
                    $deal_counter   =   1;
                    $total_deals    =   count($listing_discount_data_final);
                    $col_class      =   'col-md-4';
                    $post_author_id = get_post_field('post_author', get_the_ID());
                    foreach ($listing_discount_data_final as $key => $discount_data) :
                        if ($coupon_count >= $number_posts) {
                            break;
                        }
                    ?>
                        <div class="lp-deals-wrap margin-0">
                            <?php
                            $img_url    =   'https://placehold.co/360x260';
                            if ($discount_data['disImg']) {
                                $img_url    =   $discount_data['disImg'];
                                $img_url  = aq_resize($img_url, '360', '260', true, true, true);
                            }
                            ?>
                            <div class="<?php echo esc_attr($col_class); ?>">
                                <div class="lp-deal">
                                    <div class="deal-thumb">
                                        <img alt="image" src="<?php echo esc_attr($img_url); ?>">
                                    </div>
                                    <div class="deal-details">
                                        <?php
                                        if (!empty($discount_data['disExpE'])) :
                                            $exTime = strtotime('12:00 AM');
                                            if (!empty($discount_data['disTimeE'])) {
                                                $exTime = strtotime($discount_data['disTimeE']);
                                            }
                                            $couponExpiry  = coupon_timestamp($discount_data['disExpE'], $exTime);
                                        ?>
                                            <div class="deal-countdown-wrap">
                                                <div id="lp-deals-countdown<?php echo esc_attr($deal_counter); ?>" class="lp-countdown lp-deals-countdown<?php echo esc_attr($deal_counter); ?>" data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>" data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>" data-label-mints="<?php echo esc_html__('min', 'listingpro') ?>" data-minute="<?php echo date('i', $couponExpiry); ?>" data-hour="<?php echo date('H', $couponExpiry); ?>" data-day="<?php echo date('d', $couponExpiry); ?>" data-month="<?php echo date('m', $couponExpiry) - 1; ?>" data-year="<?php echo date('Y', $couponExpiry); ?>"></div>
                                            </div>
                                        <?php endif;
                                        if ($discount_data['disOff']) echo '<span class="lp-deal-off">' . $discount_data['disOff'] . '</span>';
                                        ?>
                                        <div class="deal-content">
                                            <?php
                                            if ($discount_data['disHea']) echo '<strong>' . $discount_data['disHea'] . '</strong>';
                                            html_entity_decode(mb_substr($discount_data['disDes'], 0, 35));
                                            global $listingpro_options;
                                            $listing_mobile_view            =   $listingpro_options['single_listing_mobile_view'];
                                            //if( $discount_data['disDes'] ) echo '<p>'. html_entity_decode(mb_substr( $discount_data['disDes'], 0, 35 )) .'</p>';
                                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                            } else {

                                                if ($discount_data['disDes']) echo '<p>' . html_entity_decode($discount_data['disDes']) . '</p>';
                                            }
                                            $btn_href   =   '';
                                            $btn_class  =   'lp-copy-code';
                                            $bnt_text_def   =   esc_html__('Click Here', 'listingpro');
                                            if (empty($discount_data['disBT'])) {
                                                $discount_data['disBT'] =   $bnt_text_def;
                                            }
                                            if ($discount_data['disBL'] && !empty($discount_data['disBL'])) {
                                                $btn_href   =   'href="' . $discount_data['disBL'] . '"';
                                                $btn_class  =   '';
                                            }
                                            ?>
                                            <a target="_blank" data-html="<?php echo esc_attr($discount_data['disBT']); ?>" data-target-code="deal-copy-<?php echo esc_attr($deal_counter); ?>" <?php echo wp_kses_post($btn_href); ?> class="deal-button <?php echo esc_attr($btn_class); ?>"><i class="fa fa-gavel" aria-hidden="true"></i> <?php echo esc_attr($discount_data['disBT']); ?></a>
                                        </div>
                                    </div>
                                    <?php
                                    if (empty($discount_data['disBL'])) :
                                    ?>
                                        <div class="dis-code-copy-pop deal-copy-<?php echo esc_attr($deal_counter); ?>" id="dicount-copy-<?php echo esc_attr($deal_counter); ?>">
                                            <span class="close-right-icon" data-target="deal-copy-<?php echo esc_attr($deal_counter); ?>"><i class="fa fa-times"></i></span>
                                            <div class="dis-code-copy-pop-inner">
                                                <div class="dis-code-copy-pop-inner-cell">
                                                    <p><?php echo esc_html__('Copy to clipboard', 'listingpro'); ?></p>
                                                    <p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo esc_attr($deal_counter); ?>" type="text" value="<?php echo esc_attr($discount_data['disCod']); ?>"> <a data-target-code="dicount-copy-<?php echo esc_attr($deal_counter); ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__('Copied', 'listingpro'); ?>"><?php echo esc_html__('Copy', 'listingpro'); ?></a></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                        $coupon_count++;
                        $deal_counter++;
                        echo '</div>';
                    endforeach;
                endif;
            endif;
        }
    }
}

/*------------------------------------------------------*/
/* Announcements By Listings
/*------------------------------------------------------*/


vc_map(array(
    "name"                      => __("LP Announcements", "js_composer"),
    "base"                      => 'lp_announcements',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Listing Ids (Comma Seprated)", "js_composer"),
            "param_name"  => "listings",
            'value'       => "",
            "description" => "Enter comma seprated listing ids e.g (21,22,23)",
            'save_always' => true
        ),
        array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("No. of posts", "js_composer"),
            "param_name"  => "number_posts",
            "value"       => "3",
            "description" => "",
            'save_always' => true
        ),
    ),
));
add_shortcode('lp_announcements', 'listingpro_shortcode_listing_announcements');
if (!function_exists('listingpro_shortcode_listing_announcements')) {
    function listingpro_shortcode_listing_announcements($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'listings'   => 'listings',
            'number_posts'   => '3'
        ), $atts));
        if (!is_array($listings)) {
            $listings = explode(',', $listings);
        }
        $announcement_query =  new \WP_Query(array(
            'post__in' => $listings,
            'post_type' => 'listing'
        ));
        $announcement_count = 0;
        require_once(THEME_PATH . "/include/aq_resizer.php");
        echo '<div class="row lp_element_announcement">';
        if ($announcement_query->have_posts()) {
            while ($announcement_query->have_posts()) : $announcement_query->the_post();
                $listing_discount_data = get_post_meta(get_the_ID(), 'listing_discount_data', true);
                $lp_listing_announcements_raw  =   get_post_meta(get_the_ID(), 'lp_listing_announcements', true);
                $lp_listing_announcements   =   array();
                if ($lp_listing_announcements_raw != '' && is_array($lp_listing_announcements_raw) && count($lp_listing_announcements_raw) > 0) {
                    foreach ($lp_listing_announcements_raw as $k => $v) {
                        if ($v['annStatus']) {
                            $lp_listing_announcements[] =   $v;
                        }
                    }
                }
                if ($lp_listing_announcements != '' && is_array($lp_listing_announcements) && count($lp_listing_announcements) > 0) :
                    if ($announcement_count >= $number_posts) {
                        break;
                    }
                        ?>
                        <div class="tab-pane" id="announcements_tab">
                            <div class="lp-listing-announcement">

                                <?php
                                foreach ($lp_listing_announcements as $k => $v) :
                                    if ($v['annLI'] == get_the_ID()) :
                                        if (!isset($v['annStatus']) || $v['annStatus'] == 1) :
                                            $annSt  =   'style1';
                                            if (isset($v['annSt']) && !empty($v['annSt'])) {
                                                $annSt  =    $v['annSt'];
                                            }
                                            $icon_class =   'fa fa-bullhorn';

                                            if (!empty($v['annIC'])) {

                                                $icon_class =   $v['annIC'];
                                            }
                                ?>
                                            <div class="announcement-wrap ann-<?php echo esc_attr($annSt); ?>">
                                                <i class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
                                                <p>
                                                    <?php
                                                    if (!empty($v['annTI'])) :
                                                    ?>
                                                        <strong><?php echo esc_attr($v['annTI']); ?></strong>
                                                    <?php endif; ?>
                                                    <span><?php echo esc_attr($v['annMsg']); ?></span>
                                                </p>
                                                <?php
                                                if (!empty($v['annBT'])) :
                                                ?>
                                                    <a target="_blank" href="<?php echo esc_attr($v['annBL']); ?>" class="announcement-btn"><?php echo esc_attr($v['annBT']); ?></a>
                                                <?php endif; ?>
                                                <div class="clearfix"></div>
                                            </div>
                                <?php endif;
                                    endif;
                                endforeach; ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
    <?php
                    $announcement_count++;
                endif;
            endwhile;
            echo '</div>';
        }
    }
}

/*------------------------------------------------------*/
/* Popular Listings
/*------------------------------------------------------*/
vc_map(array(
    "name"                      => __("LP Popular Listings", "js_composer"),
    "base"                      => 'listing_with_populars',
    "category"                  => __('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Listing Layout", "js_composer"),
            "param_name"  => "listing_layout",
            'value' => array(
                esc_html__('List View', 'js_composer') => 'list_view',
                esc_html__('Grid View', 'js_composer') => 'grid_view',
            ),
            'save_always' => true,
            "description" => "Select lists layout"
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_grid_style",
            'value' => array(
                esc_html__('Grid Style 1', 'js_composer') => 'grid_view1',
                esc_html__('Grid Style 2', 'js_composer') => 'grid_view2',
                esc_html__('Grid Style 3', 'js_composer') => 'grid_view3',
                esc_html__('Grid Style 4', 'js_composer') => 'grid_view4',
                esc_html__('Grid Style 5', 'js_composer') => 'grid_view5',
                esc_html__('Grid Classic Style', 'js_composer') => 'grid_view_classic',

            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "grid_view"
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Styles", "js_composer"),
            "param_name"  => "listing_list_style",
            'value' => array(
                esc_html__('List Style 1', 'js_composer') => 'listing_views_1',
                esc_html__('List Style 2', 'js_composer') => 'list_view_v2',
            ),
            "dependency" => array(
                "element" => "listing_layout",
                "value" => "list_view"
            ),
            'save_always' => true,
        ),
        array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => esc_html__("Posts per page", "js_composer"),
            "param_name"  => "number_posts",
            'value' => array(
                esc_html__('3 Posts', 'js_composer') => '3',
                esc_html__('6 Posts', 'js_composer') => '6',
                esc_html__('9 Posts', 'js_composer') => '9',
                esc_html__('12 Posts', 'js_composer') => '12',
                esc_html__('15 Posts', 'js_composer') => '15',
                esc_html__('All Posts', 'js_composer') => '-1',
            ),
            'save_always' => true,
            "description" => "Select number of posts you want to show"
        )
    ),
));

if (!function_exists('listingpro_shortcode_listing_grids_with_populars')) {
    function listingpro_shortcode_listing_grids_with_populars($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'listing_grid_style'   => 'grid_view1',
            'listing_list_style'   => 'listing_views_1',
            'number_posts'   => '3',
            'listing_layout'   => 'grid_view',
            'number_posts'   => '',
        ), $atts));

        $output =  listing_elements_loop_cb('listing_grids_with_popular', $atts);
        return $output;
    }
}
add_shortcode('listing_with_populars', 'listingpro_shortcode_listing_grids_with_populars');
/*------------------------------------------------------*/
/* Trending Listings
/*------------------------------------------------------*/

//vc_map( array(
//    "name"                      => __("Trending Listings", "js_composer"),
//    "base"                      => 'trending_listings',
//    "category"                  => __('Listingpro', 'js_composer'),
//    "description"               => '',
//    "params"                    => array(
//
//        array(
//            "type"        => "dropdown",
//            "class"       => "",
//            "heading"     => esc_html__("Trending Duration","js_composer"),
//            "param_name"  => "duration",
//            'value' => array(
//                esc_html__( 'Weekly', 'js_composer' ) => 'weekly',
//                esc_html__( 'Monthly', 'js_composer' ) => 'monthly',
//                esc_html__( 'Yearly', 'js_composer' ) => 'yearly',
//            ),
//            'save_always' => true,
//        ),
//        array(
//            "type"        => "dropdown",
//            "class"       => "",
//            "heading"     => esc_html__("Number of Listings","js_composer"),
//            "param_name"  => "number_posts",
//            'value' => array(
//                esc_html__( '3 Posts', 'js_composer' ) => '3',
//                esc_html__( '4 Posts', 'js_composer' ) => '4',
//                esc_html__( '5 Posts', 'js_composer' ) => '5',
//                esc_html__( '6 Posts', 'js_composer' ) => '6',
//                esc_html__( '7 Posts', 'js_composer' ) => '7',
//                esc_html__( '8 Posts', 'js_composer' ) => '8',
//                esc_html__( '9 Posts', 'js_composer' ) => '9',
//                esc_html__( '10 Posts', 'js_composer' ) => '10',
//            ),
//            'save_always' => true,
//            "description" => "Select number of listings you want to show"
//        ),
//        array(
//            "type" => "textfield",
//            "class" => "",
//            "heading" => __( "Button Text", "js_composer" ),
//            "param_name" => "activity_btn_text",
//            "description" => __( "Leave empty to hide.", "js_composer" ),
//        ),
//        array(
//            "type" => "textfield",
//            "class" => "",
//            "heading" => __( "Button Link", "js_composer" ),
//            "param_name" => "activity_btn_link",
//            "description" => __( "", "js_composer" ),
//        ),
//    ),
//) );
//
//function listingpro_shortcode_trending($atts, $content = null) {
//    extract(shortcode_atts(array(
//        'number_posts'   => '3',
//        'duration'   => 'weekly',
//        'activity_btn_text'    => '',
//        'activity_btn_link'    => '',
//    ), $atts));
//    require_once (THEME_PATH . "/include/aq_resizer.php");
//    $output = null;
//
//
////    $type   =   'view';
////    $dataResponse = array();
////    $currentUserId = get_current_user_id();
////
////    $dayNow = date("l");
////    $yearNow = date("Y");
////    $monthNow = date("F");
////    $lpTodayDate = date('Y-m-d');
////    $lpTodayTime = strtotime($lpTodayDate);
////    $opt_name = $currentUserId.'_'.$type.'_'.$yearNow;
////    $lpUserOpt = get_option($opt_name);
////    $table = "listing_stats";
////
////
////    if( !empty( $type ) )
////    {
////        $condition = "user_id='$currentUserId' AND action_type='$type'";
////        $getRow = lp_get_data_from_db($table, '*', $condition);
////        echo '<pre>';
////        print_r( $getRow );
////        echo '</pre>';
////        if( $duration == 'weekly' )
////        {
////            $lpWeekeArray = lp_get_days_of_week($lpTodayDate);
////            if(!empty($lpWeekeArray))
////            {
////                foreach($lpWeekeArray as $singleDay)
////                {
////                    if(!empty($getRow))
////                    {
////                        foreach($getRow as $singleRow)
////                        {
////                            if(isset($singleRow->date))
////                            {
////                                $singleRowDate = $singleRow->date;
////                                if($singleRowDate==$singleDay)
////                                {
////                                    $dataResponse[$singleRow->counts]   =   $singleRow->listing_id .' ';
////                                }
////                            }
////                        }
////                    }
////                }
////
////            }
////        }
////        if( $duration == 'monthly' )
////        {
////            $monthNo = date("m");
////            $yearNo = date("Y");
////
////            $condition = "user_id='$currentUserId' AND action_type='$type' AND MONTH(FROM_UNIXTIME(date))='$monthNo'";
////            $getRow = lp_get_data_from_db($table, '*', $condition);
////
////            $daysOfMonth = lp_get_days_of_month($monthNo, $yearNo);
////
////            if(!empty($daysOfMonth))
////            {
////                $count = 1;
////                foreach($daysOfMonth as $singleDay)
////                {
////                    if(!empty($getRow))
////                    {
////                        foreach($getRow as $singleRow)
////                        {
////                            if(isset($singleRow->date))
////                            {
////                                $singleRowDate = $singleRow->date;
////                                if($singleRowDate==$singleDay)
////                                {
////                                    $dataResponse[$singleRow->counts]   =   $singleRow->listing_id;
////                                }
////                            }
////                        }
////                    }
////                    $count++;
////                }
////            }
////        }
////        if( $duration == 'yearly' )
////        {
////            $monthNo = date("m");
////            $yearNo = date("Y");
////            $condition = "user_id='$currentUserId' AND action_type='$type' AND YEAR(FROM_UNIXTIME(date))='$yearNo'";
////            $getRow = lp_get_data_from_db($table, '*', $condition);
////            $allMonths = lp_get_all_months();
////
////            if(!empty($allMonths))
////            {
////                foreach($allMonths as $singleMonth)
////                {
////                    if(!empty($getRow))
////                    {
////                        foreach($getRow as $singleRow)
////                        {
////                            if(isset($singleRow->date))
////                            {
////                                $singleRowDate = $singleRow->date;
////                                $thisMonth = date('F', $singleRowDate);
////                                if($thisMonth==$singleMonth){
////                                    $dataResponse[$singleRow->counts]   =   $singleRow->listing_id;
////                                }
////                            }
////                        }
////                    }
////                }
////            }
////        }
////        echo '<pre>';
////        print_r( $dataResponse );
////        echo '</pre>';
////    }
//
//
//    $args   =   array(
//        'post_type' => 'listing',
//        'post_status' => 'publish',
//        'posts_per_page' => $number_posts,
//        'meta_key'  =>  'post_views_count',
//        'orderby' => 'meta_value_num'
//
//    );
//    $trending   =   new WP_Query( $args );
//    $GLOBALS['grid_col_class']  =   4;
//    $GLOBALS['trending_el']  =   true;
//    $activity_btn   =   '';
//    if( !empty( $activity_btn_text ) && !empty( $activity_btn_link ) )
//    {
//        $activity_btn   =   '<a class="element-inner-button" href="'. $activity_btn_text .'">'. $activity_btn_text .'</a>';
//    }
//
//    if( $trending->have_posts() ) :
//        $output .=  '<div class="lp-section-content-container"><div class="lp-listings">';
//        $output .=  $activity_btn;
//        $output .=  '    <div class="row listing-slider">';
//        while ( $trending->have_posts() ) : $trending->the_post();
//            ob_start();
//            get_template_part('templates/loop-grid-view');
//            $output .= ob_get_contents();
//            ob_end_clean();
//            ob_flush();
//        endwhile; wp_reset_postdata();
//        $output .=  '   </div></div>';
//        $output .=  '</div>';
//    endif;
//
//    return $output;
//}
//add_shortcode('trending_listings', 'listingpro_shortcode_trending');


function listingpro_shortcode_venders($atts, $content = null)
{
    extract(shortcode_atts(array(

        'listing_first_title'    => '520156',
        'listing_second_title'    => 'Vendors/Entertainer Hired',

    ), $atts));

    $output = null;



    $output .= '
	<div class="listingpro-venderds">
		<div class="padding-top-60 padding-bottom-60">
		
			<div class="">
				
				<div class="text-center">
					<h3>' . $listing_first_title . '</h3>
					<p>' . $listing_second_title . '</p>
				</div>
			</div>
		</div>
	</div>';

    return $output;
}
add_shortcode('listingpro_venders', 'Listingpro_shortcode_venders');



/*------------------------------------------------------*/
/* Promotional Element classic
/*------------------------------------------------------*/
if (function_exists('vc_map')) {
    vc_map(array(
        "name" => esc_html__("LP Promotional Classic Element", "js_composer"),
        "base" => "listingpro_promotional_classic", // Replace with your desired base name
        "category" => esc_html__('Listingpro', 'js_composer'),
        "description" => '',
        "icon" => get_template_directory_uri() . "/assets/images/vcicon.png",
        "params"                    => array(
            array(
                "type"            => "textfield",
                "class"            => "",
                "heading"        => esc_html__("Timeline Title", "js_composer"),
                "param_name"    => "titlefirts_classic_box",
                "value"            => ""
            ),
            array(
                "type" => "textarea",
                "heading" => esc_html__("Description First Box", "js_composer"),
                "param_name" => "descriptionfirts_classic_box",
                "value" => "",
            ),
            array(
                "type" => "attach_image",
                "heading" => esc_html__("Image First Box", "js_composer"),
                "param_name" => "imagefirts_classic_box",
                "value" => "",
            ),
            array(
                "type"            => "textfield",
                "class"            => "",
                "heading"        => esc_html__("URL Title", "js_composer"),
                "param_name"    => "link_titlefirts_classic_box",
                "value"            => ""
            ),
            array(
                "type"            => "textfield",
                "class"            => "",
                "heading"        => esc_html__("URL", "js_composer"),
                "param_name"    => "link_urlfirts_classic_box",
                "value"            => "https://"
            ),
            // 2nd box 
            array(
                "type" => "textfield",
                "heading" => esc_html__("Title Second Box", "js_composer"),
                "param_name" => "title_sec_box",
                "value" => "",
            ),
            array(
                "type" => "textarea",
                "heading" => esc_html__("Description Second Box", "js_composer"),
                "param_name" => "description_sec_box",
                "value" => "",
            ),
            array(
                "type" => "attach_image",
                "heading" => esc_html__("Image Second Box", "js_composer"),
                "param_name" => "image_sec_box",
                "value" => "",
            ),
            array(
                "type"            => "textfield",
                "class"            => "",
                "heading"        => esc_html__("Second URL Title ", "js_composer"),
                "param_name"    => "link_titlesec_classic_box",
                "value"            => ""
            ),
            array(
                "type"            => "textfield",
                "class"            => "",
                "heading"        => esc_html__("Second URL", "js_composer"),
                "param_name"    => "link_urlsec_classic_box",
                "value"            => "https://"
            ),
            // 3rd box 
            array(
                "type" => "textfield",
                "heading" => esc_html__("Title Third Box", "js_composer"),
                "param_name" => "title_third_box",
                "value" => "",
            ),
            array(
                "type" => "textarea",
                "heading" => esc_html__("Description Third Box", "js_composer"),
                "param_name" => "description_third_box",
                "value" => "",
            ),
            array(
                "type" => "attach_image",
                "heading" => esc_html__("Image Third Box", "js_composer"),
                "param_name" => "image_third_box",
                "value" => "",
            ),
            array(
                "type"            => "textfield",
                "class"            => "",
                "heading"        => esc_html__("Third URL Title ", "js_composer"),
                "param_name"    => "link_titlethird_classic_box",
                "value"            => ""
            ),
            array(
                "type"            => "textfield",
                "class"            => "",
                "heading"        => esc_html__("Third URL", "js_composer"),
                "param_name"    => "link_urlthird_classic_box",
                "value"            => "https://"
            ),

        ),
    ));
}
function Listingpro_shortcode_promotion_classic($atts, $content = null)
{
    extract(shortcode_atts(array(
        'titlefirts_classic_box'         => '',
        'descriptionfirts_classic_box'         => '',
        'imagefirts_classic_box'         => '',
        'link_titlefirts_classic_box'         => '',
        'link_urlfirts_classic_box'         => '',
        // 2nd
        'title_sec_box'         => '',
        'description_sec_box'         => '',
        'image_sec_box'         => '',
        'link_titlesec_classic_box'         => '',
        'link_urlsec_classic_box'         => '',
        // 3rd
        'title_third_box'         => '',
        'description_third_box'         => '',
        'image_third_box'         => '',
        'link_titlethird_classic_box'         => '',
        'link_urlthird_classic_box'         => '',
    ), $atts));
    $output = null;


    wp_enqueue_style('lp-classic-promotional');


    $thumbImg_first = '';
    if (!empty($imagefirts_classic_box)) {
        if (is_array($imagefirts_classic_box)) {
            $imagefirts_classic_box   =   $imagefirts_classic_box['id'];
        }
        $bgImage = wp_get_attachment_image_src($imagefirts_classic_box, 'full');
        $thumbImg_first = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $thumbImg_first = '';
    }

    $thumbImg_sec = '';
    if (!empty($image_sec_box)) {
        if (is_array($image_sec_box)) {
            $image_sec_box   =   $image_sec_box['id'];
        }
        $bgImage = wp_get_attachment_image_src($image_sec_box, 'full');
        $thumbImg_sec = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $thumbImg_sec = '';
    }

    $thumbImg_third = '';
    if (!empty($image_third_box)) {
        if (is_array($image_third_box)) {
            $image_third_box   =   $image_third_box['id'];
        }
        $bgImage = wp_get_attachment_image_src($image_third_box, 'full');
        $thumbImg_third = '<img src="' . $bgImage[0] . '" alt="image">';
    } else {
        $thumbImg_third = '';
    }


    $output .= '   <div class="listingpro-classic-promotional-section">
        <div class="listingpro-classic-box">
            <div class="col-md-9 m-auto">
                <div class="row">
                    <div class="col-md-4">
                        <div class="classic-box">
						<div class="classic-promotional-box-counters">1</div>
                            <div class="classic-image">
                            ' . $thumbImg_first . '
                            </div>
                            <div class="classic-text">
                                <h3>' . esc_html($titlefirts_classic_box) . '</h3>
                                <p>' . esc_html($descriptionfirts_classic_box) . '</p>
								<a class="classic-promotional-box-url" href="' . $link_urlfirts_classic_box . '">' . esc_html($link_titlefirts_classic_box) . ' <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
               
                <div class="col-md-4">
                    <div class="classic-box">
					<div class="classic-promotional-box-counters">2</div>
                        <div class="classic-image">
                           ' . $thumbImg_sec . '
                        </div>
                        <div class="classic-text">
                            <h3>' . esc_html($title_sec_box) . '</h3>
                            <p>' . esc_html($description_sec_box) . '</p>
							<a class="classic-promotional-box-url" href="' . $link_urlsec_classic_box . '">' . esc_html($link_titlesec_classic_box) . ' <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
        
            <div class="col-md-4">
                <div class="classic-box">
				<div class="classic-promotional-box-counters">3</div>
                    <div class="classic-image">
                      ' . $thumbImg_third . '
                    </div>
                    <div class="classic-text">
                        <h3>' . esc_html($title_third_box) . '</h3>
                        <p>' . esc_html($description_third_box) . '</p>
						<a class="classic-promotional-box-url" href="' . $link_urlthird_classic_box . '">' . esc_html($link_titlethird_classic_box) . ' <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>';



    return $output;
}
add_shortcode('listingpro_promotional_classic', 'Listingpro_shortcode_promotion_classic');

/*------------------------------------------------------*/
/* Lp calssic call to action element
/*------------------------------------------------------*/

vc_map(array(
    "name"                      => esc_html__("Classic Call to Action", "js_composer"),
    "base"                      => 'listingpro_pro_callto_action',
    "category"                  => esc_html__('Listingpro', 'js_composer'),
    "description"               => '',
    "icon" => get_template_directory_uri() . "/assets/images/vcicon.png", // Simply pass url to your icon here
    "params"                    => array(
        array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => esc_html__("call to action background image", "js_composer"),
            "param_name"  => "listing_pro_services_img",
            "value"       => get_template_directory_uri() . "/assets/images/servcs1.png",
            "description" => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Element Title", "js_composer"),
            "param_name"    => "listing_pro_action_title",
            "value"            => ""
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('Element Description', 'js_composer'),
            'param_name'  => 'pro_action_desc',
            'value'       => ''
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Element button title", "js_composer"),
            "param_name"    => "listing_pro_action_button_title",
            "value"            => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Element button URL", "js_composer"),
            "param_name"    => "listing_pro_action_button_url",
            "value"            => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Element button secound title", "js_composer"),
            "param_name"    => "listing_pro_action_button_sec_title",
            "value"            => ""
        ),
        array(
            "type"            => "textfield",
            "class"            => "",
            "heading"        => esc_html__("Element button secound URL", "js_composer"),
            "param_name"    => "listing_pro_action_buttosec_n_url",
            "value"            => ""
        ),
    ),
));
function listingpro_shortcode_pro_classic_call_to_action($atts, $content = null)
{
    extract(shortcode_atts(array(
        'listing_pro_services_img'      => get_template_directory_uri() . "/assets/images/servcs1.png",
        'listing_pro_action_title'         => '',
        'pro_action_desc'                     => '',
        'listing_pro_action_button_title'                     => '',
        'listing_pro_action_button_url'                     => '',
        'listing_pro_action_button_sec_title'                     => '',
        'listing_pro_action_buttosec_n_url'                     => '',
    ), $atts));
    wp_enqueue_style('lp-classic-call-to-action');
    $output = null;

    $thumbImg = '';
    if (!empty($listing_pro_services_img)) {
        if (is_array($listing_pro_services_img)) {
            $listing_pro_services_img   =   $listing_pro_services_img['id'];
        }
        $bgImage = wp_get_attachment_image_src($listing_pro_services_img, 'full');
        $thumbImg = $bgImage[0];
    } else {
        $thumbImg = '';
    }
    $output .= '
	<div class="lp-classic-call-to-action">
		<div class="lp-classic-call-to-action-details" style="background-image: url(' . esc_url($thumbImg) . ');">
			<div class="lp-classic-call-overlay"></div>
			<div class="lp-classic-call-inner">
				<h2 class="lp-classic-call-to-action-title">' . $listing_pro_action_title . '</h2>
				<p class="lp-classic-call-to-action-des">' . $pro_action_desc . '</p>
				<div class="lp-classic-call-inner-urls">';
    if (!empty($listing_pro_action_button_title)) {
        $output .= '<a class="lp-classic-call-to-action-started-button" href="' . $listing_pro_action_button_url . '">' . $listing_pro_action_button_title . '</a>';
    }
    if (!empty($listing_pro_action_button_sec_title)) {
        $output .= '<a class="lp-classic-call-to-action-started-claim"  href="' . $listing_pro_action_buttosec_n_url . '">' . $listing_pro_action_button_sec_title . '</a>';
    }
    $output .= '
				</div>
			</div>

		</div>
</div>';

    return $output;
}
add_shortcode('listingpro_pro_callto_action', 'Listingpro_shortcode_pro_classic_call_to_action');


/*------------------------------------------------------*/
/* Lp calssic call to action element
/*------------------------------------------------------*/

if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_listingpro_partners extends WPBakeryShortCodesContainer
    {
    }
    class WPBakeryShortCode_content_boxes extends WPBakeryShortCodesContainer
    {
    }
}
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_listingpro_partner extends WPBakeryShortCode
    {
    }
    class WPBakeryShortCode_content_box extends WPBakeryShortCode
    {
    }
}
