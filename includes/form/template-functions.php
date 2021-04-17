<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Renders the HTML for the Name form field.
 */
function zp_name_form_field() {
	?>
	<p class="zp-name-field">
		<label for="name" class="zp-form-label"><?php _e( 'Name', 'zodiacpress' ); ?> </label>
		<span class="zp-input-text-wrap"><input id="name" name="name" class="zp-input-text" type="text" /></span>
	</p>
	<?php
}
/**
 * Renders the HTML select options for the Month form field.
 */
function zp_month_select_options() {
	?>
	<option value=""><?php _e('Monat', 'zodiacpress'); ?></option>
	<?php foreach ( zp_get_i18n_months() as $key => $label ) { ?>
		<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
	<?php
	}

}
/**
 * Renders the HTML select options for the Day form field.
 */
function zp_day_select_options() {
	$days 	= range( 1, 31 );
	$labels = zp_i18n_numbers();
	?>
	<option value=""><?php _e('Tag', 'zodiacpress'); ?></option>
	<?php foreach ( $days as $day ) { ?>
		<option value="<?php echo $day; ?>"><?php echo $labels[ $day ]; ?></option>
		<?php
	}
}
/**
 * Renders the HTML select options for the Year form field.
 * @todo must update $accepted_yrs annually to avoid mismatch of key=>value with i18n year. Update it both here and in zp_i18n_years() so that the final year is 1 year into the future.
 */
function zp_year_select_options() {
	?>
	<option value=""><?php _e('Jahr', 'zodiacpress'); ?></option>
	<?php 
	$accepted_yrs = range( 1900, 2021);
	$labels = zp_i18n_years();
	$years = array_combine( $accepted_yrs, $labels );
	arsort( $years );

	// Allow years to be added with filter.
	$years = apply_filters( 'zp_year_select_options', $years );

	foreach ( $years as $year => $label ) {
		?>
		<option value="<?php echo $year; ?>"><?php echo $label; ?></option>
		<?php
	}
}
/**
 * Renders the HTML select options for the Hour form field.
 */
function zp_hour_select_options() {
	$hours = array_slice( zp_i18n_numbers_zeros(), 0, 24, true );
	?>
	<option value=""><?php _e( 'Stunde', 'zodiacpress' ); ?></option>
	<?php

	foreach ( $hours as $hour => $label ) {
		$key = (int) $hour;
		?>
		<option value="<?php echo $hour; ?>"><?php echo $label . ' (' . zp_get_12_hour( $key ) . ')'; ?></option>
	<?php }
}

/**
 * Renders the HTML select options for the Minute form field.
 */
function zp_minute_select_options() {
	?>
	<option value=""><?php _e( 'Minute', 'zodiacpress' ); ?></option>
	<?php
	foreach ( zp_i18n_numbers_zeros() as $minute => $label ) { ?>
		<option value="<?php echo $minute; ?>"><?php echo $label; ?></option>

	<?php }
}
/**
 * Render the Month form field
 */
function zp_month_form_field() {
	?>
	<label for="month" class="screen-reader-text">
	<?php _e( 'Geburtsmonat', 'zodiacpress' ); ?></label>
	<select id="month" name="month" required><?php zp_month_select_options(); ?></select>
	<?php
}
/**
 * Render the Day form field
 */
function zp_day_form_field() {
	?>
	<label for="day" class="screen-reader-text">
	<?php _e( 'Geburtstag', 'zodiacpress' ); ?></label>
	<select id="day" name="day" required><?php zp_day_select_options(); ?></select>
	<?php

}
