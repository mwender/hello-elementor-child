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


/* WooCommerce */
function has_bought_items( $user_var = 0,  $product_ids = 0 ) {
    global $wpdb;
    
    // Based on user ID (registered users)
    if ( is_numeric( $user_var) ) { 
        $meta_key     = '_customer_user';
        $meta_value   = $user_var == 0 ? (int) get_current_user_id() : (int) $user_var;
    } 
    // Based on billing email (Guest users)
    else { 
        $meta_key     = '_billing_email';
        $meta_value   = sanitize_email( $user_var );
    }
    
    $paid_statuses    = array_map( 'esc_sql', wc_get_is_paid_statuses() );
    $product_ids      = is_array( $product_ids ) ? implode(',', $product_ids) : $product_ids;

    $line_meta_value  = $product_ids !=  ( 0 || '' ) ? 'AND woim.meta_value IN ('.$product_ids.')' : 'AND woim.meta_value != 0';

    // Count the number of products
    $count = $wpdb->get_var( "
        SELECT COUNT(p.ID) FROM {$wpdb->prefix}posts AS p
        INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
        INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id
        WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $paid_statuses ) . "' )
        AND pm.meta_key = '$meta_key'
        AND pm.meta_value = '$meta_value'
        AND woim.meta_key IN ( '_product_id', '_variation_id' ) $line_meta_value 
    " );

    // Return true if count is higher than 0 (or false)
    return $count > 0 ? true : false;
}

/**
 * 1. Register new endpoint slug to use for My Account page
 */

/**
 * @important-note	Resave Permalinks or it will give 404 error
 */
function ts_custom_add_video_access_endpoint() {
    add_rewrite_endpoint( 'video-access', EP_ROOT | EP_PAGES );
}
  
add_action( 'init', 'ts_custom_add_video_access_endpoint' );

/**
 * 2. Add new query var
 */
  
function ts_custom_video_access_query_vars( $vars ) {
    $vars[] = 'video-access';
    return $vars;
}
  
add_filter( 'woocommerce_get_query_vars', 'ts_custom_video_access_query_vars', 0 );
  
  
/**
 * 3. Insert the new endpoint into the My Account menu
 */
  
function ts_custom_add_video_access_link_my_account( $items ) {
    $items['video-access'] = 'Video Access';
    return $items;
}
  
add_filter( 'woocommerce_account_menu_items', 'ts_custom_add_video_access_link_my_account' );
  
  
/**
 * 4. Add content to the new endpoint
 */
  
function ts_custom_video_access_content() {
	echo '<h2>One Gritty Blink Video Access</h2>';  
	if ( has_bought_items( '', 12532 ) )
	  echo do_shortcode( '[elementor-template id="12565"]' );
	else 
		echo do_shortcode( '[elementor-template id="12568"]' );
}

/**
 * @important-note  "add_action" must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format
 */
add_action( 'woocommerce_account_video-access_endpoint', 'ts_custom_video_access_content' );

function iso_reorder_my_account_menu() {
    $newtaborder = array(
        
        'dashboard'          => __( 'Dashboard', 'woocommerce' ),
        'orders'             => __( 'Previous Orders', 'woocommerce' ),
        'downloads'             => __( 'One Gritty Blink Downloads', 'woocommerce' ),
        'video-access'    => __( 'One Gritty Blink Video Access', 'woocommerce' ),
        'edit-address'       => __( 'Addresses', 'woocommerce' ),
        'payment-methods'       => __( 'Payment Methods', 'woocommerce' ),
        'edit-account'       => __( 'Account Details', 'woocommerce' ),
        'customer-logout'    => __( 'Logout', 'woocommerce' ),
    );
    return $newtaborder;
}
add_filter ( 'woocommerce_account_menu_items', 'iso_reorder_my_account_menu' );
