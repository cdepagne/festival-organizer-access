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

    /**
     * Renders a claim call-to-action button for festival pages.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_claim_button($atts)
    {
        $atts = shortcode_atts(
            array(
                'festival_id' => get_the_ID(),
                'label' => __('Reclamer ce festival', 'festival-organizer-access'),
            ),
            $atts,
            'foa_claim_button'
        );

        $festival_id = absint($atts['festival_id']);

        if (!$festival_id || FOA_Settings::get_festival_post_type() !== get_post_type($festival_id)) {
            return '';
        }

        $settings = FOA_Settings::get_settings();
        $claim_page_url = !empty($settings['claim_page_url']) ? $settings['claim_page_url'] : home_url('/reclamer-un-festival/');
        $claim_url = add_query_arg('foa_festival_id', $festival_id, $claim_page_url);

        return sprintf(
            '<p class="foa-claim-button-wrap"><a class="foa-claim-button" href="%1$s">%2$s</a></p>',
            esc_url($claim_url),
            esc_html($atts['label'])
        );
    }

    /**
     * Renders a festival claim form for the current festival page.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_claim_form($atts)
    {
        $atts = shortcode_atts(
            array(
                'festival_id' => isset($_GET['foa_festival_id']) ? absint($_GET['foa_festival_id']) : 0,
            ),
            $atts,
            'foa_claim_festival'
        );

        $festival_id = absint($atts['festival_id']);

        if (!$festival_id || FOA_Settings::get_festival_post_type() !== get_post_type($festival_id)) {
            return '<div class="foa-notice">' . esc_html__('Aucun festival valide n est associe a cette demande.', 'festival-organizer-access') . '</div>';
        }

        if (isset($_GET['foa_claim']) && 'sent' === sanitize_key(wp_unslash($_GET['foa_claim']))) {
            return '<div class="foa-notice foa-notice-success">' . esc_html__('Votre demande a bien ete envoyee. Elle sera etudiee avant ouverture des droits.', 'festival-organizer-access') . '</div>';
        }

        if (!is_user_logged_in()) {
            return '<div class="foa-notice">' . esc_html__('Connectez-vous pour reclamer la gestion de ce festival.', 'festival-organizer-access') . '</div>';
        }

        ob_start();
        ?>
        <form class="foa-claim-form" method="post">
            <h2><?php echo esc_html(get_the_title($festival_id)); ?></h2>
            <p><?php esc_html_e('Demandez le droit de mettre a jour cette fiche festival. La modification sera ouverte apres validation et abonnement annuel actif.', 'festival-organizer-access'); ?></p>

            <?php wp_nonce_field('foa_claim_festival', 'foa_claim_nonce'); ?>
            <input type="hidden" name="foa_festival_id" value="<?php echo esc_attr($festival_id); ?>">

            <p>
                <label for="foa_contact_name"><?php esc_html_e('Nom de l organisateur', 'festival-organizer-access'); ?></label>
                <input id="foa_contact_name" name="foa_contact_name" type="text" required>
            </p>

            <p>
                <label for="foa_contact_phone"><?php esc_html_e('Telephone', 'festival-organizer-access'); ?></label>
                <input id="foa_contact_phone" name="foa_contact_phone" type="text">
            </p>

            <p>
                <label for="foa_message"><?php esc_html_e('Message ou preuve de gestion', 'festival-organizer-access'); ?></label>
                <textarea id="foa_message" name="foa_message" rows="5"></textarea>
            </p>

            <p>
                <button type="submit"><?php esc_html_e('Reclamer ce festival', 'festival-organizer-access'); ?></button>
            </p>
        </form>
        <?php

        return ob_get_clean();
    }

    /**
     * Renders the current user's festival access status.
     *
     * @return string
     */
    public function render_my_festival()
    {
        if (!is_user_logged_in()) {
            return '<div class="foa-notice">' . esc_html__('Connectez-vous pour acceder a votre festival.', 'festival-organizer-access') . '</div>';
        }

        $festival = $this->get_current_user_festival(get_current_user_id());

        if (!$festival) {
            return '<div class="foa-notice">' . esc_html__('Aucun festival ne vous est encore attribue.', 'festival-organizer-access') . '</div>';
        }

        if (!FOA_Access::user_has_active_subscription(get_current_user_id())) {
            return '<div class="foa-notice">' . esc_html__('Votre festival est attribue, mais l abonnement annuel doit etre actif pour ouvrir la modification.', 'festival-organizer-access') . '</div>';
        }

        return '<div class="foa-my-festival"><h2>' . esc_html(get_the_title($festival->ID)) . '</h2><p>' . esc_html__('Votre abonnement est actif. Le formulaire de modification sera ajoute ici avec les champs autorises.', 'festival-organizer-access') . '</p></div>';
    }

    /**
     * Finds the festival assigned to a user.
     *
     * @param int $user_id User ID.
     * @return WP_Post|null
     */
    private function get_current_user_festival($user_id)
    {
        $festivals = get_posts(
            array(
                'post_type' => FOA_Settings::get_festival_post_type(),
                'post_status' => array('publish', 'draft', 'pending', 'private'),
                'posts_per_page' => 1,
                'meta_key' => FOA_Access::FESTIVAL_OWNER_META,
                'meta_value' => (int) $user_id,
            )
        );

        return !empty($festivals) ? $festivals[0] : null;
    }
}
