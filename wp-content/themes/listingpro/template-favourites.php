<?php
/**
 * Template name: Saved Listings
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */
 ?>
<?php get_header(); ?>

		<!--==================================Section Open=================================-->
	<section>
		<div class="container page-container-four">
		<?php
		$fav = getSaved();
					if(!empty($fav)) {
						?>
			<div class="row listing-page-result-row margin-bottom-25">
				<div class="col-md-12 col-sm-12  text-center">
					<p class="view-on-map">
						<!-- Marker icon by Icons8 -->
						<?php echo listingpro_icons('mapMarker'); ?>
						<a class="md-trigger mobilelink all-list-map" data-modal="modal-listing"><?php echo esc_html_e('View on map', 'listingpro'); ?></a>
					</p>
				</div>
			</div>
			
			<div class="mobile-map-space">
			
					<!-- Popup Open -->
	
					<div class="md-modal md-effect-3 mobilemap" id="modal-listing">
						<div class="md-content mapbilemap-content">
							<div class="map-pop">							
								<div id='map' class="listingmap"></div>							
							</div>
						<a class="md-close mapbilemap-close"><i class="fa fa-close"></i></a>
						</div>
					</div>
					<!-- Popup Close -->
					<div class="md-overlay md-overlayi"></div> <!-- Overlay for Popup -->
			</div>
			
			
			<div class="row lp-list-page-grid" id="content-grids" >
                <?php
                global  $listingpro_options;
                $listing_layout = $listingpro_options['listing_views'];
                if( $listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2' )
                {
                    $GLOBALS['call-from-favrt-temp']   =   'yes';
                    $listing_style  =   'list-style';
                    if( $listing_layout == 'grid_view_v2' )
                    {
                        $listing_style  =   'grid-style';
                    }
                ?>
                <div class="lp-listings <?php echo esc_attr($listing_style); ?>">
                    <div class="search-filter-response">
                        <div class="lp-listings-inner-wrap">
                <?php
                }

						$listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
				
						$args=array(
							'post_type' => 'listing',
							'post_status' => 'publish',
							'post__in' => $fav,						
						);
						
						$my_query = null;
						$my_query = new WP_Query($args);
						if( $my_query->have_posts() ) {
							while ($my_query->have_posts()) : $my_query->the_post(); 
							if( $listing_mobile_view == 'app_view' && wp_is_mobile() )
							 {
							  get_template_part('mobile/listing-loop-app-view');
							 }
							 else
							 {
							  get_template_part('listing-loop');
							 }							
							endwhile;
						}
					?>
				<div class="md-overlay"></div>
                <?php
                if( $listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2' )
                {
                    ?>
                        </div>
                    </div>
                    </div>
                    <?php
                }

                ?>

			</div>
			<?php
			}else{
							?>						
								<div class="text-center margin-top-80 margin-bottom-80">
									<h2><?php esc_html_e('No Results','listingpro'); ?></h2>
									<p><?php esc_html_e('Sorry! You have not selected any list as favorite.','listingpro'); ?></p>
									<p><?php esc_html_e('Go and select lists as favorite','listingpro'); ?>
										<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Visit Here','listingpro'); ?></a>
									</p>
								</div>									
							<?php
						}	
				?>
		</div>
	</section>
	<!--==================================Section Close=================================-->

<?php get_footer(); ?>