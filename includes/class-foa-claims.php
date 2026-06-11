<?php
/**
 * Festival claim requests.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles claim request storage and validation.
 */
class FOA_Claims
{
    const POST_TYPE = 'foa_claim';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Registers the internal claim post type.
     */
    public function register_post_type()
    {
        register_post_type(
            self::POST_TYPE,
            array(
                'labels' => array(
                    'name' => __('Demandes de reclamation', 'festival-organizer-access'),
                    'singular_name' => __('Demande de reclamation', 'festival-organizer-access'),
                    'menu_name' => __('Reclamations', 'festival-organizer-access'),
                ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => 'foa-dashboard',
                'supports' => array('title'),
                'capability_type' => 'post',
            )
        );
    }

    /**
     * Handles public claim form submissions.
     */
    public function handle_claim_submission()
    {
        if (!isset($_POST['foa_claim_nonce'], $_POST['foa_festival_id'])) {
            return;
        }

        if (!is_user_logged_in()) {
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['foa_claim_nonce'])), 'foa_claim_festival')) {
            return;
        }

        $festival_id = absint($_POST['foa_festival_id']);
        $post_type = FOA_Settings::get_festival_post_type();

        if (!$festival_id || $post_type !== get_post_type($festival_id)) {
            return;
        }

        $user = wp_get_current_user();
        $claim_id = wp_insert_post(
            array(
                'post_type' => self::POST_TYPE,
                'post_status' => 'publish',
                'post_title' => sprintf(
                    /* translators: 1: festival title, 2: user email */
                    __('Reclamation: %1$s par %2$s', 'festival-organizer-access'),
                    get_the_title($festival_id),
                    $user->user_email
                ),
            )
        );

        if (is_wp_error($claim_id) || !$claim_id) {
            return;
        }

        update_post_meta($claim_id, '_foa_festival_id', $festival_id);
        update_post_meta($claim_id, '_foa_user_id', get_current_user_id());
        update_post_meta($claim_id, '_foa_claim_status', self::STATUS_PENDING);
        update_post_meta($claim_id, '_foa_contact_name', isset($_POST['foa_contact_name']) ? sanitize_text_field(wp_unslash($_POST['foa_contact_name'])) : '');
        update_post_meta($claim_id, '_foa_contact_phone', isset($_POST['foa_contact_phone']) ? sanitize_text_field(wp_unslash($_POST['foa_contact_phone'])) : '');
        update_post_meta($claim_id, '_foa_message', isset($_POST['foa_message']) ? sanitize_textarea_field(wp_unslash($_POST['foa_message'])) : '');

        wp_safe_redirect(add_query_arg('foa_claim', 'sent', get_permalink($festival_id)));
        exit;
    }

    /**
     * Registers claim details metabox.
     */
    public function register_meta_boxes()
    {
        add_meta_box(
            'foa_claim_details',
            __('Details de la demande', 'festival-organizer-access'),
            array($this, 'render_claim_metabox'),
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * Renders claim details metabox.
     *
     * @param WP_Post $post Claim post.
     */
    public function render_claim_metabox($post)
    {
        $festival_id = (int) get_post_meta($post->ID, '_foa_festival_id', true);
        $user_id = (int) get_post_meta($post->ID, '_foa_user_id', true);
        $status = get_post_meta($post->ID, '_foa_claim_status', true);
        $contact_name = get_post_meta($post->ID, '_foa_contact_name', true);
        $contact_phone = get_post_meta($post->ID, '_foa_contact_phone', true);
        $message = get_post_meta($post->ID, '_foa_message', true);

        wp_nonce_field('foa_save_claim', 'foa_claim_admin_nonce');
        ?>
        <p><strong><?php esc_html_e('Festival', 'festival-organizer-access'); ?>:</strong>
            <?php echo $festival_id ? '<a href="' . esc_url(get_edit_post_link($festival_id)) . '">' . esc_html(get_the_title($festival_id)) . '</a>' : esc_html__('Non renseigne', 'festival-organizer-access'); ?>
        </p>
        <p><strong><?php esc_html_e('Utilisateur', 'festival-organizer-access'); ?>:</strong>
            <?php
            $user = $user_id ? get_userdata($user_id) : false;
            echo $user ? esc_html($user->user_email) : esc_html__('Non renseigne', 'festival-organizer-access');
            ?>
        </p>
        <p><strong><?php esc_html_e('Nom', 'festival-organizer-access'); ?>:</strong> <?php echo esc_html($contact_name); ?></p>
        <p><strong><?php esc_html_e('Telephone', 'festival-organizer-access'); ?>:</strong> <?php echo esc_html($contact_phone); ?></p>
        <p><strong><?php esc_html_e('Message', 'festival-organizer-access'); ?>:</strong><br><?php echo nl2br(esc_html($message)); ?></p>
        <p>
            <label for="foa_claim_status"><strong><?php esc_html_e('Statut', 'festival-organizer-access'); ?></strong></label><br>
            <select id="foa_claim_status" name="foa_claim_status">
                <option value="<?php echo esc_attr(self::STATUS_PENDING); ?>" <?php selected($status, self::STATUS_PENDING); ?>><?php esc_html_e('En attente', 'festival-organizer-access'); ?></option>
                <option value="<?php echo esc_attr(self::STATUS_APPROVED); ?>" <?php selected($status, self::STATUS_APPROVED); ?>><?php esc_html_e('Acceptee', 'festival-organizer-access'); ?></option>
                <option value="<?php echo esc_attr(self::STATUS_REJECTED); ?>" <?php selected($status, self::STATUS_REJECTED); ?>><?php esc_html_e('Refusee', 'festival-organizer-access'); ?></option>
            </select>
        </p>
        <p><?php esc_html_e('En acceptant la demande, l utilisateur recevra le role Organisateur Festival. Si aucun abonnement n existe encore, sa premiere annee gratuite sera activee automatiquement.', 'festival-organizer-access'); ?></p>
        <?php
    }

    /**
     * Saves claim status and grants access when approved.
     *
     * @param int     $post_id Claim post ID.
     * @param WP_Post $post Claim post.
     */
    public function save_claim($post_id, $post)
    {
        if (!isset($_POST['foa_claim_admin_nonce'])) {
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['foa_claim_admin_nonce'])), 'foa_save_claim')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $status = isset($_POST['foa_claim_status']) ? sanitize_key($_POST['foa_claim_status']) : self::STATUS_PENDING;
        $allowed_statuses = array(self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED);

        if (!in_array($status, $allowed_statuses, true)) {
            $status = self::STATUS_PENDING;
        }

        update_post_meta($post_id, '_foa_claim_status', $status);

        if (self::STATUS_APPROVED !== $status) {
            return;
        }

        $festival_id = (int) get_post_meta($post_id, '_foa_festival_id', true);
        $user_id = (int) get_post_meta($post_id, '_foa_user_id', true);

        if ($festival_id && $user_id) {
            update_post_meta($festival_id, FOA_Access::FESTIVAL_OWNER_META, $user_id);
        }

        if ($user_id) {
            FOA_Roles::assign_organizer_role($user_id);
            FOA_Access::grant_free_first_year($user_id);
        }
    }

    /**
     * Registers admin list columns.
     *
     * @param array $columns Existing columns.
     * @return array
     */
    public function register_admin_columns($columns)
    {
        $columns['foa_festival'] = __('Festival', 'festival-organizer-access');
        $columns['foa_user'] = __('Utilisateur', 'festival-organizer-access');
        $columns['foa_status'] = __('Statut', 'festival-organizer-access');

        return $columns;
    }

    /**
     * Renders admin list columns.
     *
     * @param string $column Column key.
     * @param int    $post_id Claim post ID.
     */
    public function render_admin_column($column, $post_id)
    {
        if ('foa_festival' === $column) {
            $festival_id = (int) get_post_meta($post_id, '_foa_festival_id', true);
            echo $festival_id ? esc_html(get_the_title($festival_id)) : '&mdash;';
        }

        if ('foa_user' === $column) {
            $user_id = (int) get_post_meta($post_id, '_foa_user_id', true);
            $user = $user_id ? get_userdata($user_id) : false;
            echo $user ? esc_html($user->user_email) : '&mdash;';
        }

        if ('foa_status' === $column) {
            echo esc_html(get_post_meta($post_id, '_foa_claim_status', true));
        }
    }
}
