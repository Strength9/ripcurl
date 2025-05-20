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
