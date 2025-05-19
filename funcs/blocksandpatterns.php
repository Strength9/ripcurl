<?php
/**
 * Block Registration and Patterns
 *
 * This file handles automatic registration of custom blocks in the /build directory,
 * registration of block patterns in the /patterns directory,
 * and adds a custom block category for the RipCurl theme.
 *
 * SECURITY: Prevents direct access and ensures safe inclusion of render files.
 *
 * @package RipCurl
 * @since 1.0.0
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Automatically register all blocks in /build.
 *
 * Registers each block found in /build/blockname/block.json. If a block directory contains a render.php,
 * it is safely included as a render callback.
 *
 * @since 1.0.0
 * @return void
 */

function ripcurl_register_all_blocks() {
    $blocks_dir = get_template_directory() . '/build';
    foreach ( glob( $blocks_dir . '/*/block.json' ) as $block_json ) {
        $block_path = dirname( $block_json );
        $args = [];
        // Securely include render.php if it exists
        $render_file = $block_path . '/render.php';
        if ( file_exists( $render_file ) ) {
            $args['render_callback'] = function ( $attributes, $content, $block ) use ( $render_file ) {
                ob_start();
                // Only allow including files within the block directory
                if ( strpos( realpath( $render_file ), realpath( dirname( $render_file ) ) ) === 0 ) {
                    include $render_file;
                }
                return ob_get_clean();
            };
        }
        register_block_type( $block_path, $args );
    }
}
add_action( 'init', 'ripcurl_register_all_blocks' );

/**
 * Register all block patterns in /patterns directory.
 *
 * Finds all PHP files in /patterns and registers them as block patterns.
 *
 * @since 1.0.0
 * @return void
 */
function ripcurl_register_all_block_patterns() {
    $patterns_dir = get_template_directory() . '/patterns';
    if ( ! is_dir( $patterns_dir ) ) {
        return;
    }
    foreach ( glob( $patterns_dir . '/*.php' ) as $pattern_file ) {
        // Parse pattern file header for metadata
        $file_contents = file_get_contents( $pattern_file );
        if ( preg_match( '/\/\*\*(.*?)\*\//s', $file_contents, $matches ) ) {
            $header = $matches[1];
            $fields = [
                'title' => '',
                'slug' => '',
                'categories' => '',
                'block types' => '',
                'inserter' => '',
            ];
            foreach ( $fields as $key => &$value ) {
                if ( preg_match( '/'.preg_quote( ucfirst( $key ), '/' ).':\s*(.+)/i', $header, $m ) ) {
                    $value = trim( $m[1] );
                }
            }
            unset($value);
            // Required: title and slug
            if ( ! empty( $fields['title'] ) && ! empty( $fields['slug'] ) ) {
                // Extract only the HTML content after the PHP header
                $pattern_content = $file_contents;
                // Remove the opening PHP tag and comment block
                $pattern_content = preg_replace( '/^<\?php\s*\/\*\*.*?\*\/\s*/s', '', $pattern_content, 1 );
                // Remove any trailing PHP closing tag
                $pattern_content = preg_replace( '/\?>\s*/', '', $pattern_content, 1 );
                $pattern_content = trim( $pattern_content );
                // Optionally parse description and viewportWidth
                $pattern_args = [
                    'title'      => $fields['title'],
                    'categories' => array_map( 'trim', explode( ',', $fields['categories'] ) ),
                    'content'    => $pattern_content,
                ];
                if ( preg_match( '/Description:\s*(.+)/i', $header, $desc_match ) ) {
                    $pattern_args['description'] = trim( $desc_match[1] );
                }
                if ( preg_match( '/ViewportWidth:\s*(\d+)/i', $header, $vw_match ) ) {
                    $pattern_args['viewportWidth'] = (int) trim( $vw_match[1] );
                }
                register_block_pattern( $fields['slug'], $pattern_args );
            }
        }
    }
}
// Register the custom block pattern category for Patterns UI
add_action( 'init', function() {
    register_block_pattern_category(
        'ripcurlblocks',
        [
            'label' => __( 'RipCurl Blocks', 'ripcurl' ),
            // 'description' => __( 'Custom patterns for RipCurl theme', 'ripcurl' ), // Optional for WP 6.5+
        ]
    );
});

add_action( 'init', 'ripcurl_register_all_block_patterns' );

/**
 * Register a custom block category called "RipCurl Blocks".
 *
 * @since 1.0.0
 * @param array $categories Existing block categories.
 * @param WP_Post $post Current post object.
 * @return array Modified block categories.
 */
function ripcurl_add_block_categories( $categories, $post ) {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'ripcurlblocks',
                'title' => __('RipCurl Blocks', 'ripcurl'),
                'icon'  => null
            ]
        ]
    );
}
add_filter('block_categories_all', 'ripcurl_add_block_categories', 10, 2);


