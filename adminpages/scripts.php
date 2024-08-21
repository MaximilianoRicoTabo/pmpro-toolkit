<?php

global $wpdb, $pmprodev_member_tables, $pmprodev_other_tables;

$pmprodev_member_tables = array(
	$wpdb->pmpro_memberships_users,
	$wpdb->pmpro_membership_orders,
	$wpdb->pmpro_discount_codes_uses,
);

$pmprodev_other_tables = array(
	$wpdb->pmpro_discount_codes,
	$wpdb->pmpro_discount_codes_levels,
	$wpdb->pmpro_membership_levels,
	$wpdb->pmpro_memberships_categories,
	$wpdb->pmpro_memberships_pages,
);

$clean_up_actions = array(
	'pmprodev_clean_member_tables'	=> array(
		'label' => __( 'Clean member tables', 'pmpro-toolkit' ),
		'description' => __( 'Delete all member data. (wp_pmpro_memberships_users, wp_pmpro_membership_orders, wp_pmpro_discount_codes_uses)', 'pmpro-toolkit' ),
		'message' => __( 'Member tables have been truncated.', 'pmpro-toolkit' ),
	),
	'pmprodev_clean_level_data'	=> array(
		'label' => __( 'Clean level data', 'pmpro-toolkit' ),
		'description' => __( 'Delete all level and discount code data. (wp_pmpro_discount_codes, wp_pmpro_discount_codes_levels, wp_pmpro_membership_levels, wp_pmpro_memberships_categories, wp_pmpro_memberships_pages)', 'pmpro-toolkit' ),
		'message' => __( 'Level and discount code tables have been truncated.', 'pmpro-toolkit' )
	),
	'pmprodev_scrub_member_data'	=> array(
		'label' => __( 'Scrub member data', 'pmpro-toolkit' ),
		'description' => __( 'Scrub member emails and transaction ids. Updates non-admins in wp_users and wp_pmpro_membership_orders tables. This may time out on slow servers or sites with large numbers of users.', 'pmpro-toolkit' ),
		'message' => __( 'Scrubbing user data...', 'pmpro-toolkit' )
	),
	'pmprodev_delete_users'	=> array(
		'label' => __( 'Delete users', 'pmpro-toolkit' ),
		'description' => __( 'Delete non-admin users. (Deletes from wp_users and wp_usermeta tables directly.) This may time out on slow servers or sites with large numbers of users.', 'pmpro-toolkit' ),
		'message' => __( 'Deleting non-admins...', 'pmpro-toolkit' )
	),
	'pmprodev_clean_pmpro_options'	=> array(
		'label' => __( 'Clean options', 'pmpro-toolkit' ),
		'description' => __( 'Delete all PMPro options. (Any option prefixed with pmpro_ but not the DB version or PMPro page_id options.)', 'pmpro-toolkit' ),
		'message' => __( 'Options deleted.', 'pmpro-toolkit' )
	),
	'pmprodev_clear_vvl_report'	=> array(
		'label' => __( 'Clear report', 'pmpro-toolkit' ),
		'description' => __( 'Clear visits, views, and logins report.', 'pmpro-toolkit' ),
		'message' => __( 'Visits, Views, and Logins report cleared.', 'pmpro-toolkit' )
	)
);

$level_actions = array(
	'pmprodev_move_level' => array(
	'label' => __( 'Move level', 'pmpro-toolkit' ),
	'description' => __( 'Change all members with a specific level ID to another level ID. Will NOT cancel any recurring subscriptions.', 'pmpro-toolkit' ),
	'message' => __( 'Users updated. Running pmpro_after_change_membership_level filter for all users...', 'pmpro-toolkit' )
	),
	'pmprodev_give_level' => array(
		'label' => __( 'Give level', 'pmpro-toolkit' ),
		'description' => __( 'Give all non-members a specific level ID. This only gives users the level via the database and does NOT fire any pmpro_change_membership_level hooks.', 'pmpro-toolkit' ),
		'message' => __( '%s users were given level %s', 'pmpro-toolkit' )
	),
	'pmprodev_cancel_level' => array(
		'label' => __( 'Cancel level', 'pmpro-toolkit' ),
		'description' => __( 'Cancel all members with a specific level ID. WILL also cancel any recurring subscriptions.', 'pmpro-toolkit' ),
		'message' => __( 'Cancelling users...', 'pmpro-toolkit' )
	),
	'pmprodev_copy_memberships_pages' => array(
		'label' => __( 'Copy memberships pages', 'pmpro-toolkit' ),
		'description' => __( 'Make all pages that require a specific level ID also require another level ID.', 'pmpro-toolkit' ),
		'message' => __( 'Require Membership options copied.', 'pmpro-toolkit' )
	)
);

