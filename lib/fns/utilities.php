<?php
namespace oaksmin\utilities;
use function oaksmin\templates\{render_template};

/**
 * Returns an HTML alert message
 *
 * @param      array  $atts {
 *   @type  string  $type         The alert type can info, warning, success, or danger (defaults to `warning`).
 *   @type  string  $title        The title of the alert.
 *   @type  string  $description  The content of the alert.
 *   @type  string  $css_classes  Additional CSS classes to add to the alert parent <div>.
 * }
 *
 * @return     html  The alert.
 */
function get_alert( $atts ){
  $args = shortcode_atts([
   'type'               => 'warning',
   'title'              => null,
   'description'        => 'Alert description goes here.',
   'css_classes' => null,
  ], $atts );

  $data = [
    'description' => $args['description'],
    'title'       => $args['title'],
    'type'        => $args['type'],
    'css_classes' => $args['css_classes'],
  ];

  return render_template( 'alert', $data );
}