<?php
	// Only admins can access this page.
	if( !function_exists( "current_user_can" ) || ( !current_user_can( "manage_options" ) ) ) {
		die( esc_html__( "You do not have permissions to perform this action.", 'pmpro-toolkit' ) );
	}

	global $msg, $msgt;
	global $pmprodev_options;

	global $msg, $msgt;

	// Bail if nonce field isn't set.
	if ( !empty( $_REQUEST['savesettings'] ) && ( empty( $_REQUEST[ 'pmpro_toolkit_nonce' ] ) 
		|| !check_admin_referer( 'savesettings', 'pmpro_toolkit_nonce' ) ) ) {
		$msg = -1;
		$msgt = __( "Are you sure you want to do that? Try again.", 'pmpro-toolkit' );
		unset( $_REQUEST[ 'savesettings' ] );
	}

	// Save settings.
	if( !empty( $_REQUEST['savesettings'] ) ) {
		$pmprodev_options['redirect_email'] = sanitize_text_field( $_POST['pmprodev_options']['redirect_email'] );
		$pmprodev_options['ipn_debug'] = sanitize_text_field( $_POST['pmprodev_options']['ipn_debug'] );
		$pmprodev_options['checkout_debug_when'] = sanitize_text_field( $_POST['pmprodev_options']['checkout_debug_when'] );
		$pmprodev_options['checkout_debug_email'] = sanitize_text_field( $_POST['pmprodev_options']['checkout_debug_email'] );

		if( isset( $_POST['pmprodev_options']['expire_memberships'] ) ) {
			$expire_memberships = intval( $_POST['pmprodev_options']['expire_memberships'] );
		} else {
			$expire_memberships = 0;
		}

		$pmprodev_options['expire_memberships'] = $expire_memberships;

		if( isset( $_POST['pmprodev_options']['expiration_warnings'] ) ) {
			$expiration_warnings = intval( $_POST['pmprodev_options']['expiration_warnings'] );
		} else {
			$expiration_warnings = 0;
		}

		$pmprodev_options['expiration_warnings'] = $expiration_warnings;

		if( isset( $_POST['pmprodev_options']['credit_card_expiring'] ) ) {
			$credit_card_expiring = intval( $_POST['pmprodev_options']['credit_card_expiring'] );
		} else {
			$credit_card_expiring = 0;
		}

		$pmprodev_options['credit_card_expiring'] = $credit_card_expiring;

		if( isset( $_POST['pmprodev_options']['view_as_enabled'] ) ) {
			$view_as_enabled = intval( $_POST['pmprodev_options']['view_as_enabled'] );
		} else {
			$view_as_enabled = 0;
		}

		$pmprodev_options['view_as_enabled'] = $view_as_enabled;

		update_option( "pmprodev_options", $pmprodev_options );


		// Assume success.
		$msg = true;
		$msgt = __( "Dev toolkit settings have been updated.", 'pmpro-toolkit' );

	}

	// Load the admin header.
	require_once PMPRO_DIR . '/adminpages/admin_header.php';
	$section = !empty( $_REQUEST['section']  ) ? sanitize_text_field( $_REQUEST[ 'section' ] ) : 'options';

