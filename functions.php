<?php

// Theme text domain
define('CC_TEXT_DOMAIN', 'ripcurl');

function rip_setup() {
    add_theme_support('block-editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_editor_style('style.css');
    add_theme_support('responsive-embeds');
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'rip_setup');

function rip_enqueue_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'rip_enqueue_editor_styles');

/**
 * Enqueue theme styles and scripts
 */
function rip_enqueue_theme_assets() {
    // Enqueue main stylesheet
    wp_enqueue_style(
        'boakesjoinery-style',
        get_stylesheet_uri(),
        array(),
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'rip_enqueue_theme_assets');



require_once get_template_directory() . '/funcs/blocksandpatterns.php';
require_once get_template_directory() . '/funcs/cleanup.php';
require_once get_template_directory() . '/funcs/enhancements.php';
require_once get_template_directory() . '/funcs/shortcodes.php';
require_once get_template_directory() . '/funcs/theme-options.php';
