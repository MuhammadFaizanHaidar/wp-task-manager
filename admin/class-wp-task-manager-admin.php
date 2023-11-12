<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://faizanhaidar.com
 * @since      1.0.0
 *
 * @package    Wp_Task_Manager
 * @subpackage Wp_Task_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Task_Manager
 * @subpackage Wp_Task_Manager/admin
 * @author     Muhammad Faizan Haidar <faizanhaider594@gmail.com>
 */
class Wp_Task_Manager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Task_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Task_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $post;
		if ( get_current_screen()->id == 'wptaskmanager' ) {
			wp_enqueue_style(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'css/wp-task-manager-admin.css',
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				$this->plugin_name . 'jquery-ui-css',
				'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
				array(),
				$this->version,
				'all'
			);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Task_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Task_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( get_current_screen()->id == 'wptaskmanager' ) {

			wp_enqueue_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'js/wp-task-manager-admin.js',
				array( 'jquery' ),
				$this->version,
				false
			);

			// Enqueue jQuery UI from Google CDN
			wp_enqueue_script(
				$this->plugin_name . 'jquery-ui-datepicker-js',
				'https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js',
				array( 'jquery' ),
				$this->version,
				false
			);
		}
	}

	/**
	 * Notifies admin for tasks based on open status/in_progress and due date/current_date.
	 *
	 * @return void
	 */
	public function wp_task_manager_due_task_admin_notifications() {

		if ( ! is_admin() ) {
			return;
		}
		

		$class   = 'notice is-dismissible error';
		$message = '';

		// Get today due and pending tasks' notificatoins

		// set query
		$query_args_post['posts_per_page'] = -1;
		$query_args_post['post_type']      = 'wptaskmanager';

		// Set meta params
		$meta_query [] = array(
			'key'     => 'wp_task_manager_task_due_date',
			'value'   => date( 'Y-m-d' ),
			'compare' => '=',
			'type'    => 'DATE',
		);

		$meta_query [] = array(
			'key'     => 'wp_task_manager_task_status',
			'value'   => array( 'in_progress', 'open' ),
			'compare' => 'IN',
		);

		$query_args_post['meta_query'] = $meta_query;
		$due_tasks                     = get_posts( $query_args_post );
		if ( ! empty( $due_tasks ) ) {
			foreach ( $due_tasks as $due_task ) :
				$message = esc_html__( 'Today due tasks: ', 'wp-task-manager' ) . '<a href="https://google.com" class="btn btn-primary">' . $due_task->post_title . '</a>';
				printf( '<div id="message" class="%s"> <p>%s</p></div>', $class, $message );
			endforeach;

		}

	}

}
