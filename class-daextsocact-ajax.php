<?php

/*
 * this class should be used to include ajax actions and callbacks
 */

class Daextsocact_Ajax {

	protected static $instance = null;
	private $shared = null;

	private function __construct() {

		//assign an instance of the plugin info
		$this->shared = Daextsocact_Shared::get_instance();

		//Ajax handler
		add_action( 'wp_ajax_daextsocact_save_action', array( $this, 'daextsocact_save_action' ) );

	}

	/*
	 * return an istance of this class
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}


	/*
	 * ajax handler used to save actions
	 */
	public function daextsocact_save_action() {

		//check the referer
		check_ajax_referer( 'daextsocact', 'security' );

		//check user permissions
		if ( ! current_user_can( get_option( $this->shared->get( 'slug' ) . '_actions_menu_capability' ) ) ) {
			die();
		}

		//sanitize data
		$description  = sanitize_text_field( $_POST['description'] );
		$edit_id      = intval( $_POST['edit_id'], 10 );
		$field_data_a = $this->shared->sanitize_field_data( $_POST['field_data'] );

		if ( $edit_id > 0 ) {

			//UPDATE AN ACTION -----------------------------------------------------------------------------------------

			//update the action database
			global $wpdb;
			$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_action";
			$safe_sql   = $wpdb->prepare( "UPDATE $table_name SET description = %s WHERE id = %d", $description,
				$edit_id );
			$result     = $wpdb->query( $safe_sql );

			//Delete all the record associated with this action in the field db table.
			global $wpdb;
			$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_field";
			$safe_sql   = $wpdb->prepare( "DELETE FROM $table_name WHERE action_id = %d ", $edit_id );
			$result     = $wpdb->query( $safe_sql );

			//Add all the record associated with this action in the field db table.
			if ( count( $field_data_a ) > 0 ) {

				foreach ( $field_data_a as $key => $field_data ) {

					//get action id
					$action_id = $edit_id;

					//save in the database
					global $wpdb;
					$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_field";
					$safe_sql   = $wpdb->prepare( "INSERT INTO $table_name SET "
					                              . "action_id = %d,"
					                              . "element_id = %s,"
					                              . "pos_x = %d,"
					                              . "pos_y = %d,"
					                              . "label_text = %s",
						$action_id,
						$field_data['element_id'],
						$field_data['pos_x'],
						$field_data['pos_y'],
						$field_data['label_text'] );
					$result     = $wpdb->query( $safe_sql );

				}

			}

		} else {

			//SAVE A NEW ACTION ----------------------------------------------------------------------------------------

			//save in the database
			global $wpdb;
			$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_action";
			$safe_sql   = $wpdb->prepare( "INSERT INTO $table_name SET "
			                              . "description = %s",
				$description );
			$result     = $wpdb->query( $safe_sql );

			//get the automatic id of the inserted element
			$action_id = $wpdb->insert_id;

			//save the elements in the field database --------------------------
			if ( count( $field_data_a ) > 0 ) {

				foreach ( $field_data_a as $key => $field_data ) {

					//save in the database
					global $wpdb;
					$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_field";
					$safe_sql   = $wpdb->prepare( "INSERT INTO $table_name SET "
					                              . "action_id = %d,"
					                              . "element_id = %s,"
					                              . "pos_x = %d,"
					                              . "pos_y = %d,"
					                              . "label_text = %s",
						$action_id,
						$field_data['element_id'],
						$field_data['pos_x'],
						$field_data['pos_y'],
						$field_data['label_text'] );
					$result     = $wpdb->query( $safe_sql );

				}

			}

		}

		//send output
		echo "success";
		die();

	}

}