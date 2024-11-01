<?php

/**
 * This class adds the options with the related callbacks and validations.
 */
class Daextsocact_Menu_Options {

	private $shared = null;

	public function __construct( $shared ) {

		//assign an instance of the plugin info
		$this->shared = $shared;

	}

	public function register_options() {

		//section general
		add_settings_section(
			'daextsocact_general_settings_section',
			null,
			null,
			$this->shared->get( 'slug' ) . '_general_options'
		);

		add_settings_field(
			'daextsocact_margin_top',
			esc_html__( "Margin Top", "soccer-action" ),
			array( $this, 'op_margin_top' ),
			'daextsocact_general_options',
			'daextsocact_general_settings_section'
		);

		register_setting(
			'daextsocact_general_options',
			'daextsocact_margin_top',
			array( $this, 'op_margin_top_validation' )
		);

		add_settings_field(
			$this->shared->get( 'slug' ) . '_margin_bottom',
			esc_html__( "Margin Bottom", "soccer-action" ),
			array( $this, 'op_margin_bottom' ),
			$this->shared->get( 'slug' ) . '_general_options',
			'daextsocact_general_settings_section'
		);

		register_setting(
			'daextsocact_general_options',
			'daextsocact_margin_bottom',
			array( $this, 'op_margin_bottom_validation' )
		);

		add_settings_field(
			$this->shared->get( 'slug' ) . '_label_font',
			esc_html__( "Label Font", "soccer-action" ),
			array( $this, 'op_label_font' ),
			$this->shared->get( 'slug' ) . '_general_options',
			'daextsocact_general_settings_section'
		);

		register_setting(
			'daextsocact_general_options',
			'daextsocact_label_font',
			array( $this, 'op_label_font_validation' )
		);

		add_settings_field(
			$this->shared->get( 'slug' ) . '_actions_menu_capability',
			esc_html__( "Actions Menu Capability", "soccer-action" ),
			array( $this, 'op_actions_menu_capability' ),
			$this->shared->get( 'slug' ) . '_general_options',
			'daextsocact_general_settings_section'
		);

		register_setting(
			'daextsocact_general_options',
			'daextsocact_actions_menu_capability',
			array( $this, 'op_actions_menu_capability_validation' )
		);

	}

	//options callbacks --------------------------------------------------------
	public function op_margin_top() {

		$html = '<input type="text" id="' . esc_attr( $this->shared->get( 'slug' ) ) . '_margin_top" name="' . esc_attr( $this->shared->get( 'slug' ) ) . '_margin_top" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_margin_top' ) ) . '" maxlength="3" />';
		$html .= '<div class="help-icon" title="' . esc_attr__( 'The top margin of the field.',
				'soccer-action' ) . '"></div>';
		echo $html;

	}

	function op_margin_top_validation( $input ) {

		return intval( $input, 10 );

	}

	public function op_margin_bottom() {

		$html = '<input type="text" id="' . esc_attr( $this->shared->get( 'slug' ) ) . '_margin_bottom" name="' . esc_attr( $this->shared->get( 'slug' ) ) . '_margin_bottom" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_margin_bottom' ) ) . '" maxlength="3" />';
		$html .= '<div class="help-icon" title="' . esc_attr__( 'The bottom margin of the field.',
				'soccer-action' ) . '"></div>';
		echo $html;

	}

	function op_margin_bottom_validation( $input ) {

		return intval( $input, 10 );

	}

	public function op_label_font() {

		$html = '<select id="daextsocact_label_font" name="daextsocact_label_font" class="daext-display-none">';

		$fonts = $this->shared->get( 'fonts' );
		foreach ( $fonts as $key => $font ) {
			$html .= '<option value="' . esc_attr( $key ) . '" ' . selected( get_option( $this->shared->get( 'slug' ) . "_label_font" ),
					$key, false ) . '>' . esc_html( $key ) . '</option>';
		}

		$html .= '</select>';
		$html .= '<div class="help-icon" title="' . esc_attr__( 'The font family of the labels.',
				'soccer-action' ) . '"></div>';

		echo $html;

	}

	function op_label_font_validation( $input ) {

		$input = sanitize_text_field( $input );

		$fonts = $this->shared->get( 'fonts' );

		if ( ! array_key_exists( $input, $fonts ) ) {
			add_settings_error( 'daextsocact_label_font', 'daextsocact_label_font',
				esc_html__( 'Please enter a valid value in the "Font Family" option.', 'soccer-action' ) );
			$output = 'Arial';
		} else {
			$output = $input;
		}

		return $output;

	}

	public function op_actions_menu_capability() {

		$html = '<input autocomplete="off" type="text" id="daextsocact_actions_menu_capability" name="daextsocact_actions_menu_capability" class="regular-text" value="' . esc_attr( get_option( "daextsocact_actions_menu_capability" ) ) . '" />';
		$html .= '<div class="help-icon" title="' . esc_attr__( 'The capability required to get access on the "Actions" menu.',
				'soccer-action' ) . '"></div>';

		echo $html;

	}

	function op_actions_menu_capability_validation( $input ) {

		return sanitize_key( $input );

	}

}