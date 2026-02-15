<?php
/*********************
 * Circus Theme Setup
 ********************/
function circus_theme_setup() {

    // Featured images
    add_theme_support('post-thumbnails');

    // Dynamic <title> tag
    add_theme_support('title-tag');

    // HTML5 support
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));

    // Custom logo support
    add_theme_support('custom-logo');

    // Register navigation menu
    register_nav_menus(array('primary' => __('Primary Menu', 'circus'),));
}
add_action('after_setup_theme', 'circus_theme_setup');

/*********************
 * Enqueue Section
 ********************/
function circus_enqueue_files() {
    // CSS
    wp_enqueue_style('circus-style', get_stylesheet_uri(), array(), filemtime(get_template_directory() . '/style.css'));

     // JS
    wp_enqueue_script('circus-main-js', get_template_directory_uri() . '/assets/js/main.js', array(), filemtime(get_template_directory() . '/assets/js/main.js'), true);
}
add_action('wp_enqueue_scripts', 'circus_enqueue_files');