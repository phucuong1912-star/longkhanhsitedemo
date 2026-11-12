<?php
/**
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */


    get_header();
    include(locate_template('templates/author/author-banner.php'));
    include(locate_template('templates/author/author-content.php'));
    get_footer();

