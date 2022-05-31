<?php
namespace oaksmin\enqueues;

/**
 * Enqueue scripts and styles.
 */
function parent_theme_enqueue_styles() {
  wp_enqueue_style( 'hello-elementor-style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'hello-elementor-child-style',
    get_stylesheet_directory_uri() . '/style.css',
    array( 'hello-elementor-style' )
  );

  wp_register_script( 'rentals', OAKSMIN_THEME_URL . 'lib/js/rentals.js', null, filemtime( OAKSMIN_THEME_PATH . 'lib/js/rentals.js'), true );
  wp_localize_script( 'rentals', 'wpApiSettings', [
    'ep'      => rest_url( 'oaksmin/v1/rentals' ),
    'loader'  => OAKSMIN_THEME_URL . 'lib/img/oaksmin-loader.gif',
    'nonce'   => wp_create_nonce( 'wp_rest' ),
  ]);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\parent_theme_enqueue_styles' );