<?php
/**
 * Festival access rules.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles ownership and annual subscription checks.
 */
class FOA_Access
{
    const FESTIVAL_OWNER_META = '_foa_owner_user_id';
    const SUBSCRIPTION_EXPIRES_META = 'foa_subscription_expires_at';

    /**
     * Returns whether a user owns a festival.
     *
     * @param int $user_id User ID.
     * @param int $festival_id Festival post ID.
     * @return bool
     */
    public static function user_owns_festival($user_id, $festival_id)
    {
        $owner_id = (int) get_post_meta($festival_id, self::FESTIVAL_OWNER_META, true);

        return $owner_id > 0 && $owner_id === (int) $user_id;
    }

    /**
     * Returns whether a user's annual subscription is active.
     *
     * @param int $user_id User ID.
     * @return bool
     */
    public static function user_has_active_subscription($user_id)
    {
        $expires_at = get_user_meta($user_id, self::SUBSCRIPTION_EXPIRES_META, true);

        if (empty($expires_at)) {
            return false;
        }

        $expires_timestamp = strtotime($expires_at);

        return $expires_timestamp && $expires_timestamp >= current_time('timestamp');
    }

    /**
     * Grants one year of access to a user.
     *
     * @param int $user_id User ID.
     */
    public static function grant_annual_subscription($user_id)
    {
        $expires_at = gmdate('Y-m-d', strtotime('+1 year', current_time('timestamp')));

        update_user_meta($user_id, self::SUBSCRIPTION_EXPIRES_META, $expires_at);
    }

    /**
     * Returns whether a user can edit a festival through this plugin.
     *
     * @param int $user_id User ID.
     * @param int $festival_id Festival post ID.
     * @return bool
     */
    public static function user_can_edit_festival($user_id, $festival_id)
    {
        return self::user_owns_festival($user_id, $festival_id)
            && self::user_has_active_subscription($user_id);
    }
}
