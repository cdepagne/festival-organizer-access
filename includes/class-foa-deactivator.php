<?php
/**
 * Fired during plugin deactivation.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles deactivation tasks.
 */
class FOA_Deactivator
{
    /**
     * Runs when the plugin is deactivated.
     */
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
