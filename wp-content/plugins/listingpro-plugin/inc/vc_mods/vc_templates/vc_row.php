<?php
extract(shortcode_atts(array(
    'row_id'                => '',
    'class'                 => '',
    'row_title'                 => '',
    'row_desc'                 => '',
    'row_type'              => 'row_center_content',
    'title_color'              => '',
    'desc_color'              => '',
    'bg_color'              => '',
    'bg_image'              => '',
    'bg_repeat'             => 'no-repeat',
    'bg_attatch'             => '',
    'disable_element'             => 'no',
    'row_title_position'             => 'center',
    'row_icon_position'             => 'title',
    'row_icon'             => '',

), $atts));

wp_enqueue_style( 'js_composer_front' );
wp_enqueue_script( 'wpb_composer_front_js' );
wp_enqueue_style('js_composer_custom_css');

// Row ID
$custom_row_id    = (!empty($row_id)) ? $row_id : uniqid("lp_");
$custom_row_class = (!empty($class)) ? $class : '';

// Row Type

$row_center_content = null;
$row_boxed = null;
$icon_markup    =   '';
$left_heading_class =   null;
$left_icon_class    =   '';
if ( !empty($row_type) && $row_type == 'row_full_center_content' ) {
    $row_center_content = 'container';
} else {
    $row_center_content = '';
}
if ($row_type == 'row_center_content' ) {
    $row_boxed = 'container';
}
if( $row_title_position == 'left' )
{
    $left_heading_class =   'left-heading';
}
if( !empty( $row_icon ) )
{
    $icon_markup    =   '<i class="fa fa-'. $row_icon .'" aria-hidden="true"></i>';
}
if( $row_icon_position == 'description' )
{
    $left_icon_class    =   'left-heading-icon';
}
$row_css = array();

if ( $bg_color ) {
    $row_css[] = 'background-color: '. $bg_color .';';
}

if ( $bg_image ) {
    $img_url = wp_get_attachment_url($bg_image);
    $row_css[] = 'background-image: url('. $img_url .');';
}


if ( $bg_repeat ) {
    $row_css[] = 'background-repeat: '. $bg_repeat .';';
}
if ( $bg_attatch ) {
    $row_css[] = 'background-attachment: '. $bg_attatch .';';
}

$row_css = implode('', $row_css);

if ( $row_css ) {
    $row_css = wp_kses( $row_css, array() );
    $row_css = ' style="' . esc_attr($row_css) . '"';
}


$rowHeading = '';

$DisplayRow = '';
if($disable_element == 'yes'){
    $DisplayRow = 'style="display:none"';
}

// Start VC Row
echo '
<div '.$DisplayRow.' id="'.$custom_row_id.'" class="lp-section-row '. $class .' ' . $left_heading_class .' '. $left_icon_class .'">';

// Row Wrapper
echo '
		<div class="lp_section_inner  clearfix '. $row_boxed .'" '. $row_css.'>';

echo '<div class="clearfix '. $row_center_content .'">';

// Row Inner
echo '<div class="row lp-section-content clearfix">';

if(!empty($row_title) || !empty($row_desc)){
    echo '<div class="lp-section-title-container text-center ">';

    if(!empty($row_title)){
        if( $row_icon_position == 'title' )
        {
            echo '<h2 style="color:'.$title_color.'">'. $icon_markup .' '.$row_title.'</h2>';
        }
        else
        {
            echo '<h2 style="color:'.$title_color.'">'.$row_title.'</h2>';
        }
    }
    if(!empty($row_desc)){
        if( $row_icon_position == 'description' )
        {
            echo '<div style="color:'.$desc_color.'" class="lp-sub-title">'. $icon_markup .' '.$row_desc.'</div>';
        }
        else
        {
            echo '<div style="color:'.$desc_color.'" class="lp-sub-title">'.$row_desc.'</div>';
        }

    }

    echo '</div>';
}
echo do_shortcode($content);

echo '</div>';


echo '</div>';


echo '
		</div>';

echo '
</div>';
