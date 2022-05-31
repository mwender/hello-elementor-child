<?php
use \LightnCandy\SafeString as SafeString;use \LightnCandy\Runtime as LR;return function ($in = null, $options = null) {
    $helpers = array();
    $partials = array();
    $cx = array(
        'flags' => array(
            'jstrue' => false,
            'jsobj' => false,
            'jslen' => false,
            'spvar' => true,
            'prop' => false,
            'method' => false,
            'lambda' => false,
            'mustlok' => false,
            'mustlam' => false,
            'mustsec' => false,
            'echo' => false,
            'partnc' => false,
            'knohlp' => false,
            'debug' => isset($options['debug']) ? $options['debug'] : 1,
        ),
        'constants' => array(),
        'helpers' => isset($options['helpers']) ? array_merge($helpers, $options['helpers']) : $helpers,
        'partials' => isset($options['partials']) ? array_merge($partials, $options['partials']) : $partials,
        'scopes' => array(),
        'sp_vars' => isset($options['data']) ? array_merge(array('root' => $in), $options['data']) : array('root' => $in),
        'blparam' => array(),
        'partialid' => 0,
        'runtime' => '\LightnCandy\Runtime',
    );
    
    $inary=is_array($in);
    return '<style>
/*! elementor - v3.4.3 - 30-08-2021 */
.elementor-alert{padding:15px;border-left:5px solid transparent;position:relative;text-align:left}.elementor-alert .elementor-alert-title{display:block;font-weight:700}.elementor-alert .elementor-alert-description{font-size:13px}.elementor-alert button.elementor-alert-dismiss{position:absolute;right:10px;top:10px;padding:3px;font-size:20px;line-height:1;background:transparent;color:inherit;border:none;cursor:pointer}.elementor-alert.elementor-alert-info{color:#31708f;background-color:#d9edf7;border-color:#bcdff1}.elementor-alert.elementor-alert-success{color:#3c763d;background-color:#dff0d8;border-color:#cae6be}.elementor-alert.elementor-alert-warning{color:#8a6d3b;background-color:#fcf8e3;border-color:#f9f0c3}.elementor-alert.elementor-alert-danger{color:#a94442;background-color:#f2dede;border-color:#e8c4c4}@media (max-width:767px){.elementor-alert{padding:10px}.elementor-alert button.elementor-alert-dismiss{right:7px;top:7px}}
</style>
<div class="elementor-alert elementor-widget elementor-alert-'.htmlspecialchars((string)(($inary && isset($in['type'])) ? $in['type'] : null), ENT_QUOTES, 'UTF-8').' '.htmlspecialchars((string)(($inary && isset($in['css_classes'])) ? $in['css_classes'] : null), ENT_QUOTES, 'UTF-8').'" role="alert">
    '.((LR::ifvar($cx, (($inary && isset($in['title'])) ? $in['title'] : null), false)) ? '<span class="elementor-alert-title">'.(($inary && isset($in['title'])) ? $in['title'] : null).'</span>' : '').'
    <span class="elementor-alert-description">'.(($inary && isset($in['description'])) ? $in['description'] : null).'</span>
</div>';
};
?>