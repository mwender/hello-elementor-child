<?php
/**
 * Hello Elementor Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package hello-elementor-child
 */

add_action( 'wp_enqueue_scripts', 'hello_elementor_parent_theme_enqueue_styles' );

/**
 * Enqueue scripts and styles.
 */
function hello_elementor_parent_theme_enqueue_styles() {
	wp_enqueue_style( 'hello-elementor-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'hello-elementor-style' )
	);

}

function hello_elementor_child_setup_theme(){
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'hello_elementor_child_setup_theme', 99 );

/**
 * Change the WooCommerce breadcrumb separator.
 */
function wcc_change_breadcrumb_delimiter( $defaults ) {
	// Change the breadcrumb delimeter from '/' to '>'
	$defaults['delimiter'] = ' &raquo; ';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_delimiter' );
