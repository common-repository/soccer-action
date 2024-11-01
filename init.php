<?php
/*
Plugin Name: Soccer Action
Description: Generates soccer actions in your WordPress posts.
Version: 1.06
Author: DAEXT
Author URI: https://daext.com
*/

//Prevent direct access to this file
if ( ! defined( 'WPINC' ) ) {
	die();
}

//Class shared across public and admin
require_once( plugin_dir_path( __FILE__ ) . 'shared/class-daextsocact-shared.php' );

//Public
require_once( plugin_dir_path( __FILE__ ) . 'public/class-daextsocact-public.php' );
add_action( 'plugins_loaded', array( 'Daextsocact_Public', 'get_instance' ) );

//Admin
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-daextsocact-admin.php' );

	// If this is not an AJAX request, create a new singleton instance of the admin class.
	if(! defined( 'DOING_AJAX' ) || ! DOING_AJAX ){
		add_action( 'plugins_loaded', array( 'Daextsocact_Admin', 'get_instance' ) );
	}

	// Activate the plugin using only the class static methods.
	register_activation_hook( __FILE__, array( 'Daextsocact_Admin', 'ac_activate' ) );

}

//Ajax
if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

	//Admin
	require_once( plugin_dir_path( __FILE__ ) . 'class-daextsocact-ajax.php' );
	add_action( 'plugins_loaded', array( 'Daextsocact_Ajax', 'get_instance' ) );

}

//Customize the action links in the "Plugins" menu
function daextsocact_customize_action_links( $actions ) {
	$actions[] = '<a href="https://daext.com/soccer-engine/">' . esc_html__('Buy Soccer Engine', 'soccer-action') . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'daextsocact_customize_action_links' );