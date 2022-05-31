<?php

namespace oaksmin\rest\rentals;
use function oaksmin\woocommerce\{has_bought_items};

add_action( 'rest_api_init', function(){
  register_rest_route( 'oaksmin/v1', '/rentals', [
    'methods'   => 'GET,POST',
    'callback'  => __NAMESPACE__ . '\\save_rental_product_first_access',
    'args'      => [
      'product_id'  => [
        'validate_callback' => function( $param, $request, $key ){
          $user_id = get_current_user_id();
          if( ! $user_id )
            return false;

          if( ! has_bought_items( $user_id, $param ) )
            return false;

          return is_numeric( $param );
        }
      ],
    ],
    'permission_callback' => function(){
      $user_id = get_current_user_id();
      if( ! $user_id )
        return false;

      return true;
    },
  ]);
});

/**
 * Saves a rental product first access.
 *
 * @param      array  $request  The request
 *
 * @return     bool    Returns TRUE upon successful save of a product's first access for a user.
 */
function save_rental_product_first_access( $request ){
  $data = [];

  $product_id = $request->get_param( 'product_id' );
  if( empty( $product_id ) ){
    uber_log( '🚨 $product_id is empty!' );
    return new \WP_Error('empty_product_id', 'No $product_id sent.', ['status' => 404]);
  }

  uber_log('🔔 Attempting to save first_access for Product #' . $product_id );
  $user_id = get_current_user_id();
  if( ! $user_id )
    $data['updated'] = false;

  /**
   * Retrieve the user's `rental_products.
   */
  $rental_products = get_user_meta( $user_id, 'rental_products', true );
  if( ! is_array( $rental_products ) || empty( $rental_products ) )
    $rental_products = [];

  /**
   * If $product_id exists as a key in $rental_products, then we've
   * already saved a `first_accessed` time. Return `false`.
   */
  if( array_key_exists( $product_id, $rental_products ) && ! empty( $rental_products[$product_id] ) )
    $data['updated'] = false;

  /**
   * Save $rental_products to the user's meta as:
   *   $rental_products[ $product_id ] = $timestamp
   */
  $timestamp = current_time( 'timestamp' );
  $rental_products[ $product_id ] = $timestamp;
  uber_log('👉 Saving $rental_products = ' . print_r( $rental_products, true ) );
  $data['updated'] = update_user_meta( $user_id, 'rental_products', $rental_products );
  if( true == $data['updated'] ){
    $response = new \WP_REST_Response( $data );
  } else {
    $response = new \WP_Error('not_updated', 'Rental Products Not Updated', ['status' => 404]);
  }

  return $response;
}