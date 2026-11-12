<?php
	global $listingpro_options;

	$lp_header_search_wrap_banner_height    =   '';
	$lp_header_search_wrap_set_top  =   '';
	$banner_height = $listingpro_options['banner_height2'];
	if( !empty( $banner_height ) && is_front_page() )
	{
		$lp_header_search_wrap_banner_height    =   'lp-header-search-wrap-banner-height';
	}
	if(is_home())
	{
		$lp_header_search_wrap_set_top  =   'lp-header-search-wrap-set-top';
	}
	$home_banner_img    =   get_template_directory_uri().'/assets/images/home-banner.jpg';
	if( !empty( $listingpro_options['home_banner']['url'] ) )
	{
        $home_banner_img    =   $listingpro_options['home_banner']['url'];
    }

    if( empty( $banner_height ) )
    {
        $banner_height  =   '610';
    }
	
?>
<div class="lp-header-search-wrap lp-header-search-wrap-sidebar-banner <?php echo esc_attr($lp_header_search_wrap_banner_height); ?> <?php echo esc_attr($lp_header_search_wrap_set_top); ?>" style="background-image: url(<?php echo esc_attr($home_banner_img )?>); height: <?php echo esc_attr($banner_height); ?>px;">
    <div class="lp-header-overlay"></div>
    <?php
   
    $videosearchlayout = $listingpro_options['video_search_layout'];
    $videoBanner = $listingpro_options['lp_video_banner_on'];
    $video_banner_img = $listingpro_options['video_banner_img']['url'];
	$homeBannerCategory = '';
	
	$courtesySwitch = $listingpro_options['courtesy_switcher'];
    if($courtesySwitch == 1) {
        $courtesyListing = $listingpro_options['courtesy_listing'];
    }

    if($videoBanner=="video_banner"){
        $video_src  =   $listingpro_options['vedio_type'];
		$vedio_url = $listingpro_options['vedio_url'];
        $vedio_url_yt = $listingpro_options['vedio_url_yt'];
		if(!empty($vedio_url) || !empty($vedio_url_yt)){
		   ?>

			 <div class="video-lp">
                 <?php
                 if( $video_src == 'video_mp4' ):
                 ?>
                 <video id="lp_vedio" muted autoplay="autoplay" loop="loop" width="0" height="0" poster="<?php echo esc_url($video_banner_img);?>">
			  <source src="<?php echo esc_url($vedio_url);?>" type="video/webm" />
			  <source src="<?php echo esc_url($vedio_url);?>" type="video/mp4" />
			  <source src="<?php echo esc_url($vedio_url);?>" type="video/ogg" />
			 </video>
                 <?php
                 else:
                     $outputEmbed =  preg_replace(	"/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","$2",$vedio_url_yt);
                     echo '
					 <iframe poster="'.$video_banner_img.' id="lp-youtube-banner" frameborder="0" width="100%" height="100%" src="https://www.youtube.com/embed/'.$outputEmbed.'?autoplay=1&loop=1&controls=0&mute=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
					 ';
                 endif;
                 ?>
			 </div>
		   <?php
		}
	}
    ?>
    <div class="lp-header-search lp-header-search-sidebar-style">
        <?php
        $top_title  =   $listingpro_options['top_main_title'];
        $main_text_des  =   $listingpro_options['main_text'];
        $banner_logo    =   $listingpro_options['banner_logo_search2'];
        ?>
        <div class="container">
            <div class="row">
				<div class="col-md-7">
					<?php
					if( is_front_page() ):
					?>
				   
					<?php
						$locationType = 'withip';
                        if( isset( $top_title ) && $top_title != '' || isset( $main_text_des ) && $main_text_des != '' ):
                            ?>
                            <div class="col-md-12 lp_auto_loc_container">
                                <?php if (isset( $top_title ) && $top_title != '') { ?>
                                    <div class="lp-header-search-tagline lp-header-search-tagline-sidebar-banner" data-locnmethod="<?php echo esc_attr($locationType); ?>"><?php echo wp_kses_post($top_title); ?></div>
                                <?php }  ?>
                                <?php if (isset( $main_text_des ) && $main_text_des != '') { ?>
                                    <div class="sidebar-banner-des" data-locnmethod="<?php echo esc_attr($locationType); ?>"><?php echo esc_attr($main_text_des); ?></div>
                                <?php }  ?>
                            </div>
                        <?php endif; ?>
					
					<?php
					$home_banner_taxonomy   =   $listingpro_options['home_banner_taxonomy'];
					$banner_tax =   'home_banner_cats';
					$banner_tax_slug    =   'listing-category';
					if( $home_banner_taxonomy == 'tax_locs' )
					{
						$banner_tax   =   'home_banner_locs';
						$banner_tax_slug    =   'location';
					}
					if( $home_banner_taxonomy == 'tax_feats' )
					{
						$banner_tax   =   'home_banner_feats';
						$banner_tax_slug    =   'features';
					}
					if( isset( $listingpro_options[$banner_tax] ) )
					{
						$homeBannerCategory = $listingpro_options[$banner_tax];
					}
					else{
						$homeBannerCategory = '';
					}
					if( isset( $homeBannerCategory ) && !empty( $homeBannerCategory ) && $homeBannerCategory != '' ):
						$ucat = array(
							'post_type' => 'listing',
							'hide_empty' => false,
							'include'=> $homeBannerCategory
						);
						$categories = get_terms( $banner_tax_slug, $ucat );
					?>
					<div class="col-md-12">
						<div class="lp-header-search-cats">
							<ul>
								<?php
								foreach ( $categories as $category ):
									$category_image = listing_get_tax_meta($category->term_id,'category','image2');
								?>
								<li>
									<a href="<?php echo get_term_link( $category ); ?>">
										<?php if( $banner_tax_slug == 'listing-category' ) : ?>
											<?php
											if( !empty( $category_image ) ):
												if( hasFontAwesomeIconClass($category_image) ){
													?>
													<i class="icon icons-search-cat <?php echo wp_kses_post($category_image); ?>"></i>
													<?php
												}else{
											?>
												<img src="<?php echo wp_kses_post($category_image); ?>" alt="<?php echo esc_attr($category->name); ?>">
											<?php
												}									
											endif; ?>
										<?php elseif ($banner_tax_slug    =   'location'): ?>
											<img alt="image" src="data:image/png;base64,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">
										<?php elseif ( $banner_tax_slug    =   'features' ): ?>
											<img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAFVSURBVGhD7Zk9TsNAFIStFISWmyDgNiCBqEiBInEU/jouQEtEwy1SkyoXCKC0Nt/iaQA7TuSn3ZX1PmmUFPvmzUiOEm0Kx3Ecx4lBWZaTqqqeY4qdV1pvB6avmEeFnTOttwHPfUy/avuorNg7Voz+YHYj4+iwe6oY/cDoBK3lGx3tPlac3WF4hMklSvFI/UIZLtBI8bph6Bw9oMWPS0aETOg+ZFTcdjg011y2hIyK244XiYhpEc4t0ZuxlrLfCOdMizxqxIzgKfuNeJEmhlTkiZcDS8mzE9MiKfEiueFFmuDcMD7snPPvkTa8SBM7FBnGj8aUeJHc2LZIuHy4Q++ay4aQCd2GjIrbDXPhOugMfdY26SDDBzrl7fbXQX/B4AilvqA7VJx+YDatbePD7mvF6A9+4RI7xSO2QnuKYQNFZrV3PNj5ovV2YDqMP3ocx3Ec5x9F8Q0/hzPQPbEy2AAAAABJRU5ErkJggg==">
										<?php endif; ?>
										<?php echo esc_attr($category->name); ?>
									</a>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<?php endif; ?>
					<?php elseif(is_home()): ?>
					<?php endif; ?>
				</div>
				<?php
                get_template_part( 'templates/headers/sidesearch' );
                ?>
            </div>
			
        </div>
    </div>
	<?php if($courtesySwitch == 1) { ?>
        <div class="img-curtasy">
            <p><?php esc_html_e('Image courtesy of','listingpro'); ?> <span><a href="<?php echo get_the_permalink($courtesyListing); ?>"><?php echo get_the_title($courtesyListing); ?> <i class="fa fa-angle-right"></i></a></span></p>
        </div>
    <?php } ?>
</div>