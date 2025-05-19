<?php

// Theme text domain
$text_domain = 'ripcurl_theme';

function ripcurl_setup() {
    add_theme_support('block-editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_editor_style('style.css');
    add_theme_support('responsive-embeds');
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'ripcurl_setup');

function ripcurl_enqueue_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'ripcurl_enqueue_editor_styles');





require_once get_template_directory() . '/funcs/blocksandpatterns.php';
require_once get_template_directory() . '/funcs/cleanup.php';
require_once get_template_directory() . '/funcs/enhancements.php';
require_once get_template_directory() . '/funcs/shortcodes.php';
require_once get_template_directory() . '/funcs/theme-options.php';
