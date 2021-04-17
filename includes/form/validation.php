<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Validate and sanitize the form data
 * @param array $data The form data
 * @param bool $partial Whether only partial data has been sent (for 1st Ajax request for timezone offset)
 * @param bool $skip_place Whether to skip validating the place
 * @param bool $force_name Whether to force validating of the name
 * @return mixed|array|string Array of form values if all is valid, otherwise the error string
 */
function zp_validate_form( $data, $partial = false, $skip_place = false, $force_name = false ) {
	$out = $data;
	$out['month'] = ( isset( $data['month'] ) && is_numeric( trim( $data['month'] ) ) ) ? $data['month'] : '';
	$out['day']	= ( isset( $data['day'] ) && is_numeric( trim( $data['day'] ) ) ) ? trim( $data['day'] ) : '';
	$out['year'] = ( isset( $data['year'] ) && is_numeric( trim( $data['year'] ) ) ) ? trim( $data['year'] ) : '';
	$out['hour'] = ( isset( $data['hour'] ) && is_numeric( trim( $data['hour'] ) ) ) ? trim( $data['hour'] ) : '';
	$out['minute'] = ( isset( $data['minute'] ) && is_numeric( trim( $data['minute'] ) ) ) ? trim( $data['minute'] ) : '';
	$out['geo_timezone_id']	= ! empty( $data['geo_timezone_id'] ) ? sanitize_text_field( $data['geo_timezone_id'] ) : '';
	$out['place'] = ! empty( $data['place'] ) ? sanitize_text_field( $data['place'] ) : '';
	$out['zp_lat_decimal'] = ( isset( $data['zp_lat_decimal'] ) && is_numeric( trim( $data['zp_lat_decimal'] ) ) ) ? trim( $data['zp_lat_decimal'] ) : '';
	$out['zp_long_decimal'] = ( isset( $data['zp_long_decimal'] ) && is_numeric( trim( $data['zp_long_decimal'] ) ) ) ? trim( $data['zp_long_decimal'] ) : '';
	$out['unknown_time'] = isset( $data['unknown_time'] ) ? $data['unknown_time'] : '';
	$out['zp-report-variation'] = empty( $data['zp-report-variation'] ) ? 'birthreport' : sanitize_text_field( $data['zp-report-variation'] );

	// Validate date.
	if ( "" == $out['month'] || "" == $out['day'] || "" == $out['year'] ) {
		return apply_filters( 'zp_form_error_notice_empty_date', __( 'Bitte wähle ein Geburtsdatum', 'zodiacpress' ) );
	} else {
		if ( ! $validdate = checkdate( $out['month'], $out['day'], $out['year'] ) ) {
			return __('Geburtsdatum ist nicht gültig', 'zodiacpress');
		}
	}

	// If unknown time is checked, skip time validation and set time to noon.
	if ( ! empty( $out['unknown_time'] ) ) {
		$out['hour']	= 12;
		$out['minute']	= '00';
	} else {

		// Validate time.

		// Time values should be 2 characters
		if ( strlen( utf8_decode( $out['hour'] ) ) !== 2 || strlen( utf8_decode( $out['minute'] ) ) !== 2  ) {

			global $zodiacpress_options;
			$allow_unknown_bt_key = $out['zp-report-variation'] . '_allow_unknown_bt';

			if ( empty( $zodiacpress_options[ $allow_unknown_bt_key ] ) ||
				in_array( $out['zp-report-variation'], apply_filters( 'zp_reports_require_birthtime', array() ) ) )
			{
				$msg = __( 'Bitte wähle eine Geburtszeit', 'zodiacpress' );
			} else {
				$msg = __( 'Bitte wähle eine Geburtszeit oder aktiviere das Kontrollkästchen für unbekannte Zeit', 'zodiacpress' );
			}

			return $msg;
		}
		if ( $out['hour'] < '00' || $out['hour'] > 23 ) {
			return __('Wähle eine gültige Geburtsstunde.', 'zodiacpress' );
		}
		if ( $out['minute'] < 0 || $out['minute'] > 59 ) {
			return __('Wähle eine Geburtsminute zwischen 0 und 59.', 'zodiacpress' );
		}
	}

	// Validate location.
	if ( ( empty( $skip_place ) && ( empty( $out['geo_timezone_id'] ) || empty( $out['place'] ) ) )
		|| "" == $out['zp_lat_decimal'] || "" == $out['zp_long_decimal'] ) {
		return __( 'Bitte wähle eine Geburtsstadt', 'zodiacpress' );
	}

	// If this is a partial submission, we are done.
	if ( $partial ) {
		return $out;
	}

	// Validate the remaining fields (on full final submission)
	// Require name only if field is shown for this type of report.
	if ( apply_filters( 'zp_form_show_name_field', true, $out['zp-report-variation'] ) || $force_name ) {
		if ( empty( $data['name'] ) ) {
			return __('Bitte gib einen Namen ein', 'zodiacpress');
		}
	}
	$out['name'] = ! empty( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';

	// Validate offset.
	$out['zp_offset_geo'] = isset( $data['zp_offset_geo'] ) ? sanitize_text_field( $data['zp_offset_geo'] ) : '';
	$out['zp_offset_geo'] = is_numeric( $out['zp_offset_geo'] ) ? $out['zp_offset_geo'] : '';
	$out['zp_offset_geo'] = trim( $out['zp_offset_geo'], '.' );// trim decimal from end, just in case

	/*
	 * Offset must match:
	 * ^-?					Optional negative sign at the start
	 * [0-9]{1,2}			1 or 2 digits
	 * (\.[0-9]{1,2})?$ 	End with optional decimal point and 1 or 2 digits
	 *
	 */
	if ( ! preg_match( '/^-?[0-9]{1,2}(\.[0-9]{1,2})?$/', $out['zp_offset_geo'] ) ) {

		return __( 'Der UTC-Zeitversatz muss eine Zahl sein (wie 5). Füge bei Bedarf ein negatives Vorzeichen oder einen Dezimalpunkt ein (z.B. -9.5). Wenn der Offset automatisch berechnet werden soll, wähle die Geburtsstadt erneut aus und klicke auf Weiter.', 'zodiacpress' );
	}

	// Validate the sidereal hidden field
	$out['sidereal'] = empty( $data['zp_report_sidereal'] ) ? false : sanitize_text_field( $data['zp_report_sidereal'] );
	if ( ! isset( zp_get_sidereal_methods()[ $out['sidereal'] ] ) ) {
		// Allow faganbradley without slash
		$out['sidereal'] = ( 'faganbradley' === $out['sidereal'] ) ? 'fagan/bradley' : false;
	}

	// Validate the custom house system hidden field
	$out['house_system'] = empty( $data['zp_report_house_system'] ) ? false : sanitize_text_field( $data['zp_report_house_system'] );
	if ( ! isset( zp_get_house_systems()[ $out['house_system'] ] ) ) {
		$out['house_system'] = false;
	}
	$out['shorten'] = empty( $data['shorten'] ) ? 0 : sanitize_text_field( $data['shorten'] );
	return $out;
}