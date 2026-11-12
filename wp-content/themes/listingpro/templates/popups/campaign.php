<?php 
$adTitle = lp_theme_option('lp_pro_title_new');
$lp_pro_text_list1 = lp_theme_option('lp_pro_text_list1');
$lp_pro_text_list2 = lp_theme_option('lp_pro_text_list2');
$lp_pro_text_list3 = lp_theme_option('lp_pro_text_list3');
$adImage = lp_theme_option_url('lp_pro_img_new');
?>
<!-- create campagin popup-->
          <div class="modal fade" id="modal-lp-submit-ad" role="dialog">
            <div class="modal-dialog  ">
            
              <!-- Modal content-->
                <div class="modal-content lp-ad-image-section-popup"> 
                    <div class="modal-header padding-0"> 
                        <div class="lp-ad-image-section">
                            <img alt="image" src="<?php echo esc_url($adImage);?>" />
                        </div>
                    </div> 
                    <div class="modal-body">
                        
                        <div class="lp-ad-image-section-content text-center">
                            <h3><?php echo wp_kses_post($adTitle); ?></h3>
                            <ul class="text-left">
								<?php
									if(!empty($lp_pro_text_list1)){?>
										<li class="clearfix">
											<div class="lp-step-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
											<div class="lp-icon-content-outer">
												<?php echo wp_kses_post($lp_pro_text_list1); ?>
											</div>
										</li>
										<?php
									}
									if(!empty($lp_pro_text_list2)){
									?>
										<li class="clearfix">
											<div class="lp-step-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
											<div class="lp-icon-content-outer">
												 <?php echo wp_kses_post($lp_pro_text_list2); ?>
											</div>
										</li>
										<?php
									}
									if(!empty($lp_pro_text_list3)){
									?>
										<li class="clearfix">
											<div class="lp-step-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
											<div class="lp-icon-content-outer">
												 <?php echo wp_kses_post($lp_pro_text_list3); ?>
											</div>
										</li>
										<?php
									}
									?>
                            </ul>
                            
                        </div>
                    </div> 
                    <div class="modal-footer">
                         <button type="button" class="lp-let-start-btn"><?php esc_html_e("Let's Get Started",'listingpro'); ?></button>
                        
                    </div> 
                     <button type="button" class="btn lp-btn-close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div> 
            </div>
          </div>
        <!--modal 7, for app view filters-->