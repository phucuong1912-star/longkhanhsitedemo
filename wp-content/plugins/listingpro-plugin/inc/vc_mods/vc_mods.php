<?php
/**
 * Mods & Additions for Visual Compressor plugin
 *
 * @package js_composer
 */

/*------------------------------------------------------*/
/* REMOVE ELEMENTS
/*------------------------------------------------------*/

vc_remove_element('vc_wp_categories');
vc_remove_element('vc_wp_rss');
vc_remove_element('vc_wp_text');
vc_remove_element('vc_wp_meta');
vc_remove_element('vc_wp_recentcomments');
vc_remove_element('vc_wp_tagcloud');
vc_remove_element('vc_wp_archives');
vc_remove_element('vc_wp_calendar');
vc_remove_element('vc_wp_pages');
vc_remove_element('vc_wp_links');
vc_remove_element('vc_wp_posts');
vc_remove_element('vc_wp_search');
vc_remove_element('vc_wp_custommenu');
vc_remove_element("vc_cta_button");
vc_remove_element("vc_cta_button2");
vc_remove_element("vc_button");
vc_remove_element("vc_facebook");
vc_remove_element("vc_tweetmeme");
vc_remove_element("vc_pinterest");
vc_remove_element("vc_flickr");
vc_remove_element("vc_progress_bar");
vc_remove_element("vc_pie");
vc_remove_element("vc_carousel");
vc_remove_element("vc_posts_slider");
vc_remove_element("vc_images_carousel");
vc_remove_element("vc_gallery");
vc_remove_element("vc_toggle");
vc_remove_element("vc_button2");
vc_remove_element("vc_custom_heading");
vc_remove_element("vc_posts_grid");
//vc_remove_element("vc_tabs");
vc_remove_element("vc_tour");



/*------------------------------------------------------*/
/* ROW
/*------------------------------------------------------*/
vc_remove_param("vc_row", "css");
vc_remove_param("vc_row", "full_width");
vc_remove_param("vc_row", "font_color");
vc_remove_param("vc_row", "el_class");
vc_remove_param("vc_row", "gap");
vc_remove_param("vc_row", "full_height");
vc_remove_param("vc_row", "el_id");
vc_remove_param("vc_row", "equal_height");
vc_remove_param("vc_row", "columns_placement");
vc_remove_param("vc_row", "content_placement");
vc_remove_param("vc_row", "video_bg");
vc_remove_param("vc_row", "video_bg_url");
vc_remove_param("vc_row", "video_bg_parallax");
vc_remove_param("vc_row", "parallax");
vc_remove_param("vc_row", "parallax_image");
vc_remove_param("vc_row", "parallax_speed_video");
vc_remove_param("vc_row", "parallax_speed_bg");
vc_remove_param("vc_row", "disable_element");

vc_add_param("vc_row", array(
	"type"        => "textfield",
	"class"       => "",
	"heading"     => __("Row ID","js_composer"),
	"param_name"  => "row_id",
	"value"       => "",
	"description" => __("Put your row ID here. This can then be used to target the row with CSS or as an anchor point to scroll to when the relevant link is clicked.","js_composer"),
));
vc_add_param("vc_row", array(
    "type"        => "icon",
    "class"       => "",
    "heading"     => __("Choose Icon","js_composer"),
    "param_name"  => "row_icon",
    "value"       => "",
    "description" => __("Choose icon to be displayed in heading area","js_composer"),
));
vc_add_param("vc_row", array(
    "type"       => "dropdown",
    "class"      => "",
    "heading"    => __("Icon Position", "js_composer"),
    "param_name" => "row_icon_position",
    "value"      => array(
        __("Above Title", "js_composer") => "title",
        __("With Description", "js_composer")    => "description"
    ),
));
vc_add_param("vc_row", array(
    "type"       => "dropdown",
    "class"      => "",
    "heading"    => __("Title Position", "js_composer"),
    "param_name" => "row_title_position",
    "value"      => array(
        __("Center", "js_composer") => "center",
        __("Left", "js_composer")    => "left"
    ),
));
vc_add_param("vc_row", array(
	"type"        => "textfield",
	"class"       => "",
	"heading"     => __("Row Title","js_composer"),
	"param_name"  => "row_title",
	"value"       => "",
	"description" => __("Put here row title","js_composer"),
));

vc_add_param("vc_row", array(
	"type"        => "textfield",
	"class"       => "",
	"heading"     => __("Row description","js_composer"),
	"param_name"  => "row_desc",
	"value"       => "",
	"description" => __("Put here row description","js_composer"),
));

vc_add_param("vc_row", array(
	"type"        => "colorpicker",
	"class"       => "",
	"heading"     => __("Title Color","js_composer"),
	"param_name"  => "title_color",
	"value"       => "",
	"description" => "",
	//"dependency"  => Array('element' => "row_style", 'value' => array('row_bg_color'))
));
vc_add_param("vc_row", array(
	"type"        => "colorpicker",
	"class"       => "",
	"heading"     => __("Title line Backgroud Color","js_composer"),
	"param_name"  => "titlebg_color",
	"value"       => "",
	"description" => "",
	//"dependency"  => Array('element' => "row_style", 'value' => array('row_bg_color'))
));
vc_add_param("vc_row", array(
	"type"       => "dropdown",
	"class"      => "",
	"heading"    => __("Row Type","js_composer"),
	"param_name" => "row_type",
	"value"      => array(
		__( "Center Content", "js_composer" )              => "row_center_content",
		__( "Full Width - Center Content", "js_composer" ) => "row_full_center_content",
        __( "Full Width", "js_composer" ) => "row_full_width_content",
	)
));


vc_add_param("vc_row", array(
	"type"        => "colorpicker",
	"class"       => "",
	"heading"     => __("Background Color","js_composer"),
	"param_name"  => "bg_color",
	"value"       => "",
	"description" => "",
	//"dependency"  => Array('element' => "row_style", 'value' => array('row_bg_color'))
));



// Backgroud Image : dependency by row_style
vc_add_param("vc_row", array(
	"type"        => "attach_image",
	"class"       => "",
	"heading"     => __("Background Image","js_composer"),
	"param_name"  => "bg_image",
	"value"       => "",
	"description" => "",
));

vc_add_param("vc_row", array(
	"type"       => "dropdown",
	"class"      => "",
	"heading"    => __("Background Repeat", "js_composer"),
	"param_name" => "bg_repeat",
	"value"      => array(
		__("No Repeat", "js_composer") => "no-repeat",
		__("Repeat", "js_composer")    => "repeat"
	),
));
vc_add_param("vc_row", array(
	"type"       => "dropdown",
	"class"      => "",
	"heading"    => __("Background Parallax", "js_composer"),
	"param_name" => "bg_attatch",
	"value"      => array(
		__("Not Parallax", "js_composer") => "scroll",
		__("Parallax", "js_composer")    => "fixed"
	),
));


vc_add_param("vc_row", array(
	"type"       => "textfield",
	"class"      => "",
	"heading"    => __("Extra Class Name", "js_composer"),
	"param_name" => "class",
	"value"      => "",
	"desc"       => __("This can then be used to target the row with CSS.","js_composer"),
));

