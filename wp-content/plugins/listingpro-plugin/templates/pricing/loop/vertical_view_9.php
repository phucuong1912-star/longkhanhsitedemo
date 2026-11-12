<?php
global $listingpro_options;
$listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
$perMonthPrce = lp_show_monthly_plan_price(get_the_ID());
$showAddListing = true;
if( isset($listing_access_only_users)&& $listing_access_only_users==1 ){
    $showAddListing = false;
    if(is_user_logged_in()){
        $showAddListing = true;
    }
}
if( $showAddListing==false ){
    wp_redirect(home_url());
    exit;
}

global $wpdb;
$lp_social_show;
$lp_social_show = $listingpro_options['listin_social_switch'];
$dbprefix = '';
$dbprefix = $wpdb->prefix;
$user_ID = '';
$user_ID = get_current_user_id();
$output1 = null;
$results = null;
$table_name = $dbprefix.'listing_orders';
$limitLefts = '';
$taxOn = $listingpro_options['lp_tax_swtich'];
$withtaxprice = false;
if($taxOn=="1"){
    $showtaxwithprice = $listingpro_options['lp_tax_with_plan_swtich'];
    if($showtaxwithprice=="1"){
        $withtaxprice = true;
    }
}

/* vertical view code */

global $post;
$plan_package_type = get_post_meta( get_the_ID(), 'plan_package_type', true );
$post_price = get_post_meta(get_the_ID(), 'plan_price', true);

$plan_time = '';
$plan_time = get_post_meta(get_the_ID(), 'plan_time', true);
$posts_allowed_in_plan = '';
$PostAllowedInPlan = get_post_meta(get_the_ID(), 'plan_text', true);
if(!empty($PostAllowedInPlan)){
    $posts_allowed_in_plan = get_post_meta(get_the_ID(), 'plan_text', true);
    $posts_allowed_in_plan = trim($posts_allowed_in_plan);
}
else{
    $posts_allowed_in_plan = 'unlimited';
}

$contact_show = get_post_meta( get_the_ID(), 'contact_show', true );
if($contact_show == 'true'){
    $contact_checked = 'checked';
}else{
    $contact_checked = 'unchecked';
}

$map_show = get_post_meta( get_the_ID(), 'map_show', true );
if($map_show == 'true'){
    $map_checked = 'checked';
}else{
    $map_checked = 'unchecked';
}

$video_show = get_post_meta( get_the_ID(), 'video_show', true );
if($video_show == 'true'){
    $video_checked = 'checked';
}else{
    $video_checked = 'unchecked';
}

$gallery_show = get_post_meta( get_the_ID(), 'gallery_show', true );
if($gallery_show == 'true'){
    $gallery_checked = 'checked';
}else{
    $gallery_checked = 'unchecked';
}

$listingproc_tagline = get_post_meta( get_the_ID(), 'listingproc_tagline', true );
if($listingproc_tagline == 'true'){
    $tagline_checked = 'checked';
}else{
    $tagline_checked = 'unchecked';
}

$listingproc_location = get_post_meta( get_the_ID(), 'listingproc_location', true );
if($listingproc_location == 'true'){
    $location_checked = 'checked';
}else{
    $location_checked = 'unchecked';
}

$listingproc_website = get_post_meta( get_the_ID(), 'listingproc_website', true );
if($listingproc_website == 'true'){
    $website_checked = 'checked';
}else{
    $website_checked = 'unchecked';
}

$listingproc_social = get_post_meta( get_the_ID(), 'listingproc_social', true );
if($listingproc_social == 'true'){
    $social_checked = 'checked';
}else{
    $social_checked = 'unchecked';
}

$listingproc_faq = get_post_meta( get_the_ID(), 'listingproc_faq', true );
if($listingproc_faq == 'true'){
    $faq_checked = 'checked';
}else{
    $faq_checked = 'unchecked';
}

$listingproc_price = get_post_meta( get_the_ID(), 'listingproc_price', true );
if($listingproc_price == 'true'){
    $price_checked = 'checked';
}else{
    $price_checked = 'unchecked';
}

$listingproc_tag_key = get_post_meta( get_the_ID(), 'listingproc_tag_key', true );
if($listingproc_tag_key == 'true'){
    $tag_key_checked = 'checked';
}else{
    $tag_key_checked = 'unchecked';
}

$listingproc_bhours = get_post_meta( get_the_ID(), 'listingproc_bhours', true );
if($listingproc_bhours == 'true'){
    $bhours_checked = 'checked';
}else{
    $bhours_checked = 'unchecked';
}

