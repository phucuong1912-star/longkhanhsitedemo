<?php
/**
 * Template part for app view footer.
 * templates/footers/footer-app-view
 * @version 2.0
*/
 
$latitude = listing_get_metabox('latitude');
$longitude = listing_get_metabox('longitude');
$phone = listing_get_metabox('phone');
$col_class  =   '4';
if(empty($phone)){
	$col_class  =   '6';
}
?>
<!--==================================Footer Open=================================-->
<?php if( !is_singular('listing')){ ?>
 <?php if(has_nav_menu('footer_menu')){ ?>		
<div class="footer-app-menu">
   <?php 
        echo listingpro_footer_menu_app();    
    ?>
</div>
<?php } ?>
<?php }else{ ?>
	<footer class="text-center">
		<div class="footer-upper-bar footer-upper-bar-for-app">
			<div class="container">
				<div class="row">
					<?php
					if( !empty($phone)){
					   ?>
						<div class="col-sm-4 col-xs-4"><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo listingpro_icons('app_phone'); ?></a></div>
						<?php
					}
					?>
					
					<div class="col-sm-<?php echo esc_attr($col_class); ?> col-xs-<?php echo esc_attr($col_class); ?>"><a class="open-lead-form-app-view"><?php echo listingpro_icons('app_form'); ?></a></div>
					
					<div class="col-sm-<?php echo esc_attr($col_class); ?> col-xs-<?php echo esc_attr($col_class); ?>"><a target="_blank" href="https://www.google.com/maps?daddr=<?php echo esc_attr($latitude); ?>,<?php echo esc_attr($longitude); ?>"><?php echo listingpro_icons('app_gaddress'); ?></a></div>
				</div>
			</div>
		</div>
		<!--/footer-upper-bar -->
	</footer>
<?php } ?>