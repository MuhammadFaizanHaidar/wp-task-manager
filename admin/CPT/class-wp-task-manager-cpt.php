<?php

/**
 * Hanldes CPT functions.
 *
 * @link       https://faizanhaidar.com
 * @since      1.0.0
 *
 * @package    Wp_Task_Manager
 * @subpackage Wp_Task_Manager/includes
 */

/**
 * Fired for the plugin CPT registeration.
 *
 * This class defines all code necessary to run for handling custom post type functionalities.
 *
 * @since      1.0.0
 * @package    Wp_Task_Manager
 * @subpackage Wp_Task_Manager/includes
 * @author     Muhammad Faizan Haidar <faizanhaider594@gmail.com>
 */
class Wp_Task_Manager_CPT {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	private $admin_errors = 'wp_task_manager_admin_errors';

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
	 * Registers Task post type.
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function wp_task_manager_create_custom_post_type_task() {

		/*
		 * The $labels describes how the post type appears.
		 */
		$labels = array(
			'name'               => esc_html__( 'WP Tasks Manager', 'wp-task-manager' ),
			'singular_name'      => esc_html__( 'WP Task Manager', 'wp-task-manager' ),
			'add_new'            => esc_html__( 'Add New Task', 'wp-task-manager' ),
			'add_new_item'       => esc_html__( 'Add New Task', 'wp-task-manager' ),
			'edit_item'          => esc_html__( 'Edit Task', 'wp-task-manager' ),
			'new_item'           => esc_html__( 'Add New Task', 'wp-task-manager' ),
			'view_item'          => esc_html__( 'View Task', 'wp-task-manager' ),
			'search_items'       => esc_html__( 'Search Task', 'wp-task-manager' ),
			'not_found'          => esc_html__( 'No task found.', 'wp-task-manager' ),
			'not_found_in_trash' => esc_html__( 'No task found in trash.', 'wp-task-manager' ),
		);

		/*
		 * The $supports parameter describes what the post type supports
		 */
		$supports = array(
			'title',        // Post title
			'editor',       // Post content
			'excerpt',      // Allows short description
			'author',       // Allows showing and choosing author
		);

		/*
		 * The $args parameter holds important parameters for the custom post type
		 */
		$args = array(
			'labels'              => $labels,
			'description'         => esc_html__( 'Post type Task', 'wp-task-manager' ), // Description
			'supports'            => $supports,
			'hierarchical'        => true, // Allows hierarchical categorization, if set to false, the Custom Post Type will behave like Post, else it will behave like Page
			'public'              => true,  // Makes the post type public
			'show_ui'             => true,  // Displays an interface for this post type
			'show_in_menu'        => true,  // Displays in the Admin Menu (the left panel)
			'show_in_nav_menus'   => true,  // Displays in Appearance -> Menus
			'show_in_admin_bar'   => true,  // Displays in the black admin bar
			'menu_position'       => 5,     // The position number in the left menu
			'menu_icon'           => true,  // The URL for the icon used for this post type
			'can_export'          => true,  // Allows content export using Tools -> Export
			'has_archive'         => true,  // Enables post type archive (by month, date, or year)
			'exclude_from_search' => false, // Excludes posts of this type in the front-end search result page if set to true, include them if set to false
			'publicly_queryable'  => true,  // Allows queries to be performed on the front-end part if set to true
			'capability_type'     => 'page', // Allows read, edit, delete like “Post”
			'show_in_rest'        => true,
		);

		register_post_type( 'wptaskmanager', $args ); // Create a post type with the slug is ‘product’ and arguments in $args.
	}

	/**
	 * Register menu and sub,enus.
	 *
	 * @since    1.0.0
	 */
	public function wp_task_manager_create_menu() {
		add_menu_page(
			esc_html__( 'WP Task Manager', 'wp-task-manager' ),
			esc_html__( 'WP Task Manager', 'wp-task-manager' ),
			'manage_options',
			'cs-shortcodes-menu',
			'',
			'dashicons-tagcloud',
			7
		);
	}