?>
<div class="wrap pmpro_admin">
	<div class="pmpro_section" data-visibility="shown" data-activated="true">
		<div class="pmpro_section_toggle">
			<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
				<span class="dashicons dashicons-arrow-up-alt2"></span>
				<?php esc_html_e( 'Clean Up Tools.', 'pmpro-toolkit' ); ?>
			</button>
		</div>
		<div class="pmpro_section_inside">

			<form id="form-scripts" method="post" action="">
				<?php wp_nonce_field( 'pmpro_toolkit_script_action', 'pmpro_toolkit_scripts_nonce' ); ?>
				<p><?php esc_html_e( 'This feature allows you to either clear data from PMPro-related database tables and options
				or to scrub member email and transaction id data to prevent real members from receiving updates or having their
				subscriptions changed. Check the options that you would like to apply. The cleanup scripts will be run upon saving
				these settings.', 'pmpro-toolkit' ); ?>
				</p>

				<div class="error">
					<p>
					<span><?php esc_html_e( 'IMPORTANT NOTE:', 'pmpro-toolkit' ); ?></span>
					<span><?php esc_html_e( 'Checking these options WILL delete data from your database. Please backup first and make sure that you intend to delete this data.', 'pmpro-toolkit' ); ?></span>
					</p>
				</div>
				
			<table class="form-table">
				<tbody>
				<?php foreach ( $clean_up_actions as $action => $details ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $details['label'] ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $action ); ?>" value="1">
								<?php echo wp_kses_post( $details['description'] ); ?>
							</label>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="pmpro_section" data-visibility="shown" data-activated="true">
		<div class="pmpro_section_toggle">
			<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
				<span class="dashicons dashicons-arrow-up-alt2"></span>
				<?php esc_html_e( 'Level Tools.', 'pmpro-toolkit' ); ?>

			</button>
		</div>
		<div class="pmpro_section_inside">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Move users from one level to another.', 'pmpro-toolkit' ); ?></th>
					<td>
							<input type="checkbox" name="pmprodev_move_level" value="1">
							<span><?php esc_html_e( 'From Level ID:', 'pmpro-toolkit' ); ?></span> <input type="text" name="move_level_a" value="">
							<span><?php esc_html_e( 'To Level ID:', 'pmpro-toolkit' ); ?></span> <input type="text" name="move_level_b" value="">
					</td>

				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Give users a specific level.', 'pmpro-toolkit' ); ?></th>
					<td>
							<input type="checkbox" name="pmprodev_give_level" value="1">
							<span><?php esc_html_e( 'Level ID:', 'pmpro-toolkit' ); ?></span> <input type="text" name="give_level_id" value="">
							<span><?php esc_html_e( 'Start Date:', 'pmpro-toolkit' ); ?></span> <input type="text" name="give_level_startdate" value="">
							<span><?php esc_html_e( 'End Date:', 'pmpro-toolkit' ); ?></span> <input type="text" name="give_level_enddate" value="">
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Cancel users with a specific level.', 'pmpro-toolkit' ); ?></th>
					<td>
							<input type="checkbox" name="pmprodev_cancel_level" value="1">
							<span><?php esc_html_e( 'Level ID:', 'pmpro-toolkit' ); ?></span> <input type="text" name="cancel_level_id" value="">
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Copy "Require Membership" pages from one level to another.', 'pmpro-toolkit' ); ?></th>
					<td>
							<input type="checkbox" name="pmprodev_copy_memberships_pages" value="1">
							<span><?php esc_html_e( 'Copy From Level ID:', 'pmpro-toolkit' ); ?></span> <input type="text" name="copy_memberships_pages_from" value="">
							<span><?php esc_html_e( 'Copy To Level ID:', 'pmpro-toolkit' ); ?></span> <input type="text" name="copy_memberships_pages_to" value="">
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
				<p class="submit">
					<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Run Selected Tools', 'pmpro-toolkit' ); ?>" />
				</p>
			</form>
		</div>
	</div>
</div>
<?php


$actions = array_merge( $clean_up_actions, $level_actions );
foreach ( $actions as $action => $options ) {
	if ( ! empty( $_POST[ $action ] ) ) {
		call_user_func( $action, $options[ 'message' ] );
	}
}

function pmprodev_clean_member_tables( $message ) {
	global $wpdb, $pmprodev_member_tables;
	foreach ( $pmprodev_member_tables as $table ) {
		$wpdb->query( "TRUNCATE $table" );
	}
	pmprodev_output_message( $message );
}

function pmprodev_clean_level_data( $message ) {
	global $wpdb, $pmprodev_other_tables;
	foreach ( $pmprodev_other_tables as $table ) {
		$wpdb->query( "TRUNCATE $table" );
	}
	pmprodev_output_message( $message );
}

function pmprodev_scrub_member_data( $message ) {
	global $wpdb;
	pmprodev_output_message( $message );
	$user_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->users} WHERE user_email NOT LIKE '%+scrub%'" );
	$count = 0;
	$admin_email = get_option( 'admin_email' );
	foreach ( $user_ids as $user_id ) {
		$count++;
		if ( ! user_can( $user_id, 'manage_options' ) ) {
			$new_email = str_replace( '@', '+scrub' . $count . '@', $admin_email );
			$wpdb->query( "UPDATE {$wpdb->users} SET user_email = '" . esc_sql( $new_email ) . "' WHERE ID = " . intval( $user_id ) . " LIMIT 1" );
		}
		$new_transaction_id = 'SCRUBBED-' . $count;
		$wpdb->query( "UPDATE {$wpdb->pmpro_membership_orders} SET payment_transaction_id = '" . esc_sql( $new_transaction_id ) . "' WHERE user_id = '" . intval( $user_id ) . "' AND payment_transaction_id <> ''" );
		$wpdb->query( "UPDATE {$wpdb->pmpro_membership_orders} SET subscription_transaction_id = '" . esc_sql( $new_transaction_id ) . "' WHERE user_id = '" . intval( $user_id ) . "' AND subscription_transaction_id <> ''" );
		update_user_meta( $user_id, 'pmpro_braintree_customerid', $new_transaction_id );
		update_user_meta( $user_id, 'pmpro_stripe_customerid', $new_transaction_id );
		echo '. ';
	}
	pmprodev_process_complete();
}

