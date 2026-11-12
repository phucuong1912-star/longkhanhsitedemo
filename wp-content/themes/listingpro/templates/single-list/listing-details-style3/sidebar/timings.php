<?php
$buisness_hours = listing_get_metabox('business_hours');
$plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());
if(!empty($plan_id)){
    $plan_id = $plan_id;
}else{
    $plan_id = 'none';
}

$hours_show = get_post_meta( $plan_id, 'listingproc_bhours', true );

if($plan_id=="none"){
    $hours_show = 'true';
}

if( $buisness_hours && $hours_show != 'false' ):
?>
    <div class="lp-listing-timings lp-widget-inner-wrap">
        <?php get_template_part( 'include/timings' ); ?>
    </div>
    <?php
endif;