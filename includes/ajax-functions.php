<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Handles ajax request to calculate timezone offset and send back to form fields
 */
function zp_ajax_get_time_offset() {
	$offset_geo = null;
	$validated = zp_validate_form( $_POST, true );
	if ( ! is_array( $validated )  ) {
		// We have an error
		echo json_encode( array( 'error' => $validated ) );
		wp_die();
	}
	$dtstamp = strftime("%Y-%m-%d %H:%M:%S", zp_mktime( $validated['hour'], $validated['minute'], $validated['month'], $validated['day'], $validated['year'] ));

	// get time offset
	$offset_geo = $validated['geo_timezone_id'] ? zp_get_timezone_offset( $validated['geo_timezone_id'], $dtstamp ) : null;

	echo json_encode( array( 'offset_geo' => $offset_geo ) );
	wp_die();
}
add_action( 'wp_ajax_zp_tz_offset', 'zp_ajax_get_time_offset' );
add_action( 'wp_ajax_nopriv_zp_tz_offset', 'zp_ajax_get_time_offset' );
/**
 * Handles ajax request to get the Birth Report upon form submission.
 */
function zp_ajax_get_birthreport() {
	$validated = zp_validate_form( $_POST );
	$image = '';
	if ( ! is_array( $validated ) ) {
		echo json_encode( array( 'error' => $validated ) );
		wp_die();
	}
	$chart = ZP_Chart::get_instance( $validated );
	if ( empty( $chart->planets_longitude ) ) {
		$report = __( 'Something went wrong.', 'zodiacpress' );
	} else {
		$birth_report = new ZP_Birth_Report( $chart, $validated );
		$report = wp_kses_post( $birth_report->get_report() );
		// get image seperately because wp_kses_post does not allow data uri
		// Add the image by default only for the "only chart drawing" report
		if ( 'drawing' === $validated['zp-report-variation'] ) {
			$report .= wp_kses_post( $birth_report->header() );
			$report .= zp_get_chart_drawing( $chart );
		} else {
			$image = zp_maybe_get_chart_drawing( $validated, $chart );
		}
	}

	echo json_encode( array(
		'report' => $report,
		'image' => $image
	) );

	wp_die();
}
add_action( 'wp_ajax_zp_birthreport', 'zp_ajax_get_birthreport' );
add_action( 'wp_ajax_nopriv_zp_birthreport', 'zp_ajax_get_birthreport' );
/**
 * Handles ajax request to get an updated chartwheel image for the live color preview for customizer.
 */
function zp_ajax_get_customizer_image() {
	$colors = array();
	foreach( $_POST['post_data'] as $k => $color ) {
		$colors[ $k ] = sanitize_hex_color( $color );
	}
	$image = zp_get_sample_chart_drawing( $colors );
	echo json_encode( array( 'image' => $image ) );
	wp_die();
}
add_action( 'wp_ajax_zp_customize_preview_image', 'zp_ajax_get_customizer_image' );