function pmprodev_delete_users( $message ) {
	global $wpdb;
	pmprodev_output_message( $message );
	$user_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->users}" );
	foreach ( $user_ids as $user_id ) {
		if ( ! user_can( $user_id, 'manage_options' ) ) {
			$wpdb->query( "DELETE FROM {$wpdb->users} WHERE ID = " . intval( $user_id ) . " LIMIT 1" );
			$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE user_id = " . intval( $user_id ) );
			echo '. ';
		}
	}
	pmprodev_process_complete();
}

function pmprodev_clean_pmpro_options( $message ) {
	delete_option( 'pmpro_db_version' );
	delete_option( 'pmpro_membership_levels' );
	delete_option( 'pmpro_non_member_page_settings' );
	delete_option( 'pmpro_options' );
	delete_option( 'pmpro_pages' );
	delete_option( 'pmpro_upgrade' );
	delete_option( 'pmpro_initial_payment' );
	delete_option( 'pmpro_recurring_payment' );
	delete_option( 'pmpro_billing_amount' );
	delete_option( 'pmpro_cycle_number' );
	delete_option( 'pmpro_cycle_period' );
	delete_option( 'pmpro_billing_limit' );
	delete_option( 'pmpro_trial_amount' );
	delete_option( 'pmpro_trial_limit' );
	delete_option( 'pmpro_code_id' );
	delete_option( 'pmpro_checkout_box_' );
	delete_option( 'pmpro_level_cost_text' );
	delete_option( 'pmpro_levels_page_settings' );
	delete_option( 'pmpro_discount_code_name' );
	delete_option( 'pmpro_discount_code_page_settings' );
	delete_option( 'pmpro_discount_code_id' );
	delete_option( 'pmpro_discount_code' );
	delete_option( 'pmpro_discount_code_page_settings' );
	delete_option( 'pmpro_discount_code' );
	pmprodev_output_message( $message );
}

function pmprodev_clear_vvl_report( $message ) {
	global $wpdb;
	$wpdb->query( "TRUNCATE {$wpdb->pmpro_visits}" );
	$wpdb->query( "TRUNCATE {$wpdb->pmpro_views}" );
	$wpdb->query( "TRUNCATE {$wpdb->pmpro_logins}" );
	pmprodev_output_message( $message );
}


