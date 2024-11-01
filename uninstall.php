<?php

//Exit if this file is called outside wordpress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

require_once( plugin_dir_path( __FILE__ ) . 'shared/class-daextsocact-shared.php' );
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-daextsocact-admin.php' );

//delete options
daextsocact_Admin::un_delete_options();

//delete database tables
daextsocact_Admin::un_delete_database_tables();