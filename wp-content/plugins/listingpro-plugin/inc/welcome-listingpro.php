<?php
//add_action( 'admin_menu', 'register_lp_welcome_page' );

function register_lp_welcome_page(){
    add_submenu_page('themes.php','welcome', 'Listingpro', 'administrator','listingpro', 'lp_welcome_page_content');
}

function lp_welcome_page_content(){?>

<div class="wrap lp-welcome-page-wraper">
	<div class="lp-theme-logo">
		<img alt="image" src="<?php echo get_template_directory_uri(); ?>/assets/images/admin/logo.png" />
	</div>
	<h1 class="wp-heading-inline">Welcome to ListingPro - Directory WordPress Theme</h1>
	<p class="wp-paragraph-inline">Congratulations! You are about to use most powerful WordPress Theme We build stuff for WordPress that can be used for your next million dollar baby.</p>
	<div class="row lp-welcome-content">
		<div class="panel-body">
			<img src="https://docs.listingprowp.com/wp-content/uploads/2017/07/kb2.jpg" alt="Block Image 1">					  <h3>Knowledge Base</h3>
		  <p>Here you will find the answer to all your Post-Sale questions.</p>
			<a class="btn btn-primary" href="https://docs.listingprowp.com/knowledge-base/" target="_blank">Find Answers →</a>
		</div>
		<div class="panel-body">
		  <img src="http://cridio.com/wp-content/uploads/2016/11/video-portal.jpg" alt="Block Image 3">					  <h3>Video Center</h3>
		  <p>If self-help isn't good enough, proceed to our support center.</p>
			<a class="btn btn-primary" href="https://www.youtube.com/channel/UCcEdsqc_vwRL0PGOGqWb75A" target="_blank">Video Channel →</a>
		</div>
		<div class="panel-body">
		  <img src="https://docs.listingprowp.com/wp-content/uploads/2017/07/sc.jpg" alt="Block Image 3">					  <h3>Support Center</h3>
		  <p>If self-help isn't good enough, proceed to our support center.</p>
			<a class="btn btn-primary" href="http://help.listingprowp.com" target="_blank">Open Ticket →</a>
		</div>
		<div class="clearfix"></div>
		<h1 class="wp-heading-inline">How To Setup ListingPro - Directory WordPress Theme</h1>
	
		<div class="lp-welcome-video">
			<iframe width="100%" height="100%" src="https://www.youtube.com/embed/4ksuc9qagEI" frameborder="0" allowfullscreen>
		</div>
	</div>
</div>

<?php
}