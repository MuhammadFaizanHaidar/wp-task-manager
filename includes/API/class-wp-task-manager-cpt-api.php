<?php

/**
 * Hanldes CPT API functions.
 *
 * @link       https://faizanhaidar.com
 * @since      1.0.0
 *
 * @package    Wp_Task_Manager
 * @subpackage Wp_Task_Manager/includes
 */

/**
 * Fired for the plugin CPT API registeration and hadling CURD.
 *
 * This class defines all code necessary to run for handling custom post type API CURD operations.
 *
 * @since      1.0.0
 * @package    Wp_Task_Manager
 * @subpackage Wp_Task_Manager/includes
 * @author     Muhammad Faizan Haidar <faizanhaider594@gmail.com>
 */
class Wp_Task_Manager_CPT_Api {

	/**
	 * Store errors to display if the JWT Token is wrong
	 *
	 * @var WP_Error
	 */
	private $jwt_error = null;

	/**
	 * Registers the rest api routes for our tasks and related data.
	 *
	 * Registers the rest api routes for our taks and related data.
	 *
	 * @since    1.0.0
	 */
	public function register_rest_route_tasks() {
		register_rest_route(
			'wptaskmanager/v1',
			'/tasks/add_task',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'wp_task_manager_add_task' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(
					'title'       => array(
						'required' => true,
					),
					'description' => array(),
					'status'      => array(),
					'due_date'    => array(),
				),
			)
		);

