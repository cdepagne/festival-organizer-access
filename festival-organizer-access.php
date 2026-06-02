<?php
/**
 * Plugin Name: Gestion Festival Auteur
 * Description: Permet aux auteurs de gerer leur festival apres abonnement.
 * Version: 0.1.0
 * Author: Skida
 * Text Domain: festival-organizer-access
 * Domain Path: /languages
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FOA_VERSION', '0.1.0');
define('FOA_PLUGIN_FILE', __FILE__);
define('FOA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FOA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FOA_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once FOA_PLUGIN_DIR . 'includes/class-foa-activator.php';
require_once FOA_PLUGIN_DIR . 'includes/class-foa-deactivator.php';
require_once FOA_PLUGIN_DIR . 'includes/class-foa-plugin.php';

register_activation_hook(__FILE__, array('FOA_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('FOA_Deactivator', 'deactivate'));

add_action('plugins_loaded', 'foa_run_plugin');

/**
 * Starts the plugin once WordPress and other plugins are loaded.
 */
function foa_run_plugin()
{
    $plugin = new FOA_Plugin();
    $plugin->run();
}
