<?php

//menu action view

if ( ! current_user_can( get_option( $this->shared->get( 'slug' ) . '_actions_menu_capability' ) ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'soccer-action' ) );
}

//process data ---------------------------------------------------------

//Preliminary operations -----------------------------------------------------------------------------------------------
global $wpdb;

//Sanitization ---------------------------------------------------------------------------------------------

//Actions
$data['edit_id']   = isset( $_GET['edit_id'] ) ? intval( $_GET['edit_id'], 10 ) : null;
$data['delete_id'] = isset( $_POST['delete_id'] ) ? intval( $_POST['delete_id'], 10 ) : null;
$data['clone_id']  = isset( $_POST['clone_id'] ) ? intval( $_POST['clone_id'], 10 ) : null;

//Filter and search data
$data['s'] = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

//delete action
if ( ! is_null( $data['delete_id'] ) ) {

	//delete the action
	$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_action";
	$safe_sql   = $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d ", $data['delete_id'] );
	$wpdb->query( $safe_sql );

	//delete all the field elements associated with this action
	$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_field";
	$safe_sql   = $wpdb->prepare( "DELETE FROM $table_name WHERE action_id = %d ", $data['delete_id'] );
	$wpdb->query( $safe_sql );

}

//clone action and elements in the field
if ( ! is_null( $data['clone_id'] ) ) {

	//clone action
	$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_action";
	$wpdb->query( "CREATE TEMPORARY TABLE tmptable_1 SELECT * FROM $table_name WHERE id = " . $data['clone_id'] );
	$wpdb->query( "UPDATE tmptable_1 SET id = NULL" );
	$wpdb->query( "INSERT INTO $table_name SELECT * FROM tmptable_1" );
	$last_inserted_id = $wpdb->insert_id;
	$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS tmptable_1" );

	//clone elements in the field
	$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_field";
	$wpdb->query( "CREATE TEMPORARY TABLE tmptable_1 SELECT * FROM $table_name WHERE action_id = " . $data['clone_id'] );
	$wpdb->query( "UPDATE tmptable_1 SET id = NULL, action_id = $last_inserted_id" );
	$wpdb->query( "INSERT INTO $table_name SELECT * FROM tmptable_1" );
	$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS tmptable_1" );

}


?>

<!-- output -->

