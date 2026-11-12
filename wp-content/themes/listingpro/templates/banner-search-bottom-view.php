	<?php
	global $listingpro_options;

	//$searchstyle = lp_get_search_views();

	$searchstyle = $listingpro_options['search_different_styles_banner_search_overlap'];

	$searchstyle_inside = $listingpro_options['search_different_styles_banner_search_inside'];

	if ( is_front_page() && is_home()  )
	{
		
	
	?>
		<div class="page-heading listing-page">
			<div class="page-heading-inner-container text-center">
				<h1><?php _e('Home', 'listingpro') ?></h1>
			</div>
			<div class="page-header-overlay"></div>
		</div>
	<?php 
	} elseif ( is_home() ) {
	?>
		<div class="page-heading listing-page">
			<div class="page-heading-inner-container text-center">
				<h1>
					<?php
						$queried_object = get_queried_object();
						echo single_post_title('', FALSE);
					?>
				</h1>
				<ul class="breadcrumbs">
					<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'listingpro') ?></a></li>
					<li><span><?php _e('Blog', 'listingpro') ?></span></li>
				</ul>
			</div>
			<div class="page-header-overlay"></div>
		</div>
	<?php 
	}elseif ( is_archive() ) {
		if(is_tax('location') || is_tax('listing-category') || is_tax('list-tags') || is_tax('features') ){
			$listing_style = $listingpro_options['listing_style'];
			if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
				$listing_style = esc_html($_GET['list-style']);
			}
			
			
			if($listing_style == '1'){
		?>
		<?php if( is_tax('listing-category') || is_tax('list-tags') ){ ?>
			<?php
				$queried_object = get_queried_object();
				$term_id = $queried_object->term_id;
				$category_banner = listing_get_tax_meta($term_id,'category','banner');
				if($category_banner){
			?>
				<div class="page-heading listing-page" style="background:url(<?php echo esc_attr($category_banner); ?>);">
			<?php
				} else{
			?>
				<div class="page-heading listing-page">
			<?php
				} 
			?>
				
		<?php  } else { ?>
				<div class="page-heading listing-page">
		<?php } ?>
				<div class="page-heading-inner-container cat-area">
					<div class="container">
						<div class="row">
							<div class="col-md-6 col-sm-6">
								<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
							</div>
							<div class="col-md-6 col-sm-6 text-right">
								<p class="view-on-map">
									<!-- Marker icon by Icons8 -->
									<?php echo listingpro_icons('whiteMapMarkerFill'); ?>
									<a class="md-trigger mobilelink all-list-map" data-modal="modal-listing"><?php echo esc_html_e('View on map', 'listingpro'); ?></a>
								</p>
								<?php
                                    $grid_list_switch   =   $listingpro_options['hide_grid_switcher'];
                                    if( $grid_list_switch == 'no' ) {
                                        ?>
                                        <div class="listing-view-layout">
                                            <ul>
                                                <li><a class="grid" href="#"><i class="fa fa-th"></i></a></li>
                                                <li><a class="list" href="#"><i class="fa fa-list-ul"></i></a></li>
                                            </ul>
                                        </div>
                                        <?php
                                    }
                                ?>
							</div>
						</div>
					</div>
				</div>
				<div class="page-header-overlay"></div>
			</div>
		<?php 
			}
		}elseif(is_author(get_queried_object_id())){			
		?>
			<div class="page-heading listing-page">
				<div class="page-heading-inner-container text-center">
					<h1><?php echo get_the_archive_title(get_queried_object_id()); ?></h1>
					<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
				</div>
				<div class="page-header-overlay"></div>
			</div> 
		 
		<?php
		}else{
			$showPageTitle = $listingpro_options['lp_showhide_pagetitle'];
			if($showPageTitle=="1"){
				?>
					<div class="page-heading listing-page">
						<div class="page-heading-inner-container text-center">
							<h1><?php echo the_archive_title(); ?></h1>
							<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
						</div>
						<div class="page-header-overlay"></div>
					</div> 
				<?php 
			}
		}
		
	}elseif ( is_front_page() ) {

		$topBannerView = lp_get_banner_style();

		$top_title = $listingpro_options['top_title'];
		$top_main_title = $listingpro_options['top_main_title'];
		$main_text = $listingpro_options['main_text'];
		$map_height = $listingpro_options['map_height'];
		$arrow_image = $listingpro_options['arrow_image'];
		$locationType = 'withip';
		
		$courtesySwitch = $listingpro_options['courtesy_switcher'];
		if($courtesySwitch == 1) {
			$courtesyListing = $listingpro_options['courtesy_listing'];
		}
		$height = '';
		if ( !empty($map_height) ) {
			$height = ' style="height:'.$map_height.'px;"';
		}else {
			$height = ' style="height:500px;"';
		}
		if( $topBannerView == 'map_view' ) {
		?>

		<div class="lp_home" id="homeMap" <?php echo wp_kses_post($height); ?>></div>
		
		<?php } elseif( $topBannerView == 'banner_view_search_bottom' || $topBannerView == 'banner_view_search_inside') 

		{ ?>
		


				<?php
			$videosearchlayout = $listingpro_options['video_search_layout'];
			$videoBanner = $listingpro_options['lp_video_banner_on'];
			$video_banner_img = $listingpro_options['video_banner_img']['url'];
			if($videoBanner=="video_banner"){
                $video_src  =   $listingpro_options['vedio_type'];
			 $vedio_url = $listingpro_options['vedio_url'];
                $vedio_url_yt = $listingpro_options['vedio_url_yt'];
				if(!empty($vedio_url) || !empty($vedio_url_yt)){
				$outputEmbed =  preg_replace(    "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","$2",$vedio_url_yt);
		   ?>

			 <div class="video-lp" data-videoid="<?php echo wp_kses_post($outputEmbed); ?>">
                 <?php
                 if( $video_src == 'video_mp4' ):
                 ?>
                 <video id="lp_vedio" muted autoplay="autoplay" loop="loop" width="0" height="0" poster="<?php echo wp_kses_post($video_banner_img);?>">
			  <source src="<?php echo esc_url($vedio_url);?>" type="video/webm" />
			  <source src="<?php echo esc_url($vedio_url);?>" type="video/mp4" />
			  <source src="<?php echo esc_url($vedio_url);?>" type="video/ogg" />
			 </video>
                 <?php
                 else:
                    echo '<div id="player" style="width: 100%; height: 100%;"></div>';
                 endif;
                 ?>
			 </div>
		   <?php
			 }
			}
			
		   
		   ?>
		   
		   <div class="lp-home-banner-contianer lp-home-banner-with-loc lp-home-banner-contianer-1">
		   
			<div class="page-header-overlay"></div>
			<?php if($courtesySwitch == 1) { ?>
			 <div class="img-curtasy">
			  <p><?php esc_html_e('Image courtesy of','listingpro'); ?> <span><a href="<?php echo get_the_permalink($courtesyListing); ?>"><?php echo get_the_title($courtesyListing); ?> <i class="fa fa-angle-right"></i></a></span></p>
			 </div>
			<?php } ?>
			
			<?php if( $videosearchlayout == 'align_center' ) { ?>
			
			<div class="lp-home-banner-contianer-inner lp-home-banner-contianer-inner-new">
			 <div class="container">
			  <div class="row">
			   <div class="col-md-12 col-sm-12 text-center lp_auto_loc_container">
					<?php if(!empty($top_main_title)) { ?>
					 <h1 data-locnmethod="<?php echo esc_attr($locationType); ?>"><?php echo wp_kses_post($top_main_title); ?></h1>
					<?php } ?>
					<?php if(!empty($top_title)) { ?>
					 <p class="lp-banner-browse-txt"><?php echo wp_kses_post($top_title); ?></p>
					<?php } ?>
			   </div>

			  </div>
			 </div>
			</div>

			<?php
				if($topBannerView == 'banner_view_search_bottom'){			
					// add class to search move down
					$addclass_for_search_up_down_view = '';
					if($topBannerView =='banner_view_search_bottom' && $searchstyle == 'search_view2'){

					$addclass_for_search_up_down_view = 'search-view-move-down';

					}elseif($topBannerView =='banner_view_search_bottom' && $searchstyle == 'search_view3'){

						$addclass_for_search_up_down_view = 'search-view-move-down';
					}
					elseif($topBannerView =='banner_view_search_bottom' && $searchstyle == 'search_view1'){

						$addclass_for_search_up_down_view = 'search-view-move-down-two';
					}elseif($topBannerView =='banner_view_search_bottom' &&  $searchstyle == 'search_view'){
						$addclass_for_search_up_down_view = 'search-view-move-down-two';
					}
				}

				if($topBannerView == 'banner_view_search_inside'){
					$addclass_for_search_up_down_view = '';
					if($topBannerView =='banner_view_search_bottom' && $searchstyle_inside == 'search_view2'){

					$addclass_for_search_up_down_view = 'search-view-move-down';
					}elseif($topBannerView =='banner_view_search_bottom' && $searchstyle_inside == 'search_view3'){

						$addclass_for_search_up_down_view = 'search-view-move-down';
					}elseif($topBannerView =='banner_view_search_bottom' && $searchstyle_inside == 'search_view1'){

						$addclass_for_search_up_down_view = 'search-view-move-down-two';
					}elseif($topBannerView =='banner_view_search_bottom' &&  $searchstyle_inside == 'search_view'){
						$addclass_for_search_up_down_view = 'search-view-move-down-two';
					}
				}
			?>

			


			

			<div class="lp-home-banner-contianer-inner lp-home-banner-contianer-inner-new-search <?php echo esc_attr($addclass_for_search_up_down_view); ?>">
				<div class="container">
			  	<div class="row">
				<div class="col-md-8 col-xs-12 col-md-offset-2 col-sm-offset-0">
			   		

					
				   	<?php //echo esc_attr($searchstyle);

				   	if($topBannerView == 'banner_view_search_bottom'){
					   	if($searchstyle == '' || $searchstyle == 'search_view'){

					   		get_template_part( 'templates/search/home-search');

					   	}elseif($searchstyle == 'search_view1'){

					   		get_template_part( 'templates/search/home-search-view1');

					   	}elseif($searchstyle == 'search_view2'){

					   		get_template_part( 'templates/search/home-search-view2');

					   	}elseif($searchstyle == 'search_view3'){

					   		get_template_part( 'templates/search/home-search-view3');
					   	}
				   	}
				   	

				   	 //echo esc_attr($searchstyle_inside)

				   	if($topBannerView == 'banner_view_search_inside'){

					   	if($searchstyle_inside == '' || $searchstyle_inside == 'search_view'){

					   		get_template_part( 'templates/search/home-search');
					   	}elseif($searchstyle_inside == 'search_view1'){

					   		get_template_part( 'templates/search/home-search-view1');

					   	}elseif($searchstyle_inside == 'search_view2'){

					   		get_template_part( 'templates/search/home-search-view2');

					   	}elseif($searchstyle_inside == 'search_view3'){

					   		get_template_part( 'templates/search/home-search-view3');
					   	}
				   	}

				   	?>

				   

					<?php //get_template_part( 'templates/search/home-search'); ?>
				

				<div class="text-center lp-search-description">
						 <?php if(!empty($main_text)) { ?>
						  <p><?php echo wp_kses_post($main_text); ?></p>
						 <?php } ?>
						 <?php if($arrow_image == 1) { ?> 
						 
						 <?php 
                            $lp_add_class_hide = '';
                            if(empty($main_text)){
                                $lp_add_class_hide = 'banner-arrow-st';
                            } ?>

						 <img src="<?php echo get_template_directory_uri(); ?>/assets/images/banner-arrow.png" alt="banner-arrow" class="banner-arrow <?php echo esc_attr($lp_add_class_hide); ?>" />
						 <?php }?> 
					</div>
			   </div>
			</div>
			</div>
			</div>


		   <?php } ?>
			
		   </div><!-- ../Home Search Container -->
		   <?php if( $videosearchlayout == 'align_bottom_video'){?>
		   <div class="lp-home-banner-contianer lp-home-banner-contianer-inner-video-outer">
		   <div class="lp-home-banner-contianer-inner-video">
			<div class="clearfix">
			 <div class="col-md-3 col-xs-12 padding-0">
			  <div class="video-bottom-search-content">
			<?php if(!empty($top_main_title)) { ?>
			  <h3 data-locnmethod="<?php echo esc_attr($locationType); ?>"><?php echo esc_attr($top_main_title); ?></h3>
			<?php } ?>
			
			  </div>
			 </div>
			 <div class="col-md-9 col-xs-12 padding-0">
			 <div class="video-bottom-search-container">
			  <?php if(!empty($top_title)) { ?>
			 <p class="lp-banner-browse-txt"><?php echo wp_kses_post($top_title); ?></p>
			  <?php } ?>
			  <?php get_template_part( 'templates/search/home-search'); ?>
			 </div>
			 </div>
			</div>
		   </div>
			 </div>
			
		   <?php }?>
		<?php } ?>




		
		

		<?php
	}elseif ( is_page()  ) {
		$showPageTitle = $listingpro_options['lp_showhide_pagetitle'];
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			if(has_shortcode( get_the_content(), 'vc_row' ) && has_shortcode( get_the_content(), 'listingpro_promotional' )){
				
			}else{
				if($showPageTitle=="1"){
			?>	
				<div class="page-heading listing-page">
					<div class="page-heading-inner-container text-center">
						<h1><?php echo get_the_title(); ?></h1>
						<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
					</div>
					<div class="page-header-overlay"></div>
				</div>
			<?php
				}
			}
		endwhile; endif; 
		
		
	 
	}elseif ( is_search() ) {
		$listing_style = $listingpro_options['listing_style'];
		if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
			$listing_style = esc_html($_GET['list-style']);
		}
			
		if($listing_style == '1' && isset($_GET['post_type']) && $_GET['post_type'] == 'listing'){
		$sterm = '';	
		$sloc = '';	
		$termName = '';	
		$locName = '';	
		if(isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])){
			$sterm = esc_html($_GET['lp_s_cat']);
			$termo = get_term_by('id', $sterm, 'listing-category');
			$termName = 'Best '.$termo->name;
		}	
		if(isset($_GET['lp_s_cat']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])){
			$sterm = esc_html($_GET['lp_s_tag']);
			$termo = get_term_by('id', $sterm, 'list-tags');
			$termName = 'Results For '.$termo->name;
		}
		if(isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])){
			$sloc = esc_html($_GET['lp_s_loc']);
			
			$checkTerm = listingpro_term_exist($sloc,'location');
			if($checkTerm==true){
				$locTerm = get_term_by('name', $sloc, 'location');
				$locName = 'In '.$locTerm->name;
			}
			else{
				$locName = 'In '.$sloc;
			}
			
		}
	?>
		<div class="page-heading listing-page app_search_header">
		<div class="page-heading-inner-container search-page-header">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-sm-6">
						<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>						
					</div>
					<div class="col-md-6 col-sm-6 text-right">
						<p class="view-on-map">
							<!-- Marker icon by Icons8 -->
							<?php echo listingpro_icons('whiteMapMarkerFill'); ?>
							<a class="md-trigger mobilelink all-list-map" data-modal="modal-listing"><?php echo esc_html_e('View on map', 'listingpro'); ?></a>
						</p>
						<?php
                        $grid_list_switch   =   $listingpro_options['hide_grid_switcher'];
                        if( $grid_list_switch == 'no' )
                        {
                        ?>
                                    <div class="listing-view-layout">
                                        <ul>
                                            <li><a class="grid" href="#"><i class="fa fa-th"></i></a></li>
                                            <li><a class="list" href="#"><i class="fa fa-list-ul"></i></a></li>
                                        </ul>
                                    </div>
                            <?php
                        }
                        ?>
					</div>
				</div>
			</div>
		</div>
		<div class="page-header-overlay"></div>
		</div>
	<?php 
		}
	}elseif ( is_404() ) {
	?>
		<div class="page-heading listing-page">
			<div class="page-heading-inner-container text-center">
				<h1>404</h1>
				<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
			</div>
			<div class="page-header-overlay"></div>
		</div>
	<?php 
	}else{
	?>
		
	<?php 
	} 
	?>