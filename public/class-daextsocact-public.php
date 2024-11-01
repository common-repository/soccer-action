<?php

/*
 * This class should be used to work with the public side of WordPress.
 */

class Daextsocact_Public {

	protected static $instance = null;
	private $shared = null;

	private function __construct() {

		//assign an instance of the plugin info
		$this->shared = Daextsocact_Shared::get_instance();

		//Load public css and js
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		//shortcode
		add_shortcode( 'soccer-action', array( $this, 'generate_soccer_action_html' ) );

	}

	/*
	 * create an instance of this class
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/*
	 * enqueue public-specific style sheets
	 */
	public function enqueue_styles() {

		//get the 2 dimensional array that contains all the fonts
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

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-google-fonts',
				esc_url( $selected_font['url'] ), false );

		}

		wp_enqueue_style( $this->shared->get( 'slug' ) . '-public-style',
			$this->shared->get( 'url' ) . 'public/assets/css/public.css', array(), $this->shared->get( 'ver' ) );

		//Add custom CSS for the top and bottom margins
		$custom_css = '.daextsocact-field{ margin-top: ' . intval( get_option( $this->shared->get( 'slug' ) . "_margin_top" ),
				10 ) . 'px !important;}';
		$custom_css .= '.daextsocact-field{ margin-bottom: ' . intval( get_option( $this->shared->get( 'slug' ) . "_margin_bottom" ),
				10 ) . 'px !important;}';

		//Add custom CSS for the font-family and font-weight
		$custom_css .= '.daextsocact-field .daextsocact-player-label-text{ font-family: ' . htmlspecialchars( stripslashes( $selected_font['font-family'] ),
				ENT_NOQUOTES ) . '; }';
		$custom_css .= '.daextsocact-field .daextsocact-player-label-text{ font-weight: ' . intval( $selected_font['font-weight'],
				10 ) . '; }';

		wp_add_inline_style( $this->shared->get( 'slug' ) . '-public-style', $custom_css );

	}

	/*
	 * enqueue public-specific javascipt
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->shared->get( 'slug' ) . '-public-script',
			$this->shared->get( 'url' ) . 'public/assets/js/public.js', array( 'jquery' ),
			$this->shared->get( 'ver' ) );

	}

	/*
	 * shortcode callback
	 */
	public function generate_soccer_action_html( $atts ) {

		if ( is_feed() or ( ! is_single() and ! is_page() ) ) {
			return;
		}

		//get the action id
		if ( isset( $atts['id'] ) ) {
			$action_id = intval( $atts['id'], 10 );
		} else {
			return '<p>' . esc_html__( 'Please enter the identifier of the action.', 'soccer-action' ) . '</p>';
		}

		//check if action exists
		if ( ! $this->action_exists( $action_id ) ) {
			return "<p>" . esc_html__( "This action doesn't exist. Please enter a valid action.",
					"soccer-action" ) . "</p>";
		}

		//create the output
		$output = '<div id="daextsocact-field-' . esc_attr( $action_id ) . '" class="daextsocact-field">';

		$output .= '<img src="' . esc_url( $this->shared->get( 'url' ) . '/shared/assets/img/field.png' ) . '">';

		$output .= $this->get_elements_in_field( $action_id );

		$output .= '</div>';

		return $output;

	}

	/*
	 * return the players in the field
	 */
	private function get_elements_in_field( $action_id ) {

		//get field elements from the database
		global $wpdb;
		$table_name     = $wpdb->prefix . $this->shared->get( 'slug' ) . "_field";
		$safe_sql       = $wpdb->prepare( "SELECT * FROM $table_name WHERE action_id = %d", $action_id );
		$field_elements = $wpdb->get_results( $safe_sql, ARRAY_A );

		//init the $output variable to avoid php notice
		$output = "";

		//generate the output
		foreach ( $field_elements as $key => $field_element ) {

			//convert coordinates to percentage
			$pos_x = $field_element['pos_x'] / 1030 * 100;
			$pos_y = $field_element['pos_y'] / 429 * 100;

			$output .= '<div data-id="' . esc_attr( $field_element['element_id'] ) . '" ' . $this->output_default_element_dimensions( $field_element['element_id'] ) . ' class="' . esc_attr( $field_element['element_id'] ) . ' daextsocact-field-element" style="left: ' . esc_attr( $pos_x ) . '%; top: ' . esc_attr( $pos_y ) . '%;">';

			//if this is a label add the input element
			if ( $field_element['element_id'] == "daextsocact-player-label" ) {

				$output .= '<div class="daextsocact-player-label-text" >' . esc_html( stripslashes( $field_element['label_text'] ) ) . '</div>';

			}

			$output .= '</div>';

		}

		return $output;

	}

	/**
	 * Check if the action exists.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	private function action_exists( $id ) {

		global $wpdb;
		$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_action";
		$safe_sql   = $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id );
		$actions    = $wpdb->get_results( $safe_sql, ARRAY_A );

		if ( count( $actions ) > 0 ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Generates the HTML of the attributes that keep the dimensions of the element.
	 *
	 * @param $element_id
	 *
	 * @return string
	 */
	private function output_default_element_dimensions( $element_id ) {

		switch ( $element_id ) {

			//blue-player-white-skin -------------------------------------------

			case "daextsocact-blue-white-stand-frontal":

				$default_height = 57;
				$default_width  = 22;
				break;

			case "daextsocact-blue-white-walk-right":

				$default_height = 56;
				$default_width  = 19;

				break;

			case "daextsocact-blue-white-walk-left":

				$default_height = 56;
				$default_width  = 22;

				break;

			case "daextsocact-blue-white-run-right":

				$default_height = 56;
				$default_width  = 40;

				break;

			case "daextsocact-blue-white-run-left":

				$default_height = 52;
				$default_width  = 44;

				break;

			case "daextsocact-blue-white-sprint-right":

				$default_height = 51;
				$default_width  = 44;

				break;

			case "daextsocact-blue-white-sprint-left":

				$default_height = 49;
				$default_width  = 44;

				break;

			case "daextsocact-blue-white-kick-2-right":

				$default_height = 54;
				$default_width  = 47;

				break;

			case "daextsocact-blue-white-kick-2-left":

				$default_height = 57;
				$default_width  = 47;

				break;

			case "daextsocact-blue-white-kick-1-right":

				$default_height = 60;
				$default_width  = 61;

				break;

			case "daextsocact-blue-white-kick-1-left":

				$default_height = 49;
				$default_width  = 62;

				break;

			//blue-player-black-skin -------------------------------------------

			case "daextsocact-blue-black-stand-frontal":

				$default_height = 57;
				$default_width  = 22;

				break;

			case "daextsocact-blue-black-walk-right":

				$default_height = 56;
				$default_width  = 19;

				break;

			case "daextsocact-blue-black-walk-left":

				$default_height = 56;
				$default_width  = 22;

				break;

			case "daextsocact-blue-black-run-right":

				$default_height = 56;
				$default_width  = 40;

				break;

			case "daextsocact-blue-black-run-left":

				$default_height = 52;
				$default_width  = 44;

				break;

			case "daextsocact-blue-black-sprint-right":

				$default_height = 51;
				$default_width  = 44;

				break;

			case "daextsocact-blue-black-sprint-left":

				$default_height = 49;
				$default_width  = 44;

				break;

			case "daextsocact-blue-black-kick-2-right":

				$default_height = 54;
				$default_width  = 47;

				break;

			case "daextsocact-blue-black-kick-2-left":

				$default_height = 57;
				$default_width  = 47;

				break;

			case "daextsocact-blue-black-kick-1-right":

				$default_height = 60;
				$default_width  = 61;

				break;

			case "daextsocact-blue-black-kick-1-left":

				$default_height = 49;
				$default_width  = 62;

				break;

			//red-player-white-skin --------------------------------------------

			case "daextsocact-red-white-stand-frontal":

				$default_height = 57;
				$default_width  = 22;

				break;

			case "daextsocact-red-white-walk-right":

				$default_height = 56;
				$default_width  = 19;

				break;

			case "daextsocact-red-white-walk-left":

				$default_height = 56;
				$default_width  = 22;

				break;

			case "daextsocact-red-white-run-right":

				$default_height = 56;
				$default_width  = 40;

				break;

			case "daextsocact-red-white-run-left":

				$default_height = 52;
				$default_width  = 44;

				break;

			case "daextsocact-red-white-sprint-right":

				$default_height = 51;
				$default_width  = 44;

				break;

			case "daextsocact-red-white-sprint-left":

				$default_height = 49;
				$default_width  = 44;

				break;

			case "daextsocact-red-white-kick-2-right":

				$default_height = 54;
				$default_width  = 47;

				break;

			case "daextsocact-red-white-kick-2-left":

				$default_height = 57;
				$default_width  = 47;

				break;

			case "daextsocact-red-white-kick-1-right":

				$default_height = 60;
				$default_width  = 61;

				break;

			case "daextsocact-red-white-kick-1-left":

				$default_height = 49;
				$default_width  = 62;

				break;

			//red-player-black-skin --------------------------------------------

			case "daextsocact-red-black-stand-frontal":

				$default_height = 57;
				$default_width  = 22;

				break;

			case "daextsocact-red-black-walk-right":

				$default_height = 56;
				$default_width  = 19;

				break;

			case "daextsocact-red-black-walk-left":

				$default_height = 56;
				$default_width  = 22;

				break;

			case "daextsocact-red-black-run-right":

				$default_height = 56;
				$default_width  = 40;

				break;

			case "daextsocact-red-black-run-left":

				$default_height = 52;
				$default_width  = 44;

				break;

			case "daextsocact-red-black-sprint-right":

				$default_height = 51;
				$default_width  = 44;

				break;

			case "daextsocact-red-black-sprint-left":

				$default_height = 49;
				$default_width  = 44;

				break;

			case "daextsocact-red-black-kick-2-right":

				$default_height = 54;
				$default_width  = 47;

				break;

			case "daextsocact-red-black-kick-2-left":

				$default_height = 57;
				$default_width  = 47;

				break;

			case "daextsocact-red-black-kick-1-right":

				$default_height = 60;
				$default_width  = 61;

				break;

			case "daextsocact-red-black-kick-1-left":

				$default_height = 49;
				$default_width  = 62;

				break;

			//goal keeper white skin -------------------------------------------

			case "daextsocact-white-keeper-1-right":

				$default_height = 54;
				$default_width  = 19;

				break;

			case "daextsocact-white-keeper-1-left":

				$default_height = 54;
				$default_width  = 19;

				break;

			case "daextsocact-white-keeper-2-right":

				$default_height = 83;
				$default_width  = 20;

				break;

			case "daextsocact-white-keeper-2-left":

				$default_height = 77;
				$default_width  = 19;

				break;

			//goal keeper black skin -------------------------------------------

			case "daextsocact-black-keeper-1-right":

				$default_height = 54;
				$default_width  = 19;

				break;

			case "daextsocact-black-keeper-1-left":

				$default_height = 54;
				$default_width  = 19;

				break;

			case "daextsocact-black-keeper-2-right":

				$default_height = 83;
				$default_width  = 20;

				break;

			case "daextsocact-black-keeper-2-left":

				$default_height = 77;
				$default_width  = 19;

				break;

			//ball -------------------------------------------------------------

			case "daextsocact-ball-no-shadow":

				$default_height = 25;
				$default_width  = 14;

				break;

			case "daextsocact-ball-shadow":

				$default_height = 25;
				$default_width  = 14;

				break;

			case "daextsocact-player-label":

				$default_height = 22;
				$default_width  = 123;

				break;

			//ball-trails and arrows -------------------------------------------

			default:

				$default_height = 62;
				$default_width  = 62;

				break;

		}

		$output = 'data-default-width="' . esc_attr( $default_width ) . '" data-default-height="' . esc_attr( $default_height ) . '"';

		return $output;

	}

}