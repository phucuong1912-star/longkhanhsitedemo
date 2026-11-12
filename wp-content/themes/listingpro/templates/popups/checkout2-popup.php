 <?php
	global $listingpro_options;
	$pub_key;
	$pay_mode;
	$sellerId;
	$requstFailError = esc_html__('Sorry! Your request failed', 'listingpro');
	$UiD = get_current_user_id();
	$author_obj = get_user_by('id', $UiD);
	$user_email = $author_obj->user_email;
	$display_name = $author_obj->display_name;
	$user_phone = get_user_meta( $UiD, 'phone', true ); 
	$user_address = get_user_meta( $UiD, 'address', true ); 
	
	if( isset($listingpro_options['2checkout_pubishable_key']) ){
		if( !empty($listingpro_options['2checkout_pubishable_key']) ){
			$pub_key = $listingpro_options['2checkout_pubishable_key'];
		}
	}
	
	if( isset($listingpro_options['2checkout_api']) ){
		if( !empty($listingpro_options['2checkout_api']) ){
			$pay_mode = $listingpro_options['2checkout_api'];
			if($pay_mode != "sandbox"){
				$pay_mode = "production";
			}
		}
		else{
			$pay_mode = "sandbox";
		}
	}
	else{
		$pay_mode = "sandbox";
	}
	
	if( isset($listingpro_options['2checkout_acount_number']) ){
		if( !empty($listingpro_options['2checkout_acount_number']) ){
			$sellerId = $listingpro_options['2checkout_acount_number'];
		}
	}
	
	$ajaxURL = admin_url( 'admin-ajax.php' );
 ?>
 <button type="button" class="btn btn-info btn-lg lp-2checkout-modal2" data-toggle="modal" data-target="#lp-2checkout-modal2"></button>
  <!-- Modal -->
  <div class="modal fade" id="lp-2checkout-modal2" role="dialog" data-backdrop="false">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content waycheckoutModal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php echo esc_html__('2Checkout', 'listingpro'); ?></h4>
        </div>
        <div class="modal-body">
			<?php
				$formAction = '';
				$formAction = LISTINGPRO_PLUGIN_URI.'inc/2checkout/payment-campaigns.php';
			?>
			<form id="myCCForm" action="<?php echo esc_url($formAction ); ?>" method="post" name="myCCForm">
				<input id="token" name="token" type="hidden" value="">
				<input id="listing_id_2check" name="listing_id" type="hidden" value="">
				<input id="packages" name="packages" type="hidden" value="">
				<input id="taxprice" name="taxprice" type="hidden" value="">
				<input id="tprice" name="tprice" type="hidden" value="">
				<input id="userid" name="userid" type="hidden" value="<?php echo esc_attr($UiD); ?>">
				<div class="row">
				
					<div class="form-group col-md-6 col-xs-12">
						<label>
							<span><?php echo esc_html__('Card Number', 'listingpro'); ?></span>
						</label>
						<input class="form-control" name="ccNo" id="ccNo" type="text" size="20" value="" autocomplete="off" />
					</div>
					<div class="form-group col-md-6 col-xs-12">
						<label>
							<span><?php echo esc_html__('CVC', 'listingpro'); ?></span>
						</label>
						<input class="form-control" name="cvv" id="cvv" size="4" type="text" value="" autocomplete="off"/>
					</div>
					
					
					
					<div class="form-group col-md-12 col-xs-12">
						<label><span><?php echo esc_html__('Expiration Date (MM/YYYY)', 'listingpro'); ?></span></label>
					</div>
					<div class="form-group col-md-5 col-xs-12">
						<input class="form-control" name="expMonth" type="text" size="2" id="expMonth"/>
					</div>
					<div class="form-group col-md-1 col-xs-12">
						<span> / </span>
					</div>
					<div class="form-group col-md-5 col-xs-12">
						<input class="form-control" name="expYear" type="text" size="2" id="expYear"/>
					</div>
					
					
					<div class="form-group col-md-3 col-xs-12">
						<label><span><?php echo esc_html__('Name', 'listingpro'); ?></span></label>
						<input class="form-control" name="uname" type="text" id="uname" value="<?php echo esc_attr($display_name); ?>"/>
					</div>
					<div class="form-group col-md-9 col-xs-12">
						<label><span><?php echo esc_html__('Email', 'listingpro'); ?></span></label>
						<input class="form-control" name="umail" type="email" id="umail" value="<?php echo esc_attr($user_email); ?>"/>
					</div>
					
					
					<div class="form-group col-md-3 col-xs-12">
						<label><span><?php echo esc_html__('Phone', 'listingpro'); ?></span></label>
						<input class="form-control" name="uphone" type="text" id="uphone" value="<?php echo esc_attr($user_phone); ?>"/>
					</div>
					<div class="form-group col-md-9 col-xs-12">
						<label><span><?php echo esc_html__('Address', 'listingpro'); ?></span></label>
						<input class="form-control" name="uaddress" type="text" id="uaddress" value="<?php echo esc_attr($user_address); ?>"/>
					</div>
					
					<div class="form-group col-md-6 col-xs-12">
						<label><span><?php echo esc_html__('City', 'listingpro'); ?></span></label>
						<input class="form-control" name="ucity" type="text" id="ucity" />
					</div>
					<div class="form-group col-md-6 col-xs-12">
						<label><span><?php echo esc_html__('State', 'listingpro'); ?></span></label>
						<input class="form-control" name="ustate" type="text" id="ustate"/>
					</div>
					
					
					<div class="form-group col-md-6 col-xs-12">
						<label><span><?php echo esc_html__('Country', 'listingpro'); ?></span></label>
						<input class="form-control" name="ucountry" type="text" id="ucountry"/>
					</div>
					<div class="form-group col-md-6 col-xs-12">
						<label><span><?php echo esc_html__('Zip', 'listingpro'); ?></span></label>
						<input class="form-control" name="uzip" type="text" id="uzip"/>
					</div>
					
					
					
					<div class="form-group col-md-12 col-xs-12">
						<input class="btn btn-default" type="submit" value="<?php echo esc_html__('Proceed', 'listingpro'); ?>">
					</div>
				</div>
			</form>
		
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo esc_html__('Close', 'listingpro'); ?></button>
        </div>
      </div>
      
    </div>
  </div>
  
