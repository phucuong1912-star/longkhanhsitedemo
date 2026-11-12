<?php

/**
 * Template part for footer company info.
 * templates/footers/company-info
 * @version 2.0
*/
 
    $copy_right = lp_theme_option('copy_right');
    $footer_address = lp_theme_option('footer_address');
    $phone_number = lp_theme_option('phone_number');
    $author_info = lp_theme_option('author_info');
?>
<ul class="footer-about-company">
<?php

if( $copy_right ){
	echo '<li>'.wp_kses_post($copy_right).'</li>';
}
?>
<?php
if( $footer_address ){
	echo '<li>'.wp_kses_post($footer_address).'</li>';
}
?>
<?php
if( $phone_number ){
	echo '<li>'.esc_html__('Tel', 'listingpro').' '.wp_kses_post($phone_number).'</li>';
}
?>
</ul>
<?php
if( $author_info ){
	echo '<p class="credit-links">'.wp_kses_post($author_info).'</p>';
}
?>