/* new options  */
$resurva_show = get_post_meta( get_the_ID(), 'listingproc_plan_reservera', true );
if($resurva_show == "true"){
    $resurva_show = 'checked';
}else{
    $resurva_show = 'unchecked';
}


$timekit_show = get_post_meta( get_the_ID(), 'listingproc_plan_timekit', true );
if($timekit_show == "true"){
    $timekit_show = 'checked';
}else{
    $timekit_show = 'unchecked';
}


$menu_show = get_post_meta( get_the_ID(), 'listingproc_plan_menu', true );
if($menu_show == "true"){
    $menu_show = 'checked';
}else{
    $menu_show = 'unchecked';
}


$announcment_show = get_post_meta( get_the_ID(), 'listingproc_plan_announcment', true );
if($announcment_show == "true"){
    $announcment_show = 'checked';
}else{
    $announcment_show = 'unchecked';
}


$deals_show = get_post_meta( get_the_ID(), 'listingproc_plan_deals', true );
if($deals_show == "true"){
    $deals_show = 'checked';
}else{
    $deals_show = 'unchecked';
}


$competitor_show = get_post_meta( get_the_ID(), 'listingproc_plan_campaigns', true );
if($competitor_show == "true"){
    $competitor_show = 'checked';
}else{
    $competitor_show = 'unchecked';
}


$featured_show = get_post_meta( get_the_ID(), 'lp_featured_imageplan', true );
if($featured_show == "true"){
    $featured_show = 'checked';
}else{
    $featured_show = 'unchecked';
}


$event_show = get_post_meta( get_the_ID(), 'lp_eventsplan', true );
if($event_show == "true"){
    $event_show = 'checked';
}else{
    $event_show = 'unchecked';
}
$bookings_show = get_post_meta( get_the_ID(), 'listingproc_bookings', true );
if($bookings_show == "true"){
    $bookings_show = 'checked';
}else{
    $bookings_show = 'unchecked';
}
$leadform_show = get_post_meta( get_the_ID(), 'listingproc_leadform', true );
if($leadform_show == "true"){
    $leadform_show = 'checked';
}else{
    $leadform_show = 'unchecked';
}
$google_ads_show = get_post_meta( get_the_ID(), 'lp_hidegooglead', true );
if($google_ads_show == "true"){
    $google_ads_show = 'checked';
}else{
    $google_ads_show = 'unchecked';
}
/* new options ends  */

$plan_hot = '';
$plan_hot = get_post_meta( get_the_ID(), 'plan_hot', true );

$plan_type = '';
$plan_type_name = '';
$plan_type = get_post_meta(get_the_ID(), 'plan_package_type', true);
if( $plan_type=="Pay Per Listing" ){
    $plan_type_name = esc_html__("Per Listing",'listingpro-plugin');
}
else{
    $plan_type_name = esc_html__("Per Package",'listingpro-plugin');
}

$currentPlanID = get_the_ID();
$plan_text = '';
$used = '';
$plan_limit_left = '';
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    $results = $wpdb->get_results( "SELECT * FROM ".$dbprefix."listing_orders WHERE user_id ='$user_ID' AND plan_id='$currentPlanID' AND status = 'success' AND plan_type='$plan_type'" );
}



if( !empty($results) && count($results)>0 ){
    $used = '';
    $used = $results[0]->used;

    if(is_numeric($posts_allowed_in_plan)){
        $plan_limit_left = (int)$posts_allowed_in_plan - (int)$used;
    }
    else{
        $plan_limit_left = 'unlimited';
    }

}
else{
    $plan_limit_left = $PostAllowedInPlan;
}

if( !empty ( $plan_package_type ) ){
    if( $plan_package_type=="Pay Per Listing" ){
        $plan_text = '';
    }
    else if( $plan_package_type=="Package" ){
        $plan_text = get_post_meta(get_the_ID(), 'plan_text', true);
        if( !empty($plan_text) && isset($plan_text) ){
            $plan_text = esc_html__('Max. listings allowed : ', 'listingpro-plugin').$plan_text;
        }
    }
}

$hotClass = '';
if(!empty($plan_hot) && $plan_hot=="true") {
    $hotClass = 'featured-plan';
}else {
    $hotClass = '';
}

// start centerlized plan
$classoffset = '';
if(isset($GLOBALS['plans_count'])){
    if($GLOBALS['plans_count'] == '1'){
        $classoffset = 'col-md-offset-4';
    }
    if($GLOBALS['plans_count'] == '2'){
        $classoffset = 'col-md-offset-2';
    }
}

