<?php

global $wpdb, $pmprodev_member_tables, $pmprodev_other_tables;

$pmprodev_member_tables = array(
	$wpdb->pmpro_memberships_users,
	$wpdb->pmpro_membership_orders,
	$wpdb->pmpro_discount_codes_uses,
	$wpdb->pmpro_subscriptions,
	$wpdb->pmpro_subscriptionmeta,
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
		'label' => __( 'Delete Member Data', 'pmpro-toolkit' ),
		'description' => __( 'Delete all member data. This script deletes data from the wp_pmpro_memberships_users, wp_pmpro_membership_orders, and wp_pmpro_discount_codes_uses tables.', 'pmpro-toolkit' ),
		'message' => __( 'Member tables have been truncated.', 'pmpro-toolkit' ),
	),
	'pmprodev_clean_level_data'	=> array(
		'label' => __( 'Reset Membership Settings', 'pmpro-toolkit' ),
		'description' => __( 'Delete all membership level, content protection, and discount code settings. This script deletes data from the wp_pmpro_discount_codes, wp_pmpro_discount_codes_levels, wp_pmpro_membership_levels, wp_pmpro_memberships_categories, and wp_pmpro_memberships_pages tables.', 'pmpro-toolkit' ),
		'message' => __( 'Level and discount code tables have been truncated.', 'pmpro-toolkit' )
	),
	'pmprodev_scrub_member_data'	=> array(
		'label' => __( 'Scrub Member Data', 'pmpro-toolkit' ),
		'description' => __( 'Scrub all member emails and transaction IDs. This script updates all non-admins in the wp_users and wp_pmpro_membership_orders tables to anonymize their email addresses and order transaction IDs. This may time out on slow servers or sites with large numbers of users.', 'pmpro-toolkit' ),
		'message' => __( 'Scrubbing user data...', 'pmpro-toolkit' )
	),
	'pmprodev_delete_users'	=> array(
		'label' => __( 'Delete Users', 'pmpro-toolkit' ),
		'description' => __( 'Delete non-admin users. This script deletes from wp_users and wp_usermeta tables directly. This may time out on slow servers or sites with large numbers of users.', 'pmpro-toolkit' ),
		'message' => __( 'Deleting non-admins...', 'pmpro-toolkit' )
	),
	'pmprodev_clean_pmpro_options'	=> array(
		'label' => __( 'Reset Options', 'pmpro-toolkit' ),
		'description' => __( 'Delete all PMPro options. This script deletes any option prefixed with pmpro_ in the wp_options table, excluding the pmpro_db_version and assigned PMPro pages.)', 'pmpro-toolkit' ),
		'message' => __( 'Options deleted.', 'pmpro-toolkit' )
	),
	'pmprodev_clear_vvl_report'	=> array(
		'label' => __( 'Clear Logins Report', 'pmpro-toolkit' ),
		'description' => __( 'Clear and reset all visits, views, and logins report data.', 'pmpro-toolkit' ),
		'message' => __( 'Visits, Views, and Logins report cleared.', 'pmpro-toolkit' )
	),
	'pmprodev_delete_test_orders' => array(
		'label' => __( 'Delete Test Orders', 'pmpro-toolkit' ),
		'description' => __( 'Delete all orders made through the testing or sandbox gateway environment', 'pmpro-toolkit' ),
		'message' => __( 'Test orders deleted.', 'pmpro-toolkit' )
	),
	'pmprodev_clear_cached_report_data' => array(
		'label' => __( 'Clear cached report data', 'pmpro-toolkit' ),
		'description' => __( 'Clear cached report data.', 'pmpro-toolkit' ),
		'message' => __( 'Cached report data cleared.', 'pmpro-toolkit' )
	),
);

$level_actions = array(
	'pmprodev_move_level' => array(
	'label' => __( 'Change Membership Level', 'pmpro-toolkit' ),
	'description' => __( 'Change all members with a specific level ID to another level ID. Note: running this script will NOT cancel any recurring subscriptions.', 'pmpro-toolkit' ),
	'message' => __( 'Users updated. Running pmpro_after_change_membership_level filter for all users...', 'pmpro-toolkit' )
	),
	'pmprodev_give_level' => array(
		'label' => __( 'Assign Membership Level', 'pmpro-toolkit' ),
		'description' => __( 'Assign a specific membership level to all users without an active membership. Note: This script only assigns membership via the database and does NOT fire any pmpro_change_membership_level hooks or process payments.', 'pmpro-toolkit' ),
		'message' => __( '%s users were given level %s', 'pmpro-toolkit' )
	),
	'pmprodev_cancel_level' => array(
		'label' => __( 'Cancel Membership', 'pmpro-toolkit' ),
		'description' => __( 'Cancel all members with a specific level ID. Note: This script WILL also cancel any recurring subscriptions.', 'pmpro-toolkit' ),
		'message' => __( 'Cancelling users...', 'pmpro-toolkit' )
	),
);

