<?php if($post->post_status == 'publish'){ ?>
	<?php if(is_active_sidebar('listing_detail_sidebar')) { ?>
		<div class="sidebar">
			<?php dynamic_sidebar('listing_detail_sidebar'); ?>
		</div>
	<?php } ?>
<?php } ?>