//End centerlized plan
// class="featured-active-plan" for active plans

$output1 .='
							<div class="col-md-4 '.get_the_ID().' '.$hotClass.' view_version8 '.$classoffset.'">
								<div class="lp-price-main lp-border-radius-8 lp-border text-center">';

$user_ID = get_current_user_id();


if( !empty($plan_type) && $plan_type=="Package" ){
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $results = $wpdb->get_results( "SELECT * FROM ".$dbprefix."listing_orders WHERE user_id ='$user_ID' AND plan_id='$currentPlanID' AND status = 'success' AND plan_type='$plan_type'" );
    }

    if(is_numeric($plan_limit_left)){
        if( !empty($results) && count($results)>0 ){
            if(!empty($post_price) && $plan_limit_left>0){
                $output1 .='<div class="lp-sales-option">
																<div class="sales-offer">
																	'.esc_html__("Active",'listingpro-plugin').'
																</div>
															</div>';
            }
        }
    }
    else if(!empty($post_price) && $plan_limit_left=="unlimited"){
        if( !empty($plan_type) && $plan_type=="Package" ){
            if( !empty($results) && count($results)>0 ){
                $output1 .='<div class="lp-sales-option">
																<div class="sales-offer">
																	'.esc_html__("Active",'listingpro-plugin').'
																</div>
															</div>';
            }
        }
    }
}
$plan_title_color = '';
$plan_title_img =   '';
$plan_title_bg  =   '';

$plan_title_img = listing_get_metabox_by_ID('lp_price_plan_bg', get_the_ID());

$plan_title_color = get_post_meta(get_the_ID(), 'plan_title_color', true);
$classForBg = 'lp-title';
if( isset($plan_title_img) && $plan_title_img != '' )
{
    $plan_title_bg  =   "background: url($plan_title_img); background-size:cover;";
    $classForBg .= ' lp-overlay-pricing';
}
else
{
    $plan_title_bg  =   "background-color: $plan_title_color;";
}

$checktitle = get_the_title();
if(!empty($checktitle)){
    $output1 .='<h2 class="v8_heading_top">'.get_the_title().'</h2>';
}

$output1 .='
									
									
										<div class="lp-plane-top-wrape">
											';
if(!empty($post_price)){

    $pricewithCurr = null;
    $post_price = round($post_price,2);
    if($withtaxprice=="1"){
        $taxrate = $listingpro_options['lp_tax_amount'];
        $taxprice = (float)(($taxrate/100)*$post_price);
        $post_price = (float)$post_price + (float)$taxprice;
    }

    $lp_currency_position = $listingpro_options['pricingplan_currency_position'];
    if(isset($lp_currency_position) && $lp_currency_position=="left"){
        $pricewithCurr = listingpro_currency_sign().$post_price;
    }
    else{
        $pricewithCurr = $post_price.listingpro_currency_sign();
    }


    if(!empty($perMonthPrce)){
        $output1 .='<p class="v8_price_sign_free">'.$perMonthPrce.'</p>';

        $output1 .='<span class="package-type-v9">'.esc_html__("per month", "listingpro-plugin").'</span><br>';

        $output1 .='<span class="package-type-v9">'.$pricewithCurr.' '.esc_html__("billed Annually", "listingpro-plugin").'</span><br>';

    }else{
        $output1 .='<p class="v8_price_sign_free">'.$pricewithCurr.'</p>';
    }

}
else{
    $output1 .='<p class="v8_price_sign_free">'.esc_html__("Free", 'listingpro-plugin').'</p>';
}

if(!empty($plan_type_name)){
    $output1 .='<span class="package-type-v9">'.$plan_type_name.'</span>';
}

if(is_numeric($plan_limit_left)){
    if( !empty($results) && count($results)>0 ){
        if(!empty($post_price) && $plan_limit_left>0){
            $output1 .= '<span style="font-size:12px;color:#fff" class="allowedListing allowedListingv8">'.esc_html__('Remaining Listings : ', 'listingpro-plugin') . $plan_limit_left.'</span>';
        }
    }
}


$output1 .='	</div>';


$output1 .='
									<div class="lp-price-list">';



$output1 .='<ul class="lp-listprc">
											<li class="outer_tooltip_price">
												<span class="icon-text">
													<i class="awesome_plan_icon_check fa fa-check-circle"></i>
												</span>';
