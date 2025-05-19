<?php
/**
 * Theme Enhancements
 *
 * This file contains functionality that extends or enhances WordPress core features.
 * It's used to add capabilities that aren't included by default in WordPress.
 *
 * SECURITY: This file includes robust security measures for SVG uploads.
 *
 * @package RipCurl
 * @since 1.0.0
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Allow SVG uploads only for administrators.
 *
 * @param array $mimes Allowed mime types.
 * @return array Modified mime types.
 */
add_filter( 'upload_mimes', function ( $mimes ) {
    if ( current_user_can( 'administrator' ) ) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
} );

/**
 * Sanitize SVG uploads to prevent XSS and malicious code.
 *
 * @param array $data File data.
 * @param string $file Path to file.
 * @param string $filename Filename.
 * @param array $mimes Allowed mimes.
 * @return array Modified file data.
 */
function rip_sanitize_svg_upload( $data, $file, $filename, $mimes ) {
    $filetype = wp_check_filetype( $filename, $mimes );
    if ( $filetype['ext'] === 'svg' && file_exists( $file ) ) {
        $svg = file_get_contents( $file );
        // Basic SVG validation
        if ( stripos( $svg, '<svg' ) === false ) {
            $data['error'] = __( 'Invalid SVG file format.', 'ripcurl-theme' );
            return $data;
        }
        // Disallow scripts and dangerous attributes
        $disallowed = [ '<script', 'onload=', 'onerror=', 'onclick=', 'javascript:', 'xlink:href', 'data:' ];
        foreach ( $disallowed as $pattern ) {
            if ( stripos( $svg, $pattern ) !== false ) {
                $data['error'] = __( 'SVG file contains potentially unsafe content.', 'ripcurl-theme' );
                return $data;
            }
        }
    }
    return $data;
}
add_filter( 'wp_handle_upload_prefilter', 'rip_sanitize_svg_upload', 10, 4 );

/**
 * Ensure correct SVG mime type after upload.
 *
 * @param array $data Filetype data.
 * @param string $file Path to file.
 * @param string $filename Filename.
 * @param array $mimes Allowed mimes.
 * @return array Modified data.
 */
add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {
    if ( empty( $data['ext'] ) || empty( $data['type'] ) ) {
        $filetype = wp_check_filetype( $filename, $mimes );
        if ( $filetype['ext'] === 'svg' ) {
            $data['ext'] = 'svg';
            $data['type'] = 'image/svg+xml';
        }
    }
    return $data;
}, 10, 4 );

/**
 * Add security headers for SVG delivery.
 */
add_action( 'send_headers', function() {
    if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], '.svg' ) !== false ) {
        header( 'X-Content-Type-Options: nosniff' );
        header( "Content-Security-Policy: default-src 'self'; script-src 'none'; sandbox;" );
    }
} );
