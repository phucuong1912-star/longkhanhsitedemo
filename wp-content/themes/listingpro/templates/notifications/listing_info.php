<?php
$free_msg   =   false;
if(isset($_GET['p']) && isset($_GET['post_type']) && !is_admin()) {
    $listing_id =   wp_kses_post($_GET['p']);
    $plan_id    =   listing_get_metabox_by_ID('Plan_id', $listing_id);
    if(!$plan_id) {
        $free_msg   =   true;
    } else {
        $plan_price =   get_post_meta($plan_id, 'plan_price', true);
        if(empty($plan_price)) {
            $free_msg   =   true;
        }
    }

}
if($free_msg){
    echo esc_html__('Your listing is pending for review.', 'listingpro');
} else {
    echo esc_html__('Your listing is pending! Please proceed to make it published', 'listingpro');
}

?>