$output1 .= '<span>';
if( !empty($plan_time) ){
    $output1 .= esc_html__('Duration', 'listingpro-plugin').' : '.$plan_time.' '.esc_html__('days', 'listingpro-plugin');
}
else{
    $output1 .= esc_html__('Duration', 'listingpro-plugin');
    $output1 .= esc_html__(' : Unlimited days', 'listingpro-plugin');
}
$output1 .= '</span>';
$output1 .= '<div class="tooltip_price_features">
															<span><i class="fa fa-question"></i></span>
															<p class="lp_tooltip_text lp_tooltip_text2">'.esc_html__('Lorem ipsum dolor sit amet, lorem sit.', 'listingpro-plugin').'</p>
															</div>';
$output1 .='</li>';

$recurring_output = apply_filters('lp_price_plan_recurring_duration', '', get_the_ID(), 'vertical_view_9' );
$output1 .= $recurring_output;

if(!empty($posts_allowed_in_plan) && $plan_type=="Package"){
    $output1 .='<li class="outer_tooltip_price">';
    $output1 .='<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>';
    $output1 .= '<span>'.esc_html__('Max. Listings : ', 'listingpro-plugin'). $posts_allowed_in_plan.'</span>';
    $output1 .='</li>';
}

if(empty($posts_allowed_in_plan) && $plan_type=="Package"){
    $output1 .='<li>';
    $output1 .='<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>';
    $output1 .= '<span>'.esc_html__('Max. Listings : Unlimited', 'listingpro-plugin').'</span>';

    $output1 .='</li>';
}

if($listingpro_options['lp_showhide_address']=="1"){
    if(get_post_meta(get_the_ID(), 'map_show_hide', true)==''){
        $output1 .='
													<li>
														<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($map_checked).'</span>
														<span>'.esc_html__('Map Display', 'listingpro-plugin').'</span>
													</li>';
    }
}
if($listingpro_options['phone_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'contact_show_hide', true)==''){
        $output1 .='
															<li>
																<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($contact_checked).'</span>
																<span>'.esc_html__('Contact Display', 'listingpro-plugin').'</span>

															</li>';
    }
}
if($listingpro_options['file_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'gall_show_hide', true)==''){
        $output1 .='
														<li>
															<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($gallery_checked).'</span>
															<span>'.esc_html__('Image Gallery', 'listingpro-plugin').'</span>
														</li>
														';
    }
}
if($listingpro_options['vdo_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'video_show_hide', true)==''){
        $output1 .='
														<li>
															<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($video_checked).'</span>
															<span>'.esc_html__('Video', 'listingpro-plugin').'</span>
														</li>
														';
    }
}
if(get_post_meta(get_the_ID(), 'tagline_show_hide', true)==''){
    $output1 .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($tagline_checked).'</span>
													<span>'.esc_html__('Business Tagline', 'listingpro-plugin').'</span>
												</li>
												';
}
if($listingpro_options['location_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'location_show_hide', true)==''){
        $output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($location_checked).'</span>
															<span>'.esc_html__('Location', 'listingpro-plugin').'</span>
														</li>';
    }
}
if($listingpro_options['web_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'website_show_hide', true)==''){
        $output1 .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($website_checked).'</span>
														<span>'.esc_html__('Website', 'listingpro-plugin').'</span>
													</li>';
    }

}

if($listingpro_options['listin_social_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'social_show_hide', true)==''){
        $output1 .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($social_checked).'</span>
														<span>'.esc_html__('Social Links', 'listingpro-plugin').'</span>
													</li>
													';
    }
}
if($listingpro_options['faq_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'faqs_show_hide', true)==''){
        $output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($faq_checked).'</span>
															<span>'.esc_html__('FAQ', 'listingpro-plugin').'</span>
														</li>
														';
    }
}
if($listingpro_options['currency_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'price_show_hide', true)==''){
        $output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($price_checked).'</span>
															<span>'.esc_html__('Price Range', 'listingpro-plugin').'</span>
														</li>
														';
    }
}

