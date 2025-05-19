<?php

// Theme text domain
$text_domain = 'ripcurl-theme';

// Remove unnecessary <head> tags, emoji scripts, and heartbeat
add_action( 'init', function () use ( $text_domain ) {
    // Remove various <head> links and meta tags
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'rest_output_link_wp_head' );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

    // Remove emoji scripts and styles
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );

    // Disable heartbeat script on frontend
    if ( ! is_admin() ) {
        wp_deregister_script( 'heartbeat' );
    }
});

// Remove type='text/javascript' from <script> tags
add_filter( 'script_loader_tag', function ( $tag ) {
    return str_replace( "type='text/javascript'", '', $tag );
}, 10, 1 );

// Deregister the wp-embed script
add_action( 'wp_footer', function () {
    wp_deregister_script( 'wp-embed' );
} );

// Disable XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );

// Disable all feeds (RSS, Atom, etc.)
foreach ( [ 'do_feed', 'do_feed_rdf', 'do_feed_rss', 'do_feed_rss2', 'do_feed_atom' ] as $feed_action ) {
    add_action( $feed_action, function () use ( $text_domain ) {
        wp_die( __( 'Feeds are disabled.', $text_domain ), '', [ 'response' => 403 ] );
    }, 1 );
}