<?php
namespace oaksmin\woocommerce\new_order;
use function oaksmin\utilities\{get_alert};

/**
 * Update a user's $rental_products meta after purchase.
 *
 * When a user re-purchases rental content, we use this
 * function to clear out their "first accessed"
 * timestamp from any previous purchases.
 *
 * @param      int  $order_id  The Order ID
 */
function update_user_rentals( $order_id ){
  $update_rental_products = false;
  $order = new \WC_Order( $order_id );

  /**
   * Steps:
   *
   * 1. Get $user_id from order.
   * 2. Get user's $rental_products meta.
   * 3. Get line-items from order.
   * 4. Check each $product_id to see if it has an entry in $rental_products.
   * 5. If $product_id in $rental_products, remove it from $rental_products.
   * 6. Update $rental_products meta if #5.
   */

  // 1. Get the $user_id from the order:
  $user_id = $order->get_user_id();

  // 2. Get the user's $rental_products:
  $rental_products = get_user_meta( $user_id, 'rental_products', true );
  if( empty( $rental_products ) )
    $rental_products = []; // Initialize $rental_products if empty.

  // 3. Get line-items from order:
  foreach ( $order->get_items() as  $item_key => $item_values ) {
      $item_data = $item_values->get_data();
      $product_id = $item_data['product_id'];

      // 4. and 5. Check if $product_id is in $rental_products and remove if "yes".
      if( is_array( $rental_products ) && array_key_exists( $product_id, $rental_products ) ){
        unset( $rental_products[ $product_id ] );
        $update_rental_products = true;
      }
  }

  // 6. Update $rental_products meta:
  if( $update_rental_products )
    update_user_meta( $user_id, 'rental_products', $rental_products );
}
add_action( 'woocommerce_new_order', __NAMESPACE__ . '\\update_user_rentals' );
add_action( 'woocommerce_order_status_completed', __NAMESPACE__ . '\\update_user_rentals' );
add_action( 'woocommerce_order_status_processing', __NAMESPACE__ . '\\update_user_rentals' );