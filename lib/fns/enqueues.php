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

}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\parent_theme_enqueue_styles' );