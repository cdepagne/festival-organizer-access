<?php
/**
 * Public area setup.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles public-facing hooks.
 */
class FOA_Public
{
    /**
     * Enqueues public assets.
     */
    public function enqueue_assets()
    {
        wp_enqueue_style(
            'foa-public',
            FOA_PLUGIN_URL . 'assets/css/public.css',
            array(),
            FOA_VERSION
        );

        wp_enqueue_script(
            'foa-public',
            FOA_PLUGIN_URL . 'assets/js/public.js',
            array(),
            FOA_VERSION,
            true
        );
    }
}
