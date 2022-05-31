<?php
namespace oaksmin\options;

if( function_exists( 'acf_add_options_page' ) ){
  acf_add_options_page([
    'page_title'  => 'Oaks Ministries Options',
    'menu_title'  => 'OaksMin Options',
    'menu_slug'   => 'oaksmin-options',
    'capability'  => 'edit_posts',
  ]);
}