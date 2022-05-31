<?php
//*
function OAKSMIN_acf_json_save_point( $path ) {
  // update path
  $path = OAKSMIN_THEME_PATH . 'lib/acf-json';
  //uber_log( '🔔 ACF Local JSON Save Path = ' . "\n" . $path );

  // return
  return $path;
}
add_filter('acf/settings/save_json', 'OAKSMIN_acf_json_save_point');

function OAKSMIN_acf_json_load_point( $paths ) {
    // remove original path
    unset($paths[0]);

    // append path
    $paths[] = OAKSMIN_THEME_PATH . 'lib/acf-json';
    //uber_log( '🚀 $paths = ' . "\n" . print_r( $paths, true ) );

    // return
    return $paths;
}
add_filter('acf/settings/load_json', 'OAKSMIN_acf_json_load_point');
/**/