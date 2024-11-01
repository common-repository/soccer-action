<?php

/*
 * this class should be used to work with the administrative side of wordpress
 */

class Daextsocact_Admin {

	protected static $instance = null;
	private $shared = null;
	private $screen_id_actions = null;
	private $screen_id_help = null;
	private $screen_id_pro_version = null;
	private $screen_id_options = null;

	private $menu_options = null;

	private function __construct() {

		//assign an instance of the plugin info
		$this->shared = Daextsocact_Shared::get_instance();

		//Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		//Load the options API registrations and callbacks
		add_action( 'admin_init', array( $this, 'op_register_options' ) );

		//Add the admin menu
		add_action( 'admin_menu', array( $this, 'me_add_menu' ) );

		//Require and instantiate the class used to register the menu options
		require_once( $this->shared->get( 'dir' ) . 'admin/inc/class-daextsocact-menu-options.php' );
		$this->menu_options = new Daextsocact_Menu_Options( $this->shared );

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
	 * enqueue admin-specific style sheet
	 */
	public function enqueue_admin_styles() {

		$screen = get_current_screen();

		if ( $screen->id == $this->screen_id_actions ) {

			//jQuery UI Dialog
			wp_enqueue_style( $this->shared->get( 'slug' ) . '-jquery-ui-dialog',
				$this->shared->get( 'url' ) . 'admin/assets/css/jquery-ui-dialog.css', array(),
				$this->shared->get( 'ver' ) );
			wp_enqueue_style( $this->shared->get( 'slug' ) . '-jquery-ui-dialog-custom',
				$this->shared->get( 'url' ) . 'admin/assets/css/jquery-ui-dialog-custom.css', array(),
				$this->shared->get( 'ver' ) );

			//get the two dimensional array that contains all the fonts
			$fonts = $this->shared->get( 'fonts' );

			//get the sub-array with this specific font
			$selected_font = $fonts[ get_option( $this->shared->get( "slug" ) . "_label_font" ) ];

			//If is set a Google Font load it in the page
			$label_font = get_option( $this->shared->get( "slug" ) . "_label_font" );
			if ( $label_font === 'Arimo (Google Fonts)' or
			     $label_font === 'Maven Pro (Google Fonts)' or
			     $label_font === 'Open Sans (Google Fonts)' or
			     $label_font === 'Lato (Google Fonts)' or
			     $label_font === 'Lora (Google Fonts)' ) {

				wp_enqueue_style( $this->shared->get( 'slug' ) . '-label-font',
					esc_url( $selected_font['url'] ), false );

			}

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu',
				$this->shared->get( 'url' ) . 'admin/assets/css/framework/menu.css', array(),
				$this->shared->get( 'ver' ) );
			wp_enqueue_style( $this->shared->get( 'slug' ) . '-menu-actions',
				$this->shared->get( 'url' ) . 'admin/assets/css/menu-actions.css', array(),
				$this->shared->get( 'ver' ) );

			//Add custom CSS for the font-family and font-weight
			$custom_css = '#daextsocact-field > .daextsocact-player-label > input{ font-family: ' . htmlspecialchars( stripslashes( $selected_font['font-family'] ),
					ENT_NOQUOTES ) . '; }';
			$custom_css .= '#daextsocact-field > .daextsocact-player-label > input{ font-weight: ' . intval( $selected_font['font-weight'],
					10 ) . '; }';
			wp_add_inline_style( $this->shared->get( 'slug' ) . '-menu-actions', $custom_css );

		}

		//Menu Help
		if ( $screen->id == $this->screen_id_help ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-menu-help',
				$this->shared->get( 'url' ) . 'admin/assets/css/menu-help.css', array(), $this->shared->get( 'ver' ) );

		}

		//menu pro version
		if ( $screen->id == $this->screen_id_pro_version ) {
			wp_enqueue_style( $this->shared->get( 'slug' ) . '-menu-pro-version',
				$this->shared->get( 'url' ) . 'admin/assets/css/menu-pro-version.css', array(),
				$this->shared->get( 'ver' ) );
		}

