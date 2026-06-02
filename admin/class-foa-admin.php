<?php
/**
 * Admin area setup.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles WordPress admin hooks.
 */
class FOA_Admin
{
    /**
     * Enqueues admin assets.
     *
     * @param string $hook_suffix Current admin page hook suffix.
     */
    public function enqueue_assets($hook_suffix)
    {
        if ('toplevel_page_foa-dashboard' !== $hook_suffix) {
            return;
        }

        wp_enqueue_style(
            'foa-admin',
            FOA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            FOA_VERSION
        );

        wp_enqueue_script(
            'foa-admin',
            FOA_PLUGIN_URL . 'assets/js/admin.js',
            array(),
            FOA_VERSION,
            true
        );
    }

    /**
     * Registers the admin menu page.
     */
    public function register_menu()
    {
        add_menu_page(
            __('Gestion Festival', 'festival-organizer-access'),
            __('Gestion Festival', 'festival-organizer-access'),
            'manage_options',
            'foa-dashboard',
            array($this, 'render_dashboard'),
            'dashicons-tickets-alt',
            26
        );
    }

    /**
     * Renders the plugin dashboard.
     */
    public function render_dashboard()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap foa-admin">
            <h1><?php esc_html_e('Gestion Festival Auteur', 'festival-organizer-access'); ?></h1>
            <p><?php esc_html_e('Le socle du plugin est pret. Les prochaines fonctionnalites pourront etre ajoutees ici.', 'festival-organizer-access'); ?></p>
        </div>
        <?php
    }
}
