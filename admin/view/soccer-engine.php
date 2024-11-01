<?php

if ( ! current_user_can( 'edit_posts' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'soccer-action' ) );
}

?>

<!-- output -->

<div class="wrap">

    <h2><?php esc_html_e( 'Soccer Action - Soccer Engine', 'soccer-action' ); ?></h2>

    <div id="daext-menu-wrapper">

        <p><?php echo esc_html__( 'For professional users, we distribute ',
					'soccer-action' ) . '<a href="https://daext.com/soccer-engine/">' . esc_html__( 'Soccer Engine',
					'soccer-action' ) . '</a> ' . esc_html__( ', a complete solution to store and display soccer data on a WordPress website.',
					'soccer-action' ) . '</p>'; ?>
        <p><?php esc_html_e( 'This plugin can be used for example by:', 'soccer-action' ); ?></p>
        <ul>
            <li><?php esc_html_e( 'Clubs that want to register and display the results of their senior and junior teams.',
					'soccer-action' ); ?></li>
            <li><?php esc_html_e( 'Clubs that want to create an advanced registry of players, staff members, match results, competitions and formations.',
					'soccer-action' ); ?></li>
            <li><?php esc_html_e( 'Bloggers that wants to review and analyze matches with timelines and commentaries.',
					'soccer-action' ); ?></li>
            <li><?php esc_html_e( 'The organizers of local competitions that want to list fixtures, results, and awards of the competition.',
					'soccer-action' ); ?></li>
            <li><?php esc_html_e( 'Transfer market news and rumors related websites interested in creating a registry of player transfers, team contracts, player agencies, and agency contracts.',
					'soccer-action' ); ?></li>
            <li><?php esc_html_e( 'News networks that want to improve the soccer section with results, standings table, and fixtures.',
					'soccer-action' ); ?></li>
        </ul>
        <h2><?php esc_html_e( 'Additional Benefits for the customers of Soccer Engine', 'soccer-action' ); ?></h2>
        <ul>
            <li><?php esc_html_e( '24 hours support provided 7 days a week', 'soccer-action' ); ?></li>
            <li><?php echo esc_html__( '30 day money back guarantee (more information is available on the',
						'soccer-action' ) . ' <a href="https://daext.com/refund-policy/">' . esc_html__( 'Refund Policy',
						'soccer-action' ) . '</a> ' . esc_html__( 'page', 'soccer-action' ) . ')'; ?></li>
        </ul>
        <h2><?php esc_html_e( 'Get Started', 'soccer-action' ); ?></h2>
        <p><?php echo esc_html__( 'Download ',
					'soccer-action' ) . ' <a href="https://daext.com/soccer-engine/">' . esc_html__( 'Soccer Engine',
					'soccer-action' ) . '</a> ' . esc_html__( 'now by selecting one of the available plans.',
					'soccer-action' ); ?></p>
    </div>

</div>

