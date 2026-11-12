<?php
	global $listingpro_options;
	$lp_detail_page_additional_detail_position = $listingpro_options['lp_detail_page_additional_styles'];
	if($lp_detail_page_additional_detail_position == 'left' ){
		echo listing_all_extra_fields($post->ID);
	}
?>