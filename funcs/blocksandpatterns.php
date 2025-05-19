<?php

/**
 * Automatically register all blocks in /build
 * and add a custom block category: "RipCurl Blocks"
 */

 function ripcurl_register_all_blocks() {
    $blocks_dir = get_template_directory() . '/build';

    foreach (glob($blocks_dir . '/*/block.json') as $block_json) {
        $block_path = dirname($block_json);
        $args = [];

        // Check if there's a render.php in the block folder
        $render_file = $block_path . '/render.php';
        if (file_exists($render_file)) {
            $args['render_callback'] = function ($attributes, $content, $block) use ($render_file) {
                ob_start();
                include $render_file;
                return ob_get_clean();
            };
        }

        register_block_type($block_path, $args);
    }
}
add_action('init', 'ripcurl_register_all_blocks');

/**
 * Register a custom block category called "RipCurl Blocks"
 */
function ripcurl_add_block_categories($categories, $post) {
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