// Move level function
function pmprodev_move_level( $message ) {
	global $wpdb;

	pmprodev_output_message( $message );

	$from_level_id = intval( $_REQUEST['move_level_a'] );
	$to_level_id = intval( $_REQUEST['move_level_b'] );

	if ( $from_level_id < 1 || $to_level_id < 1 ) {
		pmprodev_output_message( __( 'Please enter a level ID > 1 for each option.', 'pmpro-toolkit' ) );
	} else {
		$user_ids = $wpdb->get_col( "SELECT user_id FROM $wpdb->pmpro_memberships_users WHERE membership_id = $from_level_id AND status = 'active'" );

		if ( empty( $user_ids ) ) {
			pmprodev_output_message( sprintf( __( 'Couldn\'t find users with level ID %d.', 'pmpro-toolkit' ), $from_level_id ) );
		} else {
			$wpdb->query( "UPDATE $wpdb->pmpro_memberships_users SET membership_id = $to_level_id WHERE membership_id = $from_level_id AND status = 'active';" );

			foreach ( $user_ids as $user_id ) {
				do_action( 'pmpro_after_change_membership_level', $to_level_id, $user_id, $from_level_id );
			}

			pmprodev_process_complete();
		}
	}
}

// Give level function
function pmprodev_give_level( $message ) {
	global $wpdb;

	$give_level_id = intval( $_REQUEST['give_level_id'] );
	$give_level_startdate = sanitize_text_field( $_REQUEST['give_level_startdate'] );
	$give_level_enddate = sanitize_text_field( $_REQUEST['give_level_enddate'] );

	if ( $give_level_id < 1 || empty( $give_level_startdate ) ) {
		pmprodev_output_message( __( 'Please enter a valid level ID and start date.', 'pmpro-toolkit' ) );
	} else {
		$sqlQuery = $wpdb->prepare(
			"INSERT INTO {$wpdb->pmpro_memberships_users} (user_id, membership_id, status, startdate, enddate)
			SELECT u.ID, %d, 'active', %s, %s
			FROM {$wpdb->users} u 
			LEFT JOIN {$wpdb->pmpro_memberships_users} mu
			ON u.ID = mu.user_id 
			AND mu.status = 'active' 
			WHERE mu.id IS NULL;",
			$give_level_id,
			$give_level_startdate,
			$give_level_enddate
		);

		$wpdb->query( $sqlQuery );

		pmprodev_output_message( $message );
	}
}

// Cancel level function
function pmprodev_cancel_level( $message ) {
	global $wpdb;

	pmprodev_output_message( $message );

	$cancel_level_id = intval( $_REQUEST['cancel_level_id'] );
	$user_ids = $wpdb->get_col( "SELECT user_id FROM $wpdb->pmpro_memberships_users WHERE membership_id = $cancel_level_id AND status = 'active'" );

	if ( empty( $user_ids ) ) {
		pmprodev_output_message( sprintf( __( 'Couldn\'t find users with level ID %d.', 'pmpro-toolkit' ), $cancel_level_id ) );
	} else {
		pmprodev_output_message( sprintf( __( 'Cancelling %s users...', 'pmpro-toolkit' ), count( $user_ids ) ) );
		foreach ( $user_ids as $user_id ) {
			pmpro_cancelMembershipLevel( $cancel_level_id, $user_id );
		}

		pmprodev_process_complete();
	}
}

// Copy memberships pages function
function pmprodev_copy_memberships_pages( $message ) {
	global $wpdb;

	$from_level_id = intval( $_REQUEST['copy_memberships_pages_a'] );
	$to_level_id = intval( $_REQUEST['copy_memberships_pages_b'] );

	$wpdb->query(
		$wpdb->prepare(
			"INSERT IGNORE INTO {$wpdb->pmpro_memberships_pages} (membership_id, page_id) 
			SELECT %d, page_id FROM {$wpdb->pmpro_memberships_pages} WHERE membership_id = %d",
			$to_level_id,
			$from_level_id
		)
	);

	pmprodev_output_message( $message );
}

function pmprodev_output_message( $message ) {
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}

function pmprodev_process_complete() {
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Process complete.', 'pmpro-toolkit' ) . '</p></div>';
}
?>
