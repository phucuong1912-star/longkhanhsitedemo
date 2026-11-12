jQuery(function() {
    jQuery('#plan_package_type').change(function() {
        var selected = jQuery('#plan_package_type option:selected').val();
        var alertmsg = jQuery('#plan_package_type select').data('alertmsg');
        if (selected === "Pay Per Listing") {
            jQuery('#plan_text_box').slideUp();
            /* jQuery('#plan_duration_type').slideUp(); */
            jQuery("input#plan_time").prop("disabled", !1);
            jQuery("input#plan_time").prop("readonly", !1)
        } else {
			alert(alertmsg);
            jQuery('#plan_text_box').slideDown();
            /* jQuery('#plan_duration_type').slideDown(); */
            jQuery("input#plan_time").prop("disabled", !1);
            jQuery("input#plan_time").prop("readonly", !1)
        }
    })
});
jQuery(document).ready(function($) {
    
	jQuery('select#plan_usge_for').on('change', function(){
		$cval = jQuery(this).val();
		if($cval=="default"){
			jQuery('#plan_cats').slideUp();
			jQuery('#plan_cats').find('input[type=checkbox]').prop('checked', false);
		}else{
			jQuery('#plan_cats').slideDown();
		}
	});
	$cval = jQuery('select#plan_usge_for').val();
	if($cval=="default"){
		jQuery('#plan_cats').slideUp();
	}else{
		jQuery('#plan_cats').slideDown();
	}
});

jQuery(window).load(function(){
        var selected = jQuery('#plan_package_type option:selected').val();
        if (selected === "Pay Per Listing") {
            jQuery('#plan_text_box').slideUp();
            /* jQuery('#plan_duration_type').slideUp(); */
            jQuery("input#plan_time").prop("disabled", !1);
            jQuery("input#plan_time").prop("readonly", !1)
        } else {
            jQuery('#plan_text_box').slideDown();
            /* jQuery('#plan_duration_type').slideDown(); */
            jQuery("input#plan_time").prop("disabled", !1);
            jQuery("input#plan_time").prop("readonly", !1)
        }
		
		/* to enable all plan option */
		jQuery('#bulk_enable_price_options').on('change', function(){
			if (jQuery(this).is(':checked')) {
				jQuery('#contact_show').prop('checked', true);
				jQuery('#map_show').prop('checked', true);
				jQuery('#video_show').prop('checked', true);
				jQuery('#gallery_show').prop('checked', true);
				jQuery('#listingproc_tagline').prop('checked', true);
				jQuery('#listingproc_location').prop('checked', true);
				jQuery('#listingproc_website').prop('checked', true);
				jQuery('#listingproc_social').prop('checked', true);
				jQuery('#listingproc_faq').prop('checked', true);
				jQuery('#listingproc_price').prop('checked', true);
				jQuery('#listingproc_tag_key').prop('checked', true);
				jQuery('#listingproc_bhours').prop('checked', true);
				jQuery('#listingproc_plan_reservera').prop('checked', true);
				jQuery('#listingproc_plan_timekit').prop('checked', true);
				jQuery('#listingproc_plan_menu').prop('checked', true);
                jQuery('#listingproc_bookings').prop('checked', true);
                jQuery('#listingproc_leadform').prop('checked', true);
				jQuery('#listingproc_plan_announcment').prop('checked', true);
				jQuery('#listingproc_plan_deals').prop('checked', true);
				jQuery('#listingproc_plan_campaigns').prop('checked', true);
				jQuery('#lp_eventsplan').prop('checked', true);
				jQuery('#lp_hidegooglead').prop('checked', true);
			}else{
				jQuery('#contact_show').prop('checked', false);
				jQuery('#map_show').prop('checked', false);
				jQuery('#video_show').prop('checked', false);
				jQuery('#gallery_show').prop('checked', false);
				jQuery('#listingproc_tagline').prop('checked', false);
				jQuery('#listingproc_location').prop('checked', false);
				jQuery('#listingproc_website').prop('checked', false);
				jQuery('#listingproc_social').prop('checked', false);
				jQuery('#listingproc_faq').prop('checked', false);
				jQuery('#listingproc_price').prop('checked', false);
				jQuery('#listingproc_tag_key').prop('checked', false);
				jQuery('#listingproc_bhours').prop('checked', false);
				jQuery('#listingproc_plan_reservera').prop('checked', false);
				jQuery('#listingproc_plan_timekit').prop('checked', false);
				jQuery('#listingproc_plan_menu').prop('checked', false);
				jQuery('#listingproc_plan_announcment').prop('checked', false);
				jQuery('#listingproc_plan_deals').prop('checked', false);
                jQuery('#listingproc_bookings').prop('checked', false);
                jQuery('#listingproc_leadform').prop('checked', false);
				jQuery('#listingproc_plan_campaigns').prop('checked', false);
				jQuery('#lp_eventsplan').prop('checked', false);
				jQuery('#lp_hidegooglead').prop('checked', false);
			}
		});
		/* to hide all plan option */
		jQuery('#bulk_hide_price_options').on('change', function(){
			if (jQuery(this).is(':checked')) {
				jQuery('#contact_show_hide').prop('checked', true);
				jQuery('#map_show_hide').prop('checked', true);
				jQuery('#video_show_hide').prop('checked', true);
				jQuery('#gall_show_hide').prop('checked', true);
				jQuery('#tagline_show_hide').prop('checked', true);
				jQuery('#location_show_hide').prop('checked', true);
				jQuery('#website_show_hide').prop('checked', true);
				jQuery('#social_show_hide').prop('checked', true);
				jQuery('#faqs_show_hide').prop('checked', true);
				jQuery('#price_show_hide').prop('checked', true);
				jQuery('#tags_show_hide').prop('checked', true);
				jQuery('#bhours_show_hide').prop('checked', true);
				jQuery('#reserva_show_hide').prop('checked', true);
				jQuery('#timekit_show_hide').prop('checked', true);
				jQuery('#menu_show_hide').prop('checked', true);
				jQuery('#announcment_show_hide').prop('checked', true);
				jQuery('#deals_show_hide').prop('checked', true);
				jQuery('#metacampaign_show_hide').prop('checked', true);
				jQuery('#events_show_hide').prop('checked', true);
			}else{
				jQuery('#contact_show_hide').prop('checked', false);
				jQuery('#map_show_hide').prop('checked', false);
				jQuery('#video_show_hide').prop('checked', false);
				jQuery('#gall_show_hide').prop('checked', false);
				jQuery('#tagline_show_hide').prop('checked', false);
				jQuery('#location_show_hide').prop('checked', false);
				jQuery('#website_show_hide').prop('checked', false);
				jQuery('#social_show_hide').prop('checked', false);
				jQuery('#faqs_show_hide').prop('checked', false);
				jQuery('#price_show_hide').prop('checked', false);
				jQuery('#tags_show_hide').prop('checked', false);
				jQuery('#bhours_show_hide').prop('checked', false);
				jQuery('#reserva_show_hide').prop('checked', false);
				jQuery('#timekit_show_hide').prop('checked', false);
				jQuery('#menu_show_hide').prop('checked', false);
				jQuery('#announcment_show_hide').prop('checked', false);
				jQuery('#deals_show_hide').prop('checked', false);
				jQuery('#metacampaign_show_hide').prop('checked', false);
				jQuery('#events_show_hide').prop('checked', false);
			}
		});
});