<script>
            // Called when token created successfully.
            var successCallback = function(data) {
                var myForm = document.getElementById('myCCForm');
                // Set the token as the value for the token input
                myForm.token.value = data.response.token.token;
                // IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
				ptoken = jQuery('#myCCForm #token').val();
				listing_id = jQuery('#myCCForm #listing_id_2check').val();
				tprice = jQuery('#myCCForm #tprice').val();
				
				
				userid = jQuery('#myCCForm #userid').val();
				uname = jQuery('#myCCForm #uname').val();
				umail = jQuery('#myCCForm #umail').val();
				uphone = jQuery('#myCCForm #uphone').val();
				uaddress = jQuery('#myCCForm #uaddress').val();
				ucity = jQuery('#myCCForm #ucity').val();
				ustate = jQuery('#myCCForm #ustate').val();
				ucountry = jQuery('#myCCForm #ucountry').val();
				uzip = jQuery('#myCCForm #uzip').val();
				
				jQuery.ajax({
					type: "POST",
					dataType: "json",
					url: "<?php echo esc_url($ajaxURL); ?>",
					data: { 
						"action": "listingpro_2checkout_pay_campaigns", 
						"token": ptoken, 
						"tprice": tprice, 
						"listing_id": listing_id,
						"packages": packages,						
						"taxprice": taxprice,						
						"userid": userid,
						"uname": uname,
						"umail": umail, 
						"uphone": uphone, 
						"uaddress": uaddress, 
						"ucity": ucity, 
						"ustate": ustate, 
						"ucountry": ucountry, 
						"uzip": uzip 
					},   
					success: function(res){
						if(res.status=="success"){
							redURL = res.redirect;
							if(res.status=="success"){
								window.location.href = redURL;
								jQuery("body").removeClass("listingpro-loading");
							}
						}
						if(res.status=="error"){
							alert(res.msg);
							jQuery("body").removeClass("listingpro-loading");
						}
						jQuery("body").removeClass("listingpro-loading");
					},
					error: function(errorThrown){
						alert('<?php echo esc_attr($requstFailError); ?>');
						jQuery("body").removeClass("listingpro-loading");
					} 
				});
                //myForm.submit();
            };

            // Called when token creation fails.
            var errorCallback = function(data) {
                if (data.errorCode === 200) {
                    tokenRequest();
                } else {
                    alert(data.errorMsg);
                }
            };

            var tokenRequest = function() {
                // Setup token request arguments
                var args = {
                    sellerId: "<?php echo esc_attr($sellerId); ?>",
                    publishableKey: "<?php echo esc_attr($pub_key); ?>",
                    ccNo: jQuery("#ccNo").val(),
                    cvv: jQuery("#cvv").val(),
                    expMonth: jQuery("#expMonth").val(),
                    expYear: jQuery("#expYear").val()
                };

                // Make the token request
                TCO.requestToken(successCallback, errorCallback, args);
            };

            jQuery(function() {
                // Pull in the public encryption key for our environment
                TCO.loadPubKey('<?php echo esc_attr($pay_mode); ?>');
                jQuery("#myCCForm").submit(function(e) {
					var ucard = jQuery(this).find('#ccNo').val();
					var ucvv = jQuery(this).find('#cvv').val();
					var uexpMonth = jQuery(this).find('#expMonth').val();
					var uexpYear = jQuery(this).find('#expYear').val();
					var uname = jQuery(this).find('#uname').val();
					var umail = jQuery(this).find('#umail').val();
					var uphone = jQuery(this).find('#uphone').val();
					var uaddress = jQuery(this).find('#uaddress').val();
					var ucity = jQuery(this).find('#ucity').val();
					var ustate = jQuery(this).find('#ustate').val();
					var ucountry = jQuery(this).find('#ucountry').val();
					var uzip = jQuery(this).find('#uzip').val();
					
					if(ucard== "" || ucvv== "" || uexpMonth== "" || uexpYear== "" || uname== "" || umail== "" || uphone== "" || uaddress== "" || ucity== "" || ustate== "" || ucountry== "" || uzip== "" ){
						alert('please fill all fields');
					}else{
						// Call our token request function
						jQuery("body").addClass("listingpro-loading");
						tokenRequest();
						// Prevent form from submitting
					}
                    
                    return false;
                });
            });
        </script>