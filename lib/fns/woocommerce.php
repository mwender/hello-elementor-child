<?php
namespace oaksmin\woocommerce;

/**
 * Change the WooCommerce breadcrumb separator.
 */
function wcc_change_breadcrumb_delimiter( $defaults ) {
  // Change the breadcrumb delimeter from '/' to '>'
  $defaults['delimiter'] = ' &raquo; ';
  return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_delimiter' );

/**
 * Determines if customer has bought items.
 *
 * @param      int         $user_var     The user ID
 * @param      int|string  $product_ids  The product IDs
 *
 * @return     bool        True if bought items, False otherwise.
 */
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
 * Register new endpoint slug to use for My Account page
 *
 * @important-note  Resave Permalinks or it will give 404 error
 */
function custom_add_video_access_endpoint() {
    add_rewrite_endpoint( 'video-access', EP_ROOT | EP_PAGES );
}
add_action( 'init', __NAMESPACE__ . '\\custom_add_video_access_endpoint' );

 /**
  * Add new query variables for WooCommerce
  *
  * @param      array  $vars   The query variables
  *
  * @return     array  The query variables array
  */
function custom_video_access_query_vars( $vars ) {
    $vars[] = 'video-access';
    return $vars;
}
add_filter( 'woocommerce_get_query_vars', __NAMESPACE__ . '\\custom_video_access_query_vars', 0 );

/**
 * Insert new endpoints into the My Account menu.
 *
 * @param      array  $items  The items
 *
 * @return     array  Filtered My Account menu items.
 */
function custom_add_video_access_link_my_account( $items ) {
    $items['video-access'] = 'Video Access';
    return $items;
}
add_filter( 'woocommerce_account_menu_items', __NAMESPACE__ . '\\custom_add_video_access_link_my_account' );

/**
 * Add content to the "Video Access" endpoint.
 */
function custom_video_access_content() {
  echo '<h2>One Gritty Blink Video Access</h2>';
  if ( has_bought_items( '', 12532 ) )
    echo do_shortcode( '[elementor-template id="12565"]' );
  else
    echo do_shortcode( '[elementor-template id="12568"]' );
}
/**
 * @important-note  "add_action" must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format
 */
add_action( 'woocommerce_account_video-access_endpoint', __NAMESPACE__ . '\\custom_video_access_content' );

/**
 * Reorder tabs on WooCommerce My Account panel
 *
 * @return     array  Reordered tabs for WC My Account panel.
 */
function reorder_my_account_menu() {
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
add_filter ( 'woocommerce_account_menu_items', __NAMESPACE__ . '\\reorder_my_account_menu' );
