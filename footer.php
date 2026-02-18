<?php
$footer_text = function_exists('get_field') ? get_field('footer_text', 'option') : '';
$created_by  = function_exists('get_field') ? get_field('created_by', 'option') : '';
?>

<footer class="site-footer" role="contentinfo">
  <div class="footer-top">
    <div class="container footer-top-inner">
      <div class="footer-branding">
        <?php if (function_exists('the_custom_logo') && has_custom_logo()) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <a class="footer-logo-text" href="<?php echo esc_url(home_url('/')); ?>">
            <?php bloginfo('name'); ?>
          </a>
        <?php endif; ?>

        <?php if (!empty($footer_text)) : ?>
          <p class="footer-text"><?php echo esc_html($footer_text); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container footer-bottom-inner">
      <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer menu', 'circus'); ?>">
        <?php
        wp_nav_menu([
          'theme_location' => 'footer',
          'container'      => false,
          'menu_class'     => 'footer-menu',
          'fallback_cb'    => false,
          'depth'          => 1,
        ]);
        ?>
      </nav>

      <div class="footer-createdby">
        <?php if (!empty($created_by)) : ?>
          <span><?php echo esc_html($created_by); ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