$other_actions = array(
	'pmprodev_copy_memberships_pages' => array(
		'label' => __( 'Copy Content Restrictions', 'pmpro-toolkit' ),
		'description' => __( 'Make all pages that require a specific level ID also require another level ID.', 'pmpro-toolkit' ),
		'message' => __( 'Content restrictions copied.', 'pmpro-toolkit' )
	),
	'pmprodev_delete_incomplete_orders' => array(
		'label' => __( 'Delete Incomplete Orders', 'pmpro-toolkit' ),
		'description' => __( 'Delete all orders in token, pending, or review status that are older than a specified number of days.', 'pmpro-toolkit' ),
		'message' => __( 'Incomplete orders deleted.', 'pmpro-toolkit' )
	)
);

?>
<h2><?php esc_html_e( 'Database Scripts', 'pmpro-toolkit' ); ?></h2>
<p><?php esc_html_e( 'Toolkit scripts allow you to clean up, delete, duplicate, or anonymize data in your membership site. We recommend only running one script at a time. Check a setting below and click save to run the script.', 'pmpro-toolkit' ); ?></p>
<p><?php esc_html_e( 'Note: Running the scripts below will delete or modify data in your database. These changes cannot be reversed. Please take a site backup before running a script.', 'pmpro-toolkit' ); ?></p>
<form id="form-scripts" method="post" action="">
<?php wp_nonce_field( 'pmpro_toolkit_script_action', 'pmpro_toolkit_scripts_nonce' ); ?>
	<div class="pmpro_section" data-visibility="shown" data-activated="true">
		<div class="pmpro_section_toggle">
			<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
				<span class="dashicons dashicons-arrow-up-alt2"></span>
				<?php esc_html_e( 'Database Clean Up Scripts', 'pmpro-toolkit' ); ?>
			</button>
		</div>
		<div class="pmpro_section_inside">
			<table class="form-table">
				<tbody>
				<?php foreach ( $clean_up_actions as $action => $details ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $details['label'] ); ?></th>
						<td>
							<input type="checkbox" id="<?php echo esc_attr( $action ); ?>" name="<?php echo esc_attr( $action ); ?>" value="1">
							<label for="<?php echo esc_attr( $action ); ?>"><?php echo wp_kses_post( $details['description'] ); ?></label>
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
				<?php esc_html_e( 'Member Scripts', 'pmpro-toolkit' ); ?>
			</button>
		</div>
		<div class="pmpro_section_inside">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><?php echo esc_html( $level_actions['pmprodev_move_level']['label'] ); ?></th>
					<td>
						<input type="checkbox" id="pmprodev_move_level" name="pmprodev_move_level" value="1">
						<label for="pmprodev_move_level">
							<?php
								printf(
									/* translators: %s: script name */
									esc_html__( 'Run the %s script.', 'pmpro-toolkit' ),
									esc_html( $level_actions['pmprodev_move_level']['label'] )
								);
							?>
						</label>
						<div id="pmprodev_move_level_actions" style="display: none;">
							<p>
								<?php esc_html_e( 'From Level ID:', 'pmpro-toolkit' ); ?>
								<input type="number" name="move_level_a" value="" size="5" step="1">
								<?php esc_html_e( 'To Level ID:', 'pmpro-toolkit' ); ?>
								<input type="number" name="move_level_b" value="" size="5">
							</p>
							<p class="description"><?php echo esc_html( $level_actions['pmprodev_move_level']['description'] ); ?></p>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html( $level_actions['pmprodev_give_level']['label'] ); ?></th>
					<td>
						<input type="checkbox" id="pmprodev_give_level" name="pmprodev_give_level" value="1">
						<label for="pmprodev_give_level">
							<?php
								printf(
									/* translators: %s: script name */
									esc_html__( 'Run the %s script.', 'pmpro-toolkit' ),
									esc_html( $level_actions['pmprodev_give_level']['label'] )
								);
							?>
						</label>
						<div id="pmprodev_give_level_actions" style="display: none;">
							<p>
								<?php esc_html_e( 'Level ID:', 'pmpro-toolkit' ); ?>
								<input type="number" name="give_level_id" value="" size="5">
								<?php esc_html_e( 'Start Date:', 'pmpro-toolkit' ); ?>
								<input type="date" name="give_level_startdate" value="">
								<?php esc_html_e( 'End Date:', 'pmpro-toolkit' ); ?>
								<input type="date" name="give_level_enddate" value="">
							</p>
							<p class="description"><?php echo esc_html( $level_actions['pmprodev_give_level']['description'] ); ?></p>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html( $level_actions['pmprodev_cancel_level']['label'] ); ?></th>
					<td>
						<input type="checkbox" id="pmprodev_cancel_level" name="pmprodev_cancel_level" value="1">
						<label for="pmprodev_cancel_level">
							<?php
								printf(
									/* translators: %s: script name */
									esc_html__( 'Run the %s script.', 'pmpro-toolkit' ),
									esc_html( $level_actions['pmprodev_cancel_level']['label'] )
								);
							?>
						</label>
						<div id="pmprodev_cancel_level_actions" style="display: none;">
							<p>
								<?php esc_html_e( 'Level ID:', 'pmpro-toolkit' ); ?>
								<input type="number" name="cancel_level_id" value="" size="5">
							</p>
							<p class="description"><?php echo esc_html( $level_actions['pmprodev_cancel_level']['description'] ); ?></p>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="pmpro_section" data-visibility="shown" data-activated="true">
		<div class="pmpro_section_toggle">
			<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
				<span class="dashicons dashicons-arrow-up-alt2"></span>
				<?php esc_html_e( 'Other Scripts', 'pmpro-toolkit' ); ?>
			</button>
		</div>
		<div class="pmpro_section_inside">
			<table class="form-table">
				<tr>
					<th scope="row"><?php echo esc_html( $other_actions['pmprodev_copy_memberships_pages']['label'] ); ?></th>
					<td>
						<input type="checkbox" id="pmprodev_copy_memberships_pages" name="pmprodev_copy_memberships_pages" value="1">
						<label for="pmprodev_copy_memberships_pages">
							<?php
								printf(
									/* translators: %s: script name */
									esc_html__( 'Run the %s script.', 'pmpro-toolkit' ),
									esc_html( $other_actions['pmprodev_copy_memberships_pages']['label'] )
								);
							?>
						</label>
						<div id="pmprodev_copy_memberships_pages_actions" style="display: none;">
							<p>
								<?php esc_html_e( 'Copy From Level ID:', 'pmpro-toolkit' ); ?>
								<input type="number" name="copy_memberships_pages_from" value="">
								<?php esc_html_e( 'Copy To Level ID:', 'pmpro-toolkit' ); ?>
								<input type="number" name="copy_memberships_pages_to" value="">
							</p>
							<p class="description"><?php echo esc_html( $other_actions['pmprodev_copy_memberships_pages']['description'] ); ?></p>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html( $other_actions['pmprodev_delete_incomplete_orders']['label'] ); ?></th>
					<td>
						<input type="checkbox" id="pmprodev_delete_incomplete_orders" name="pmprodev_delete_incomplete_orders" value="1">
						<label for="pmprodev_delete_incomplete_orders">
							<?php
								printf(
									/* translators: %s: script name */
									esc_html__( 'Run the %s script.', 'pmpro-toolkit' ),
									esc_html( $other_actions['pmprodev_delete_incomplete_orders']['label'] )
								);
							?>
						</label>
						<div id="pmprodev_delete_incomplete_orders_actions" style="display: none;">
							<p>
								<?php esc_html_e( 'Days:', 'pmpro-toolkit' ); ?>
								<input type="number" name="delete_incomplete_orders_days" value="">
							</p>
							<p class="description"><?php echo esc_html( $other_actions['pmprodev_delete_incomplete_orders']['description'] ); ?></p>
						</div>
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

