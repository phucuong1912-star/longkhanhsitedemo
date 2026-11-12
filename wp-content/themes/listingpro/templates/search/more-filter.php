<div class="outer_all_page_overflow">
<?php 
    $term_id = '';
    if( !isset($_GET['s'])){
        $queried_object = get_queried_object();
        $term_id = $queried_object->term_id;
    }elseif( isset($_GET['lp_s_cat']) ){
        $term_id = wp_kses_post($_GET['lp_s_cat']);
    }
    echo lp_get_extrafield_in_filterAjax($term_id);
?>
</div>