<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Shows the tools panel which contains ZP-specific tools.
 */
function zp_tools_page() {
	$active_tab = ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'cleanup';
	$tabs = zp_get_tools_tabs();
	$current_tab_name = $tabs[ $active_tab ];
	?>
	<div id="zp-tools" class="wrap">
	<?php zp_admin_links(); ?>
	<nav class="nav-tab-wrapper clear">
		<?php foreach( zp_get_tools_tabs() as $tab_id => $tab_name ) {
			$active = $active_tab == $tab_id ? ' nav-tab-active' : '';
			$tab_url = add_query_arg( array( 'tab' => $tab_id ) );
			// Remove the arg that triggers the admin notice
			$tab_url = remove_query_arg( 'zp-done', $tab_url );

			echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">' . esc_html( $tab_name ) . '</a>';
		}
		?>
	</nav>
	<h1 class="screen-reader-text"><?php echo $current_tab_name; ?></h1>
	<div class="metabox-holder">
		<?php do_action( 'zp_tools_tab_' . $active_tab ); ?>
	</div>
	</div>
	<?php
}
function zp_get_tools_tabs() {
	$tabs = array(
		'cleanup' 		=> __( 'Aufräumen', 'zodiacpress' ),
		'import_export' => __( 'Exportieren/Importieren', 'zodiacpress' )
	);
	return apply_filters( 'zp_tools_tabs', $tabs );
}
/**
 * Build the link that triggers a specific tool.
 *
 * @param  string $tool Tool to trigger
 * @return string URL that triggers the tool function
 */
function zp_tool_link( $tool ) {
	$args['action']	= $tool;
	$args['_nonce']	= wp_create_nonce( 'zp_' . $tool );
	return esc_url( add_query_arg( $args, admin_url( 'admin-post.php' ) ) );
}
function zp_get_cleanup_tools() {
	$tools = array(
		'natal_in_signs'	=> array(
								'label'	=> __( 'Geburtsplaneten in Zeichen', 'zodiacpress' ),
								'desc'	=> __( 'Lösche alle Interpretationstexte für Geburtsplaneten/-punkte in den Zeichen.', 'zodiacpress' )
		),
		'natal_in_houses'	=> array(
								'label'	=> __( 'Geburtsplaneten in Häusern', 'zodiacpress' ),
								'desc'	=> __( 'Lösche alle Interpretationstexte für Geburtsplaneten/-punkte in den Häusern.', 'zodiacpress' )
		),
		'natal_aspects'		=> array(
								'label'	=> __( 'Geburtsaspekte', 'zodiacpress' ),
								'desc'	=> __( 'Lösche alle Interpretationstexte für Geburtsaspekte.', 'zodiacpress' )
		)
	);
	return apply_filters( 'zp_cleanup_tools', $tools );
}
/**
 * Display Cleanup Tools tab
 */
function zp_tools_cleanup_display() {
	?>
	<div class="stuffbox">
		<div class="inside">
		<h2><?php _e( 'Interpretationen löschen', 'zodiacpress' ); ?></h2>
		<p><?php _e( 'Verwende diese Tools, um Deinen Interpretationstext <strong>dauerhaft</strong> zu löschen. Beachte dass durch Klicken auf diese Schaltflächen Ihre Interpretationen <strong>dauerhaft</strong> gelöscht werden.', 'zodiacpress' ); ?></p>
		<table class="widefat zp-tools-table" id="zp-tools-cleanup">
			<?php foreach( zp_get_cleanup_tools() as $id => $tool ) { 
				$toolkey = 'erase_' . $id; ?>
				<tr>
					<td class="row-title"><label for="tablecell"><?php echo esc_html( $tool['label'] ); ?></label></td>
					<td><a href="<?php echo esc_url( zp_tool_link( $toolkey ) ); ?>" class="button-secondary"><?php _e( 'Löschen', 'zodiacpress' ); ?></a>
						<span class="zp-tools-desc"><?php echo esc_html( $tool['desc'] ); ?></span></td>
				</tr>
			<?php }
			do_action( 'zp_system_tools_cleanup_table' ); ?>

		</table>
		</div>
	</div>
	<?php
}
add_action( 'zp_tools_tab_cleanup', 'zp_tools_cleanup_display' );

/**
 * Display the tools export/import tab
 */