<script>
	jQuery(document).ready(function($) {
		$('#pmprodev_move_level').change(function() {
			if ($(this).is(':checked')) {
				$('#pmprodev_move_level_actions').show();
			} else {
				$('#pmprodev_move_level_actions').hide();
			}
		});
		$('#pmprodev_give_level').change(function() {
			if ($(this).is(':checked')) {
				$('#pmprodev_give_level_actions').show();
			} else {
				$('#pmprodev_give_level_actions').hide();
			}
		});
		$('#pmprodev_cancel_level').change(function() {
			if ($(this).is(':checked')) {
				$('#pmprodev_cancel_level_actions').show();
			} else {
				$('#pmprodev_cancel_level_actions').hide();
			}
		});
		$('#pmprodev_copy_memberships_pages').change(function() {
			if ($(this).is(':checked')) {
				$('#pmprodev_copy_memberships_pages_actions').show();
			} else {
				$('#pmprodev_copy_memberships_pages_actions').hide();
			}
		});
		$('#pmprodev_delete_incomplete_orders').change(function() {
			if ($(this).is(':checked')) {
				$('#pmprodev_delete_incomplete_orders_actions').show();
			} else {
				$('#pmprodev_delete_incomplete_orders_actions').hide();
			}
		});
	});
</script>
<?php


