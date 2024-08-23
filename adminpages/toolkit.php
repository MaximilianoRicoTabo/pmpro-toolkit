<?php
	// Only admins can access this page.
	if( !function_exists( "current_user_can" ) || ( !current_user_can( "manage_options" ) ) ) {
		die( esc_html__( "You do not have permissions to perform this action.", 'pmpro-toolkit' ) );
	}

	// Load the admin header.
	require_once PMPRO_DIR . '/adminpages/admin_header.php';

	//$section = !empty( $_REQUEST['section']  ) ? sanitize_text_field( $_REQUEST[ 'section' ] ) : 'options';
	$section = pmpro_getParam( 'section', 'REQUEST', 'options', );

?>
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

<?php
	// Show the appropriate section.
	if( 'scripts' == $section ) {
		require_once 'scripts.php';
	} elseif( $section == 'migration' ) {
		require_once( 'migration.php' );
	} else {
		require_once( 'settings.php' );
	}
?>

<?php
// Load the admin footer.
require_once PMPRO_DIR . '/adminpages/admin_footer.php';


