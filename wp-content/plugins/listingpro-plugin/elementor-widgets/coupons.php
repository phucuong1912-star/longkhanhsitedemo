<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listing_Coupons extends Widget_Base {

    public function get_name() {
        return 'listing-coupons';
    }

    public function get_title() {
        return __( 'LP Coupons', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );

        $this->add_control(
            'listings',
            [
				'label' => __( 'Enter listings id (Comma Separated)', 'text-domain' ),
				'type' => Controls_Manager::TEXT,
            ]
        );
		$this->add_control(
            'style',
            [
				'label' => __( 'Select Style', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => array('style_1'=> 'Style 1','style_2'=> 'Style 2'),
				'default' => 'style_1'
            ]
        );
        $this->add_control(
            'number_posts',
            [
                'label' => __( 'Posts per page', 'elementor-listingpro' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '3'
            ]
        );
        $this->end_controls_section();
    }
	// Function to retrieve a list of available listings
	private function get_listing_options() {
		$args = array(
			'post_type' => 'listing',
			'posts_per_page' => -1,
		);

		$listings = get_posts( $args );
		$options = array();

		foreach ( $listings as $listing ) {
			$options[ $listing->ID ] = $listing->post_title;
		}

		return $options;
	}
    protected function render() {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_listing_coupons( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_listing_coupons')) {
    function listingpro_shortcode_listing_coupons($atts, $content = null) {
        extract(shortcode_atts(array(
            'listings'   => 'listings',
            'number_posts'   => '3',
            'style'   => 'style_1',
        ), $atts));
		if( !is_array( $listings )){
			$listings = explode( ',', $listings);
		}
		$coupon_query =  new \WP_Query( array(
			'post__in' => $listings,
			'post_type' => 'listing'
		) );
		global $coupon_count;
		$coupon_count = 0;
		require_once (THEME_PATH . "/include/aq_resizer.php");
		echo '<div class="row">';
		if( $coupon_query->have_posts() ) {
			while ($coupon_query->have_posts()) : $coupon_query->the_post();
				$listing_discount_data = get_post_meta( get_the_ID(), 'listing_discount_data', true );
				lp_coupon_grid_style( $listing_discount_data , $style , $number_posts );
			endwhile;
			echo '</div>';
		}
    }
}
if(!function_exists('lp_coupon_grid_style')) {
	function lp_coupon_grid_style( $listing_discount_data , $style , $number_posts){
		global $coupon_count;
		if(  $style == 'style_1' ){
			if( isset( $listing_discount_data ) && is_array( $listing_discount_data ) && !empty( $listing_discount_data ) ):
				$timezone_string = get_option('timezone_string');
				if(isset($timezone_string) && !empty($timezone_string)){
					date_default_timezone_set($timezone_string);
				}
				$strNow =  strtotime("NOW");
				$discount_counter   =   1;
				$nopadding  =   'no-padding-sidebar';
				$post_author_id = get_post_field( 'post_author', get_the_ID() );
				$discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );
				$col_class  =   'col-md-4';
				foreach ( $listing_discount_data as $key => $discount_data ):
					$couponExpiryE = null;
					$couponExpiryS = null;
					if ((isset($discount_data['disExpE']) && isset($discount_data['disTimeE'])) && (!empty($discount_data['disExpE']) && !empty($discount_data['disTimeE']))) {
						$couponExpiryE = coupon_timestamp($discount_data['disExpE'], $discount_data['disTimeE']);
						$couponExpiryS = coupon_timestamp($discount_data['disExpS'], $discount_data['disTimeS']);
					}
					if( ( $strNow < $couponExpiryE || empty( $discount_data['disExpE'] ) ) && ( $strNow > $couponExpiryS || empty( $discount_data['disExpS'] ) ) ) :
						$img_url    =   'https://placehold.co/100x100';
						if( !empty( $discount_data['disImg'] ) )
						{
							$img_url  = aq_resize( $discount_data['disImg'], '100', '100', true, true, true);
						}
						if( $coupon_count >= $number_posts){
							break;
						}
						?>
						<div class="<?php echo esc_attr( $col_class ); ?>">
							<div class="lp-widget lp-discount-widget">
									<div class="lp-discount-top">
										<?php
										if( $discount_data['disOff'] ) echo '<span class="lp-discount-thumb-tagline">'. $discount_data['disOff'] .'</span>';
										?>
										<div class="lp-discount-thumb">
											<img src="<?php echo esc_attr( $img_url ); ?>" alt="<?php echo esc_attr( $discount_data['disHea'] ); ?>">
										</div>
									</div>
									<div class="lp-discount-bottom">
										<?php
										if( !empty( $discount_data['disHea'] ) ){ echo '<strong class="dishead">'. $discount_data['disHea'] .'</strong>'; }
										?>
										<?php
										if( !empty( $discount_data['disDes'] ) ){ echo html_entity_decode($discount_data['disDes']); }
										?>
										<?php
										if( isset( $discount_data['disExpE'] ) && isset( $discount_data['disTimeE'] ) && !empty( $discount_data['disExpE'] ) && !empty( $discount_data['disTimeE'] ) ) :
											$couponExpiry  = coupon_timestamp($discount_data['disExpE'],$discount_data['disTimeE']);
											?>
											<p><strong><?php echo esc_html__( 'Validity:', 'listingpro' ); ?></strong> <?php echo date_i18n( get_option('date_format'), $discount_data['disExpE'] ); ?></p>
											<div class="lp-discount-count-wrap">
												<div id="lp-discount-countdown-<?php echo esc_attr( $discount_counter ); ?>" class="lp-discount-countdown lp-countdown"
													 data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>"
														data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>"
														data-label-mints="<?php echo esc_html__('min', 'listingpro') ?>"												
													 data-minute = "<?php echo date( 'i', $couponExpiry ); ?>"												
													 data-hour = "<?php echo date( 'H', $couponExpiry ); ?>"											 
													 data-day="<?php echo date('d', $couponExpiry ); ?>"
													 data-month="<?php echo date('m', $couponExpiry )-1; ?>"
													 data-year="<?php echo date('Y', $couponExpiry ); ?>"></div>
											</div>
										<?php endif; ?>
									</div>
									<?php
									$bnt_text_def   =   esc_html__( 'Click Here', 'listingpro' );
									   if( empty( $discount_data['disBT'] ) )
									   {
										   $discount_data['disBT'] =   $bnt_text_def;
									   }
									if( isset( $discount_data['disBT'] ) && !empty( $discount_data['disBT'] ) ):
										?>
										<a data-html="<?php echo esc_attr( $discount_data['disBT'] ) ; ?>" data-target-code="dicount-copy-<?php echo esc_attr( $discount_counter ); ?>" <?php if( isset( $discount_data['disBL'] ) && !empty( $discount_data['disBL'] ) ): echo 'target="_blank" href="'. $discount_data['disBL'] .'"';  endif; ?> class="lp-discount-btn <?php if( empty( $discount_data['disBL'] ) ){ echo 'lp-copy-code'; } ?>"><?php echo esc_attr( $discount_data['disBT']) ; ?></a>
									<?php endif; ?>
							<?php
							if( empty( $discount_data['disBL'] ) ):
								?>
									<div class="dis-code-copy-pop extra-bottom dicount-copy-<?php echo esc_attr( $discount_counter ); ?>" id="dicount-copy-<?php echo esc_attr( $discount_counter ); ?>">
										<div class="dis-code-copy-pop-inner">
											<div class="dis-code-copy-pop-inner-cell">
												<p><?php echo esc_html__( 'Copy to clipboard', 'listingpro' ); ?></p>
												<p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo esc_attr( $discount_counter ); ?>" type="text" value="<?php echo esc_attr( $discount_data['disCod'] ); ?>"> <a data-target-code="dicount-copy-<?php echo esc_attr( $discount_counter ); ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__( 'Copied', 'listingpro' ); ?>"><?php echo esc_html__( 'Copy', 'listingpro' ); ?></a></p>
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
		}else{
			$listing_discount_data  =   get_post_meta( get_the_ID(), 'listing_discount_data', true );
			$listing_discount_data_final    =   array();
			$timezone_string = get_option('timezone_string');
			if(isset($timezone_string) && !empty($timezone_string)){
				date_default_timezone_set($timezone_string);
			}
			$strNow =  strtotime("NOW");
			if( !empty( $listing_discount_data ) ):
				foreach ( $listing_discount_data as $key => $val )
				{
					if( isset($val['disExpE'] ) && isset($val['disTimeE']) && isset($val['disTimeS']) ) :

							$couponExpiryE  = coupon_timestamp($val['disExpE'],$val['disTimeE']);

							$couponExpiryS  = coupon_timestamp($val['disExpS'],$val['disTimeS']);

						if( ( $strNow < $couponExpiryE || empty( $discount_data['disExpE'] ) ) && ( $strNow > $couponExpiryS || empty( $discount_data['disExpS'] ) ) ) :
							$listing_discount_data_final[]  =   $val;
						endif;
					endif;
				}
				if( is_array( $listing_discount_data_final ) && !empty( $listing_discount_data_final ) ):
					require_once (THEME_PATH . "/include/aq_resizer.php");
					?>
					<?php
					$deal_counter   =   1;
					$total_deals    =   count( $listing_discount_data_final );
					$col_class      =   'col-md-4';
					$post_author_id = get_post_field( 'post_author', get_the_ID() );
					foreach ( $listing_discount_data_final as $key => $discount_data ):
						if( $coupon_count >= $number_posts){
							break;
						}
							?>
							<div class="lp-deals-wrap margin-0">
						<?php
						$img_url    =   'https://placehold.co/360x260';
						if( $discount_data['disImg'] )
						{
							$img_url    =   $discount_data['disImg'];
							$img_url  = aq_resize( $img_url, '360', '260', true, true, true);
						}
	?>
						<div class="<?php echo esc_attr( $col_class ); ?>">
							<div class="lp-deal">
								<div class="deal-thumb">
									<img alt="image" src="<?php echo esc_attr( $img_url ); ?>">
								</div>
								<div class="deal-details">
									<?php
									if( !empty( $discount_data['disExpE'] ) ):
										$exTime = strtotime('12:00 AM');
										if (!empty($discount_data['disTimeE'])) {
											$exTime = strtotime($discount_data['disTimeE']);
										}
										$couponExpiry  = coupon_timestamp($discount_data['disExpE'],$exTime);
										?>
										<div class="deal-countdown-wrap">
											<div id="lp-deals-countdown<?php echo esc_attr( $deal_counter ); ?>" class="lp-countdown lp-deals-countdown<?php echo esc_attr( $deal_counter ); ?>"
												 data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>"
												data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>"
												data-label-mints="<?php echo esc_html__('min', 'listingpro') ?>"
												 data-minute = "<?php echo date( 'i', $couponExpiry ); ?>"
												 data-hour = "<?php echo date( 'H', $couponExpiry ); ?>"
												 data-day="<?php echo date( 'd', $couponExpiry ); ?>"
												 data-month="<?php echo date( 'm', $couponExpiry )-1; ?>"
												 data-year="<?php echo date( 'Y', $couponExpiry ); ?>"></div>
										</div>
									<?php endif; 
									if( $discount_data['disOff'] ) echo '<span class="lp-deal-off">'. $discount_data['disOff'] .'</span>';
									?>
									<div class="deal-content">
										<?php
										if( $discount_data['disHea'] ) echo '<strong>'. $discount_data['disHea'] .'</strong>';
										html_entity_decode(mb_substr( $discount_data['disDes'], 0, 35 ));
										global $listingpro_options;
										$listing_mobile_view            =   $listingpro_options['single_listing_mobile_view'];
										//if( $discount_data['disDes'] ) echo '<p>'. html_entity_decode(mb_substr( $discount_data['disDes'], 0, 35 )) .'</p>';
										if( $listing_mobile_view == 'app_view' && wp_is_mobile() ){

										}
										else{

											if( $discount_data['disDes'] ) echo '<p>'. html_entity_decode($discount_data['disDes']) .'</p>';
										}
										$btn_href   =   '';
										$btn_class  =   'lp-copy-code';
										$bnt_text_def   =   esc_html__( 'Click Here', 'listingpro' );
									   if( empty( $discount_data['disBT'] ) )
									   {
										   $discount_data['disBT'] =   $bnt_text_def;
									   }
										if( $discount_data['disBL'] && !empty( $discount_data['disBL'] ) )
										{
											$btn_href   =   'href="'. $discount_data['disBL'] .'"';
											$btn_class  =   '';
										}
										?>
										<a target="_blank" data-html="<?php echo esc_attr( $discount_data['disBT'] ); ?>" data-target-code="deal-copy-<?php echo esc_attr( $deal_counter ); ?>" <?php echo wp_kses_post( $btn_href ); ?> class="deal-button <?php echo esc_attr( $btn_class ); ?>"><i class="fa fa-gavel" aria-hidden="true"></i> <?php echo esc_attr( $discount_data['disBT'] ); ?></a>
									</div>
								</div>
								<?php
								if( empty( $discount_data['disBL'] ) ):
									?>
									<div class="dis-code-copy-pop deal-copy-<?php echo esc_attr( $deal_counter ); ?>" id="dicount-copy-<?php echo esc_attr( $deal_counter ); ?>">
										<span class="close-right-icon" data-target="deal-copy-<?php echo esc_attr( $deal_counter ); ?>"><i class="fa fa-times"></i></span>
										<div class="dis-code-copy-pop-inner">
											<div class="dis-code-copy-pop-inner-cell">
												<p><?php echo esc_html__( 'Copy to clipboard', 'listingpro' ); ?></p>
												<p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo esc_attr( $deal_counter ); ?>" type="text" value="<?php echo esc_attr( $discount_data['disCod'] ); ?>"> <a data-target-code="dicount-copy-<?php echo esc_attr( $deal_counter ); ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__( 'Copied', 'listingpro' ); ?>"><?php echo esc_html__( 'Copy', 'listingpro' ); ?></a></p>
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