	/**
	 * Adds metaboxes to shortcode post type.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wp_task_manager_add_post_metaboxes() {
		add_meta_box(
			'wp_task_manager_task_status',
			'<span style="color:green">' . esc_html__( 'Task Status', 'wp-task-manager' ) . '</span>',
			array( $this, 'wp_task_manager_task_status_content' ),
			array( 'wptaskmanager' ),
			'side',
			'default'
		);

		add_meta_box(
			'wp_task_manager_task_due_date',
			'<span style="color:red">' . esc_html__( 'Task due date', 'wp-task-manager' ) . '</span>',
			array( $this, 'wp_task_manager_task_due_date_content' ),
			array( 'wptaskmanager' ),
			'normal',
			'default'
		);
	}

	/**
	 * Renders Content for Task status.
	 *
	 * @param [type] $post
	 * @return void
	 * @since 1.0.0
	 */
	public function wp_task_manager_task_status_content( $post ) {
		// Retrieve the existing task status value
		$task_status = get_post_meta( $post->ID, 'wp_task_manager_task_status', true ) ?? 'open';

		// Output the status radio buttons
		?>
		<label>
			<input type="radio" name="wp_task_manager_task_status" value="open" <?php checked( $task_status, 'open' ); ?>>
			<?php esc_html_e( 'Open', 'wp-task-manager' ); ?>
		</label>
		<br>
		<label>
			<input type="radio" name="wp_task_manager_task_status" value="in_progress" <?php checked( $task_status, 'in_progress' ); ?>>
			<?php esc_html_e( 'In Progress', 'wp-task-manager' ); ?>
		</label>
		<br>
		<label>
			<input type="radio" name="wp_task_manager_task_status" value="completed" <?php checked( $task_status, 'completed' ); ?>>
			<?php esc_html_e( 'Completed', 'wp-task-manager' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders Content for date picker.
	 *
	 * @param WP_POST $post
	 * @return void
	 * @since 1.0.0
	 */
	public function wp_task_manager_task_due_date_content( $post ) {
		// Retrieve the existing due date value.
		$wp_task_manager_task_due_date = get_post_meta( $post->ID, 'wp_task_manager_task_due_date', true ) ?? '00-00-0000';
		// Output the datepicker input field.
		?>
		<label for="wp_task_manager_task_due_date"><?php esc_html_e( 'Due date:', 'wp-task-manager' ); ?></label>
		<input type="text" id="wp_task_manager_task_due_date_content" name="wp_task_manager_task_due_date" value="<?php echo esc_attr( date( 'F j, Y', strtotime( $wp_task_manager_task_due_date ) ) ); ?>" />
		<script>
			jQuery(document).ready(function ($) {
				$("#wp_task_manager_task_due_date_content").datepicker();
			});
		</script>
		<?php
	}

	/**
	 * Hanldes Task post save/updates.
	 *
	 * @param integer $post_id
	 * @param WP_Post $post
	 * @param [bool/WP_Post mix]  $update
	 * @return void
	 * @since 1.0.0
	 */
	public function wp_task_manager_save_post_meta( int $post_id, WP_Post $post, $update ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save the due date value.
		if ( isset( $_POST['wp_task_manager_task_due_date'] ) ) {
			update_post_meta( $post_id, 'wp_task_manager_task_due_date', date( 'Y-m-d', strtotime( sanitize_text_field( $_POST['wp_task_manager_task_due_date'] ) ) ) );
		}

		// Save the task status value
		if ( isset( $_POST['wp_task_manager_task_status'] ) ) {
			update_post_meta( $post_id, 'wp_task_manager_task_status', sanitize_text_field( $_POST['wp_task_manager_task_status'] ) );
		}

	}

	/**
	 * Adds extra column to wptaskmanager listing
	 *
	 * @param [array] $columns
	 * @return array $columns
	 * @since 1.0.0
	 */
	public function wp_task_manager_add_custom_column_wptaskmanager( $columns ) {

		$columns['wp_task_manager_task_status'] = '<span style="color:green">' . esc_html__( 'Task Status', 'wp-task-manager' ) . '</span>';
		$columns['wp_task_manager_task_due_date'] = '<span style="color:red">' . esc_html__( 'Task due date', 'wp-task-manager' ) . '</span>';
		return $columns;
	}

	/**
	 * Manages data shows in custom column in wptaskmanager listing.
	 *
	 * @param [string] $column
	 * @param [int] $post_id
	 * @return void
	 * @since 1.0.0
	 */
	public function wp_task_manager_manage_custom_column_wptaskmanager( $column, $post_id ) {
		switch ( $column ) {

			case 'wp_task_manager_task_status':
				echo esc_html( get_post_meta( $post_id, 'wp_task_manager_task_status', true ) ) ?? 'Open';
				break;
			case 'wp_task_manager_task_due_date':
				echo esc_html( get_post_meta( $post_id, 'wp_task_manager_task_due_date', true ) ) ?? 'No due date';
				break;
			default:
			break;

		}
	}

}
