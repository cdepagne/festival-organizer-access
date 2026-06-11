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
require_once FOA_PLUGIN_DIR . 'includes/class-foa-access.php';
require_once FOA_PLUGIN_DIR . 'includes/class-foa-acf-fields.php';
require_once FOA_PLUGIN_DIR . 'includes/class-foa-claims.php';
require_once FOA_PLUGIN_DIR . 'includes/class-foa-roles.php';
require_once FOA_PLUGIN_DIR . 'includes/class-foa-settings.php';
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
        $this->define_common_hooks();
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
     * Registers hooks shared by the admin and public areas.
     */
    private function define_common_hooks()
    {
        $claims = new FOA_Claims();

        add_action('init', array('FOA_Roles', 'register_roles'));
        add_action('init', array($claims, 'register_post_type'));
        add_action('init', array($claims, 'handle_claim_submission'));
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
        $settings = new FOA_Settings();
        $claims = new FOA_Claims();

        add_action('admin_enqueue_scripts', array($admin, 'enqueue_assets'));
        add_action('admin_menu', array($admin, 'register_menu'));
        add_action('admin_init', array($settings, 'register_settings'));
        add_action('add_meta_boxes', array($claims, 'register_meta_boxes'));
        add_action('save_post_foa_claim', array($claims, 'save_claim'), 10, 2);
        add_filter('manage_foa_claim_posts_columns', array($claims, 'register_admin_columns'));
        add_action('manage_foa_claim_posts_custom_column', array($claims, 'render_admin_column'), 10, 2);
    }

    /**
     * Registers public-facing hooks.
     */
    private function define_public_hooks()
    {
        $public = new FOA_Public();

        add_action('wp_enqueue_scripts', array($public, 'enqueue_assets'));
        add_shortcode('foa_claim_button', array($public, 'render_claim_button'));
        add_shortcode('foa_claim_festival', array($public, 'render_claim_form'));
        add_shortcode('foa_my_festival', array($public, 'render_my_festival'));
    }
}