$actions = array_merge( $clean_up_actions, $level_actions, $other_actions );
foreach ( $actions as $action => $options ) {
	if ( ! empty( $_POST[ $action ] ) ) {
		//Check nonce
		if ( ! isset( $_POST['pmpro_toolkit_scripts_nonce'] ) ||
		! check_admin_referer( 'pmpro_toolkit_script_action', 'pmpro_toolkit_scripts_nonce' ) ) {
			pmprodev_output_message( __( 'Security check failed.', 'pmpro-toolkit' ), 'error' );
			return;
		}
		call_user_func( $action, $options[ 'message' ] );
	}
}

function pmprodev_clean_member_tables( $message ) {
	global $wpdb, $pmprodev_member_tables;
	foreach ( $pmprodev_member_tables as $table ) {
		$wpdb->query( "TRUNCATE $table" );
	}
	pmprodev_clear_cached_report_data( '' );
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
		$wpdb->query( "UPDATE {$wpdb->pmpro_subscriptions} SET subscription_transaction_id = '" . esc_sql( $new_transaction_id ) . "' WHERE user_id = '" . intval( $user_id ) . "' AND subscription_transaction_id <> ''" );
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

/**
 * Delete all orders with a sandbox gateway environment
 *
 * @param string $message The message to display after the orders are deleted.
 * @return void
 * @since TBD
 */
function pmprodev_delete_test_orders( $message ) {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->pmpro_membership_orders} WHERE gateway_environment = 'sandbox'" );
	pmprodev_output_message( $message );
}
/** Clear cached report data
 *
 * @param string $message The message to display after the process is complete.
 * @since TBD
 * @return void
 */
function pmprodev_clear_cached_report_data( $message ) {
	pmpro_report_memberships_delete_transients();
	pmpro_report_sales_delete_transients();

	pmprodev_output_message( $message );
}

// Move level function
function pmprodev_move_level( $message ) {
	global $wpdb;

	pmprodev_output_message( $message );

	$from_level_id = intval( $_REQUEST['move_level_a'] );
	$to_level_id = intval( $_REQUEST['move_level_b'] );

	if ( $from_level_id < 1 || $to_level_id < 1 ) {
		pmprodev_output_message( __( 'Please enter a level ID > 1 for each option.', 'pmpro-toolkit' ), 'warning' );
	} else {
		$user_ids = $wpdb->get_col( "SELECT user_id FROM $wpdb->pmpro_memberships_users WHERE membership_id = $from_level_id AND status = 'active'" );

		if ( empty( $user_ids ) ) {
			pmprodev_output_message( sprintf( __( 'Couldn\'t find users with level ID %d.', 'pmpro-toolkit' ), $from_level_id ), 'warning' );
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
		pmprodev_output_message( __( 'Please enter a valid level ID and start date.', 'pmpro-toolkit' ), 'warning' );
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
		pmprodev_output_message( sprintf( __( 'Couldn\'t find users with level ID %d.', 'pmpro-toolkit' ), $cancel_level_id ), 'warning' );
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

	$from_level_id = intval( $_REQUEST['copy_memberships_pages_from'] );
	$to_level_id = intval( $_REQUEST['copy_memberships_pages_to'] );

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

// Delete incomplete orders function
function pmprodev_delete_incomplete_orders( $message ) {
	global $wpdb;

	if ( empty( $_REQUEST['delete_incomplete_orders_days'] ) ) {
		pmprodev_output_message( __( 'Please enter a number of days.', 'pmpro-toolkit' ), 'warning' );
		return;
	}

	if ( ! is_numeric( $_REQUEST['delete_incomplete_orders_days'] ) || intval( $_REQUEST['delete_incomplete_orders_days'] ) < 1 ) {
		pmprodev_output_message( __( 'Please enter a valid number of days.', 'pmpro-toolkit' ), 'warning' );
		return;
	}

	$days = intval( $_REQUEST['delete_incomplete_orders_days'] );
	$deleted = $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->pmpro_membership_orders} WHERE status IN('token', 'pending', 'review') AND timestamp < %s",
			date( 'Y-m-d', strtotime( "-$days days" ) )
		)
	);

	pmprodev_output_message( sprintf( __( '%d orders deleted.', 'pmpro-toolkit' ), (int) $deleted ) );
}

function pmprodev_output_message( $message, $type = 'success' ) {
	if ( empty( $message ) ) {
		return;
	}
	echo '<div class="notice notice-' . esc_attr( $type ) . '"><p>' . esc_html( $message ) . '</p></div>';
}

function pmprodev_process_complete() {
	echo '<div class="notice notice-success"><p>' . esc_html__( 'Process complete.', 'pmpro-toolkit' ) . '</p></div>';
}
?>