?>
<div class="wrap pmpro_admin">

	<form action="" method="POST" enctype="multipart/form-data">
		<?php wp_nonce_field( 'savesettings', 'pmpro_toolkit_nonce' );?>
		<hr class="wp-header-end">
		<h1><?php esc_html_e( 'Developer\'s Toolkit', 'pmpro-toolkit' ); ?></h1>

	<!-- nav tabs -->

	<h2 class="nav-tab-wrapper">
		<a href="?page=pmpro-toolkit" class="nav-tab
		<?php if( $section == 'options' ) { echo ' nav-tab-active'; } ?>">
			<?php esc_html_e( 'Toolkit Options', 'pmpro-toolkit' ); ?>
		</a>
		<a href="?page=pmpro-toolkit&section=scripts" class="nav-tab
		<?php if( $section == 'scripts' ) { echo ' nav-tab-active'; } ?>">
			<?php esc_html_e( 'Database Scripts', 'pmpro-toolkit' ); ?>
		</a>
		<a href="?page=pmpro-toolkit&section=migration" class="nav-tab
		<?php if( $section == 'migration' ) { echo ' nav-tab-active'; } ?>">
			<?php esc_html_e( 'Migration Assistant', 'pmpro-toolkit' ); ?>
		</a>
	</h2>

	<!--Email debugging section -->
	<div class="section-options-wrapper"  <?php if( $section != 'options' ) { ?> style="display:none"><?php } ?>>
		<div class="pmpro_section" data-visibility="shown" data-activated="true">
			<div class="pmpro_section_toggle">
				<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
					<?php esc_html_e( 'Email debugging', 'pmpro-toolkit' ); ?>
				</button>
			</div>
			<div class="pmpro_section_inside">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row" valign="top">
								<label for="pmprodev_options[redirect_email]"> <?php esc_html_e( 'Redirect PMPro Emails', 'pmpro-toolkit' ); ?></label>
							</th>
							<td>
								<input type="email" id="pmprodev_options[redirect_email]" name="pmprodev_options[redirect_email]" value="<?php echo esc_attr( $pmprodev_options['redirect_email'] ); ?>">
								<p class="description"><?php echo esc_html_e( 'Redirect all Paid Memberships Pro emails to a specific address.', 'pmpro-toolkit' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<!--Scheduled Cron Job Debugging section -->
		<div class="pmpro_section" data-visibility="shown" data-activated="true">
			<div class="pmpro_section_toggle">
				<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
					<?php esc_html_e( 'Scheduled Cron Job Debugging', 'pmpro-toolkit' ); ?>
				</button>
			</div>
			<div class="pmpro_section_inside">
				<table class="form-table">
					<tbody>
						<!--  Expire Memberships row  -->
						<tr>
							<th scope="row" valign="top">
								<label for="expire_memberships"><?php esc_html_e( 'Expire Memberships', 'pmpro-toolkit' ); ?></label>
							</th>
							<td>
								<input id="expire_memberships" type="checkbox"  name="pmprodev_options[expire_memberships]" value="1" <?php checked( $pmprodev_options['expire_memberships'], 1, true ); ?>>
								<label for="expire_memberships">
									<?php echo esc_html_e( 'Check to disable the script that checks for expired memberships.', 'pmpro-toolkit' ); ?>
								</label>
							</td>
						</tr>
						<!-- another row but for Expiration Warning -->
						<tr>
							<th scope="row" valign="top">
								<label for="expiration_warnings"><?php esc_html_e( 'Expiration Warnings', 'pmpro-toolkit' ); ?></label>
							</th>
							<td>
								<input id="expiration_warnings" type="checkbox" name="pmprodev_options[expiration_warnings]" value="1" <?php checked( $pmprodev_options['expiration_warnings'], 1, true ); ?>>
								<label for="expiration_warnings">
									<?php echo esc_html_e( 'Check to disable the script that sends expiration warnings.', 'pmpro-toolkit' ); ?>
								</label>
							</td>
						<tr>
						<!-- another row but for Credit Card Expiring -->
						<tr>
							<th scope="row" valign="top">
								<label for="credit_card_expiring"><?php esc_html_e( 'Credit Card Expiring', 'pmpro-toolkit' ); ?></label>
							</th>
							<td>
								<input id="credit_card_expiring" type="checkbox" name="pmprodev_options[credit_card_expiring]" value="1" <?php checked( $pmprodev_options['credit_card_expiring'], 1, true ); ?>>
								<label for="credit_card_expiring">
									<?php echo esc_html_e( 'Check to disable the script that checks for expired credit cards.', 'pmpro-toolkit' ); ?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<!--Gateway/Checkout Debugging section -->
		<div class="pmpro_section" data-visibility="shown" data-activated="true">
			<div class="pmpro_section_toggle">
				<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
					<?php esc_html_e( 'Gateway/Checkout Debugging', 'pmpro-toolkit' ); ?>
				</button>
			</div>
			<div class="pmpro_section_inside">
				<table class="form-table">
				<tbody>
					<!--  Expire Memberships row  -->
					<tr>
						<th scope="row" valign="top">
							<label for="ipn_debug"><?php esc_html_e( 'Gateway Callback Debug Email', 'pmpro-toolkit' ); ?></label>
						</th>
							<td>
							<input type="email" id="ipn_debug" name="pmprodev_options[ipn_debug]" value="<?php echo esc_attr( $pmprodev_options['ipn_debug'] ); ?>">
							<label for="ipn_debug">
									<?php echo esc_html_e( 'Check to disable the script that checks for expired memberships.', 'pmpro-toolkit' ); ?>
							</label>
						</td>
					</tr>
					<!-- another row but for Send Checkout Debug Email	 -->
					<tr>
						<th scope="row" valign="top">
							<label for="checkout_debug_email"><?php esc_html_e( 'Send Checkout Debug Email', 'pmpro-toolkit' ); ?></label>
						</th>
						<td>
							<select name="pmprodev_options[checkout_debug_when]">
								<option value="" <?php selected( $pmprodev_options['checkout_debug_when'], '' ); ?>><?php esc_html_e( 'Never (Off)', 'pmpro-toolkit' ); ?></option>
								<option value="on_checkout" <?php selected( $pmprodev_options['checkout_debug_when'], 'on_checkout' ); ?>><?php esc_html_e( 'Yes, Every Page Load', 'pmpro-toolkit' ); ?></option>
								<option value="on_submit" <?php selected( $pmprodev_options['checkout_debug_when'], 'on_submit' ); ?>><?php esc_html_e( 'Yes, Submitted Forms Only', 'pmpro-toolkit' ); ?></option>
								<option value="on_error" <?php selected( $pmprodev_options['checkout_debug_when'], 'on_error' ); ?>><?php esc_html_e( 'Yes, Errors Only', 'pmpro-toolkit' ); ?></option>
							</select>
							<span><?php esc_html_e( 'to email:', 'pmpro-toolkit' ); ?></span><input type="email" id="checkout_debug_email" name="pmprodev_options[checkout_debug_email]" value="<?php echo esc_attr( $pmprodev_options['checkout_debug_email'] ); ?>">
							<p class="description">
								<?php esc_html_e( 'Send an email every time the Checkout page is hit.', 'pmpro-toolkit' ); ?>
							</p>
							<p><?php esc_html_e( 'This email will contain data about the request, user, membership level, order, and other information.', 'pmpro-toolkit' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

		<!-- View as section -->
		<div class="pmpro_section" data-visibility="shown" data-activated="true">
			<div class="pmpro_section_toggle">
				<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
					<?php esc_html_e( '"View As..."', 'pmpro-toolkit' ); ?>
				</button>
			</div>
			<div class="pmpro_section_inside">
				<?php
					global $wpdb;
					// get example level info
					$level = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->pmpro_membership_levels . ' LIMIT 1' );
					if( ! empty( $level ) ) {
						$level_name = $level->name;
						$level_id = $level->id;
						$example_link = '<a href="' . add_query_arg( 'pmprodev_view_as', $level_id, home_url() ) . '">' . add_query_arg( 'pmprodev_view_as', $level_id, home_url() ) . '</a>';
				?>
				<p>
					<?php esc_html_e( 'Enabling "View as..." will allow admins to view any page as if they had any membership level(s) for a brief period of time.', 'pmpro-toolkit' ); ?>
				</p>
				<p>
					<?php echo sprintf( esc_html__( 'To use it, add the query string parameter %s to your URL, passing a series of level IDs separated by hyphens.', 'pmpro-toolkit' ), '<code>pmprodev_view_as</code>' ); ?>
				</p>
				<p>
					<?php echo sprintf( __( 'For example, view your homepage as %s with the link %s', 'pmpro-toolkit' ), $level_name, $example_link ); ?>
				</p>
				<p>
					<?php esc_html_e( 'Use "r" to reset the "View as" filter, and any nonexistent level ID (for example, "n" will never be a level ID) to emulate having no membership level.', 'pmpro-toolkit' );  } ?>
				</p>

				<table class="form-table">
				<tbody>
					<!--  Expire Memberships row  -->
					<tr>
						<th scope="row" valign="top">
							<label for="view_as_enabled"><?php esc_html_e( 'Enable "View As..."', 'pmpro-toolkit' ); ?></label>
						</th>
						<td>
							<input id="view_as_enabled" type="checkbox"  name="pmprodev_options[view_as_enabled]" value="1" <?php checked( $pmprodev_options['view_as_enabled'], 1, true ); ?>>
							<label for="view_as_enabled"><?php _e( 'Check to enable the View As feature.', 'pmpro-toolkit' ); ?></label>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
		<p class="submit topborder">
			<input name="savesettings" type="submit" class="button-primary" value="<?php esc_html_e( 'Save Settings', 'pmpro-toolkit' ); ?>">
		</p>
	</form>
	</div>
</div>
<div class="section-database-wrapper" <?php if( $section != 'scripts' ){ ?> style="display:none"><?php } ?>>
	<?php require_once 'scripts.php'; ?>
</div> 
<div class="section-migration-wrapper" <?php if( $section != 'migration' ){ ?> style="display:none"><?php } ?>>
	<?php require_once 'migration.php'; ?>
<?php





		