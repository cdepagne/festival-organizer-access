<?php
/**
 * Plugin settings.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Stores and retrieves plugin options.
 */
class FOA_Settings
{
    const OPTION_NAME = 'foa_settings';

    /**
     * Registers plugin settings.
     */
    public function register_settings()
    {
        register_setting(
            'foa_settings',
            self::OPTION_NAME,
            array($this, 'sanitize_settings')
        );
    }

    /**
     * Returns all settings merged with defaults.
     *
     * @return array
     */
    public static function get_settings()
    {
        $defaults = array(
            'festival_post_type' => 'festival',
            'annual_price' => '20',
            'claim_page_url' => '',
        );

        $settings = get_option(self::OPTION_NAME, array());

        if (!is_array($settings)) {
            $settings = array();
        }

        return wp_parse_args($settings, $defaults);
    }

    /**
     * Returns the configured festival post type.
     *
     * @return string
     */
    public static function get_festival_post_type()
    {
        $settings = self::get_settings();

        return sanitize_key($settings['festival_post_type']);
    }

    /**
     * Sanitizes settings before saving.
     *
     * @param array $settings Submitted settings.
     * @return array
     */
    public function sanitize_settings($settings)
    {
        if (!is_array($settings)) {
            $settings = array();
        }

        return array(
            'festival_post_type' => isset($settings['festival_post_type']) ? sanitize_key($settings['festival_post_type']) : 'festival',
            'annual_price' => isset($settings['annual_price']) ? sanitize_text_field($settings['annual_price']) : '20',
            'claim_page_url' => isset($settings['claim_page_url']) ? esc_url_raw($settings['claim_page_url']) : '',
        );
    }
}
