<?php
namespace oaksmin\woocommerce;
use function oaksmin\utilities\{get_alert};

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
    if ( is_numeric( $user_var ) ) {
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
  $user_id = get_current_user_id();

  /**
   * Get the HTML for our Restricted Content Notification we
   * show to users who have not purchased the product
   * required to view the content.
   */
  $restricted_access_notification_id = get_field( 'restricted_content_notification', 'option' );
  $restricted_access_notification_html = do_shortcode( '[elementor-template id="' . $restricted_access_notification_id . '"]' );

  /**
   * Product IDs:
   * 12532 - Leader Guide Bundle
   * 12589 - Question 1 Video Rental
   *
   * if has_bought_items( Leader Guide Bundle )
   *   show ALL
   * if has_bought_items( Single Video )
   *   show SINGLE VIDEO
   *
   * @var array $content_access       An ACF option of all Products we've defined as providing access to restricted content.
   * @var array $purchased_products   An array of products which provide access to content.
   * @var array $all_bundled_products An array of all content access products which are bundled inside other products.
   */
  if( have_rows( 'content_access', 'option' ) ):
    $purchased_products = [];
    $all_bundled_products = [];
    while( have_rows( 'content_access', 'option' ) ): the_row();
      $product_id = get_sub_field( 'product' );
      if( ! has_bought_items( $user_id, $product_id ) )
        continue;

      $elementor_template_id = get_sub_field( 'content' );
      $bundled_products = get_sub_field( 'bundled_products' );

      $purchased_products[ $product_id ] = [
        'product_name' => get_the_title( $product_id ),
        'elementor_template_id' => $elementor_template_id,
        'rental_period' => get_sub_field( 'rental_period' ),
      ];

      $rental_period = get_sub_field( 'rental_period' );
      if( 'unlimited' == $rental_period ){
        $purchased_products[ $product_id ]['rental_period'] = 0;
      } else {
        $purchased_products[ $product_id ]['rental_period'] = $rental_period;
        $purchased_products[ $product_id ]['rental_period_activate_template'] = get_sub_field( 'rental_period_activate_template' );
      }

      if( is_array( $bundled_products ) && has_bought_items( $user_id, $product_id ) )
        $all_bundled_products = array_merge( $all_bundled_products, $bundled_products );
    endwhile;
  endif;

  /**
   * Get the user's rentals.
   *
   * @var  array  $rental_products Array of products the user has rented.
   */
  $rental_products = get_user_meta( $user_id, 'rental_products', true );
  if( empty( $rental_products ) )
    $rental_products = [];

  $show_restricted_access_notification = true;
  if( 0 < count( $purchased_products ) ):
    foreach( $purchased_products as $product_id => $product ){
      if( has_bought_items( $user_id, $product_id ) && ( ! in_array( $product_id, $all_bundled_products ) ) )
      {
        // $product['rental_period'] = Number of hours for the rental period.
        if( $product['rental_period'] )
        {
          wp_enqueue_script( 'rentals' );

          /**
           * Check $rental_products to see if user has accessed this rental:
           */
          if( ! array_key_exists( $product_id, $rental_products ) ){
            /**
             * User has not activated this rental. Show the ACTIVATE template:
             */
            $rental_period_activate_template = do_shortcode( '[elementor-template id="' .$product['rental_period_activate_template'] . '"]' );
            $search = [ '{product_name}', '{rental_period}', '{product_id}' ];
            $replace = [ $product['product_name'], $product['rental_period'], $product_id ];
            echo str_replace( $search, $replace, $rental_period_activate_template );
          } else {
            /**
             * User has activated this rental. Show either rental content or expired notice.
             */
            $first_accessed = intval( $rental_products[ $product_id ] );
            //uber_log('🔔 $first_accessed = ' . $first_accessed );
            $start_date = new \DateTime( '@' . $first_accessed );
            $end_date_ts = $first_accessed + ( $product['rental_period'] * 3600 );
            $end_date = new \DateTime( '@' . $end_date_ts );
            $current_ts = current_time( 'timestamp' );

            if( $current_ts < $end_date_ts )
            {
              /**
               * Rental is CURRENT, show the restricted content:
               */
              $alert = get_alert(['description' => '👆 Your rental of "' . $product['product_name'] . '" will end on ' . $end_date->format('M, j, Y') .' at ' . $end_date->format( 'g:i a' ) . '.', 'type' => 'info' ]);
              echo do_shortcode( '[elementor-template id="' . $product['elementor_template_id'] . '"/]' );
              echo '<div style="padding: 10px;">' . $alert . '</div>';
            }
            else
            {
              /**
               * Rental is EXPIRED:
               */
              $alert = get_alert(['title' => '⏰ ' . $product['product_name'] . ' - EXPIRED', 'description' => 'Your rental period for "<a href="' . get_permalink( $product_id ) . '">' . $product['product_name'] . '</a>" has expired. If you would like to rent this product again, <a href="' . get_permalink( $product_id ) . '">click here</a>.' ]);
              echo '<div style="padding: 10px;">' . $alert . '</div>';
            }
          }
        }
        else
        {
          /**
           * Product has UNLIMITED access. Show it:
           */
          echo do_shortcode( '[elementor-template id="' . $product['elementor_template_id'] . '"]' );
        }
        $show_restricted_access_notification = false;
      }
    }
  endif;

  if( $show_restricted_access_notification )
    echo $restricted_access_notification_html;
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
        'video-access'    => __( 'One Gritty Blink Video Access', 'woocommerce' ),
        'orders'             => __( 'Previous Orders', 'woocommerce' ),
        'downloads'             => __( 'One Gritty Blink Downloads', 'woocommerce' ),
        'edit-address'       => __( 'Addresses', 'woocommerce' ),
        'payment-methods'       => __( 'Payment Methods', 'woocommerce' ),
        'edit-account'       => __( 'Account Details', 'woocommerce' ),
        'customer-logout'    => __( 'Logout', 'woocommerce' ),
    );
    return $newtaborder;
}
add_filter ( 'woocommerce_account_menu_items', __NAMESPACE__ . '\\reorder_my_account_menu' );
