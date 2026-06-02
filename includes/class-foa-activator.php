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
        flush_rewrite_rules();
    }
}
