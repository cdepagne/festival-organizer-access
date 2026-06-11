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

        add_submenu_page(
            'foa-dashboard',
            __('Reglages', 'festival-organizer-access'),
            __('Reglages', 'festival-organizer-access'),
            'manage_options',
            'foa-settings',
            array($this, 'render_settings')
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
            <p><?php esc_html_e('Le plugin gere les demandes de reclamation de festivals et limite la modification aux organisateurs valides avec abonnement annuel actif.', 'festival-organizer-access'); ?></p>
            <ol>
                <li><?php esc_html_e('L organisateur reclame un festival depuis une page separee.', 'festival-organizer-access'); ?></li>
                <li><?php esc_html_e('Vous acceptez ou refusez la demande dans les reclamations.', 'festival-organizer-access'); ?></li>
                <li><?php esc_html_e('Une demande acceptee attribue le role Organisateur Festival et active la premiere annee gratuite.', 'festival-organizer-access'); ?></li>
                <li><?php esc_html_e('Le renouvellement sera ensuite conditionne a l abonnement annuel payant.', 'festival-organizer-access'); ?></li>
            </ol>
        </div>
        <?php
    }

    /**
     * Renders plugin settings.
     */
    public function render_settings()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = FOA_Settings::get_settings();
        ?>
        <div class="wrap foa-admin">
            <h1><?php esc_html_e('Reglages Gestion Festival', 'festival-organizer-access'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('foa_settings'); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="foa_festival_post_type"><?php esc_html_e('Type de contenu Festival', 'festival-organizer-access'); ?></label>
                        </th>
                        <td>
                            <input
                                id="foa_festival_post_type"
                                name="foa_settings[festival_post_type]"
                                type="text"
                                value="<?php echo esc_attr($settings['festival_post_type']); ?>"
                                class="regular-text"
                            >
                            <p class="description"><?php esc_html_e('Exemple: festival. Ce code doit correspondre au post type deja utilise par le site.', 'festival-organizer-access'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="foa_annual_price"><?php esc_html_e('Prix annuel', 'festival-organizer-access'); ?></label>
                        </th>
                        <td>
                            <input
                                id="foa_annual_price"
                                name="foa_settings[annual_price]"
                                type="text"
                                value="<?php echo esc_attr($settings['annual_price']); ?>"
                                class="small-text"
                            >
                            <span><?php esc_html_e('EUR', 'festival-organizer-access'); ?></span>
                            <p class="description"><?php esc_html_e('Ce montant servira au renouvellement annuel apres la premiere annee gratuite.', 'festival-organizer-access'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="foa_claim_page_url"><?php esc_html_e('Page de reclamation', 'festival-organizer-access'); ?></label>
                        </th>
                        <td>
                            <input
                                id="foa_claim_page_url"
                                name="foa_settings[claim_page_url]"
                                type="url"
                                value="<?php echo esc_attr($settings['claim_page_url']); ?>"
                                class="regular-text"
                            >
                            <p class="description"><?php esc_html_e('URL de la page qui contient le shortcode [foa_claim_festival].', 'festival-organizer-access'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <h2><?php esc_html_e('Shortcodes disponibles', 'festival-organizer-access'); ?></h2>
            <p><code>[foa_claim_button]</code> <?php esc_html_e('a placer sur une fiche festival pour afficher le bouton de reclamation.', 'festival-organizer-access'); ?></p>
            <p><code>[foa_claim_festival]</code> <?php esc_html_e('a placer sur la page separee de reclamation.', 'festival-organizer-access'); ?></p>
            <p><code>[foa_my_festival]</code> <?php esc_html_e('a placer sur la page espace organisateur.', 'festival-organizer-access'); ?></p>
        </div>
        <?php
    }
}
