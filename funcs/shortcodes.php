<?php
/**
 * Theme Shortcodes
 *
 * This file registers and documents custom shortcodes for the RipCurl theme.
 *
 * SECURITY: Prevents direct access and ensures all shortcode output is escaped.
 *
 * @package RipCurl
 * @since 1.0.0
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * [year] shortcode
 *
 * Outputs the current year. Output is escaped for safety.
 * Usage: [year]
 *
 * @since 1.0.0
 * @return string
 */
add_shortcode( 'year', function() {
    return esc_html( date( 'Y' ) );
} );

/**
 * [site_copyright] shortcode
 *
 * Outputs the site copyright value from theme options. Output is escaped for safety.
 * Usage: [site_copyright]
 *
 * @since 1.0.0
 * @return string
 */
add_shortcode( 'site_copyright', function() {
    if ( function_exists( 'ripcurl_get_theme_option' ) ) {
        $copyright = ripcurl_get_theme_option( 'copyright' );
        return esc_html( $copyright );
    }
    return '';
} );


