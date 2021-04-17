<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Register font-end styles and scripts
 */
function zp_register_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_style( 'zp', ZODIACPRESS_URL . 'assets/css/zp' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	// for RTL languages
	wp_register_style( 'zp-rtl', ZODIACPRESS_URL . 'assets/css/zp-rtl' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	// core script
	wp_register_script( 'zp', ZODIACPRESS_URL . 'assets/js/zp' . $suffix . '.js', array(), ZODIACPRESS_VERSION );
	wp_localize_script( 'zp', 'zp_strings', zp_script_localization_data() );
}
add_action( 'wp_enqueue_scripts', 'zp_register_scripts' );
/**
 * Register admin-specific scripts and styles.
 */
function zp_admin_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_style( 'zp-admin', ZODIACPRESS_URL . 'assets/css/zp-admin' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	wp_enqueue_style( 'zp-admin' );
	if ( zp_is_admin_page() ) {
		wp_register_script( 'zp-admin', ZODIACPRESS_URL . 'assets/js/admin.js', array(), ZODIACPRESS_VERSION, true );
		wp_enqueue_script( 'zp-admin' );
	}
}
add_action( 'admin_enqueue_scripts', 'zp_admin_scripts', 100 );
/**
 * Get strings for the ZP core script.
 * @since 1.8
 */
function zp_script_localization_data() {
	global $zodiacpress_options;
	// If language is other than English, get lang code to tranlsate Autocomplete cities.
	$wplang = get_locale();
	$langcode = substr( $wplang, 0, 2 );
	$city_list_lang = ( 'en' != $langcode ) ? $langcode : '';
	$geonames_username = empty( $zodiacpress_options['geonames_user'] ) ? '' : trim( $zodiacpress_options['geonames_user'] );	
	$draw = isset( $zodiacpress_options['add_drawing_to_birthreport'] ) ? $zodiacpress_options['add_drawing_to_birthreport'] : '';
	$data = array(
		'lang'		=> $city_list_lang,
		'u'			=> $geonames_username,		
		'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
		'draw'		=> $draw
	);
	return apply_filters( 'zp_localize_script', $data );
}
