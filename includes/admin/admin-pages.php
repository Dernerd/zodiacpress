<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Creates the ZP admin pages
 */
function zp_add_admin_pages() {
	add_menu_page( __( 'ZodiacPress', 'zodiacpress' ), __( 'ZodiacPress', 'zodiacpress' ), 'manage_zodiacpress_interps', 'zodiacpress', 'zp_interpretations_page', 'dashicons-universal-access-alt', '21.9' );

	add_submenu_page( 'zodiacpress', __( 'ZodiacPress Werkzeuge', 'zodiacpress' ), __( 'Werkzeuge', 'zodiacpress' ), 'manage_zodiacpress_settings', 'zodiacpress-tools', 'zp_tools_page' );

	add_submenu_page( 'zodiacpress', __( 'ZodiacPress Einstellungen', 'zodiacpress' ), __( 'Einstellungen', 'zodiacpress' ), 'manage_zodiacpress_settings', 'zodiacpress-settings', 'zp_options_page' );
}
add_action( 'admin_menu', 'zp_add_admin_pages' );
/**
 * Determines whether the current page is a ZP admin page.
 */
function zp_is_admin_page() {
	global $pagenow;
	$page 			= isset( $_GET['page'] ) ? strtolower( sanitize_text_field( $_GET['page'] ) ) : false;
	$found			= false;
	$zp_pages 		= apply_filters( 'zp_admin_pages', array( 'zodiacpress', 'zodiacpress-settings', 'zodiacpress-tools' ) );

	if ( 'admin.php' == $pagenow && in_array( $page, $zp_pages ) ) {
		$found = true;
	}
	return $found;
}
