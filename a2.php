<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              unitycode.tech
 * @since             1.0.0
 * @package           A2
 *
 * @wordpress-plugin
 * Plugin Name:       A2 Manager
 * Plugin URI:        unitycode.tech
 * Description:       Acompanhantes A2 é uma plataforma de anúncios de acompanhantes para adultos. Este plugin é responsável pelo gerenciamento, funcionalidades e regras de negócio do sistema.
 * Version:           1.0.0
 * Author:            jnz93
 * Author URI:        unitycode.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       a2
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'A2_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-a2-activator.php
 */
function activate_a2() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-a2-activator.php';
	A2_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-a2-deactivator.php
 */
function deactivate_a2() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-a2-deactivator.php';
	A2_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_a2' );
register_deactivation_hook( __FILE__, 'deactivate_a2' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-a2.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_a2() {

	$plugin = new A2();
	$plugin->run();

}
run_a2();
