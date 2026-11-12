<div id="lp-paypal-button"></div>
<?php
$MODE = 'sandbox';
$currency_code = lp_theme_option('currency_paid_submission');
define("APP_URL", "http://localhost/v2/index.php/payment-checkout/");

define("PayPal_CLIENT_ID", "AQ_0YOjVJAtks9Lu4QjP61jM-0KsuLUpojcMH_nKxtwllS2RakWUJSTCeMETp8Gq_VfxafyiZfLtfZ_s");
define("PayPal_SECRET", "EBHN9S_BoJNn1Zmr_sxwikp2H1Hkz2xIEnlrw7xNfLmiOEBs7aVO0I4hPQ7wYMxaT_rWpPu_fYXogUew");
define("PayPal_BASE_URL", "https://api.sandbox.paypal.com/v1/");
define("CURRENCY", $currency_code);

$total = 45;
?>
<input type="hidden" id="lp_paypal_price" />
<script>
	paypal.Button.render({
		<?php if($MODE == 'live') { ?>
		env: 'production',
		<?php } else {?>
		env: 'sandbox',
		<?php } ?>
 
		commit: true,
 
		client: {
			sandbox: '<?php echo PayPal_CLIENT_ID; ?>',
			production: '<?php echo PayPal_CLIENT_ID; ?>'
		},
 
		payment: function (data, actions) {
 
			return actions.payment.create({
				payment: {
					transactions: [
						{
							amount: {
								/* total: jQuery('#lp_paypal_price').val(), */
								
								total: <?php echo $total; ?>,
								currency: '<?php echo CURRENCY; ?>'
							}
						}
					]
				}
			});
		},
 
		onAuthorize: function (data, actions) {
 
			return actions.payment.execute().then(function () {
				window.location = "<?php echo APP_URL ?>" +  "execute-payment.php?payment_id=" + data.paymentID + "&payer_id=" + data.payerID + "&token=" + data.paymentToken;
			});
		}
	}, '#lp-paypal-button');
</script>