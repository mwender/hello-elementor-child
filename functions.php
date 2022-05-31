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
 * Load Composer dependencies
 */
if( file_exists( OAKSMIN_THEME_PATH . 'vendor/autoload.php' ) ){
  require_once OAKSMIN_THEME_PATH . 'vendor/autoload.php';
} else {
  add_action( 'admin_notices', function(){
    $class = 'notice notice-error';
    $message = __( 'Missing required Composer libraries. Please run `composer install` from the root directory of this plugin.', 'tka' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  } );
}

/**
 * Load required files
 */
require_once( OAKSMIN_THEME_PATH . 'lib/fns/acf-json-save-point.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/enqueues.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/options.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/rest.rentals.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/setup_theme.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/templates.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/utilities.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/woocommerce.php' );
require_once( OAKSMIN_THEME_PATH . 'lib/fns/woocommerce.new_order.php' );

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
