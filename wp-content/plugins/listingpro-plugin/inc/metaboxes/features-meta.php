<?php
	
	/* ============== ListingPro features meta ============ */
	global $features_meta;

	$features_meta = Array(
		Array(
			'name' => esc_html__('Feature icon', 'listingpro-plugin'),
			'id' => 'lp_features_icon',
			'type' => 'text',
			'value' => '',
			'desc' => 'Font Awesome icons from <a href="https://fontawesome.com/v4.7/icons/" target="blank">FontAwesome Website</a> Icone Code should be like `fa-address-book `',
			),
			
	);


	/* ============== ListingPro features meta add field ============ */
	
	if (!function_exists('listingpro_features_meta_add')) {
		add_action( 'features_add_form_fields', 'listingpro_features_meta_add' );
		function listingpro_features_meta_add() {
			
			global $features_meta; 
			
					foreach ($features_meta as $meta) {
						 call_user_func('settings_'.$meta['type'], $meta);
						
					}
				
		 }
	}
	/* ============== ListingPro features meta edit ============ */
	
	if (!function_exists('listingpro_features_meta_edit')) {
		add_action( 'features_edit_form_fields', 'listingpro_features_meta_edit' );
		function listingpro_features_meta_edit( $term ) {
		   global $features_meta; 
		
			foreach ($features_meta as $meta) {
				$value  = listingpro_get_term_meta( $term->term_id, $meta['id']);

				$meta['value'] = $value;

				 call_user_func('settings_'.$meta['type'], $meta);
				
			}

		 }
	 }
	/* ============== ListingPro features meta save ============ */
	
	if (!function_exists('listingpro_features_meta_save')) {
		add_action( 'edit_features',   'listingpro_features_meta_save' );
		add_action( 'create_features', 'listingpro_features_meta_save' );
		function listingpro_features_meta_save( $term_id ) {

			global $features_meta;

			$metaboxes = $features_meta;
			if(!empty($metaboxes)) {
				foreach ($metaboxes as $metabox) {
					if(isset($_POST[$metabox['id']])){
						$old_value  = listingpro_get_term_meta( $term_id,$metabox['id']);
						$new_value = $_POST[$metabox['id']] ;
						
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
	/* ============== ListingPro features column ============ */
	
	if (!function_exists('listingpro_features_column')) {
		add_filter( 'manage_edit-features_columns', 'listingpro_features_column' );
		function listingpro_features_column( $columns ) {
			global $features_meta;
			 $metaboxes = $features_meta;
			 foreach ($metaboxes as $metabox) {
				 $columns[$metabox['id']] = $metabox['name'];		   
			 }
			
			return $columns;
		}
	}
	/* ============== ListingPro features column render ============ */
	
	if (!function_exists('listingpro_features_column_manage')) {
		add_filter( 'manage_features_custom_column', 'listingpro_features_column_manage', 10, 3 );
		function listingpro_features_column_manage( $out, $column, $term_id ) {
			global $features_meta;
			$metaboxes = $features_meta;
			 foreach ($metaboxes as $metabox) {
				if ( $metabox['id'] === $column ) {
					$value  = listingpro_get_term_meta( $term_id, $metabox['id']);

					if ( ! $value )
						$value = '';
					if($metabox['type'] == 'file'){
						$out = sprintf( '<img width="80" alt="image" src="%s" />', esc_attr( $value ) );
					}elseif($metabox['type'] == 'mselect'){
						if (!empty($value)){
							foreach ($value as $val) {
								
								$oute =  get_term_by('id', $val, 'listing-category');
								if(!empty($oute)){
									echo $oute->name.',';
								}
							}
						}
					}else{
					$out = sprintf( '<span class="term-meta-text-block" style="" ><i class="fa %s"></i></div>', esc_attr( $value ) );
					}
				}
			   
			 }
			
			return $out;
		}
	}