<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://faizanhaidar.com
 * @since             1.0.0
 * @package           Wp_Task_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       WP Task Manager
 * Plugin URI:        https://https://github.com/MuhammadFaizanHaidar/wp-task-manager/
 * Description:       Plugin serves as a RESTful API for a specialized to-do list application. The plugin showcases my proficiency in coding standards, creativity in problem-solving, and ability in integrating functionalities within the WordPress framework.
 * Version:           1.0.0
 * Author:            Muhammad Faizan Haidar
 * Author URI:        https://faizanhaidar.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-task-manager
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
define( 'WP_TASK_MANAGER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-task-manager-activator.php
 */
function activate_wp_task_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-task-manager-activator.php';
	Wp_Task_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-task-manager-deactivator.php
 */
function deactivate_wp_task_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-task-manager-deactivator.php';
	Wp_Task_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_task_manager' );
register_deactivation_hook( __FILE__, 'deactivate_wp_task_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-task-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_task_manager() {
	$plugin = Wp_Task_Manager::getInstance();
	$plugin->run();
}
add_action( 'plugins_loaded', 'run_wp_task_manager', 10 );
