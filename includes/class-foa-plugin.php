<?php
/**
 * Main plugin class.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once FOA_PLUGIN_DIR . 'admin/class-foa-admin.php';
require_once FOA_PLUGIN_DIR . 'public/class-foa-public.php';

/**
 * Coordinates plugin hooks.
 */
class FOA_Plugin
{
    /**
     * Registers WordPress hooks.
     */
    public function run()
    {
        $this->load_textdomain();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Loads translation files.
     */
    private function load_textdomain()
    {
        load_plugin_textdomain(
            'festival-organizer-access',
            false,
            dirname(FOA_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Registers admin-only hooks.
     */
    private function define_admin_hooks()
    {
        if (!is_admin()) {
            return;
        }

        $admin = new FOA_Admin();

        add_action('admin_enqueue_scripts', array($admin, 'enqueue_assets'));
        add_action('admin_menu', array($admin, 'register_menu'));
    }

    /**
     * Registers public-facing hooks.
     */
    private function define_public_hooks()
    {
        $public = new FOA_Public();

        add_action('wp_enqueue_scripts', array($public, 'enqueue_assets'));
    }
}
