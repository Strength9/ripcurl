<?php
declare(strict_types=1);
/**
 * Theme Options Page for RipCurl
 *
 * @package RipCurl
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Theme Options Page
 */
function rip_register_theme_options_page() {
    add_menu_page(
        __('Theme Options', 'ripcurl'),
        __('Theme Options', 'ripcurl'),
        'manage_options',
        'rip-theme-options',
        'rip_render_theme_options_page',
        'dashicons-admin-generic',
        61
    );
}
add_action('admin_menu', 'rip_register_theme_options_page');

// Enqueue media uploader for admin
function rip_theme_options_admin_scripts($hook) {
    if ($hook === 'toplevel_page_ripcurl-theme-options') {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'rip_theme_options_admin_scripts');

/**
 * Register Settings
 */
function rip_register_theme_settings() {
    register_setting('rip_theme_options_group', 'rip_theme_options', [
        'type' => 'array',
        'sanitize_callback' => 'rip_sanitize_theme_options',
        'show_in_rest' => false,
    ]);
}

add_action('admin_init', 'rip_register_theme_settings');

/**
 * Sanitize Options
 */
function rip_sanitize_theme_options($options) {
    $fields = [
        'copyright', 'facebook', 'instagram', 'twitter', 'linkedin', 'youtube', 'kick', 'footer_text', 'footer_logo',
        'address', 'telephone', 'email'
    ];
    $sanitized = [];
    foreach ($fields as $field) {
        if ($field === 'address') {
            $sanitized[$field] = isset($options[$field]) ? wp_kses_post($options[$field]) : '';
        } else if ($field === 'email') {
            $sanitized[$field] = isset($options[$field]) ? sanitize_email($options[$field]) : '';
        } else if ($field === 'telephone') {
            $sanitized[$field] = isset($options[$field]) ? preg_replace('/[^\d\+\-\(\) ]/', '', $options[$field]) : '';
        } else {
            $sanitized[$field] = isset($options[$field]) ? sanitize_text_field($options[$field]) : '';
        }
    }
    return $sanitized;
}

/**
 * Render Theme Options Page
 */
function rip_render_theme_options_page() {
    $options = get_option('rip_theme_options', []);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Theme Options', 'ripcurl'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('rip_theme_options_group');
            do_settings_sections('rip_theme_options_group');
            ?>
            <h2 style="margin-top:2em;">General</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Site Copyright', 'ripcurl'); ?></th>
                    <td><input type="text" name="rip_theme_options[copyright]" value="<?php echo esc_attr($options['copyright'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <h2 style="margin-top:2em;">Social Links</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Facebook URL</th>
                    <td><input type="url" name="rip_theme_options[facebook]" value="<?php echo esc_attr($options['facebook'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Instagram URL</th>
                    <td><input type="url" name="rip_theme_options[instagram]" value="<?php echo esc_attr($options['instagram'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Twitter URL</th>
                    <td><input type="url" name="rip_theme_options[twitter]" value="<?php echo esc_attr($options['twitter'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">LinkedIn URL</th>
                    <td><input type="url" name="rip_theme_options[linkedin]" value="<?php echo esc_attr($options['linkedin'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">YouTube URL</th>
                    <td><input type="url" name="rip_theme_options[youtube]" value="<?php echo esc_attr($options['youtube'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Kick URL</th>
                    <td><input type="url" name="rip_theme_options[kick]" value="<?php echo esc_attr($options['kick'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <h2 style="margin-top:2em;">Footer</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Footer Text</th>
                    <td><input type="text" name="rip_theme_options[footer_text]" value="<?php echo esc_attr($options['footer_text'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Footer Logo URL</th>
                    <td>
                        <input id="footer_logo" type="text" name="rip_theme_options[footer_logo]" value="<?php echo esc_attr($options['footer_logo'] ?? ''); ?>" class="regular-text" />
                        <button type="button" class="button" id="footer_logo_upload">Upload</button>
                    </td>
                </tr>
            </table>
            <h2 style="margin-top:2em;">Contact Information</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Contact Address</th>
                    <td><textarea name="rip_theme_options[address]" class="large-text" style="max-width:400px;width:100%;height:200px;" rows="10"><?php echo esc_textarea($options['address'] ?? ''); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Telephone</th>
                    <td><input type="text" name="rip_theme_options[telephone]" value="<?php echo esc_attr($options['telephone'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Email Address</th>
                    <td><input type="email" name="rip_theme_options[email]" value="<?php echo esc_attr($options['email'] ?? ''); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <script>
    (function($){
        $('#footer_logo_upload').on('click', function(e) {
            e.preventDefault();
            var custom_uploader = wp.media({
                title: 'Select Footer Logo',
                button: { text: 'Use this logo' },
                multiple: false
            })
            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#footer_logo').val(attachment.url);
            })
            .open();
        });
    })(jQuery);
    </script>
    <?php
}

/**
 * Helper: Get theme option by key
 */
function rip_get_theme_option($key) {
    $options = get_option('rip_theme_options', []);
    return $options[$key] ?? '';
}
