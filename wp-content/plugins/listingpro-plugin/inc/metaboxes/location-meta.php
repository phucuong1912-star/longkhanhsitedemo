<?php
	
	/* ============== ListingPro location meta ============ */
	global $location_meta;
	$location_meta = Array(
		Array(
			'name' => esc_html__('Location Image', 'listingpro-plugin'),
			'id' => 'lp_location_image',
			'type' => 'file',
			'value' => '',
			'desc' => ''
			),
		Array(
			'name' => esc_html__('Location Image ID ', 'listingpro-plugin'),
			'id' => 'lp_location_image_id',
			'type' => 'hidden',
			'value' => '',
			'desc' => ''
			),
			
	);


/* ============== ListingPro location meta add field ============ */
	
	if (!function_exists('listingpro_location_meta_add')) {
		add_action( 'location_add_form_fields', 'listingpro_location_meta_add' );
		function listingpro_location_meta_add() {
			
			global $location_meta; 
			
					foreach ($location_meta as $meta) {
						 call_user_func('settings_'.$meta['type'], $meta);
						
					}
				
		 }
	}
	/* ============== ListingPro location meta edit ============ */
	
	if (!function_exists('listingpro_location_meta_edit')) {
		add_action( 'location_edit_form_fields', 'listingpro_location_meta_edit' );
		function listingpro_location_meta_edit( $term ) {
		   global $location_meta; 
		
			foreach ($location_meta as $meta) {
				$value  = listingpro_get_term_meta( $term->term_id, $meta['id']);
				$meta2 =  Array(
				'name' => $meta['name'],
				'id' => $meta['id'],
				'type' => $meta['type'],
				'value' => $value,
				'desc' => $meta['desc']);
				 call_user_func('settings_'.$meta['type'], $meta2);
				
			}

		 }
	 }
	/* ============== ListingPro location meta save ============ */
	
	if (!function_exists('listingpro_location_meta_save')) {
		add_action( 'edit_location',   'listingpro_location_meta_save' );
		add_action( 'create_location', 'listingpro_location_meta_save' );
		function listingpro_location_meta_save( $term_id ) {

			global $location_meta;
			//Don't update on Quick Edit
			if (defined('DOING_AJAX') ) {
				return $term_id;
			}
			$metaboxes = $location_meta;
			if(!empty($metaboxes)) {
				$myMeta = array();

				foreach ($metaboxes as $metabox) {
					if(isset($_POST[$metabox['id']])){
						$old_value  = listingpro_get_term_meta( $term_id,$metabox['id']);
						$new_value = $_POST[$metabox['id']];
						if ( $old_value && '' === $new_value )
							delete_term_meta( $term_id, $metabox['id'] );
						else if ( $old_value !== $new_value )
						update_term_meta( $term_id, $metabox['id'], $new_value );
					}
					else{
						delete_term_meta( $term_id, $metabox['id'] );
					}
				}

			}
		}
	}
	/* ============== ListingPro location column ============ */
	
	if (!function_exists('listingpro_location_column')) {
		add_filter( 'manage_edit-location_columns', 'listingpro_location_column' );
		function listingpro_location_column( $columns ) {
			global $location_meta;
			 $metaboxes = $location_meta;
			 foreach ($metaboxes as $metabox) {
				 $columns[$metabox['id']] = $metabox['name'];
		   
			 }
			
			return $columns;
		}
	}
	/* ============== ListingPro location column render ============ */
	
	if (!function_exists('listingpro_location_column_manage')) {
		add_filter( 'manage_location_custom_column', 'listingpro_location_column_manage', 10, 3 );
		function listingpro_location_column_manage( $out, $column, $term_id ) {
			global $location_meta;
			$metaboxes = $location_meta;
			 foreach ($metaboxes as $metabox) {
				if ( $metabox['id'] === $column ) {
					$value  = listingpro_get_term_meta( $term_id, $metabox['id']);
                    if( empty($value) ){
						return false;
					}
					if ( ! $value )
						$value = '';
					if($metabox['type'] == 'file'){
						$out = sprintf( '<img width="80" alt="image" src="%s" />', esc_attr( $value ) );
					}else{
					$out = sprintf( '<span class="term-meta-text-block" style="" >%s</div>', esc_attr( $value ) );
					}
				}
			   
			 }
			
			return $out;
		}
	}