if($listingpro_options['tags_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'tags_show_hide', true)==''){
        $output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($tag_key_checked).'</span>
															<span>'.esc_html__('Tags/Keywords', 'listingpro-plugin').'</span>
														</li>
														';
    }
}
if($listingpro_options['oph_switch']=="1"){
    if(get_post_meta(get_the_ID(), 'bhours_show_hide', true)==''){
        $output1 .='		
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($bhours_checked).'</span>
														<span>'.esc_html__('Business Hours', 'listingpro-plugin').'</span>
													</li>
													';
    }
}
/* new option */
if(lp_theme_option('lp_featured_file_switch')){
    if(get_post_meta(get_the_ID(), 'reserva_show_hide', true)==''){
        $output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($resurva_show).'</span>
															<span>'.esc_html__('Resurva', 'listingpro-plugin').'</span>
														</li>
														';
    }
}
if(get_post_meta(get_the_ID(), 'timekit_show_hide', true)==''){
    $output1 .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($timekit_show).'</span>
														<span>'.esc_html__('Timekit', 'listingpro-plugin').'</span>
													</li>
													';
}
if(get_post_meta(get_the_ID(), 'menu_show_hide', true)==''){

    $output1 .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($menu_show).'</span>
													<span>'.esc_html__('Menu', 'listingpro-plugin').'</span>
												</li>
												';
}
if(get_post_meta(get_the_ID(), 'announcment_show_hide', true)==''){
    $output1 .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($announcment_show).'</span>
													<span>'.esc_html__('Announcement', 'listingpro-plugin').'</span>
												</li>
												';
}
if(get_post_meta(get_the_ID(), 'deals_show_hide', true)==''){
    $output1 .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($deals_show).'</span>
													<span>'.esc_html__('Deals-Offers-Discounts', 'listingpro-plugin').'</span>
												</li>
												';
}
if(get_post_meta(get_the_ID(), 'metacampaign_show_hide', true)==''){
    $output1 .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($competitor_show).'</span>
													<span>'.esc_html__('Hide competitors Ads', 'listingpro-plugin').'</span>
												</li>
												';
}
if(get_post_meta(get_the_ID(), 'events_show_hide', true)==''){
    $output1 .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($event_show).'</span>
													<span>'.esc_html__('Events', 'listingpro-plugin').'</span>
												</li>
												';
}
if(get_post_meta(get_the_ID(), 'bookings_show_hide', true)==''){
    $output1 .='
                                                <li>
                                                    <span class="icon-text">'.listingpro_fontawesome_icon($bookings_show).'</span>
                                                    <span>'.esc_html__('Bookings', 'listingpro-plugin').'</span>
                                                </li>
                                                ';
}
if(get_post_meta(get_the_ID(), 'leadform_show_hide', true)==''){
    $output1 .='
                                                <li>
                                                    <span class="icon-text">'.listingpro_fontawesome_icon($leadform_show).'</span>
                                                    <span>'.esc_html__('Lead Form', 'listingpro-plugin').'</span>
                                                </li>
                                                ';
}
if(get_post_meta(get_the_ID(), 'googlead_show_hide', true)==''){
    $output1 .='
                                                <li>
                                                    <span class="icon-text">'.listingpro_fontawesome_icon($google_ads_show).'</span>
                                                    <span>'.esc_html__('Hide Google Ads', 'listingpro-plugin').'</span>
                                                </li>
                                                ';
}
/* new option emd */




$lp_plan_more_fields = listing_get_metabox_by_ID('lp_price_plan_addmore',get_the_ID());
if(!empty($lp_plan_more_fields)){
    foreach($lp_plan_more_fields as $morefield){
        if(!empty($morefield)){
            $output1 .='<li>
															<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>
															<span>'.$morefield.'</span>
														</li>';
        }
    }
}
$output1 .='
										</ul>';
$output1 .='<form method="post" action="'.listingpro_url('submit-listing').'" class="price-plan-button">
											<input type="hidden" name="plan_id" value="'.$post->ID.'" />';

if(empty($post_price) && $plan_type=="Package"){
    $output1 .='<p>A <strong>'.esc_html__("Package",'listingpro-plugin').'</strong>'.esc_html__(" should have a price ",'listingpro-plugin').'</p>';

}
else if( !empty($plan_type) && $plan_type=="Package" ){
    if(!empty($plan_limit_left)){

        $output1 .='<input id="submit'.$post->ID.'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('CHOOSE PLAN', 'listingpro-plugin').'" name="submit">';
    }
    else{
        $output1 .='<input id="submit'.$post->ID.'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('CHOOSE PLAN', 'listingpro-plugin').'" name="submit">';
    }
}
else{

    $output1 .='<input id="submit'.$post->ID.'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('CHOOSE PLAN', 'listingpro-plugin').'" name="submit">';


}
$output1 .= wp_nonce_field( 'price_nonce', 'price_nonce_field'.$post->ID ,true, false );

if(isset($_POST['lp_cat_plan_submit'])){
    $lp_s_cat= $_POST['lp-slected-plan-cat'];
    $output1 .= '<input type="hidden" value="'.$lp_s_cat.'" name="lp_pre_selected_cats" />';
}

$output1 .='
										</form>
									</div>
								</div>
							</div>';
echo $output1;
?>