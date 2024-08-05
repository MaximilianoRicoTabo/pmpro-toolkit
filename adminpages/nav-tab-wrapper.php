<nav class="nav-tab-wrapper">
		<a href="<?php echo admin_url( 'admin.php?page=pmpro-toolkit' );?>" class="nav-tab<?php if($view == 'pmpro-toolkit') { ?> nav-tab-active<?php } ?>"><?php esc_html_e('Toolkit Options', 'pmpro-toolkit' );?></a>
		<a href="<?php echo admin_url( 'admin.php?page=pmpro-dev-database-scripts' );?>" class="nav-tab<?php if($view == 'pmprodev-database-scripts') { ?> nav-tab-active<?php } ?>"><?php esc_html_e('Database Scripts', 'pmpro-toolkit' );?></a>
		<a href="<?php echo admin_url( 'admin.php?page=pmpro-dev-migration-assistant' );?>" class="nav-tab<?php if($view == 'pmprodev-migration-assistant') { ?> nav-tab-active<?php } ?>"><?php esc_html_e('Migration Assistant', 'pmpro-toolkit' );?></a>
</nav>