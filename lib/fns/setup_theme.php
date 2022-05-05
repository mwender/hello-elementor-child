<?php
namespace oaksmin\setuptheme;

function setup_theme(){
  add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup_theme', 99 );