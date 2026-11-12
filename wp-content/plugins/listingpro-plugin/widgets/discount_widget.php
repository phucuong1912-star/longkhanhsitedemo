<?php
class LP_Discount_V2 extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'lp-discount-widget',
            'ListingPro - Discount',
            array('description' => 'Allows you to add coupon code in sidebar')
        );
    }
    public function widget($args, $instance)
    {
        if( !is_singular( 'listing' ) ) return false; //widget only works on listing detail page
        $post_author_id = get_post_field( 'post_author', get_the_ID() );
        $discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );
        if( $discount_displayin == 'content' || empty( $discount_displayin ) ) return false;
        global $listingpro_options;
        $discounts_dashoard =   true;
        if( isset( $listingpro_options['discounts_dashoard'] ) && $listingpro_options['discounts_dashoard'] == 0 )
        {
            $discounts_dashoard =   false;
        }
        if( $discounts_dashoard == false ) return false;
        get_template_part( 'templates/single-list/listing-details-style3/content/list-offer-deals-discount' );
    }
}
add_action('widgets_init', 'LP_Discount_V2_cb');
if(!function_exists('LP_Discount_V2_cb')) {
    function LP_Discount_V2_cb()
    {
        register_widget('LP_Discount_V2');
    }
}