<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_shortcode( 'birthreport', 'zp_birthreport_shortcode' );
/**
 * The form displayed by the birth report shortcode.
 */
function zp_birthreport_shortcode( $atts ) {
	$report_atts = shortcode_atts( array(
		'report'		=> 'birthreport',
		'sidereal'		=> false,
		'house_system'	=> false,
		'sell'			=> false,
		'shorten'		=> 0
	), $atts, 'birthreport' );

	wp_enqueue_style( 'zp' );
	if ( is_rtl() ) {
		wp_enqueue_style( 'zp-rtl' );
	}
	wp_enqueue_script( 'zp' );
	do_action( 'zp_report_shortcode_before', $report_atts );// This hook can be used by addons to swap out the .js
	ob_start();
	?>
	<div id="zp-form-wrap">
		<?php
		zp_form( 'birthreport', $report_atts );
		do_action( 'zp_form_after', $report_atts );
		?>
	</div><!-- #zp-form-wrap -->
	<div id="zp-report-wrap" class="zp-report-<?php echo esc_attr( $report_atts['report'] ); ?>">
		<?php
		// allow Start Over link to be manipulated with filter
		if ( apply_filters( 'zp_show_start_over_link', true, $report_atts['report'] ) ) { ?>
			<p class="zp-report-backlink">
				<a href="<?php the_permalink(); ?>"><?php _e('Von vorn anfangen', 'zodiacpress'); ?></a>
			</p>
		<?php
		}
		do_action( 'zp_birthreport_content_before', array( 'report' => $report_atts['report'] ) );
		?>
		<div id="zp-report-content"></div><!-- will be filled by ajax -->
	</div>
	<?php
	return ob_get_clean();
}