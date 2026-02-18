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
    add_theme_support('custom-logo', array(
        'height'      => 69,
        'width'       => 266,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Register navigation menu
    register_nav_menus(array(
    'primary' => __('Primary Menu', 'circus'),
    'footer'  => __('Footer Menu', 'circus'),
    ));

}
add_action('after_setup_theme', 'circus_theme_setup');

/*********************
 * Enqueue Section
 ********************/
function circus_enqueue_files() {

    // Google Fonts
    wp_enqueue_style('circus-fonts','https://fonts.googleapis.com/css2?family=Poltawski+Nowy:wght@500;600&display=swap',array(),null);

    // CSS
    wp_enqueue_style('circus-style', get_stylesheet_uri(), array(), filemtime(get_template_directory() . '/style.css'));

     // JS
    wp_enqueue_script('circus-main-js', get_template_directory_uri() . '/assets/js/main.js', array(), filemtime(get_template_directory() . '/assets/js/main.js'), true);

}
add_action('wp_enqueue_scripts', 'circus_enqueue_files');

function circus_enqueue_editor_canvas_assets() {

    // Poltawski (da radi i u iframeu)
    wp_enqueue_style(
        'circus-fonts',
        'https://fonts.googleapis.com/css2?family=Poltawski+Nowy:wght@500;600&display=swap',
        array(),
        null
    );

    // Editor styles (Satoshi @font-face + wrapper font-family)
    wp_enqueue_style(
        'circus-editor-style',
        get_template_directory_uri() . '/assets/css/editor.css',
        array(),
        filemtime(get_template_directory() . '/assets/css/editor.css')
    );
}
add_action('enqueue_block_assets', 'circus_enqueue_editor_canvas_assets');
