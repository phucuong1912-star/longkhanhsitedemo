<?php

/**
 * Template part for footer social.
 * templates/footers/footer-social
 * @version 2.0
*/
 
    $fb = lp_theme_option('fb');
    $tw = lp_theme_option('tw');
    $insta = lp_theme_option('insta');
    $tumb = lp_theme_option('tumb');
    $fyout = lp_theme_option('f-yout');
    $flinked = lp_theme_option('f-linked');
    $fpintereset = lp_theme_option('f-pintereset');
    $fvk = lp_theme_option('f-vk');
?>
     
<?php if(!empty($tw) || !empty($fb) || !empty($insta) || !empty($tumb) || !empty($fpintereset) || !empty($flinked) || !empty($fyout) || !empty($fvk)){ ?>
	<ul class="social-icons footer-social-icons">
		<?php if(!empty($fb)){ ?>
			<li>
				<a href="<?php echo esc_url($fb); ?>" target="_blank">
					<?php echo listingpro_icons('facebook'); ?>
				</a>
			</li>
		<?php } ?>
		<?php if(!empty($tw)){ ?>
			<li>
				<a href="<?php echo esc_url($tw); ?>" target="_blank">
					<?php echo listingpro_icons('tw-footer'); ?>
				</a>
			</li>
		<?php } ?>
		<?php if(!empty($insta)){ ?>
			<li>
				<a href="<?php echo esc_url($insta); ?>" target="_blank">
					<?php echo listingpro_icons('instagram'); ?>
				</a>
			</li>
		<?php } ?>
		<?php if(!empty($fyout)){ ?>
			<li>
				<a href="<?php echo esc_url($fyout); ?>" target="_blank">
					<?php echo listingpro_icons('ytwite'); ?>
				</a>
			</li>
		<?php } ?>
		<?php if(!empty($flinked)){ ?>
			<li>
				<a href="<?php echo esc_url($flinked); ?>" target="_blank">
					<?php echo listingpro_icons('linkedin'); ?>
				</a>
			</li>
		<?php } ?>
		<?php if(!empty($fpintereset)){ ?>
			<li>
				<a href="<?php echo esc_url($fpintereset); ?>" target="_blank">
					<?php echo listingpro_icons('pinterest'); ?>
				</a>
			</li>
		<?php } ?>
		<?php if(!empty($tumb)){ ?>
			<li>
				<a href="<?php echo esc_url($tumb); ?>" target="_blank">
					<?php echo listingpro_icons('tumbler'); ?>
				</a>
			</li>
		<?php } ?>
		<?php if(!empty($fvk)){ ?>
			<li>
				<a href="<?php echo esc_url($fvk); ?>" target="_blank">
					<?php echo listingpro_icons('vk'); ?>
				</a>
			</li>
		<?php } ?>

	</ul>
<?php } ?>