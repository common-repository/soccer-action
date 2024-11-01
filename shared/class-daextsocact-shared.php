<?php

/*
 * This class should be used to stores value shared by the admin and public side
 * of WordPress.
 */

class Daextsocact_Shared {

	protected static $instance = null;
	private $data = array();

	private function __construct() {

		$this->data['slug'] = 'daextsocact';
		$this->data['ver']  = '1.06';
		$this->data['dir']  = substr( plugin_dir_path( __FILE__ ), 0, - 7 );
		$this->data['url']  = substr( plugin_dir_url( __FILE__ ), 0, - 7 );

		$this->data['fonts'] = $this->get_fonts();

		//Here are stored the plugin option with the related default values
		$this->data['options'] = [

			//database version -----------------------------------------------------
			$this->get( 'slug' ) . "_database_version"        => "0",

			//general --------------------------------------------------------------
			$this->get( 'slug' ) . "_margin_top"              => "20",
			$this->get( 'slug' ) . "_margin_bottom"           => "20",
			$this->get( 'slug' ) . "_label_font"              => "Arial",
			$this->get( 'slug' ) . "_actions_menu_capability" => "edit_posts"

		];

	}

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	//retrieve data
	public function get( $index ) {
		return $this->data[ $index ];
	}

	//return the array with the data of the selectable fonts
	private function get_fonts() {

		return array(

			//web safe fonts -------------------------------------------------------------------------------------------
			'Arial'                => array(
				'font-family' => "Arial,Helvetica,Verdana,sans-serif",
				'font-weight' => '400',
				'url'         => ""
			),
			'Georgia'              => array(
				'font-family' => "\"Georgia\",\"Times New Roman\",Times,serif",
				'font-weight' => '400',
				'url'         => ""
			),
			'Verdana'              => array(
				'font-family' => "Verdana,Arial,Helvetica,sans-serif",
				'font-weight' => '400',
				'url'         => ""
			),
			'Helvetica'            => array(
				'font-family' => "Helvetica,\"HelveticaNeue\",\"Helvetica Neue\",Arial,Verdana,sans-serif",
				'font-weight' => '400',
				'url'         => ""
			),
			'Helvetica Neue'       => array(
				'font-family' => "\"HelveticaNeue\",\"Helvetica Neue\",Helvetica,Arial,Verdana,sans-serif",
				'font-weight' => '400',
				'url'         => ""
			),
			'Lucida'               => array(
				'font-family' => "\"Lucida Sans\",\"Lucida Grande\",\"Lucida Sans Unicode\",Helvetica,Arial,sans-serif",
				'font-weight' => '400',
				'url'         => ""
			),

			//google fonts ---------------------------------------------------------------------------------------------
			'Arimo (Google Fonts)' => array(
				'font-family' => "\"Arimo\",\"HelveticaNeue\",\"Helvetica Neue\",Helvetica,Arial,sans-serif",
				'font-weight' => '400',
				'url'         => 'https://fonts.googleapis.com/css2?family=Arimo&display=swap'
			),

			'Maven Pro (Google Fonts)' => array(
				'font-family' => "\"Maven Pro\",\"HelveticaNeue\",\"Helvetica Neue\",Helvetica,Arial,sans-serif",
				'font-weight' => '400',
				'url'         => 'https://fonts.googleapis.com/css2?family=Maven+Pro&display=swap'
			),

			'Open Sans (Google Fonts)' => array(
				'font-family' => "\"Open Sans\",\"HelveticaNeue\",\"Helvetica Neue\",Helvetica,Arial,sans-serif",
				'font-weight' => '400',
				'url'         => 'https://fonts.googleapis.com/css2?family=Open+Sans&display=swap'
			),

			'Lato (Google Fonts)' => array(
				'font-family' => "\"Lato\",\"HelveticaNeue\",\"Helvetica Neue\",Helvetica,Arial,sans-serif",
				'font-weight' => '400',
				'url'         => 'https://fonts.googleapis.com/css2?family=Lato&display=swap'
			),

			'Lora (Google Fonts)' => array(
				'font-family' => "\"Lora\",\"HelveticaNeue\",\"Helvetica Neue\",Helvetica,Arial,sans-serif",
				'font-weight' => '400',
				'url'         => 'https://fonts.googleapis.com/css2?family=Lora&display=swap'
			)

		);

	}

	/**
	 * Sanitized the data included in the $field_data_a array.
	 *
	 * @param $field_data_a
	 *
	 * @return bool
	 */
	public function sanitize_field_data( $field_data_a ) {

		$sanitized_field_data_a = [];

		if ( ! is_array( $field_data_a ) ) {
			return false;
		}

		foreach ( $field_data_a as $key => $field_data ) {

			$sanitized_field_data_a[] = [
				'element_id' => sanitize_key( $field_data['element_id'] ),
				'pos_x'      => intval( $field_data['pos_x'], 10 ),
				'pos_y'      => intval( $field_data['pos_y'], 10 ),
				'label_text' => sanitize_text_field( $field_data['label_text'] )
			];

		}

		return $sanitized_field_data_a;

	}

}