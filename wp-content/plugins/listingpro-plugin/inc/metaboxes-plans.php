<?php


    if(!function_exists('load_plans_scripts')) {
        function load_plans_scripts(){
            $screen = get_current_screen();
            $pageTitle = '';
            $pageTitle = $screen->id;
            if(!empty($pageTitle) && $pageTitle=="price_plan"){
                wp_register_script( 'plans', plugins_url( '/assets/js/plans.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ) );
                wp_enqueue_script( 'plans' );
            }
        }
    }
	add_action( 'admin_enqueue_scripts', 'load_plans_scripts' );

    if(!function_exists('plan_package_type')) {
        function plan_package_type() {
            add_meta_box(
                'plan_package_type',
                __( 'Select Package Type', 'listingpro-plugin' ),
                'plan_type_package',
                'price_plan'
            );
        }
    }
	add_action( 'add_meta_boxes', 'plan_package_type' );

    if(!function_exists('plan_type_package')) {
        function plan_type_package( $post ) {

            $plan_package_type = get_post_meta( $post->ID, 'plan_package_type', true );

            echo '<label for="plan_package_type"></label>';

            echo '<select name="plan_package_type" id="plan_package_type" data-alertmsg="'.esc_html__('Please Make sure to add no. of posts in this package', 'listingpro-plugin').'">';

            if( !empty ( $plan_package_type ) ){

                if( $plan_package_type=="Pay Per Listing" ){

                    echo '<option value="Package">'.esc_html__('Package', 'listingpro-plugin').'</option>';
                    echo '<option value="Pay Per Listing" selected>'.esc_html__('Pay Per Listing', 'listingpro-plugin').'</option>';

                }
                else if( $plan_package_type=="Package" ){
                    echo '<option value="Package" selected>'.esc_html__('Package', 'listingpro-plugin').'</option>';
                    echo '<option value="Pay Per Listing">'.esc_html__('Pay Per Listing', 'listingpro-plugin').'</option>';
                }

            }
            else{
                echo '<option value="Pay Per Listing">'.esc_html__('Pay Per Listing', 'listingpro-plugin').'</option>';
                echo '<option value="Package">'.esc_html__('Package', 'listingpro-plugin').'</option>';
            }

            echo '</select>';

        }
    }



	add_action( 'save_post', 'plan_package_type_save' );
    if(!function_exists('plan_package_type_save')) {
        function plan_package_type_save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{
                global $plan_package_type;

                if(isset($_POST["plan_package_type"]))
                    $plan_package_type = $_POST['plan_package_type'];
                update_post_meta( $post_id, 'plan_package_type', $plan_package_type );
            }

        }
    }


/* for style 3 */
add_action( 'add_meta_boxes', 'plan_desc_box_fun' );

if(!function_exists('plan_desc_box_fun')) {
    function plan_desc_box_fun() {
        add_meta_box(
            'plan_desc_box',
            __( 'Enter Description here', 'listingpro-plugin' ),
            'plan_desc_box_content',
            'price_plan'
        );
    }
}

if(!function_exists('plan_desc_box_content')){
    function plan_desc_box_content( $post ) {

        $plan_desc_content= get_post_meta( $post->ID, 'plan_desc_content', true );
        ?>
        <label for="plan_desc_content"></label>
        <textarea id="plan_desc_content" name="plan_desc_content" placeholder="<?php echo esc_html__('Plan Description...', 'listingpro-plugin'); ?>"><?php echo $plan_desc_content; ?></textarea>
        <p class="lp-new-price-des"><?php echo esc_html__('Will only work with "Virtical View 3" of Pricing Plan Page Style', 'listingpro-plugin'); ?></p>


        <?php
    }
}


add_action( 'save_post', 'plan_desc_box_content_save' );

if(!function_exists('plan_desc_box_content_save')) {
    function plan_desc_box_content_save( $post_id ) {
        if (!isset($_POST['plan_desc_content'])) {
            return;
        }
        $post_type = get_post_type($post_id);
        if ( "price_plan" != $post_type ){
            return;
        }
        else{
            global $plan_desc_content;

            if(isset($_POST["plan_desc_content"]))
                $plan_desc_content = $_POST['plan_desc_content'];
            update_post_meta( $post_id, 'plan_desc_content', $plan_desc_content );
        }

    }
}

/* end for style 3 */
		
	
	add_action( 'add_meta_boxes', 'plan_text_box' );

	if(!function_exists('plan_text_box')) {
        function plan_text_box() {
            add_meta_box(
                'plan_text_box',
                __( 'Enter no. of post in the package', 'listingpro-plugin' ),
                'plan_text_content',
                'price_plan'
            );
        }
    }

    if(!function_exists('plan_text_content')) {
        function plan_text_content( $post ) {

            $plan_text = get_post_meta( $post->ID, 'plan_text', true );

            echo '<label for="plan_text"></label>';
            echo '<input type="text" id="plan_text" name="plan_text" placeholder="'.esc_html__('Total Posts in Package', 'listingpro-plugin' ).'" value="';
            echo $plan_text;
            echo '">';

        }
    }



	add_action( 'save_post', 'plan_text_save' );

	if(!function_exists('plan_text_save')) {
        function plan_text_save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{
                global $plan_text;

                if(isset($_POST["plan_text"]))
                    $plan_text = $_POST['plan_text'];
                update_post_meta( $post_id, 'plan_text', $plan_text );
            }

        }
    }

	
	//Title color option for meta boxs starts
	add_action( 'add_meta_boxes', 'plan_color_box' );

	if(!function_exists('plan_color_box')) {
        function plan_color_box() {
            add_meta_box(
                'plan_color_box',
                __( 'Select color for the title background box', 'listingpro-plugin' ),
                'plan_title_content_color',
                'price_plan'
            );
        }
    }

    if(!function_exists('plan_title_content_color')) {
        function plan_title_content_color( $post ) {

            $plan_title_color = get_post_meta( $post->ID, 'plan_title_color', true );

            echo '<label for="plan_title_color"></label>';
            echo '<input type="color" id="plan_title_color" name="plan_title_color"  value="';
            echo $plan_title_color;
            echo '">';

        }
    }

	add_action( 'save_post', 'plan_text_save_color' );

	if(!function_exists('plan_text_save_color')) {
        function plan_text_save_color( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{
                global $plan_title_color;

                if(isset($_POST["plan_title_color"]))
                    $plan_title_color = $_POST['plan_title_color'];
                update_post_meta( $post_id, 'plan_title_color', $plan_title_color );
            }
        }
    }

	//Title color option for meta boxs endeds

	
	/* for monthly yearly */
	$listingpro_month_year_plans = lp_theme_option('listingpro_month_year_plans');
	if($listingpro_month_year_plans=="yes"){
		add_action( 'add_meta_boxes', 'plan_duration_type_box' );

		if(!function_exists('plan_duration_type_box')) {
            function plan_duration_type_box() {
                add_meta_box(
                    'plan_duration_type',
                    __( 'Duration Type', 'listingpro-plugin' ),
                    'plan_duration_type',
                    'price_plan'
                );
            }
        }

        if(!function_exists('plan_duration_type')) {
            function plan_duration_type( $post ) {

                $plan_duration_type = get_post_meta( $post->ID, 'plan_duration_type', true );

                echo '<label for="plan_duration_type"></label>';

                echo '<select name="plan_duration_type" id="plan_duration_type">';

                if( !empty ( $plan_duration_type ) ){

                    if( $plan_duration_type=="monthly" ){

                        echo '<option data-days="30" value="monthly" selected>'.esc_html__('Monthly', 'listingpro-plugin').'</option>';
                        echo '<option data-days="" value="default">'.esc_html__('Default', 'listingpro-plugin').'</option>';
                        echo '<option data-days="365" value="yearly">'.esc_html__('Yearly', 'listingpro-plugin').'</option>';

                    }
                    else if( $plan_duration_type=="yearly" ){
                        echo '<option data-days="" value="default">'.esc_html__('Default', 'listingpro-plugin').'</option>';
                        echo '<option data-days="30" value="monthly">'.esc_html__('Monthly', 'listingpro-plugin').'</option>';
                        echo '<option data-days="365" value="yearly" selected>'.esc_html__('Yearly', 'listingpro-plugin').'</option>';
                    }elseif( $plan_duration_type=="default" ){
                        echo '<option data-days="" value="default" selected>'.esc_html__('Default', 'listingpro-plugin').'</option>';
                        echo '<option data-days="30" value="monthly">'.esc_html__('Monthly', 'listingpro-plugin').'</option>';
                        echo '<option data-days="365" value="yearly">'.esc_html__('Yearly', 'listingpro-plugin').'</option>';
                    }

                }
                else{
                    echo '<option data-days="" value="default">'.esc_html__('Default', 'listingpro-plugin').'</option>';
                    echo '<option data-days="30" value="monthly">'.esc_html__('Monthly', 'listingpro-plugin').'</option>';
                    echo '<option data-days="365" value="yearly">'.esc_html__('Yearly', 'listingpro-plugin').'</option>';
                }

                echo '</select>';

            }
        }



		add_action( 'save_post', 'plan_duration_type_save' );

		if(!function_exists('plan_duration_type_save')) {
            function plan_duration_type_save( $post_id ) {
                if (!isset($_POST['lp_metaplans_hidden'])) {
                    return;
                }
                $post_type = get_post_type($post_id);
                if ( "price_plan" != $post_type ){
                    return;
                }
                else{
                    global $plan_duration_type;

                    if(isset($_POST["plan_duration_type"]))
                        $plan_duration_type = $_POST['plan_duration_type'];
                    update_post_meta( $post_id, 'plan_duration_type', $plan_duration_type );
                }

            }
        }

	}

	/* for plan image limit box */
	add_action( 'add_meta_boxes', 'plan_no_of_image_box' );

	if(!function_exists('plan_no_of_image_box')) {
        function plan_no_of_image_box() {
            add_meta_box(
                'plan_no_of_image_box',
                __( 'Max. Images In Gallery (add only integer value)', 'listingpro-plugin' ),
                'plan_no_of_image_content',
                'price_plan'
            );
        }
    }

    if(!function_exists('plan_no_of_image_content')) {
        function plan_no_of_image_content( $post ) {

            $plan_no_of_img = get_post_meta( $post->ID, 'plan_no_of_img', true );

            echo '<label for="plan_no_of_img"></label>';
            echo '<input type="text" id="plan_no_of_img" name="plan_no_of_img" placeholder="" value="';
            echo $plan_no_of_img;
            echo '">';
            echo '<p class="lp-new-price-des">'.esc_html__('Empty field will be considered as unlimited" ', 'listingpro-plugin').'</p>';

        }
    }



	add_action( 'save_post', 'plan_no_of_images_Save' );

	if(!function_exists('plan_no_of_images_Save')) {
        function plan_no_of_images_Save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{
                global $plan_no_of_img;

                if(isset($_POST["plan_no_of_img"]))
                    $plan_no_of_img = $_POST['plan_no_of_img'];
                update_post_meta( $post_id, 'plan_no_of_img', $plan_no_of_img );
            }

        }
    }



	/* for plan image size limite */
	add_action( 'add_meta_boxes', 'plan_img_limit_box' );

	if(!function_exists('plan_img_limit_box')) {
        function plan_img_limit_box() {
            add_meta_box(
                'plan_img_limit_box',
                __( 'Max Gallery Size (Put only integer value) units are in MBs', 'listingpro-plugin' ),
                'plan_img_limit_content',
                'price_plan'
            );
        }
    }

    if(!function_exists('plan_img_limit_content')) {
        function plan_img_limit_content( $post ) {

            $plan_img_lmt = get_post_meta( $post->ID, 'plan_img_lmt', true );

            echo '<label for="plan_img_lmt"></label>';
            echo '<input type="text" id="plan_img_lmt" name="plan_img_lmt" placeholder="" value="';
            echo $plan_img_lmt;
            echo '">';
            echo '<p class="lp-new-price-des">'.esc_html__('Empty field will be considered as unlimited', 'listingpro-plugin').'</p>';

        }
    }



	add_action( 'save_post', 'plan_img_limite_save' );

	if(!function_exists('plan_img_limite_save')) {
        function plan_img_limite_save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{
                global $plan_img_lmt;

                if(isset($_POST["plan_img_lmt"]))
                    $plan_img_lmt = $_POST['plan_img_lmt'];
                update_post_meta( $post_id, 'plan_img_lmt', $plan_img_lmt );
            }

        }
    }



	/* for plan price box */
	add_action( 'add_meta_boxes', 'plan_price_box' );

	if(!function_exists('plan_price_box')) {
        function plan_price_box() {
            add_meta_box(
                'plan_price_box',
                __( 'Price (Do not use currency sign)', 'listingpro-plugin' ),
                'plan_price_content',
                'price_plan'
            );
        }
    }

    if(!function_exists('plan_price_content')) {
        function plan_price_content( $post ) {

            $plan_price = get_post_meta( $post->ID, 'plan_price', true );

            echo '<label for="plan_price"></label>';
            echo '<input type="text" id="plan_price" name="plan_price" placeholder="" value="';
            echo $plan_price;
            echo '">';
            echo '<p class="lp-new-price-des">'.esc_html__('Empty field will be considered as free plan. Free plan option only works in "Pay per Listing" ', 'listingpro-plugin').'</p>';

        }
    }



	add_action( 'save_post', 'plan_price_save' );

	if(!function_exists('plan_price_save')) {
        function plan_price_save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{
                global $plan_price;

                if(isset($_POST["plan_price"]))
                    $plan_price = $_POST['plan_price'];
                update_post_meta( $post_id, 'plan_price', $plan_price );
            }

        }
    }



	/* for plans duration */

	add_action( 'add_meta_boxes', 'plan_time_box' );

	if(!function_exists('plan_time_box')) {
        function plan_time_box() {
            $listingpro_month_year_plans = lp_theme_option('listingpro_month_year_plans');
            $plan_typ_div_id = 'plan_time_box';
            if($listingpro_month_year_plans=="yes"){
                /* for backward compatibility */
                $plan_typ_div_id = 'plan_time_monthyear_box';
            }

            add_meta_box(
                $plan_typ_div_id,
                __( 'Duration( in days )', 'listingpro-plugin' ),
                'plan_time_content',
                'price_plan'
            );
        }
    }

    if(!function_exists('plan_time_content')) {
        function plan_time_content( $post ) {

            $plan_time = get_post_meta( $post->ID, 'plan_time', true );

            echo '<label for="plan_time"></label>';
            echo '<input type="text" id="plan_time" name="plan_time" placeholder="Leave empty for unlimited" value="';
            echo $plan_time;
            echo '">';

        }
    }



	add_action( 'save_post', 'plan_time_save' );

	if(!function_exists('plan_time_save')) {
        function plan_time_save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{
                global $plan_time;

                if(isset($_POST["plan_time"]))
                    $plan_time = $_POST['plan_time'];
                update_post_meta( $post_id, 'plan_time', $plan_time );
            }

        }
    }

	

	add_action( 'add_meta_boxes', 'plan_free_continue' );

	if(!function_exists('plan_free_continue')) {
        function plan_free_continue() {
            add_meta_box(
                'plan_free_continue',
                __( 'Continue free plan after expire', 'listingpro-plugin' ),
                'free_plan_continue_content',
                'price_plan'
            );
        }
    }

    if(!function_exists('free_plan_continue_content')){
        function free_plan_continue_content( $post ) {

            $f_plan_continue = get_post_meta( $post->ID, 'f_plan_continue', true );
            $checked = '';
            if($f_plan_continue == 'true'){
                $checked = 'checked';
            }
            echo '<label class="switch lp_f_plan_continue">';
            echo '<input '.$checked.' type="checkbox" id="f_plan_continue" name="f_plan_continue" value="';
            echo wp_kses_post($f_plan_continue);
            echo '">';
            echo '<span class="slider round"></span>';
            echo'</label>';
            echo '<label for="f_plan_continue">  Check if you want users to continue with free plan after "Expire". This option only works with free plan</label><br/>';

        }
    }


	add_action( 'save_post', 'plan_free_save' );

    if(!function_exists('plan_free_save')) {
        function plan_free_save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{

                if(isset($_POST["f_plan_continue"])){
                    update_post_meta( $post_id, 'f_plan_continue', 'true' );
                }
                else{
                    update_post_meta( $post_id, 'f_plan_continue', 'false' );
                }
            }
        }
    }

	
	add_action( 'add_meta_boxes', 'plan_hot_box' );

    if(!function_exists('plan_hot_box')) {
        function plan_hot_box() {
            add_meta_box(
                'plan_hot_box',
                __( 'Hot Plan', 'listingpro-plugin' ),
                'plan_hot_content',
                'price_plan'
            );
        }
    }

    if(!function_exists('plan_hot_content')) {
        function plan_hot_content( $post ) {

            $plan_hot = get_post_meta( $post->ID, 'plan_hot', true );
            $checked = '';
            if($plan_hot == 'true'){
                $checked = 'checked';
            }
            echo '<label class="switch lp_f_plan_continue">';
            echo '<input '.$checked.' type="checkbox" id="plan_hot" name="plan_hot" value="';
            echo wp_kses_post($plan_hot);
            echo '">';
            echo '<span class="slider round"></span>';
            echo'</label>';
            echo '<label for="plan_hot">  Check if you want to make this plan "Hot"</label><br/>';

        }
    }



	add_action( 'save_post', 'plan_hot_save' );

    if(!function_exists('plan_hot_save')) {
        function plan_hot_save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            else{

                if(isset($_POST["plan_hot"])){
                    update_post_meta( $post_id, 'plan_hot', 'true' );
                }
                else{
                    update_post_meta( $post_id, 'plan_hot', 'false' );
                }
            }
        }
    }

		
	/* for new option */

	$plans_by_cat = lp_theme_option('listingpro_plans_cats');
	if($plans_by_cat=='yes'){

		add_action( 'add_meta_boxes', 'plan_usage_type' );


		if(!function_exists('plan_usage_type')) {
            function plan_usage_type() {
                add_meta_box(
                    'plan_usage',
                    __( 'Plan Usage', 'listingpro-plugin' ),
                    'plan_usge_for',
                    'price_plan'
                );
            }
        }

        if(!function_exists('plan_usge_for')) {
            function plan_usge_for( $post ) {

                $plan_usge_for = get_post_meta( $post->ID, 'plan_usge_for', true );

                echo '<label for="plan_usge_for"></label>';

                echo '<select name="plan_usge_for" id="plan_usge_for" data-current="'.$plan_usge_for.'">';
                if( !empty ( $plan_usge_for ) ){

                    if( $plan_usge_for=="default" ){

                        echo '<option value="default" selected>'.esc_html__('General', 'listingpro-plugin').'</option>';
                        echo '<option value="by category">'.esc_html__('By Category', 'listingpro-plugin').'</option>';

                    }
                    else if( $plan_usge_for=="by category" ){
                        echo '<option value="by category" selected>'.esc_html__('By Category', 'listingpro-plugin').'</option>';
                        echo '<option value="default">'.esc_html__('General', 'listingpro-plugin').'</option>';
                    }

                }
                else{
                    echo '<option value="default">'.esc_html__('General', 'listingpro-plugin').'</option>';
                    echo '<option value="by category">'.esc_html__('By Category', 'listingpro-plugin').'</option>';
                }

                echo '</select>';

            }
        }



		add_action( 'save_post', 'plan_usage_save' );

		if(!function_exists('plan_usage_save')){
            function plan_usage_save( $post_id ) {
                if (!isset($_POST['lp_metaplans_hidden'])) {
                    return;
                }
                $post_type = get_post_type($post_id);
                if ( "price_plan" != $post_type ){
                    return;
                }
                else{
                    global $plan_usge_for;

                    if(isset($_POST["plan_usge_for"]))
                        $plan_usge_for = $_POST['plan_usge_for'];
                    update_post_meta( $post_id, 'plan_usge_for', $plan_usge_for );
                }

            }
        }


		/* ajax categorie dropdown */
		add_action( 'add_meta_boxes', 'plan_parent_cats' );

		if(!function_exists('plan_parent_cats')) {
            function plan_parent_cats() {
                add_meta_box(
                    'plan_cats',
                    __( 'Category', 'listingpro-plugin' ),
                    'plan_cats_callback',
                    'price_plan'
                );
            }
        }

        if(!function_exists('plan_cats_callback')) {
            function plan_cats_callback( $post ) {

                $parent_cats = lp_get_all_cats_array(false);
                $plan_assigned_cats = get_post_meta( $post->ID, 'lp_selected_cats', true );
                $alreadyCheckedCats = array();
                if(!empty($plan_assigned_cats)){
                    //$savedCatsArray = explode(',',$plan_assigned_cats);
                    $savedCatsArray =$plan_assigned_cats;
                    if(!empty($savedCatsArray)){
                        foreach($savedCatsArray as $catID){
                            $seletetTerm = get_term_by( 'id', $catID, 'listing-category' );
                            if(!empty($seletetTerm) && !is_wp_error($seletetTerm)){
                                $termName = $seletetTerm->name;
                                $alreadyCheckedCats[$catID] = $termName;
                                echo '<label for="plan_cats'.$catID.'"><input type="checkbox" name="lp_selected_cats[]" id="plan_cats'.$catID.'" value="'.$catID.'" checked>'.$termName.'</input></label>';
                            }
                        }

                        if(!empty($parent_cats)){
                            $parent_cats = array_diff_assoc($parent_cats,$alreadyCheckedCats);
                            if(!empty($parent_cats)){
                                foreach($parent_cats as $catID=>$catName){
                                    echo '<label for="plan_cats'.$catID.'"><input type="checkbox" name="lp_selected_cats[]"  id="plan_cats'.$catID.'" value="'.$catID.'">'.$catName.'</input></label>';
                                }
                            }
                        }
                    }
                }else{

                    $parent_cats = lp_get_all_cats_array(false);
                    if(!empty($parent_cats)){
                        foreach($parent_cats as $catID=>$catName){
                            echo '<label for="plan_cats'.$catID.'"><input type="checkbox" name="lp_selected_cats[]"  id="plan_cats'.$catID.'" value="'.$catID.'">'.$catName.'</input></label>';
                        }
                    }

                }

                echo '<input type="button" class="check button checkbox-all-btn" data-clickval="'.esc_html__('Unselect all', 'listingpro-plugin').'" value="'.esc_html__('Select all', 'listingpro-plugin').'">';

            }
        }



		add_action( 'save_post', 'plan_cats_save' );

        if(!function_exists('plan_cats_save')) {
            function plan_cats_save( $post_id ) {
                if (!isset($_POST['lp_metaplans_hidden'])) {
                    return;
                }
                $post_type = get_post_type($post_id);
                if ( "price_plan" != $post_type ){
                    return;
                }
                else{
                    global $plan_assigned_cats;
                    if(isset($_POST["lp_selected_cats"])){
                        $plan_assigned_cats = $_POST['lp_selected_cats'];
                    }
                    $lp_prev_selected_cats = get_post_meta( $post_id, 'lp_selected_cats', true );
                    update_post_meta( $post_id, 'lp_selected_cats', $plan_assigned_cats );
                    /* saving data to category meta */
                    if(!empty($plan_assigned_cats)){
                        foreach($plan_assigned_cats as $cat_id){
                            $lp_attached_plan = get_term_meta($cat_id, 'lp_attached_plans', true);
                            if(!empty($lp_attached_plan)){
                                /* if already saved in meta */
                                if (is_array($lp_attached_plan)) {
                                    $lp_attached_plans = array_unique($lp_attached_plan);
                                    array_push($lp_attached_plans, $post_id);
                                }
                                else{
                                    $lp_attached_plans[] = $post_id;
                                }
                            }else{
                                /* if not already */
                                $lp_attached_plans[0] = $post_id;
                            }
                            if ($lp_attached_plans !== NULL) {
                                update_term_meta($cat_id, 'lp_attached_plans', $lp_attached_plans);
                            }
                            elseif($lp_attached_plans == NULL || $lp_attached_plans == ''){
                                delete_term_meta($cat_id, 'lp_attached_plans');
                            }
                        }
                    }
                    elseif (!empty($lp_prev_selected_cats) && empty($plan_assigned_cats)) {
                        foreach ($lp_prev_selected_cats as $prev_cat_id) {
                            delete_term_meta($prev_cat_id, 'lp_attached_plans');
                        }
                    }
                    /* end of saving data to category meta */
                }
            }
        }

	}

	/* end for new option */



		
		
		
	add_action( 'add_meta_boxes', 'plan_contact_box' );

	if(!function_exists('plan_contact_box')) {
        function plan_contact_box() {
            $screens = array( 'price_plan');
            foreach ( $screens as $screen ) {
                add_meta_box(
                    'plan_contact_box',
                    __( 'Enable/Disable Listing Fields By Plan', 'listingpro-plugin' ),
                    'plan_contact_content',
                    $screen,
                    'normal',
                    'high'
                );
            }
        }
    }

	

        if(!function_exists('plan_contact_content')) {
            function plan_contact_content( $post ) {

                $contact_show = get_post_meta( $post->ID, 'contact_show', true );
                $checked = '';
                if($contact_show == 'true'){
                    $checked = 'checked';
                }
                ?>



                <div style="border-bottom: 1px solid #222;padding: 10px 0px; margin-bottom:30px">
                    <div style="width:100%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo '<input type="checkbox" id="bulk_enable_price_options" value="';
                        echo wp_kses_post('Enable All Fields');
                        echo '">';
                        echo '<span class="slider round"></span>';
                        echo'</label>';
                        echo __('<label for="bulk_enable_price_options"><b>Enable All Following</b></label>', 'listingpro-plugin');
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <?php
                ?>




                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php

                        echo '<label class="switch">';
                        echo '<input '.$checked.' type="checkbox" id="contact_show" name="contact_show" value="';
                        echo wp_kses_post($contact_show);
                        echo '">';
                        echo '<span class="slider round"></span>';
                        echo'</label>';

                        echo __('<label for="contact_show"><b>contact information</b></label>', 'listingpro-plugin');

                        $checked = get_post_meta( $post->ID, 'contact_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="contact_show_hide" name="contact_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';

                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <?php
                ?>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $map_show = get_post_meta( $post->ID, 'map_show', true );
                        $checked = '';
                        if($map_show == 'true'){
                            $checked = 'checked';
                        }
                        echo '<label class="switch">';
                        echo '<input '.$checked.' type="checkbox" id="map_show" name="map_show" value="';
                        echo wp_kses_post($map_show);
                        echo '">';
                        echo '<span class="slider round"></span>';
                        echo'</label>';
                        echo __('<label for="map_show"><b>Google map</b></label>', 'listingpro-plugin');

                        $checked =get_post_meta( $post->ID, 'map_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="map_show_hide" name="map_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';

                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <?php
                ?>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $video_show = get_post_meta( $post->ID, 'video_show', true );
                        $checked = '';
                        if($video_show == 'true'){
                            $checked = 'checked';
                        }
                        echo '<label class="switch">';
                        echo '<input '.$checked.' type="checkbox" id="video_show" name="video_show" value="';
                        echo wp_kses_post($video_show);
                        echo '">';
                        echo '<span class="slider round"></span>';
                        echo'</label>';
                        echo __('<label for="video_show"><b>video</b></label>', 'listingpro-plugin');

                        $checked =get_post_meta( $post->ID, 'video_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="video_show_hide" name="video_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';

                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <?php
                ?>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $gallery_show = get_post_meta( $post->ID, 'gallery_show', true );
                        $checked = '';
                        if($gallery_show == 'true'){
                            $checked = 'checked';
                        }
                        echo '<label class="switch">';
                        echo '<input '.$checked.' type="checkbox" id="gallery_show" name="gallery_show" value="';
                        echo wp_kses_post($gallery_show);
                        echo '">';
                        echo '<span class="slider round"></span>';
                        echo'</label>';
                        echo __('<label for="gallery_show"><b>Gallery</b></label>', 'listingpro-plugin');

                        $checked =get_post_meta( $post->ID, 'gall_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="gall_show_hide" name="gall_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <?php
                $meta_value_tagline = get_post_meta( $post->ID, 'listingproc_tagline', true );
                $checked = '';
                if($meta_value_tagline == 'true'){
                    $checked = 'checked';
                }
                ?>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <label class="switch">
                            <input <?php echo $checked; ?> type="checkbox" id="listingproc_tagline" name="listingproc_tagline" value="<?php echo wp_kses_post($meta_value_tagline); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_tagline"> <?php echo __('<b>Tagline</b>', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'tagline_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="tagline_show_hide" name="tagline_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_location = get_post_meta( $post->ID, 'listingproc_location', true );
                        $checked = '';
                        if($meta_value_location == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_location"  name="listingproc_location" value="<?php echo wp_kses_post($meta_value_location) ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_location"> <?php echo __('<b>Location.</b>', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'location_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="location_show_hide" name="location_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_web = get_post_meta( $post->ID, 'listingproc_website', true );
                        $checked = '';
                        if($meta_value_web == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_website" name="listingproc_website" value="<?php echo wp_kses_post($meta_value_web); ?>"/>
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_website"><?php echo __('<b>Website.</b>', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'website_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="website_show_hide" name="website_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_bookings     = get_post_meta( $post->ID, 'listingproc_bookings', true );
                        $checked = '';
                        if($meta_value_bookings == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_bookings" name="listingproc_bookings" value="<?php echo wp_kses_post($meta_value_bookings); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_bookings"><?php echo __('<b>Appointments.</b>', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'bookings_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="bookings_show_hide" name="bookings_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_leadform     = get_post_meta( $post->ID, 'listingproc_leadform', true );
                        $checked = '';
                        if($meta_value_leadform == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_leadform" name="listingproc_leadform" value="<?php echo wp_kses_post($meta_value_leadform); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_leadform"><?php echo __('<b>Lead Form.</b>', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'leadform_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="leadform_show_hide" name="leadform_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>


                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_social     = get_post_meta( $post->ID, 'listingproc_social', true );
                        $checked = '';
                        if($meta_value_social == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_social" name="listingproc_social" value="<?php echo wp_kses_post($meta_value_social); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_social"><?php echo __('<b>Social Media links.</b>', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'social_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="social_show_hide" name="social_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <?php
                $meta_value_faq     = get_post_meta( $post->ID, 'listingproc_faq', true );
                $checked = '';
                if($meta_value_faq == 'true'){
                    $checked = 'checked';
                }
                ?>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">

                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_faq" name="listingproc_faq" value="<?php echo wp_kses_post($meta_value_faq); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_faq"><?php echo __('<b>FAQs list.</b>', 'listingpro-plugin'); ?></label>
                    </div>
                    <?php
                    $checked =get_post_meta( $post->ID, 'faqs_show_hide', 'true' );
                    if(empty($checked)){
                        $checked = 'checked';
                    }
                    ?>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="faqs_show_hide" name="faqs_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_price     = get_post_meta( $post->ID, 'listingproc_price', true );
                        $checked = '';
                        if($meta_value_price == 'true'){
                            $checked = 'checked';
                        }
                        ?>

                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_price" name="listingproc_price" value="<?php echo wp_kses_post($meta_value_price); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_price"><?php echo __('<b>Price Range.</b>', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'price_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="price_show_hide" name="price_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_tag_key     = get_post_meta( $post->ID, 'listingproc_tag_key', true );
                        $checked = '';
                        if($meta_value_tag_key == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_tag_key" name="listingproc_tag_key" value="<?php echo wp_kses_post($meta_value_tag_key); ?>"/>
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_tag_key"><?php echo __('<b>Tags or Keywords.</b>', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'tags_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="tags_show_hide" name="tags_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_bhours     = get_post_meta( $post->ID, 'listingproc_bhours', true );
                        $checked = '';
                        if($meta_value_bhours == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_bhours" name="listingproc_bhours" value="<?php echo wp_kses_post($meta_value_bhours); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_bhours"><?php echo __('<b>Business Hours.</b>', 'listingpro-plugin'); ?></label>

                        <?php
                        $checked =get_post_meta( $post->ID, 'bhours_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="bhours_show_hide" name="bhours_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        /* for 2.0 */
                        $meta_value_resurva     = get_post_meta( $post->ID, 'listingproc_plan_reservera', true );
                        $checked = '';
                        if($meta_value_resurva == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_plan_reservera" name="listingproc_plan_reservera" value="<?php echo wp_kses_post($meta_value_resurva); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_plan_reservera"><?php echo __('<b>Reserva Booking.</b>', 'listingpro-plugin'); ?></label>

                        <?php
                        $checked =get_post_meta( $post->ID, 'reserva_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="reserva_show_hide" name="reserva_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_timket     = get_post_meta( $post->ID, 'listingproc_plan_timekit', true );
                        $checked = '';
                        if($meta_value_timket == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_plan_timekit" name="listingproc_plan_timekit" value="<?php echo wp_kses_post($meta_value_timket); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_plan_timekit"><?php echo __('<b>Timekit Booking.</b>', 'listingpro-plugin'); ?></label>

                        <?php
                        $checked =get_post_meta( $post->ID, 'timekit_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="timekit_show_hide" name="timekit_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_menu     = get_post_meta( $post->ID, 'listingproc_plan_menu', true );
                        $checked = '';
                        if($meta_value_menu == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_plan_menu" name="listingproc_plan_menu" value="<?php echo wp_kses_post($meta_value_menu); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_plan_menu"><?php echo __('<b>Menu.</b>', 'listingpro-plugin'); ?></label>

                        <?php
                        $checked =get_post_meta( $post->ID, 'menu_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="menu_show_hide" name="menu_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_announcment     = get_post_meta( $post->ID, 'listingproc_plan_announcment', true );
                        $checked = '';
                        if($meta_value_announcment == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_plan_announcment" name="listingproc_plan_announcment" value="<?php echo wp_kses_post($meta_value_announcment); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_plan_announcment"><?php echo __('<b>Announcement.</b>', 'listingpro-plugin'); ?></label>

                        <?php
                        $checked =get_post_meta( $post->ID, 'announcment_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="announcment_show_hide" name="announcment_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_deals     = get_post_meta( $post->ID, 'listingproc_plan_deals', true );
                        $checked = '';
                        if($meta_value_deals == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_plan_deals" name="listingproc_plan_deals" value="<?php echo wp_kses_post($meta_value_deals); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="listingproc_plan_deals"><?php echo __('<b>Deals, Offers, Discounts.</b>', 'listingpro-plugin'); ?></label>

                        <?php
                        $checked =get_post_meta( $post->ID, 'deals_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="deals_show_hide" name="deals_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $meta_value_campaigns    = get_post_meta( $post->ID, 'listingproc_plan_campaigns', true );
                        $checked = '';
                        if($meta_value_campaigns == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="listingproc_plan_campaigns" name="listingproc_plan_campaigns" value="<?php echo wp_kses_post($meta_value_campaigns); ?>" />
                            <span class="slider round"></span>
                        </label>

                        <label for="listingproc_plan_campaigns"><?php echo __('<b>competitor campaigns</b> on Listing Detail Page', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'metacampaign_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="metacampaign_show_hide" name="metacampaign_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>
                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px; display:none">
                    <div style="width:60%;float:left">
                        <?php
                        $lp_featured_imageplan    = get_post_meta( $post->ID, 'lp_featured_imageplan', true );
                        $checked = '';
                        if($lp_featured_imageplan == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="lp_featured_imageplan" name="lp_featured_imageplan" value="<?php echo wp_kses_post($lp_featured_imageplan); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="lp_featured_imageplan"><?php echo __('<b>featured image</b> on Listing Detail Page', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'featimg_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo ' <input type="checkbox" id="featimg_show_hide" name="featimg_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>


                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $lp_eventsplan    = get_post_meta( $post->ID, 'lp_eventsplan', true );
                        $checked = '';
                        if($lp_eventsplan == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="lp_eventsplan" name="lp_eventsplan" value="<?php echo wp_kses_post($lp_eventsplan); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="lp_eventsplan"><?php echo __('<b>Events in listings', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'events_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo '<input type="checkbox" id="events_show_hide" name="events_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>


                <div style="border-bottom: 1px solid #ccc;padding: 10px 0px;">
                    <div style="width:60%;float:left">
                        <?php
                        $lp_hidegooglead    = get_post_meta( $post->ID, 'lp_hidegooglead', true );
                        $checked = '';
                        if($lp_hidegooglead == 'true'){
                            $checked = 'checked';
                        }
                        ?>
                        <label class="switch">
                            <input <?php echo $checked ?> type="checkbox" id="lp_hidegooglead" name="lp_hidegooglead" value="<?php echo wp_kses_post($lp_hidegooglead); ?>" />
                            <span class="slider round"></span>
                        </label>
                        <label for="lp_hidegooglead"><?php echo __('<b>Hide Google Ads', 'listingpro-plugin'); ?></label>
                        <?php
                        $checked =get_post_meta( $post->ID, 'googlead_show_hide', 'true' );
                        if(empty($checked)){
                            $checked = 'checked';
                        }
                        ?>
                    </div>
                    <div style="width:40%;float:left">
                        <?php
                        echo '<label class="switch">';
                        echo '<input type="checkbox" id="googlead_show_hide" name="googlead_show_hide" '.$checked.'  />';
                        echo '<span class="slider round slider2"></span>';
                        echo'</label>';
                        ?>
                    </div>
                    <br clear="all" />
                </div>





                <?php
                $lp_adswithplan    = get_post_meta( $post->ID, 'lp_ads_wih_plan', true );
                ?>

                <input type="hidden" id="lp_ads_wih_plan" placeholder="5" name="lp_ads_wih_plan" value="<?php echo wp_kses_post($lp_adswithplan); ?>" />

                <?php
                wp_nonce_field( '', 'lp_metaplans_hidden' );
            }
        }


	add_action( 'save_post', 'plan_contact_box_save' );

	if(!function_exists('plan_contact_box_save')) {
        function plan_contact_box_save( $post_id ) {
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            $post_type = get_post_type($post_id);
            if ( "price_plan" != $post_type ){
                return;
            }
            if (!isset($_POST['lp_metaplans_hidden'])) {
                return;
            }
            else{
                /*  */

                if(isset($_POST["lp_ads_wih_plan"])){
                    $freeads = $_POST["lp_ads_wih_plan"];
                    if(!empty($freeads)){
                        update_post_meta( $post_id, 'lp_ads_wih_plan', $freeads );
                    }else{
                        update_post_meta( $post_id, 'lp_ads_wih_plan', 0 );
                    }
                }else{
                    update_post_meta( $post_id, 'lp_ads_wih_plan', 0 );
                }

                if(isset($_POST["lp_hidegooglead"])){
                    update_post_meta( $post_id, 'lp_hidegooglead', 'true' );
                }else{
                    update_post_meta( $post_id, 'lp_hidegooglead', 'false' );
                }




                if(isset($_POST["lp_eventsplan"])){
                    update_post_meta( $post_id, 'lp_eventsplan', 'true' );
                }else{
                    update_post_meta( $post_id, 'lp_eventsplan', 'false' );
                }


                if(isset($_POST["lp_featured_imageplan"])){
                    update_post_meta( $post_id, 'lp_featured_imageplan', 'true' );
                }else{
                    update_post_meta( $post_id, 'lp_featured_imageplan', 'false' );
                }

                if(isset($_POST["listingproc_plan_campaigns"])){
                    update_post_meta( $post_id, 'listingproc_plan_campaigns', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_plan_campaigns', 'false' );
                }

                if(isset($_POST["listingproc_plan_deals"])){
                    update_post_meta( $post_id, 'listingproc_plan_deals', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_plan_deals', 'false' );
                }

                if(isset($_POST["listingproc_plan_timekit"])){
                    update_post_meta( $post_id, 'listingproc_plan_timekit', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_plan_timekit', 'false' );
                }

                if(isset($_POST["listingproc_plan_announcment"])){
                    update_post_meta( $post_id, 'listingproc_plan_announcment', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_plan_announcment', 'false' );
                }

                if(isset($_POST["listingproc_plan_menu"])){
                    update_post_meta( $post_id, 'listingproc_plan_menu', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_plan_menu', 'false' );
                }

                if(isset($_POST["listingproc_plan_reservera"])){
                    update_post_meta( $post_id, 'listingproc_plan_reservera', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_plan_reservera', 'false' );
                }


                /*  */

                if(isset($_POST["contact_show"])){
                    update_post_meta( $post_id, 'contact_show', 'true' );
                }else{
                    update_post_meta( $post_id, 'contact_show', 'false' );
                }

                if(isset($_POST["map_show"])){
                    update_post_meta( $post_id, 'map_show', 'true' );
                }else{
                    update_post_meta( $post_id, 'map_show', 'false' );
                }

                if(isset($_POST["video_show"])){
                    update_post_meta( $post_id, 'video_show', 'true' );
                }else{
                    update_post_meta( $post_id, 'video_show', 'false' );
                }

                if(isset($_POST["gallery_show"])){
                    update_post_meta( $post_id, 'gallery_show', 'true' );
                }else{
                    update_post_meta( $post_id, 'gallery_show', 'false' );
                }

                if(isset($_POST["gallery_show"])){
                    update_post_meta( $post_id, 'gallery_show', 'true' );
                }else{
                    update_post_meta( $post_id, 'gallery_show', 'false' );
                }


                if(isset($_POST["listingproc_tagline"])){
                    update_post_meta( $post_id, 'listingproc_tagline', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_tagline', 'false' );
                }

                if(isset($_POST["listingproc_location"])){
                    update_post_meta( $post_id, 'listingproc_location', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_location', 'false' );
                }

                if(isset($_POST["listingproc_website"])){
                    update_post_meta( $post_id, 'listingproc_website', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_website', 'false' );
                }

                if(isset($_POST["listingproc_social"])){
                    update_post_meta( $post_id, 'listingproc_social', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_social', 'false' );
                }
                if(isset($_POST["listingproc_leadform"])){
                    update_post_meta( $post_id, 'listingproc_leadform', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_leadform', 'false' );
                }
                if(isset($_POST["listingproc_bookings"])){
                    update_post_meta( $post_id, 'listingproc_bookings', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_bookings', 'false' );
                }
                if(isset($_POST["listingproc_faq"])){
                    update_post_meta( $post_id, 'listingproc_faq', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_faq', 'false' );
                }

                if(isset($_POST["listingproc_price"])){
                    update_post_meta( $post_id, 'listingproc_price', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_price', 'false' );
                }

                if(isset($_POST["listingproc_tag_key"])){
                    update_post_meta( $post_id, 'listingproc_tag_key', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_tag_key', 'false' );
                }

                if(isset($_POST["listingproc_bhours"])){
                    update_post_meta( $post_id, 'listingproc_bhours', 'true' );
                }else{
                    update_post_meta( $post_id, 'listingproc_bhours', 'false' );
                }

                /* for show hide checkbox */

                if(isset($_POST["contact_show_hide"])){
                    update_post_meta( $post_id, 'contact_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'contact_show_hide', 'true' );
                }

                if(isset($_POST["map_show_hide"])){
                    update_post_meta( $post_id, 'map_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'map_show_hide', 'true' );
                }

                if(isset($_POST["video_show_hide"])){
                    update_post_meta( $post_id, 'video_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'video_show_hide', 'true' );
                }

                if(isset($_POST["gall_show_hide"])){
                    update_post_meta( $post_id, 'gall_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'gall_show_hide', 'true' );
                }

                if(isset($_POST["tagline_show_hide"])){
                    update_post_meta( $post_id, 'tagline_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'tagline_show_hide', 'true' );
                }

                if(isset($_POST["location_show_hide"])){
                    update_post_meta( $post_id, 'location_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'location_show_hide', 'true' );
                }

                if(isset($_POST["website_show_hide"])){
                    update_post_meta( $post_id, 'website_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'website_show_hide', 'true' );
                }

                if(isset($_POST["social_show_hide"])){
                    update_post_meta( $post_id, 'social_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'social_show_hide', 'true' );
                }

                if(isset($_POST["leadform_show_hide"])){
                    update_post_meta( $post_id, 'leadform_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'leadform_show_hide', 'true' );
                }

                if(isset($_POST["faqs_show_hide"])){
                    update_post_meta( $post_id, 'faqs_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'faqs_show_hide', 'true' );
                }
                if(isset($_POST["bookings_show_hide"])){
                    update_post_meta( $post_id, 'bookings_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'bookings_show_hide', 'true' );
                }
                if(isset($_POST["price_show_hide"])){
                    update_post_meta( $post_id, 'price_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'price_show_hide', 'true' );
                }

                if(isset($_POST["tags_show_hide"])){
                    update_post_meta( $post_id, 'tags_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'tags_show_hide', 'true' );
                }

                if(isset($_POST["bhours_show_hide"])){
                    update_post_meta( $post_id, 'bhours_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'bhours_show_hide', 'true' );
                }

                if(isset($_POST["reserva_show_hide"])){
                    update_post_meta( $post_id, 'reserva_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'reserva_show_hide', 'true' );
                }

                if(isset($_POST["timekit_show_hide"])){
                    update_post_meta( $post_id, 'timekit_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'timekit_show_hide', 'true' );
                }

                if(isset($_POST["menu_show_hide"])){
                    update_post_meta( $post_id, 'menu_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'menu_show_hide', 'true' );
                }

                if(isset($_POST["announcment_show_hide"])){
                    update_post_meta( $post_id, 'announcment_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'announcment_show_hide', 'true' );
                }

                if(isset($_POST["deals_show_hide"])){
                    update_post_meta( $post_id, 'deals_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'deals_show_hide', 'true' );
                }

                if(isset($_POST["metacampaign_show_hide"])){
                    update_post_meta( $post_id, 'metacampaign_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'metacampaign_show_hide', 'true' );
                }

                if(isset($_POST["featimg_show_hide"])){
                    update_post_meta( $post_id, 'featimg_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'featimg_show_hide', 'true' );
                }


                if(isset($_POST["events_show_hide"])){
                    update_post_meta( $post_id, 'events_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'events_show_hide', 'true' );
                }


                //show hide
                if(isset($_POST["googlead_show_hide"])){
                    update_post_meta( $post_id, 'googlead_show_hide', '' );
                }else{
                    update_post_meta( $post_id, 'googlead_show_hide', 'true' );
                }

                /* end for show hide checkbox */

            }
        }
    }