		if ( $screen->id == $this->screen_id_options ) {

			//Select2
			wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2',
				$this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/css/select2.min.css', array(),
				$this->shared->get( 'ver' ) );
			wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2-custom',
				$this->shared->get( 'url' ) . 'admin/assets/css/select2-custom.css', array(),
				$this->shared->get( 'ver' ) );

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-jquery-ui-tooltip-custom',
				$this->shared->get( 'url' ) . 'admin/assets/css/jquery-ui-tooltip-custom.css', array(),
				$this->shared->get( 'ver' ) );
			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-options',
				$this->shared->get( 'url' ) . 'admin/assets/css/framework/options.css', array(),
				$this->shared->get( 'ver' ) );

		}

	}

	/*
	 * enqueue admin-specific javascript
	 */
	public function enqueue_admin_scripts() {

		$screen = get_current_screen();

		if ( $screen->id == $this->screen_id_actions ) {

			//Store the JavaScript parameters in the window.DAEXTSOCACT_PARAMETERS object
			$php_data = 'window.DAEXTSOCACT_PARAMETERS = {';
			$php_data .= 'ajax_url: "' . admin_url( 'admin-ajax.php' ) . '",';
			$php_data .= 'nonce: "' . wp_create_nonce( "daextsocact" ) . '",';
			$php_data .= 'admin_url: "' . admin_url( 'admin.php' ) . '"';
			$php_data .= '};';

			$wp_localize_script_data = array(
				'deleteText' => esc_html__( 'Delete', 'soccer-action' ),
				'cancelText' => esc_html__( 'Cancel', 'soccer-action' ),
			);

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-admin-script-draggable',
				$this->shared->get( 'url' ) . 'admin/assets/js/draggable.js', array( 'jquery', 'jquery-ui-draggable' ),
				$this->shared->get( 'ver' ) );
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-admin-script-edit-save',
				$this->shared->get( 'url' ) . 'admin/assets/js/edit-save.js', array( 'jquery', 'jquery-ui-dialog' ),
				$this->shared->get( 'ver' ) );

			wp_add_inline_script( $this->shared->get( 'slug' ) . '-admin-script-edit-save', $php_data, 'before' );
			wp_localize_script( $this->shared->get( 'slug' ) . '-admin-script-edit-save', 'objectL10n',
				$wp_localize_script_data );

		}

		if ( $screen->id == $this->screen_id_options ) {

			//JQuery UI Tooltips
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-jquery-ui-tooltip-init',
				$this->shared->get( 'url' ) . 'admin/assets/js/jquery-ui-tooltip-init.js', array( 'jquery' ),
				$this->shared->get( 'ver' ) );

			//Select2
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-select2',
				$this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/js/select2.min.js', array( 'jquery' ),
				$this->shared->get( 'ver' ) );

			//Menu Options
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu-options',
				$this->shared->get( 'url' ) . 'admin/assets/js/menu-options.js',
				array( 'jquery' ),
				$this->shared->get( 'ver' ) );

		}

	}

	/*
	 * register the admin menu
	 */
	public function me_add_menu() {

		//The icon in Base64 format
		$icon_base64 = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNS4yLjMsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyMCAyMCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjAgMjA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxwYXRoIGQ9Ik02LjgsOS4xTDEwLDYuOGwzLjIsMi4zTDEyLDEyLjlIOEw2LjgsOS4xeiBNMTAsMGMxLjQsMCwyLjcsMC4zLDMuOSwwLjhzMi4zLDEuMiwzLjIsMi4xYzAuOSwwLjksMS42LDEuOSwyLjEsMy4yDQoJUzIwLDguNywyMCwxMHMtMC4yLDIuNi0wLjgsMy45Yy0wLjYsMS4zLTEuMywyLjMtMi4xLDMuMmMtMC45LDAuOS0xLjksMS42LTMuMiwyLjFTMTEuMywyMCwxMCwyMHMtMi42LTAuMy0zLjktMC44UzMuOCwxOCwyLjksMTcuMQ0KCXMtMS42LTEuOS0yLjEtMy4yUzAsMTEuMywwLDEwczAuMy0yLjYsMC44LTMuOVMyLDMuOCwyLjksMi45czItMS42LDMuMi0yLjFTOC42LDAsMTAsMHogTTE2LjksMTUuMWMxLjEtMS41LDEuNy0zLjIsMS43LTUuMWwwLDANCglsLTEuMSwxbC0yLjctMi41bDAuNy0zLjZMMTcsNWMtMS4xLTEuNS0yLjYtMi42LTQuMy0zLjFsMC42LDEuNEwxMCw1TDYuOCwzLjJsMC42LTEuNEM1LjYsMi40LDQuMiwzLjQsMyw1bDEuNS0wLjFsMC43LDMuNkwyLjYsMTENCglsLTEuMS0xbDAsMGMwLDEuOSwwLjYsMy42LDEuNyw1LjFsMC4zLTEuNUw3LjEsMTRsMS42LDMuM2wtMS4zLDAuOGMwLjksMC4zLDEuOCwwLjQsMi43LDAuNHMxLjgtMC4xLDIuNy0wLjRsLTEuMy0wLjhsMS42LTMuMw0KCWwzLjYtMC40TDE2LjksMTUuMXoiLz4NCjwvc3ZnPg0K';

		//The icon in the data URI scheme
		$icon_data_uri = 'data:image/svg+xml;base64,' . $icon_base64;

		add_menu_page(
			esc_html__( 'Soccer Action', 'soccer-action' ),
			esc_html__( 'Soccer Action', 'soccer-action' ),
			get_option( $this->shared->get( 'slug' ) . '_actions_menu_capability' ),
			$this->shared->get( 'slug' ) . '-actions',
			array( $this, 'me_display_menu_actions' ),
			$icon_data_uri
		);

		$this->screen_id_actions = add_submenu_page(
			$this->shared->get( 'slug' ) . '-actions',
			esc_html__( 'SA - Actions', 'soccer-action' ),
			esc_html__( "Actions", "soccer-action" ),
			get_option( $this->shared->get( 'slug' ) . '_actions_menu_capability' ),
			$this->shared->get( 'slug' ) . '-actions',
			array( $this, 'me_display_menu_actions' )
		);

		$this->screen_id_help = add_submenu_page(
			$this->shared->get( 'slug' ) . '-actions',
			esc_html__( 'SA - Help', 'soccer-action' ),
			esc_html__( 'Help', 'soccer-action' ),
			'manage_options',
			$this->shared->get( 'slug' ) . '-help',
			array( $this, 'me_display_menu_help' )
		);

		$this->screen_id_pro_version = add_submenu_page(
			$this->shared->get( 'slug' ) . '-actions',
			esc_html__( 'SA - Soccer Engine', 'soccer-action' ),
			esc_html__( 'Soccer Engine', 'soccer-action' ),
			'manage_options',
			$this->shared->get( 'slug' ) . '-soccer-engine',
			array( $this, 'me_display_menu_soccer_engine' )
		);

		$this->screen_id_options = add_submenu_page(
			$this->shared->get( 'slug' ) . '-actions',
			esc_html__( 'SA - Options', 'soccer-action' ),
			esc_html__( "Options", "soccer-action" ),
			'manage_options',
			$this->shared->get( 'slug' ) . '-options',
			array( $this, 'me_display_menu_options' )
		);

	}

	/*
	 * includes the formations view
	 */
	public function me_display_menu_actions() {
		include_once( 'view/actions.php' );
	}

	/*
	 * includes the options view
	 */
	public function me_display_menu_help() {
		include_once( 'view/help.php' );
	}

	/*
	 * includes the options view
	 */
	public function me_display_menu_soccer_engine() {
		include_once( 'view/soccer-engine.php' );
	}

	/*
	 * includes the options view
	 */
	public function me_display_menu_options() {
		include_once( 'view/options.php' );
	}

	/*
	 * register options
	 */
	public function op_register_options() {

		$this->menu_options->register_options();

	}

	/*
	 * plugin activation
	 */
	static public function ac_activate() {

		self::ac_initialize_options();
		self::ac_create_database_tables();

	}

	/*
	 * initialize plugin options
	 */
	static private function ac_initialize_options() {

		//assign an instance of daextsocact_Shared
		$shared = daextsocact_Shared::get_instance();

		foreach ( $shared->get( 'options' ) as $key => $value ) {
			add_option( $key, $value );
		}

	}

	/*
	 * create the plugin database tables
	 */
	static private function ac_create_database_tables() {

		//check database version and create the database
		if ( intval( get_option( 'daextsocact_database_version' ) ) < 1 ) {

			global $wpdb;

			//Get the database character collate that will be appended at the end of each query
			$charset_collate = $wpdb->get_charset_collate();

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			//create *prefix*_action
			global $wpdb;
			$table_name = $wpdb->prefix . "daextsocact_action";
			$sql        = "CREATE TABLE $table_name (
              id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
              description VARCHAR(255) DEFAULT '' NOT NULL
            ) $charset_collate";

			dbDelta( $sql );

			//create *prefix*_field
			global $wpdb;
			$table_name = $wpdb->prefix . "daextsocact_field";
			$sql        = "CREATE TABLE $table_name (
              id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
              action_id BIGINT NOT NULL,
              element_id VARCHAR(255) DEFAULT '' NOT NULL,
              pos_x INT NOT NULL,
              pos_y INT NOT NULL,
              label_text VARCHAR(255) DEFAULT '' NOT NULL
            ) $charset_collate";

			dbDelta( $sql );

			//Update database version
			update_option( 'daextsocact_database_version', "1" );

		}

	}

	/*
	 * delete plugin options
	 */
	static public function un_delete_options() {

		//assign an instance of daextsocact_Shared
		$shared = daextsocact_Shared::get_instance();

		foreach ( $shared->get( 'options' ) as $key => $value ) {
			delete_option( $key );
		}

	}

	/*
	 * delete plugin database
	 */
	static public function un_delete_database_tables() {

		//assign an instance of the plugin info
		$shared = daextsocact_Shared::get_instance();

		global $wpdb;

		$table_name = $wpdb->prefix . $shared->get( 'slug' ) . "_action";
		$sql        = "DROP TABLE $table_name";
		$wpdb->query( $sql );

		$table_name = $wpdb->prefix . $shared->get( 'slug' ) . "_field";
		$sql        = "DROP TABLE $table_name";
		$wpdb->query( $sql );

	}

	/*
	 * Return the HTML of the elements included in the field with related inline css styles for the position.
	 */
	public function add_elements_in_field( $action_id ) {

		//get field elements from the database
		global $wpdb;
		$table_name     = $wpdb->prefix . $this->shared->get( 'slug' ) . "_field";
		$safe_sql       = $wpdb->prepare( "SELECT * FROM $table_name WHERE action_id = %d", $action_id );
		$field_elements = $wpdb->get_results( $safe_sql, ARRAY_A );

		//init $output variable to avoid php notice
		$output = "";

		//generate the output
		foreach ( $field_elements as $key => $field_element ) {

			$output .= '<div data-id="' . esc_attr( $field_element['element_id'] ) . '" class="' . esc_attr( $field_element['element_id'] ) . ' daextsocact-draggable-element ui-draggable" style="display: block; position: relative; left: ' . esc_attr( $field_element['pos_x'] ) . 'px; top: ' . esc_attr( $field_element['pos_y'] ) . 'px;">';

			//if this is a label add the input element
			if ( $field_element['element_id'] == "daextsocact-player-label" ) {

				$output .= '<input value="' . esc_attr( stripslashes( $field_element['label_text'] ) ) . '" type="text">';

			}

			$output .= '</div>';

		}

		return $output;

	}

}