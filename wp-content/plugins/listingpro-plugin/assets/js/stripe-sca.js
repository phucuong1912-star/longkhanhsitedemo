jQuery('#listings_checkout_form').on('submit', function(e){
	var $this = jQuery(this);
	var method      =   $this.find('input[name="plan"]:checked').val(),
		listing_id  =   $this.find('input[name="listing_id"]:checked').val(),
		plan_id     =   $this.find('input[name="listing_id"]:checked').data('planid'),
		pinfo       =   $this.find('input[name="listing_id"]:checked').data('title'),
		plan_price  =   jQuery('span.lp-subtotal-total-price').data('subtotal'),
		currency    =   jQuery('input[name=currency]').val(),
		taxRate     =   $this.find('input[name="listing_id"]:checked').data('taxrate'),
		ajaxurl     =   ajax_search_term_object.ajaxurl,
		coupon_used =   '',
		recurring  =   'no',
		recurrText  =   'no';
	if(  jQuery('#listings_checkout_form').find('input[name="lp-recurring-option"]').prop("checked") == true ){
		recurring = 'yes';
		recurrText = 'yes';
	}
	var callFrom = 'listing';
	if(jQuery('#claimID').length) {
		callFrom = 'claim';
	} 	
	if($this.find('input[name=coupon-text-field]').length) {
		coupon_used =   $this.find('input[name=coupon-text-field]').val();
	}
	if(method == 'stripe') {
		jQuery("body").addClass("listingpro-loading");
		jQuery.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: {
				"action": "lp_sca_stripe_action",
				"pinfo": pinfo, 
				"callFrom": callFrom,
				"plan_id": plan_id, 
				"currency": currency,
				"taxRate": taxRate,
				"recurring": recurring,
				"listing_id": listing_id,
				"amount": plan_price,
				"coupon": coupon_used,
				'lpNonce': jQuery('#lpNonce').val()
			},
			success: function(res){
				jQuery("body").removeClass("listingpro-loading");
				if(res.status == 'success') {
					var callbackUrl =   res.redirect_url;
					window.location = callbackUrl;
				} else {
					//console.log('error');
				}
			},
			error: function(errorThrown){
				alert(errorThrown);
				jQuery("body").removeClass("listingpro-loading");
			}
		});
		e.preventDefault();
	}
});
var apackages = [];
jQuery('input[name="lpadsoftype[]"]').click(function(){
	if( jQuery(this).is(':checked') ){
		apackages.push(jQuery(this).val());
	}
	else{
		var i = apackages.indexOf(jQuery(this).val());
		if(i != -1) {
			apackages.splice(i, 1);
		}
	}
});
jQuery('#lp-new-ad-compaignForm').on('submit', function(e){
	var $this = jQuery(this),
		lpTotalPrice    =   jQuery('input[name="ads_price"]').val(),
		adsTypeVal      =   jQuery('input[name="adsTypeval"]').val(),
		adsDays         =   '',
		taxPrice        =   jQuery('input[name="taxprice"]').val(),
		listing_id      =   jQuery('select[name="lp_ads_for_listing"]').val(),
		method          =   jQuery('input[name="method"]:checked').val(),
		currency        =   $this.find('input[name="currency"]').val(),
		ajaxurl         =   ajax_search_term_object.ajaxurl;
	var totalPrice  =   lpTotalPrice;
	if($this.data('type') == 'adsperclick') {
		totalPrice  =   jQuery('input[name="adsprice_pc"]').val();
	}
	if(jQuery('input[name="ads_days"]').length) {
		adsDays = jQuery('input[name="ads_days"]').val();
	}
	if(method === 'stripe'){
		jQuery("body").addClass("listingpro-loading");
		jQuery.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: {
				"action": "lp_sca_stripe_campaign_action",
				"adsTypeVal": adsTypeVal,
				"adsDays": adsDays,
				"packages": apackages,
				"pinfo": listing_id,
				"amount": lpTotalPrice,
				"taxPrice": taxPrice,
				"totalPrice": totalPrice,
				"currency": currency,
				'lpNonce': jQuery('#lpNonce').val()
			},
			success: function(res){
				jQuery("body").removeClass("listingpro-loading");
				if(res.status == 'success') {
					var callbackUrl =   res.redirect_url;
					window.location = callbackUrl;
				} else {
					//console.log('error');
				}
			},
			error: function(errorThrown){
				alert(errorThrown);
				jQuery("body").removeClass("listingpro-loading");
			}
		});
		e.preventDefault();
	}
});