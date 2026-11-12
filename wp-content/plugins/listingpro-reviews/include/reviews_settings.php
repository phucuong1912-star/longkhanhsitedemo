<?php

$user_id    =   get_current_user_id();
$lp_rating_settings =   get_option( 'lp-ratings-settings' );
$listingpro_terms   =   get_terms( array( 'listing-category' , 'hide_empty' => false  ) );


$default_ratings_settings   =   get_option( 'lp-ratings-default-settings' );
$default_rating_fields_temp  =   array('default' => array('Cleanliness', 'Service', 'Ambience', 'Price'));

?>
<div class="wrap">
    <h1 class="wp-heading-inline">Multi-Criteria Rating</h1>
    <?php
    if( !$default_ratings_settings || (is_array($default_ratings_settings) && count($default_ratings_settings) == 0) )
    {
        update_option( 'lp-ratings-default-settings', $default_rating_fields_temp );
    }
    ?>
    <div class="clearfix"></div>
    <div class="rating-setting-info-wrap">
        <p>Allows user to rate listings based on multi-rating criteria. You can have <strong>Global Criteria</strong> that are non-category specific and/or You can have <strong>Category Specific Criteria</strong>.</p>
        <div class="example-settings-wrap">
            <h3>Example category specific Criteria</h3>
            <ul>
                <li><strong>Hotels</strong></li>
                <li>- Accuracy</li>
                <li>- Cleanliness</li>
                <li>- Communication</li>
                <li>- Location</li>
                <li>- Value</li>
            </ul>
            <ul>
                <li><strong>Restaurants</strong></li>
                <li>- Taste</li>
                <li>- Cleanliness</li>
                <li>- Service</li>
                <li>- Ambience</li>
                <li>- Price</li>
                <li>- Portion</li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <img alt="image" src="<?php echo WP_PLUGIN_URL ?>/listingpro-reviews/images/review-example.png">
    </div>
    <div class="rating-settings-outer-wrap">
        <div class="ratings-fields-wrap">
            <?php
            $default_ratings_settings   =   get_option( 'lp-ratings-default-settings' );
            if( $default_ratings_settings && is_array( $default_ratings_settings ) && count( $default_ratings_settings ) > 0 )
            {
                ?>
                <div class="ratings-fields-group default-fields-group" data-groupid="default">
                    <div class="rating-fields-group-header">
                        <strong>Global Criteria</strong>
                        <span class="edit-rating-group">edit</span>
                        <div class="clearfix"></div>
                    </div>
                    <div class="rating-fields-group-fields">
                        <div class="rating-fields-group-inner">
                            <?php
                            $item_counter   =   0;
                            foreach ( $default_ratings_settings['default'] as $item )
                            {
                                ?>
                                <div class="rating-group-field" data-label="<?php echo $item; ?>">
                                    <span class="rating-field-text"><?php echo $item; ?></span>
                                    <span class="remove-rating-field remove-default-field" data-target="<?php echo $item_counter; ?>">remove</span>
                                    <div class="clearfix"></div>
                                </div>
                                <?php
                                $item_counter++;
                            }
                            ?>
                        </div>
                        <div class="add-field-wrap">
                            <input type="text" class="field-label" value="" placeholder="eg.cleanliness">
                            <button class="button save-field save-field-def">+ add</button>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="rating-save-btn-wrap">
                <p class="unsaved-message-global unsaved-message-def">you have unsaved criteria!</p>
                <button class="button button-primary save-default-rating-settings">Save</button>
            </div>
            <div class="settings-for-categories-wrap">
                <strong class="settings-heading">Categories Specific Criteria</strong>
                <?php
                if( $lp_rating_settings && is_array( $lp_rating_settings ) && count( $lp_rating_settings ) > 0 ) {
                    $group_counter  =   0;
                    foreach ( $lp_rating_settings as $cat_id => $lp_ratings_field ) {
                        ?>
                        <div class="categories-for-inner-wrap">
                            <div class="ratings-fields-group" data-groupid="<?php echo $cat_id; ?>">
                                <div class="rating-fields-group-header">
                                    <strong>Group for <?php echo $cat_id; ?></strong>
                                    <span class="edit-rating-group">edit</span>
                                    <span class="remove-rating-group" data-target="<?php echo $cat_id; ?>">remove</span>
                                    <div class="clearfix"></div>
                                </div>
                                <?php
                                if( $lp_ratings_field && is_array( $lp_ratings_field ) && count( $lp_ratings_field ) ) {
                                    ?>
                                    <div class="rating-fields-group-fields">
                                        <div class="rating-fields-group-inner">
                                            <?php
                                            $item_counter   =   0;
                                            foreach ( $lp_ratings_field as $k => $item ) {
                                                ?>
                                                <div class="rating-group-field" data-label="<?php echo $item; ?>">
                                                    <span class="rating-field-text"><?php echo $item; ?></span>
                                                    <span class="remove-rating-field" data-target="<?php echo $cat_id; ?>-<?php echo $item_counter; ?>">remove</span>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <?php
                                                $item_counter++;
                                            }
                                            ?>
                                        </div>
                                        <div class="add-field-wrap">
                                            <input type="text" class="field-label" value="" placeholder="eg.cleanliness">
                                            <button class="button save-field">+ add</button>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                        $group_counter++;
                    }
                }
                ?>
            </div>
        </div>
        <div class="ratings-group-buttons rating-save-btn-wrap">
            <p class="unsaved-message-global unsaved-message-cats">you have unsaved criteria!</p>
            <button class="button button-primary button-large save-ratings-settings">Save</button>
        </div>
		 <div class="">
			<div class="rating-fields-group-header2">
				<strong>Add New Category Group</strong>
				
				<div class="clearfix"></div>
             </div>
			
            <div class="rating-save-btn-wrap2">
				<div class="add-new-group-form">
					<select id="select-group-cat" multiple>
						<?php
						foreach ( $listingpro_terms as $term )
						{
							echo '<option value="'. $term->name.'">'. $term->name .'</option>';
						}
						?>
					</select>
					<button class="button button-primary button-large save-new-group">+ Add</button>
				</div>
            </div>
           
        </div>
    </div>
    <div class="clearfix"></div>
</div>