<?php
/**
 * Fired during plugin activation.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles activation tasks.
 */
class FOA_Activator
{
    /**
     * Runs when the plugin is activated.
     */
    public static function activate()
    {
        if (class_exists('FOA_Roles')) {
            FOA_Roles::register_roles();
        }

        if (class_exists('FOA_Claims')) {
            $claims = new FOA_Claims();
            $claims->register_post_type();
        }

        flush_rewrite_rules();
    }
}