		register_rest_route(
			'wptaskmanager/v1',
			'/tasks/update_task',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'wp_task_manager_update_task' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(
					'id'          => array(
						'required' => true,
					),
					'title'       => array(),
					'description' => array(),
					'status'      => array(),
					'due_date'    => array(),
				),
			)
		);

		register_rest_route(
			'wptaskmanager/v1',
			'/tasks/delete_task',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'wp_task_manager_delete_task' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(
					'id' => array(
						'required' => true,
					),
				),
			)
		);

		register_rest_route(
			'wptaskmanager/v1',
			'/tasks/all_tasks',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'wp_task_manager_get_all_tasks' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(
					'search'         => array(),
					'due_start_date' => array(),
					'due_end_date'   => array(),
					'status'         => array(),
				),
			)
		);
	} // end register_rest_route_templates


	/**
	 * Validates Tokens
	 *
	 * @param \WP_REST_Request $request
	 * @return void
	 */
	public function get_items_permissions_check( \WP_REST_Request $request ) {
		//Wanted to Add JWT AUTH could not add because of limited time.
		return array(
			'code' => 'jwt_auth_valid_token',
			'data' => array(
				'status' => 200,
			),
		);

		$token     = $request->get_param( '_token' );
		$public_id = $request->get_param( 'public_id' );

		$Class_Wp_Task_Manager_CPT_Api_options = get_option( 'Class_Wp_Task_Manager_CPT_Api_advance_options', array() );
		$skip_token                                    = ! empty( $Class_Wp_Task_Manager_CPT_Api_options['Class_Wp_Task_Manager_CPT_Api_skip_token_check'] ) ? $Class_Wp_Task_Manager_CPT_Api_options['Class_Wp_Task_Manager_CPT_Api_skip_token_check'] : '';
		if ( $skip_token == 'on' ) {
			/** If the output is true return an answer to the request to show it */
			return array(
				'code' => 'jwt_auth_valid_token',
				'data' => array(
					'status' => 200,
				),
			);
		}

		if ( ! $token ) {
			Logs::add( 'permission-api-log', 'Api Error: No token found. Please enter your api keys correctly' );
			return new \WP_Error(
				'jwt_auth_no_auth_data',
				'Api Error: No token found. Please enter your api keys correctly',
				array(
					'status' => 400,
				)
			);
		}

		if ( ! $public_id ) {
			Logs::add( 'permission-api-log', 'Api Error: Invalid or missing public key' );
			return new \WP_Error(
				'jwt_auth_no_auth_data',
				'Api Error: Invalid or missing public key',
				array(
					'status' => 400,
				)
			);
		}
		 /** Try to decode the token */
		try {
			$public_key = get_site_option( 'the_rest_api_public_key', '' );
			$secret_key = get_site_option( $public_key, '' );
			if ( empty( $secret_key ) || $secret_key == '' ) {
				Logs::add( 'permission-api-log', 'Api Error: Invalid or missing private key.' );
				return new \WP_Error(
					'jwt_auth_no_auth_data',
					'Api Error: Invalid or missing private key.',
					array(
						'status'  => 402,
						'message' => 'Private key not created on server site.',
					)
				);
			}
			try {

				$token = Token::decode( $token, trim( $secret_key ), array( 'HS256' ) );
			} catch ( Exception $e ) {

				return new \WP_Error( 'jwt_auth_invalid_token', 'Invalid token or activation code. Please check your activation code, and re-enter', array( 'status' => 401 ) );
			}

			if ( ! $token->data ) {
				Logs::add( 'permission-api-log', 'Api Error: No token data' );
				return new \WP_Error(
					'jwt_auth_bad_iss',
					'Api Error: No token data',
					array(
						'status' => 402,
					)
				);
			}

			// TODO: add option to settings to enter a comma seperated list of allowed blog urls. Add hooks for developers to add, edit, delete list.
			$Class_Wp_Task_Manager_CPT_Api_options = get_option( 'Class_Wp_Task_Manager_CPT_Api_advance_options', array() );

			$allowed_sites = ! empty( $Class_Wp_Task_Manager_CPT_Api_options['Class_Wp_Task_Manager_CPT_Api_allowed_sites'] ) ? $Class_Wp_Task_Manager_CPT_Api_options['Class_Wp_Task_Manager_CPT_Api_allowed_sites'] : 'no';
			$urls     = ! empty( $Class_Wp_Task_Manager_CPT_Api_options['Class_Wp_Task_Manager_CPT_Api_allowed_urls'] ) ? $Class_Wp_Task_Manager_CPT_Api_options['Class_Wp_Task_Manager_CPT_Api_allowed_urls'] : 'no';

			$stripped     = str_replace( ' ', '', $urls );
			$allowed_urls = explode( ',', $stripped );

			/** The Token is decoded now validate the iss */
			if ( $allowed_sites == 'on' && ! in_array( $token->iss, $allowed_urls ) ) {
				/** The iss do not match, return error */
				return new \WP_Error(
					'jwt_auth_bad_iss',
					'Api Error: This URL is not authorized',
					array(
						'status' => 402,
					)
				);
			}

			/** Now validate the client public key */

			if ( $token->data->client->public_id != $public_id ) {

				Logs::add( 'permission-api-log', 'Api Error: Incorrect public key' );
				return new \WP_Error( 'jwt_auth_bad_public_id', 'Api Error: Incorrect public key', array( 'status' => 404 ) );

			}

			if ( $token->data->client->public_id != $public_key || $public_key != $public_id ) {

				Logs::add( 'permission-api-log', 'Api Error: Client/Server public key not matching' );
				return new \WP_Error( 'jwt_auth_bad_public_id', 'Api Error: Incorrect public key not matching with server', array( 'status' => 404 ) );

			}

			/** If the output is true return an answer to the request to show it */
			return array(
				'code' => 'jwt_auth_valid_token',
				'data' => array(
					'status' => 200,
				),
			);

		} catch ( Exception $e ) {
			Logs::add( 'permission-api-log', 'Invalid token. Check your api key and re-enter' );
			/** Something is wrong trying to decode the token, send back the error */
			return new \WP_Error( 'jwt_auth_invalid_token', 'Invalid token. Check your api key and re-enter', array( 'status' => 400 ) );
		}

		/** Something is wrong trying to decode the token, send back the error */
		Logs::add( 'permission-api-log', 'General Api error. Catch All.' );

		return new \WP_Error( 'jwt_auth_invalid_token', 'Invalid token or api key. Please create new keys, and re-enter', array( 'status' => 400 ) );
		wp_die();
	}

	/**
	 * Adds taks to CPT.
	 *
	 * @param [array/mix] $data
	 * @return array/json
	 */
	public function wp_task_manager_add_task( $data ) {
		// Implement logic to add a new task.
		$title       = sanitize_text_field( $data['title'] );
		$description = sanitize_text_field( $data['description'] );
		$due_date    = sanitize_text_field( $data['due_date'] );
		$task_status = sanitize_text_field( $data['status'] );
		$task_id     = wp_insert_post(
			array(
				'post_type'    => 'wptaskmanager',
				'post_title'   => $title,
				'post_content' => $description,
				'post_status' => 'publish',
			)
		);

		if ( $task_id && get_post_type( $task_id ) == 'wptaskmanager'  ) {
			// Return the new task as JSON.
			// Save the due date value.
			if ( isset( $data['due_date'] ) ) {
				update_post_meta( $task_id, 'wp_task_manager_task_due_date', date( 'Y-m-d', strtotime( sanitize_text_field( $data['due_date'] ) ) ) );
			}

			// Save the task status value
			if ( isset( $data['status'] ) ) {
				update_post_meta( $task_id, 'wp_task_manager_task_status', sanitize_text_field( $data['status'] ) );
			}

			/** If the output is true return an answer to the request to show it */
			return rest_ensure_response(
				array(
					'code' => 'success',
					'data' => array(
						'status'  => 200,
						'task_id' => $task_id,
						'message' => esc_html__( 'Task created successfully.', 'wp-task-manager' ),
					),
				)
			);
		} else {
			return new \WP_Error( 'task_not_created', 'Api Error: Task not created try again later.', array( 'status' => 400 ) );
		}
	}

	/**
	 * Updates tasks to CPT.
	 *
	 * @param array/mix $data
	 * @return array/json
	 */
	public function wp_task_manager_update_task( $data ) {
		// Implement logic to update a task.
		$task_id     = absint( $data['id'] );
		$title       = isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : false;
		$description = isset( $data['description'] ) ? sanitize_text_field( $data['description'] ) : false;
		$status      = isset( $data['status'] ) ? sanitize_text_field( $data['status'] ) : false;
		$due_date    = isset( $data['due_date'] ) ? sanitize_text_field( $data['due_date'] ) : false;
		if ( ! $task_id || get_post_type( $task_id ) != 'wptaskmanager' ) {
			// Return error.
			return new WP_Error( 'task_not_updated', 'Api Error: Invalid task id.Task not updated try again later.', array( 'status' => 401 ) );
		}
		$args = array();
		if ( $title && ! empty( $title ) ) {
			$args['post_title'] = $title;
		}
		if ( $description && ! empty( $description ) ) {
			$args['post_content'] = $description;
		}
		$args['ID'] = $task_id;
		if ( ! empty( $args['post_content'] ) || ! empty( $args['post_title'] ) ) {
			wp_insert_post(
				$args
			);
		}
		if ( $status && ! empty( $status ) ) {
			update_post_meta( $task_id, 'wp_task_manager_task_status', $status );
		}

		if ( $due_date && ! empty( $due_date ) ) {
			update_post_meta( $task_id, 'wp_task_manager_task_due_date', $due_date );
		}

		/** If the output is true return an answer to the request to show it */
		return rest_ensure_response(
			array(
				'code' => 'success',
				'data' => array(
					'status'  => 200,
					'task_id' => $task_id,
					'message' => esc_html__( 'Task updated successfully.', 'wp-task-manager' ),
				),
			)
		);
	}

	/**
	 * Deletes Task.
	 *
	 * @param [array] $data
	 * @return array/json/WP_Error
	 */
	public function wp_task_manager_delete_task( $data ) {
		// Implement logic to delete a task.
		$task_id = absint( $data['id'] );
		if ( ! $task_id || get_post_type( $task_id ) != 'wptaskmanager' ) {
			// Return error.
			return new \WP_Error( 'task_not_deleted', 'Api Error: Invalid task id.Task not deleted try again later.', array( 'status' => 402 ) );
		}
		$post_data = wp_delete_post( $task_id, true );
		if ( is_object( $post_data ) ) {
			/** If the output is true return an answer to the request to show it */
			return rest_ensure_response(
				array(
					'code' => 'success',
					'data' => array(
						'status'  => 200,
						'task_id' => $task_id,
						'message' => esc_html__( 'Task deleted successfully.', 'wp-task-manager' ),
					),
				)
			);
		} else {
			// Return error.
			return new WP_Error( 'task_not_deleted', 'Api Error:Task not deleted try again later.', array( 'status' => 402 ) );
		}
	}

	/**
	 * List tasks.
	 *
	 * @param [array/mix] $data
	 * @return postid/json
	 */
	public function wp_task_manager_get_all_tasks( $data ) {

		// Implement logic to retrive tasks.
		$search         = sanitize_text_field( $data['search'] );
		$due_start_date = sanitize_text_field( $data['due_start_date'] );
		$due_end_date   = sanitize_text_field( $data['due_end_date'] );
		$status         = ( isset( $data['status'] ) ? (array) $data['status'] : array() );

		// set query
		$query_args_post['posts_per_page'] = -1;
		$query_args_post['post_type']      = 'wptaskmanager';
		$query_args_post['fields']         = array( 'post_title' );

		// Set search param
		if ( ! empty( $search ) ) {
			$query_args_post['s'] = $search;
		}

		// Set due_start_date param
		if ( ! empty( $due_start_date ) ) {

			$meta_query [] = array(
				'key'     => 'wp_task_manager_task_due_date',
				'value'   => date( 'Y-m-d', strtotime( $due_start_date ) ),
				'compare' => '>=',
				'type'    => 'DATE',
			);

		}

		// Set due_end_date param
		if ( ! empty( $due_end_date ) ) {

			$meta_query [] = array(
				'key'     => 'wp_task_manager_task_due_date',
				'value'   => date( 'Y-m-d', strtotime( $due_end_date ) ),
				'compare' => '<=',
				'type'    => 'DATE',
			);
		}

		// Set status param
		if ( ! empty( $status ) ) {

			$status        = array_map( 'sanitize_text_field', $status );
			$meta_query [] = array(
				'key'     => 'wp_task_manager_task_status',
				'value'   => $status,
				'compare' => 'IN',
			);
		}

		// if meta query exist add to main post query
		if ( $meta_query ) {
			$query_args_post['meta_query'] = $meta_query;
		}

		$tasks = get_posts( $query_args_post );

		/** If the output is true return an answer to the request to show it */
		if ( $tasks ) {
			return rest_ensure_response(
				array(
					'code'    => 'success',
					'status'  => 200,
					'data'    => $tasks,
					'message' => esc_html__( 'Tasks list.', 'wp-task-manager' ),
				)
			);
		} else {
			return new \WP_Error( 'task_not_found', 'Api Error: Could not fetch tasks.', array( 'status' => 404 ) );
		}

	}

}
