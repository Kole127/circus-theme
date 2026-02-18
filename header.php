<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/favicon.png' ); ?>">

    <meta property="og:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>">
    <meta property="og:description" content="<?php bloginfo('description'); ?>">
    <meta property="og:type" content="<?php echo is_front_page() ? 'website' : 'article'; ?>">
    <meta property="og:url" content="<?php echo esc_url( home_url( add_query_arg( array(), $wp->request ) ) ); ?>">
    <meta property="og:image" content="<?php echo esc_url( get_template_directory_uri() . '/assets/og-image.jpg' ); ?>">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container header-inner">

        <div class="site-branding">
            <?php the_custom_logo(); ?>
        </div>

        <nav class="site-nav" aria-label="<?php esc_attr_e('Primary menu', 'circus'); ?>">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-menu',
                'fallback_cb'    => false,
                'depth'          => 1
            ));
            ?>
        </nav>

    </div>
</header>