function zp_tools_import_export_display() {
	?>
	<div class="stuffbox">
		<div class="inside">
			<h2><?php _e( 'Interpretationen exportieren', 'zodiacpress' ); ?></h2>
			<p><?php _e( 'Exportiere Deine ZodiacPress-Interpretationen für diese Seite als .json-Datei. Auf diese Weise kannst Du Deine Interpretationen einfach in eine andere Seite importieren.', 'zodiacpress' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
				<p>
					<input type="hidden" name="action" value="zp_export_interps" />
					<?php wp_nonce_field( 'zp_export_interps_nonce', 'zp_export_interps_nonce' ); ?>
					<?php submit_button( __( 'Interpretationen exportieren', 'zodiacpress' ), 'secondary', 'submit', false ); ?>
				</p>
			</form>
		</div>
	</div>
	<div class="stuffbox">
		<div class="inside">
			<h2><?php _e( 'Interpretationen importieren', 'zodiacpress' ); ?></h2>
			<p><?php _e( 'Importiere Deine ZodiacPress-Interpretationen aus einer .json-Datei. Diese Datei erhältst Du, indem Du die Interpretationen über die Schaltfläche oben auf eine andere Seite exportierst.', 'zodiacpress' ); ?></p>
			<p><?php _e( 'HINWEIS: IMPORTIERTE AUSLEGUNGEN ÜBERSCHREIBEN JEGLICHE AKTUELLE BESTIMMTE AUSLEGUNGEN AUF DIESER WEBSITE VOLLSTÄNDIG. BESTEHENDE INTERPRETATIONEN AUF DIESER WEBSEITE WERDEN GELÖSCHT.', 'zodiacpress' ); ?></p>
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
				<p>
					<input type="file" name="import_file"/>
				</p>
				<p>
					<input type="hidden" name="action" value="zp_import_interps" />
					<?php wp_nonce_field( 'zp_import_interps_nonce', 'zp_import_interps_nonce' ); ?>
					<?php submit_button( __( 'Interpretationen importieren', 'zodiacpress' ), 'secondary', 'submit', false ); ?>
				</p>
			</form>
		</div>
	</div>
	<div class="stuffbox">
		<div class="inside">
			<h2><?php _e( 'Exporteinstellungen', 'zodiacpress' ); ?></h2>
			<p><?php _e( 'Exportiere die ZodiacPress-Einstellungen für diese Seite als JSON-Datei. Auf diese Weise kannst Du die Konfiguration einfach in eine andere Seite importieren.', 'zodiacpress' ); ?></p>
			<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
				<p>
					<input type="hidden" name="action" value="zp_export_settings" />
					<?php wp_nonce_field( 'zp_export_settings_nonce', 'zp_export_settings_nonce' ); ?>
					<?php submit_button( __( 'Exporteinstellungen', 'zodiacpress' ), 'secondary', 'submit', false ); ?>
				</p>
			</form>
		</div>
	</div>
	<div class="stuffbox">
		<div class="inside">
			<h2><?php _e( 'Importeinstellungen', 'zodiacpress' ); ?></h2>
			<p><?php _e( 'Importiere die ZodiacPress-Einstellungen aus einer .json-Datei. Diese Datei erhältst Du, indem Du die Einstellungen über die Schaltfläche oben auf einee anderen Seite exportierst.', 'zodiacpress' ); ?></p>
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
				<p>
					<input type="file" name="import_file"/>
				</p>
				<p>
					<input type="hidden" name="action" value="zp_import_settings" />
					<?php wp_nonce_field( 'zp_import_settings_nonce', 'zp_import_settings_nonce' ); ?>
					<?php submit_button( __( 'Importeinstellungen', 'zodiacpress' ), 'secondary', 'submit', false ); ?>
				</p>
			</form>
		</div>
	</div>
	<?php
}
add_action( 'zp_tools_tab_import_export', 'zp_tools_import_export_display' );

/**
 * Process a settings export that generates a .json file of the ZodiacPress settings
 *
 * @return      void
 */
function zp_export_settings() {
	if ( ! wp_verify_nonce( $_POST['zp_export_settings_nonce'], 'zp_export_settings_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		return;
	}
	$settings = array();
	$settings = get_option( 'zodiacpress_settings' );
	ignore_user_abort( true );
	if ( zp_is_func_enabled( 'set_time_limit' ) ) {
		set_time_limit( 0 );
	}
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=zp-settings-export-' . date( 'm-d-Y' ) . '.json' );
	header( "Expires: 0" );
	echo json_encode( $settings );
	exit;
}
add_action( 'admin_post_zp_export_settings', 'zp_export_settings' );

/**
 * Process a settings import from a json file
 *
 * @return void
 */
function zp_import_settings() {
	if ( ! wp_verify_nonce( $_POST['zp_import_settings_nonce'], 'zp_import_settings_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		return;
	}
	if ( zp_get_file_extension( $_FILES['import_file']['name'] ) != 'json' ) {
		wp_die( __( 'Bitte lade eine gültige .json-Datei hoch', 'zodiacpress' ), __( 'Fehler', 'zodiacpress' ), array( 'response' => 400 ) );
	}

	if ( 0 !== strpos( $_FILES['import_file']['name'], 'zp-settings-export' ) ) {
		wp_die( __( 'Bitte lade eine gültige ZodiacPress zp-settings-export-Datei hoch', 'zodiacpress' ), __( 'Fehler', 'zodiacpress' ), array( 'response' => 400 ) );	
	}

	$import_file = $_FILES['import_file']['tmp_name'];
	if ( empty( $import_file ) ) {
		wp_die( __( 'Bitte lade eine Datei zum Importieren hoch', 'zodiacpress' ), __( 'Fehler', 'zodiacpress' ), array( 'response' => 400 ) );
	}
	// Retrieve the settings from the file and convert the json object to an array
	$settings = zp_object_to_array( json_decode( file_get_contents( $import_file ) ) );

	update_option( 'zodiacpress_settings', $settings );
	wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=zodiacpress-tools&tab=import_export&zp-done=settings-imported' ) ) ); exit;
}
add_action( 'admin_post_zp_import_settings', 'zp_import_settings' );

/**
 * Process an Interpretations export that generates a .json file of your ZP Interpretations
 *
 * @return      void
 */
function zp_export_interps() {
	if ( ! wp_verify_nonce( $_POST['zp_export_interps_nonce'], 'zp_export_interps_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		return;
	}

	// Collect all interpretions options
	$option_names = zp_get_all_interps_options_names();
	$interps = array();
	foreach ( $option_names as $option ) {
		$interps[ $option ] = get_option( $option );
	}

	ignore_user_abort( true );
	if ( zp_is_func_enabled( 'set_time_limit' ) ) {
		set_time_limit( 0 );
	}
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=zp-interpretations-' . date( 'm-d-Y' ) . '.json' );
	header( "Expires: 0" );

	echo json_encode( $interps );
	exit;
}
add_action( 'admin_post_zp_export_interps', 'zp_export_interps' );

/**
 * Process an Interpretations import from a json file
 *
 * @return void
 */
function zp_import_interps() {
	if ( ! wp_verify_nonce( $_POST['zp_import_interps_nonce'], 'zp_import_interps_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		return;
	}

	if ( zp_get_file_extension( $_FILES['import_file']['name'] ) != 'json' ) {
		wp_die( __( 'Bitte lade eine gültige .json-Datei hoch', 'zodiacpress' ), __( 'Fehler', 'zodiacpress' ), array( 'response' => 400 ) );
	}

	if ( 0 !== strpos( $_FILES['import_file']['name'], 'zp-interpretations' ) ) {
		wp_die( __( 'Bitte lade eine gültige ZodiacPress-Interpretationsdatei hoch', 'zodiacpress' ), __( 'Fehler', 'zodiacpress' ), array( 'response' => 400 ) );	
	}

	$import_file = $_FILES['import_file']['tmp_name'];
	if ( empty( $import_file ) ) {
		wp_die( __( 'Bitte lade eine Datei zum Importieren hoch', 'zodiacpress' ), __( 'Fehler', 'zodiacpress' ), array( 'response' => 400 ) );
	}
	// Retrieve the interpretations from the file and convert the json object to an array
	$interps = zp_object_to_array( json_decode( file_get_contents( $import_file ) ) );

	foreach ( $interps as $option => $value ) {

		// Make sure this option is 1 of our interpretations
		$all_possible_interps_options = zp_get_all_interps_options_names();
		if ( in_array( $option, $all_possible_interps_options ) ) {
			update_option( $option, $value );
		}

	}
	wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=zodiacpress-tools&tab=import_export&zp-done=interps-imported' ) ) ); exit;
}
add_action( 'admin_post_zp_import_interps', 'zp_import_interps' );