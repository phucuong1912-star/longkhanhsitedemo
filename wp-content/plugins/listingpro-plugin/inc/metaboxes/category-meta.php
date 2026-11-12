<?php
	
	/* ============== ListingPro category meta ============ */
	
	global $category_meta;
	$category_meta = Array(
		Array(
			'name' => esc_html__('Category Icon', 'listingpro-plugin'),
			'id' => 'lp_category_image',
			'type' => 'file',
			'value' => '',
			'desc' => esc_html__('For Archive and Search Page', 'listingpro-plugin'),
			'std'=> 'Please use Base64 from icon8 or Fontawesome class like "fa-solid fa-car"',
			),
		Array(
			'name' => esc_html__('Icon On Banner', 'listingpro-plugin'),
			'id' => 'lp_category_image2',
			'type' => 'file',
			'value' => '',
			'desc' => esc_html__('Icon For Home Page Banner', 'listingpro-plugin'),
			'std'=> 'Please use Base64 from icon8 or Fontawesome class like "fa-solid fa-car"',
			),		
		Array(
			'name' => esc_html__('Category Banner ', 'listingpro-plugin'),
			'id' => 'lp_category_banner',
			'type' => 'file',
			'value' => '',
			'desc' => ''
			),
		Array(
			'name' => esc_html__('Category Banner ID ', 'listingpro-plugin'),
			'id' => 'lp_category_banner_id',
			'type' => 'hidden',
			'value' => '',
			'desc' => ''
			),	
		Array(
			'name' => esc_html__('Features', 'listingpro-plugin'),
			'id' => 'lp_category_tags',
			'type' => 'mselect',
			'value' => '',
			'desc' => esc_html__('To add new Listing Features click below.', 'listingpro-plugin'),
			'std'=> '',
			'options'=>listing_get_feature_array(),
			),
			
	);


/* ============== ListingPro category meta add field ============ */
	
	if (!function_exists('listingpro_category_meta_add')) {
		add_action( 'listing-category_add_form_fields', 'listingpro_category_meta_add' );
		function listingpro_category_meta_add() {
			
			global $category_meta; 
			
					foreach ($category_meta as $meta) {
						 call_user_func('settings_'.$meta['type'], $meta);
						
					}
				
		 }
	}
	/* ============== ListingPro category meta edit ============ */
	
	if (!function_exists('listingpro_category_meta_edit')) {
		add_action( 'listing-category_edit_form_fields', 'listingpro_category_meta_edit' );
		function listingpro_category_meta_edit( $term ) {
		   global $category_meta; 
		
			foreach ($category_meta as $meta) {
				$value  = listingpro_get_term_meta( $term->term_id, $meta['id']);
				$meta['value'] = $value;

				 call_user_func('settings_'.$meta['type'], $meta);
				
			}

		 }
	 }
	/* ============== ListingPro category meta save ============ */
	
	if (!function_exists('listingpro_category_meta_save')) {
		add_action( 'edit_listing-category',   'listingpro_category_meta_save' );
		add_action( 'create_listing-category', 'listingpro_category_meta_save' );
		function listingpro_category_meta_save( $term_id ) {

			global $category_meta;
			//Don't update on Quick Edit
			if (defined('DOING_AJAX') ) {
				return $term_id;
			}
			$metaboxes = $category_meta;
			//if(!empty($metaboxes)) {
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

			//}
		}
	}
	/* ============== ListingPro category column ============ */
	
	if (!function_exists('listingpro_category_column')) {
		add_filter( 'manage_edit-listing-category_columns', 'listingpro_category_column' );
		function listingpro_category_column( $columns ) {
			global $category_meta;
			 $metaboxes = $category_meta;
			 foreach ($metaboxes as $metabox) {
				 $columns[$metabox['id']] = $metabox['name'];
				unset($columns['description']);
			 }
			
			return $columns;
		}
	}
	/* ============== ListingPro category column render ============ */
	
	if (!function_exists('listingpro_category_column_manage')) {
		add_filter( 'manage_listing-category_custom_column', 'listingpro_category_column_manage', 10, 3 );
		function listingpro_category_column_manage( $out, $column, $term_id ) {
			global $category_meta;
			$metaboxes = $category_meta;
			 foreach ($metaboxes as $metabox) {
				if ( $metabox['id'] === $column ) {
					$value  = listingpro_get_term_meta( $term_id, $metabox['id']);
					if( empty($value) ){
						return false;
					}
					
					if( !is_array($value) &&  hasFontAwesomeIconClass($value) ){
						echo $value;
						return $out;
					}
					if ( ! $value )
						$value = '';
					
					if($metabox['type'] == 'file'){
						$out = sprintf( '<img width="80" alt="image" src="%s" />', esc_attr( $value ) );
					}elseif($metabox['type'] == 'mselect'){
						if (!empty($value)){
							foreach ($value as $val) {
								
								$oute =  get_term_by('id', $val, 'features');
								if(!empty($oute)){
									echo $oute->name.',';
								}
								
							}
						}
					}else{
					$out = sprintf( '<span class="term-meta-text-block" style="" >%s</div>', esc_attr( $value ) );
					}
				}
			   
			 }
			
			return $out;
		}
	}