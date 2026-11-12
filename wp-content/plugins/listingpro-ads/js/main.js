jQuery( function() {
	if(jQuery('select[name=ads_mode]').val()=='perclick'){
		jQuery( "#lp_field_remaining_balance" ).slideDown();
		jQuery( "#lp_field_click_performed" ).slideDown();
		jQuery( "#lp_field_duration" ).slideUp();
		
	}else{
		jQuery( "#lp_field_remaining_balance" ).slideUp();
		jQuery( "#lp_field_click_performed" ).slideUp();
		jQuery( "#lp_field_duration" ).slideDown();
	}
	
	
	/* jquery on change */
	
	jQuery('select[name="ads_mode"]').on('change', function(){
		$thisval = jQuery(this).val();
		
		if($thisval=='perclick'){
			jQuery( "#lp_field_remaining_balance" ).slideDown();
			jQuery( "#lp_field_click_performed" ).slideDown();
			jQuery( "#lp_field_duration" ).slideUp();
		}else{
			jQuery( "#lp_field_remaining_balance" ).slideUp();
			jQuery( "#lp_field_click_performed" ).slideUp();
			jQuery( "#lp_field_duration" ).slideDown();
		}
	});
	
  });