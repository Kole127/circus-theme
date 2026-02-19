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

    // Lenis
	wp_enqueue_script('lenis', 'https://unpkg.com/lenis@1.3.1/dist/lenis.min.js', array(), null, true);

    // AJAX
    wp_localize_script('circus-main-js', 'circusAjax', ['ajax_url' => admin_url('admin-ajax.php'),]);

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


// API - WP REMOTE GET
function circus_get_jokes_ajax() {

  $response = wp_remote_get('https://official-joke-api.appspot.com/random_ten', [
    'timeout' => 10,
  ]);

  if (is_wp_error($response)) {
    wp_send_json_error(['message' => 'API request failed.']);
  }

  $status = wp_remote_retrieve_response_code($response);
  if ($status !== 200) {
    wp_send_json_error(['message' => 'API returned a non-200 response.']);
  }

  $body = wp_remote_retrieve_body($response);
  $data = json_decode($body, true);

  if (!is_array($data)) {
    wp_send_json_error(['message' => 'Invalid API response.']);
  }

  wp_send_json_success($data);
}
add_action('wp_ajax_circus_get_jokes', 'circus_get_jokes_ajax');
add_action('wp_ajax_nopriv_circus_get_jokes', 'circus_get_jokes_ajax');

// Lenis Script
function initialize_lenis_scroll() {
    wp_add_inline_script('lenis', "
        if (window.innerWidth > 1024) { // pokreni Lenis samo na desktopu
            const lenis = new Lenis({
                duration: 1.2,
                easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                smooth: true,
                smoothTouch: false,
                direction: 'vertical',
                gestureDirection: 'vertical',
                lerp: 0.1,
                wheelMultiplier: 1,
                touchMultiplier: 1.2
            });

            window.lenis = lenis;

            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);
        }
    ");
}
add_action('wp_enqueue_scripts', 'initialize_lenis_scroll', 11);


// SPLIT LINK HOVER ANIMATION - GSAP
add_action('wp_footer', function () { ?>
  <style>
    /* Base clickable styles */
    #menu-main-menu a,
    .wp-block-button__link {
      position: relative;
      overflow: hidden;
      display: inline-flex;
      align-items: center;
      line-height: 1.3em!important;
    }

    /* Letter wrap */
    .gsap-split-hover .letter-wrap {
      position: relative;
      display: inline-block;
      overflow: hidden;
      vertical-align: top;
    }

    /* Letter */
    .gsap-split-hover .letter {
      display: block;
      transform: translateY(0%);
      will-change: transform;
    }

    /* Clone (second line) */
    .gsap-split-hover .letter.clone {
      position: absolute !important;
      top: 100%;
      left: 0;
      transform: translateY(0%);
    }
  </style>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {

      const selector = `
        #menu-main-menu a,
        .wp-block-button__link
      `;

      function split(el) {
        if (!el || el.classList.contains("split-init")) return;

        const text = (el.textContent || "").trim();
        if (!text) return;

        el.classList.add("split-init", "gsap-split-hover");

        // accessibility
        if (!el.getAttribute("aria-label")) el.setAttribute("aria-label", text);

        el.textContent = "";

        text.split("").forEach(char => {
          const wrap = document.createElement("span");
          wrap.className = "letter-wrap";

          const o = document.createElement("span");
          o.className = "letter";
          o.textContent = char === " " ? "\u00A0" : char;

          const c = document.createElement("span");
          c.className = "letter clone";
          c.textContent = o.textContent;

          wrap.append(o, c);
          el.appendChild(wrap);
        });

        const originals = el.querySelectorAll(".letter:not(.clone)");
        const clones = el.querySelectorAll(".letter.clone");

        const onEnter = () => {
          gsap.to(originals, { y: "-100%", duration: 0.45, ease: "power2.inOut", stagger: 0.04 });
          gsap.to(clones,    { y: "-100%", duration: 0.45, ease: "power2.inOut", stagger: 0.04 });
        };

        const onLeave = () => {
          gsap.to(originals, { y: "0%", duration: 0.45, ease: "power2.inOut", stagger: 0.04 });
          gsap.to(clones,    { y: "0%", duration: 0.45, ease: "power2.inOut", stagger: 0.04 });
        };

        el.addEventListener("mouseenter", onEnter);
        el.addEventListener("mouseleave", onLeave);
        el.addEventListener("focus", onEnter);
        el.addEventListener("blur", onLeave);
      }

      function init(context = document) {
        context.querySelectorAll(selector).forEach(split);
      }

      init();

      // if menu is injected/changed dynamically
      const observer = new MutationObserver(mutations => {
        for (const m of mutations) {
          for (const n of m.addedNodes) {
            if (n.nodeType === 1) init(n);
          }
        }
      });

      observer.observe(document.body, { childList: true, subtree: true });
    });
  </script>
<?php });
