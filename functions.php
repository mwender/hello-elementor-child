<?php
/**
 * Setup constants
 */
$css_dir = ( stristr( site_url(), '.local' ) || SCRIPT_DEBUG )? 'css' : 'dist' ;
define( 'OAKSMIN_CSS_DIR', $css_dir );
$dev_env = ( '.local' == stristr( site_url(), '.local' ) ) ? true : false ;
define( 'OAKSMIN_DEV_ENV', $dev_env );
define( 'OAKSMIN_THEME_PATH', trailingslashit( get_stylesheet_directory( __FILE__ ) ) );
define( 'OAKSMIN_THEME_URL', trailingslashit( dirname( get_stylesheet_uri( __FILE__ ) ) ) );

/**
 * Load required files
 */
require_once( OAKSMIN_THEME_PATH . 'lib/fns/enqueues.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/setup_theme.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/woocommerce.php' );

/**
 * Enhanced logging.
 *
 * @param      string  $message  The log message
 */
if( ! function_exists( 'uber_log' ) ){
  function uber_log( $message = null ){
    static $counter = 1;

    $bt = debug_backtrace();
    $caller = array_shift( $bt );

    if( 1 == $counter )
      error_log( "\n\n" . str_repeat('-', 25 ) . ' STARTING DEBUG [' . date('h:i:sa', current_time('timestamp') ) . '] ' . str_repeat('-', 25 ) . "\n\n" );
    error_log( "\n" . $counter . '. ' . basename( $caller['file'] ) . '::' . $caller['line'] . "\n" . $message . "\n---\n" );
    $counter++;
  }
}