<div class="wrap">

    <div id="daext-header-wrapper" class="daext-clearfix">

        <h2><?php esc_html_e( "Soccer Action - Actions", "soccer-action" ); ?></h2>

        <form action="admin.php" method="get" id="daext-search-form">

            <input type="hidden" name="page" value="daextsocact-actions">

            <p><?php esc_html_e( 'Perform your Search', 'soccer-formation-ve' ); ?></p>

			<?php
			if ( ! is_null( $data['s'] ) and mb_strlen( trim( $data['s'] ) ) > 0 ) {
				$search_string = $data['s'];
			} else {
				$search_string = '';
			}

			?>

            <input type="text" name="s"
                   value="<?php echo esc_attr( stripslashes( $search_string ) ); ?>" autocomplete="off" maxlength="255">
            <input type="submit" value="">

        </form>

    </div>

    <div id="daext-menu-wrapper">

        <!-- list of actions with pagination --------------------------- -->

		<?php

		//create the query part used to filter the results when a search is performed
		if ( ! is_null( $data['s'] ) and mb_strlen( trim( $data['s'] ) ) > 0 ) {

			//create the query part used to filter the results when a search is performed
			$filter = $wpdb->prepare( 'WHERE (description LIKE %s)',
				'%' . $data['s'] . '%' );

		} else {
			$filter = '';
		}

		//retrieve the total number of actions
		$table_name  = $wpdb->prefix . $this->shared->get( 'slug' ) . "_action";
		$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $filter" );

		//Initialize the pagination class
		require_once( $this->shared->get( 'dir' ) . '/admin/inc/class-daextsocact-pagination.php' );
		$pag = new daextsocact_Pagination();
		$pag->set_total_items( $total_items );//Set the total number of items
		$pag->set_record_per_page( 10 ); //Set records per page
		$pag->set_target_page( "admin.php?page=" . $this->shared->get( 'slug' ) . "-actions" );//Set target page
		$pag->set_current_page();//set the current page number from $_GET

		?>

        <!-- Query the database -->
		<?php
		$query_limit = $pag->query_limit();
		$results     = $wpdb->get_results( "SELECT * FROM $table_name $filter ORDER BY id DESC $query_limit ",
			ARRAY_A ); ?>

		<?php if ( count( $results ) > 0 ) : ?>

            <div class="daext-items-container">

                <!-- list of tables -->
                <table class="daext-items">
                    <thead>
                    <tr>
                        <th><?php esc_html_e( "ID", "soccer-action" ); ?></th>
                        <th><?php esc_html_e( "Description", "soccer-action" ); ?></th>
                        <th><?php esc_html_e( "Shortcode", "soccer-action" ); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

					<?php foreach ( $results as $result ) : ?>
                        <tr>
                            <td><?php echo esc_html( $result['id'] ); ?></td>
                            <td><?php echo esc_html( stripslashes( $result['description'] ) ); ?></td>
                            <td>[soccer-action id="<?php echo esc_html( $result['id'] ); ?>"]</td>
                            <td class="icons-container">
                                <form method="POST"
                                      action="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-actions">
                                    <input type="hidden" name="clone_id"
                                           value="<?php echo intval( $result['id'], 10 ); ?>">
                                    <input class="menu-icon clone help-icon" type="submit" value="">
                                </form>
                                <a class="menu-icon edit"
                                   href="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-actions&edit_id=<?php echo intval( $result['id'],
									   10 ); ?>"></a>
                                <form id="form-delete-<?php echo intval( $result['id'], 10 ); ?>" method="POST"
                                      action="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-actions">
                                    <input type="hidden" value="<?php echo intval( $result['id'], 10 ); ?>"
                                           name="delete_id">
                                    <input class="menu-icon delete" type="submit" value="">
                                </form>
                            </td>
                        </tr>
					<?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- Display the pagination -->
			<?php if ( $pag->total_items > 0 ) : ?>

                <div class="daext-tablenav daext-clearfix">
                    <div class="daext-tablenav-pages">
                        <span class="daext-displaying-num"><?php echo esc_html( $pag->total_items ); ?>&nbsp<?php esc_html_e( 'items',
								'soccer-action' ); ?></span>
						<?php $pag->show(); ?>
                    </div>
                </div>

			<?php endif; ?>

		<?php else : ?>

			<?php

			if ( mb_strlen( trim( $filter ) ) > 0 ) {
				echo '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__( 'There are no results that match your filter.',
						'soccer-action' ) . '</p></div>';
			}

			?>

		<?php endif; ?>

        <!-- form ------------------------------------------------------ -->

        <table class="form-table">

			<?php if ( ! is_null( $data['edit_id'] ) ) : ?>

                <tr valign="top">
                    <th scope="row"><h3><?php esc_html_e( "Edit Action",
								"soccer-action" ); ?>&nbsp<?php echo esc_html( $data['edit_id'] ); ?></h3></th>
                </tr>

				<?php

				//get the table data
				$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . "_action";
				$safe_sql   = $wpdb->prepare( "SELECT id, description FROM $table_name WHERE id = %d ",
					$data['edit_id'] );
				$action_obj = $wpdb->get_row( $safe_sql );

				?>

                <!-- Ajax Nonce -->
                <input id="daextsocact-edit-id" type="hidden" value="<?php echo esc_attr( $action_obj->id ); ?>">

                <!-- Description -->
                <tr valign="top">
                    <th scope="row"><label for="description"><?php esc_html_e( "Description",
								"soccer-action" ); ?></label></th>
                    <td><input value="<?php echo esc_attr( stripslashes( $action_obj->description ) ); ?>" type="text"
                               id="daextsocact-description" maxlength="255" class="regular-text" name="description"/>
                    </td>
                </tr>

			<?php else : ?>

                <tr valign="top">
                    <th scope="row"><h3><?php esc_html_e( "Create Action", "soccer-action" ); ?></h3></th>
                </tr>

                <!-- Description -->
                <tr valign="top">
                    <th scope="row"><label for="description"><?php esc_html_e( "Description",
								"soccer-action" ); ?></label></th>
                    <td><input type="text" id="daextsocact-description" maxlength="255" class="regular-text"
                               name="description"/></td>
                </tr>

			<?php endif; ?>

            <!-- Layout -->
            <tr valign="top">
                <th scope="row"><label for="layout"><?php esc_html_e( "Assets", "soccer-action" ); ?></label></th>
                <td>
                    <select id="daextsocact-select-assets">

                        <option value="daextsocact-blue-player-white-skin"
                                selected="selected"><?php esc_html_e( "Blue Player White Skin",
								"soccer-action" ); ?></option>
                        <option value="daextsocact-blue-player-black-skin"><?php esc_html_e( "Blue Player Black Skin",
								"soccer-action" ); ?></option>
                        <option value="daextsocact-red-player-white-skin"><?php esc_html_e( "Red Player White Skin",
								"soccer-action" ); ?></option>
                        <option value="daextsocact-red-player-black-skin"><?php esc_html_e( "Red Player Black Skin",
								"soccer-action" ); ?></option>
                        <option value="daextsocact-goal-keeper-white-skin"><?php esc_html_e( "Goal Keeper White Skin",
								"soccer-action" ); ?></option>
                        <option value="daextsocact-goal-keeper-black-skin"><?php esc_html_e( "Goal Keeper Black Skin",
								"soccer-action" ); ?></option>
                        <option value="daextsocact-ball"><?php esc_html_e( "Ball", "soccer-action" ); ?></option>
                        <option value="daextsocact-arrow"><?php esc_html_e( "Arrow", "soccer-action" ); ?></option>
                        <option value="daextsocact-label"><?php esc_html_e( "Label", "soccer-action" ); ?></option>

                    </select>
                </td>
            </tr>

            <!-- Elements -->
            <tr valign="top">
                <th scope="row"><label for="description"><?php esc_html_e( "Elements", "soccer-action" ); ?></label>
                </th>
                <td>

                    <div id="daextsocact-elements" class="daext-clearfix">

                        <!-- blue-player-white-skin -->
                        <div class="daextsocact-blue-white-stand-frontal daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-stand-frontal"></div>
                        <div class="daextsocact-blue-white-walk-right daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-walk-right"></div>
                        <div class="daextsocact-blue-white-walk-left daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-walk-left"></div>
                        <div class="daextsocact-blue-white-run-right daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-run-right"></div>
                        <div class="daextsocact-blue-white-run-left daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-run-left"></div>
                        <div class="daextsocact-blue-white-sprint-right daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-sprint-right"></div>
                        <div class="daextsocact-blue-white-sprint-left daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-sprint-left"></div>
                        <div class="daextsocact-blue-white-kick-2-right daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-kick-2-right"></div>
                        <div class="daextsocact-blue-white-kick-2-left daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-kick-2-left"></div>
                        <div class="daextsocact-blue-white-kick-1-right daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-kick-1-right"></div>
                        <div class="daextsocact-blue-white-kick-1-left daextsocact-blue-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-white-kick-1-left"></div>

                        <!-- daextsocact-blue-player-black-skin -->
                        <div class="daextsocact-blue-black-stand-frontal daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-stand-frontal"></div>
                        <div class="daextsocact-blue-black-walk-right daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-walk-right"></div>
                        <div class="daextsocact-blue-black-walk-left daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-walk-left"></div>
                        <div class="daextsocact-blue-black-run-right daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-run-right"></div>
                        <div class="daextsocact-blue-black-run-left daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-run-left"></div>
                        <div class="daextsocact-blue-black-sprint-right daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-sprint-right"></div>
                        <div class="daextsocact-blue-black-sprint-left daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-sprint-left"></div>
                        <div class="daextsocact-blue-black-kick-2-right daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-kick-2-right"></div>
                        <div class="daextsocact-blue-black-kick-2-left daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-kick-2-left"></div>
                        <div class="daextsocact-blue-black-kick-1-right daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-kick-1-right"></div>
                        <div class="daextsocact-blue-black-kick-1-left daextsocact-blue-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-blue-black-kick-1-left"></div>

                        <!-- daextsocact-red-player-white-skin -->
                        <div class="daextsocact-red-white-stand-frontal daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-stand-frontal"></div>
                        <div class="daextsocact-red-white-walk-right daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-walk-right"></div>
                        <div class="daextsocact-red-white-walk-left daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-walk-left"></div>
                        <div class="daextsocact-red-white-run-right daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-run-right"></div>
                        <div class="daextsocact-red-white-run-left daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-run-left"></div>
                        <div class="daextsocact-red-white-sprint-right daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-sprint-right"></div>
                        <div class="daextsocact-red-white-sprint-left daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-sprint-left"></div>
                        <div class="daextsocact-red-white-kick-2-right daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-kick-2-right"></div>
                        <div class="daextsocact-red-white-kick-2-left daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-kick-2-left"></div>
                        <div class="daextsocact-red-white-kick-1-right daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-kick-1-right"></div>
                        <div class="daextsocact-red-white-kick-1-left daextsocact-red-player-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-white-kick-1-left"></div>

                        <!-- daextsocact-red-player-black-skin -->
                        <div class="daextsocact-red-black-stand-frontal daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-stand-frontal"></div>
                        <div class="daextsocact-red-black-walk-right daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-walk-right"></div>
                        <div class="daextsocact-red-black-walk-left daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-walk-left"></div>
                        <div class="daextsocact-red-black-run-right daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-run-right"></div>
                        <div class="daextsocact-red-black-run-left daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-run-left"></div>
                        <div class="daextsocact-red-black-sprint-right daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-sprint-right"></div>
                        <div class="daextsocact-red-black-sprint-left daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-sprint-left"></div>
                        <div class="daextsocact-red-black-kick-2-right daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-kick-2-right"></div>
                        <div class="daextsocact-red-black-kick-2-left daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-kick-2-left"></div>
                        <div class="daextsocact-red-black-kick-1-right daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-kick-1-right"></div>
                        <div class="daextsocact-red-black-kick-1-left daextsocact-red-player-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-red-black-kick-1-left"></div>

                        <!-- daextsocact-goal-keeper-white-skin -->
                        <div class="daextsocact-white-keeper-1-right daextsocact-goal-keeper-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-white-keeper-1-right"></div>
                        <div class="daextsocact-white-keeper-1-left daextsocact-goal-keeper-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-white-keeper-1-left"></div>
                        <div class="daextsocact-white-keeper-2-right daextsocact-goal-keeper-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-white-keeper-2-right"></div>
                        <div class="daextsocact-white-keeper-2-left daextsocact-goal-keeper-white-skin daextsocact-draggable-element"
                             data-id="daextsocact-white-keeper-2-left"></div>

                        <!-- daextsocact-goal-keeper-black-skin -->
                        <div class="daextsocact-black-keeper-1-right daextsocact-goal-keeper-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-black-keeper-1-right"></div>
                        <div class="daextsocact-black-keeper-1-left daextsocact-goal-keeper-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-black-keeper-1-left"></div>
                        <div class="daextsocact-black-keeper-2-right daextsocact-goal-keeper-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-black-keeper-2-right"></div>
                        <div class="daextsocact-black-keeper-2-left daextsocact-goal-keeper-black-skin daextsocact-draggable-element"
                             data-id="daextsocact-black-keeper-2-left"></div>

                        <!-- ball -->
                        <div class="daextsocact-ball-no-shadow daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-no-shadow"></div>
                        <div class="daextsocact-ball-shadow daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-shadow"></div>
                        <div class="daextsocact-ball-cr-trail-0 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-0 daextsocact-ball"></div>
                        <div class="daextsocact-ball-cr-trail-22-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-22-5"></div>
                        <div class="daextsocact-ball-cr-trail-45 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-45"></div>
                        <div class="daextsocact-ball-cr-trail-67-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-67-5"></div>
                        <div class="daextsocact-ball-cr-trail-90 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-90"></div>
                        <div class="daextsocact-ball-cr-trail-112-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-112-5"></div>
                        <div class="daextsocact-ball-cr-trail-135 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-135"></div>
                        <div class="daextsocact-ball-cr-trail-157-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-157-5"></div>
                        <div class="daextsocact-ball-cr-trail-180 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-180"></div>
                        <div class="daextsocact-ball-cr-trail-202-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-202-5"></div>
                        <div class="daextsocact-ball-cr-trail-225 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-225"></div>
                        <div class="daextsocact-ball-cr-trail-247-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-247-5"></div>
                        <div class="daextsocact-ball-cr-trail-270 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-270"></div>
                        <div class="daextsocact-ball-cr-trail-292-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-292-5"></div>
                        <div class="daextsocact-ball-cr-trail-315 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-315"></div>
                        <div class="daextsocact-ball-cr-trail-337-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-cr-trail-337-5"></div>
                        <div class="daextsocact-ball-ccr-trail-0 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-0"></div>
                        <div class="daextsocact-ball-ccr-trail-22-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-22-5"></div>
                        <div class="daextsocact-ball-ccr-trail-45 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-45"></div>
                        <div class="daextsocact-ball-ccr-trail-67-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-67-5"></div>
                        <div class="daextsocact-ball-ccr-trail-90 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-90"></div>
                        <div class="daextsocact-ball-ccr-trail-112-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-112-5"></div>
                        <div class="daextsocact-ball-ccr-trail-135 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-135"></div>
                        <div class="daextsocact-ball-ccr-trail-157-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-157-5"></div>
                        <div class="daextsocact-ball-ccr-trail-180 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-180"></div>
                        <div class="daextsocact-ball-ccr-trail-202-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-202-5"></div>
                        <div class="daextsocact-ball-ccr-trail-225 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-225"></div>
                        <div class="daextsocact-ball-ccr-trail-247-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-247-5"></div>
                        <div class="daextsocact-ball-ccr-trail-270 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-270"></div>
                        <div class="daextsocact-ball-ccr-trail-292-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-292-5"></div>
                        <div class="daextsocact-ball-ccr-trail-315 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-315"></div>
                        <div class="daextsocact-ball-ccr-trail-337-5 daextsocact-ball daextsocact-draggable-element"
                             data-id="daextsocact-ball-ccr-trail-337-5"></div>

                        <!-- arrows -->
                        <div class="daextsocact-arrow-0 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-0"></div>
                        <div class="daextsocact-arrow-22-5 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-22-5"></div>
                        <div class="daextsocact-arrow-45 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-45"></div>
                        <div class="daextsocact-arrow-67-5 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-67-5"></div>
                        <div class="daextsocact-arrow-90 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-90"></div>
                        <div class="daextsocact-arrow-112-5 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-112-5"></div>
                        <div class="daextsocact-arrow-135 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-135"></div>
                        <div class="daextsocact-arrow-157-5 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-157-5"></div>
                        <div class="daextsocact-arrow-180 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-180"></div>
                        <div class="daextsocact-arrow-202-5 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-202-5"></div>
                        <div class="daextsocact-arrow-225 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-225"></div>
                        <div class="daextsocact-arrow-247-5 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-247-5"></div>
                        <div class="daextsocact-arrow-270 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-270"></div>
                        <div class="daextsocact-arrow-292-5 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-292-5"></div>
                        <div class="daextsocact-arrow-315 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-315"></div>
                        <div class="daextsocact-arrow-337-5 daextsocact-arrow daextsocact-draggable-element"
                             data-id="daextsocact-arrow-337-5"></div>

                        <!-- label -->
                        <div class="daextsocact-player-label daextsocact-label daextsocact-draggable-element"
                             data-id="daextsocact-player-label"><input type="text"></div>

                    </div>

                </td>

            </tr>

        </table>

        <div id="daextsocact-field">

            <!-- if we are in the edit mode include the players in the field -->
			<?php if ( ! is_null( $data['edit_id'] ) ) {
				echo $this->add_elements_in_field( intval( $data['edit_id'] ), 10 );
			} ?>

        </div>

        <!-- submit button -->
        <p class="submit">
            <input id="daextsocact-submit-action" class="button-primary" type="submit" name="submit"
                   value="<?php esc_attr_e( "Save Changes", "soccer-action" ); ?>">
            <a class="button-secondary" href="admin.php?page=daextsocact-actions"><?php esc_html_e( "Cancel",
					"soccer-action" ); ?></a>
        </p>

        </form>

    </div>

</div>

<!-- Dialog Confirm -->
<div id="dialog-confirm" title="<?php esc_attr_e( 'Delete the action?', 'soccer-action' ); ?>" class="display-none">
    <p><?php esc_html_e( 'This action will be permanently deleted and cannot be recovered. Are you sure?',
			'soccer-action' ); ?></p>
</div>