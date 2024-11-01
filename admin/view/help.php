<?php

if ( ! current_user_can( 'edit_posts' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'soccer-action' ) );
}

?>

<!-- output -->

<div class="wrap">

    <h2><?php esc_html_e( 'Soccer Action - Help', 'soccer-action' ); ?></h2>

    <div id="daext-menu-wrapper">

        <p><?php esc_html_e( 'Visit the resources below to find your answers or to ask questions directly to the plugin developers.',
				'soccer-action' ); ?></p>
        <ul>
            <li><a href="https://daext.com/support/"><?php esc_html_e( 'Support Conditions', 'soccer-action' ); ?></li>
            <li><a href="https://daext.com"><?php esc_html_e( 'Developer Website', 'soccer-action' ); ?></a></li>
            <li><a href="https://daext.com/soccer-engine/"><?php esc_html_e( 'Pro Version',
						'soccer-action' ); ?></a></li>
            <li><a href="https://wordpress.org/plugins/soccer-action/"><?php esc_html_e( 'WordPress.org Plugin Page',
						'soccer-action' ); ?></a></li>
            <li>
                <a href="https://wordpress.org/support/plugin/soccer-action/"><?php esc_html_e( 'WordPress.org Support Forum',
						'soccer-action' ); ?></a></li>
        </ul>

    </div>

</div>