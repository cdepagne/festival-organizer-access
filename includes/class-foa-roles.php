<?php
/**
 * Plugin roles.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles organizer roles.
 */
class FOA_Roles
{
    const ORGANIZER_ROLE = 'organisateur_festival';

    /**
     * Registers plugin roles.
     */
    public static function register_roles()
    {
        add_role(
            self::ORGANIZER_ROLE,
            __('Organisateur Festival', 'festival-organizer-access'),
            array(
                'read' => true,
            )
        );
    }

    /**
     * Assigns the organizer role to a user.
     *
     * @param int $user_id User ID.
     */
    public static function assign_organizer_role($user_id)
    {
        $user = get_userdata($user_id);

        if (!$user) {
            return;
        }

        $user->set_role(self::ORGANIZER_ROLE);
    }
}
