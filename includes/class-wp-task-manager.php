<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://faizanhaidar.com
 * @since      1.0.0
 *
 * @package    Wp_Task_Manager
 * @subpackage Wp_Task_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Task_Manager
 * @subpackage Wp_Task_Manager/includes
 * @author     Muhammad Faizan Haidar <faizanhaider594@gmail.com>
 */
class Wp_Task_Manager {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Task_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The instance the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      class    $instance    The instance of the class.
	 */
	
	private static $instance = null;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_TASK_MANAGER_VERSION' ) ) {
			$this->version = WP_TASK_MANAGER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-task-manager';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	// The object is created from within the class itself
	// only if the class has no instance.
	public static function getInstance() {
		if ( self::$instance == null ) {
			self::$instance = new Wp_Task_Manager();
		}
	
		return self::$instance;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Task_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Task_Manager_i18n. Defines internationalization functionality.
	 * - Wp_Task_Manager_Admin. Defines all hooks for the admin area.
	 * - Wp_Task_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-task-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-task-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-task-manager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area for CPT registeration and management.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/CPT/class-wp-task-manager-cpt.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-task-manager-public.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/API/class-wp-task-manager-cpt-api.php';

		$this->loader = new Wp_Task_Manager_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Task_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Task_Manager_i18n();

		$this->loader->add_action(
			'plugins_loaded',
			$plugin_i18n,
			'load_plugin_textdomain'
		);

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Task_Manager_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_cpt   = new Wp_Task_Manager_CPT( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action(
			'init',
			$plugin_cpt,
			'wp_task_manager_create_custom_post_type_task',
			10
		);

		$this->loader->add_action(
			'admin_notices',
			$plugin_admin,
			'wp_task_manager_due_task_admin_notifications'
		);

		$this->loader->add_action(
			'admin_enqueue_scripts',
			$plugin_admin,
			'enqueue_styles'
		);

		$this->loader->add_action(
			'admin_enqueue_scripts',
			$plugin_admin,
			'enqueue_scripts'
		);

		$this->loader->add_action(
			'add_meta_boxes',
			$plugin_cpt,
			'wp_task_manager_add_post_metaboxes'
		);

		$this->loader->add_action(
			'save_post',
			$plugin_cpt,
			'wp_task_manager_save_post_meta',
			10,
			3
		);

		$this->loader->add_action(
			'post_updated',
			$plugin_cpt,
			'wp_task_manager_save_post_meta',
			10,
			3
		);

		$this->loader->add_filter(
			'manage_wptaskmanager_posts_columns',
			$plugin_cpt,
			'wp_task_manager_add_custom_column_wptaskmanager'
		);

		$this->loader->add_action(
			'manage_wptaskmanager_posts_custom_column',
			$plugin_cpt,
			'wp_task_manager_manage_custom_column_wptaskmanager',
			10,
			2
		);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function define_public_hooks() {

		$plugin_public    = new Wp_Task_Manager_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_tasks_API = new Wp_Task_Manager_CPT_Api();

		$this->loader->add_action(
			'rest_api_init',
			$plugin_tasks_API,
			'register_rest_route_tasks'
		);

		$this->loader->add_action(
			'wp_enqueue_scripts',
			$plugin_public,
			'enqueue_styles'
		);

		$this->loader->add_action(
			'wp_enqueue_scripts',
			$plugin_public,
			'enqueue_scripts'
		);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Task_Manager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
