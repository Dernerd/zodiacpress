<?php
/**
 * Admin View: Notice - Install
 * @since 1.8
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="updated zp-atlas-message">
	<p><strong><?php echo __( 'Willkommen bei ZodiacPress', 'zodiacpress' ); ?></strong> &#8211; 
			<?php _e( 'Du bist fast bereit, Berichte zu erstellen.', 'zodiacpress' ); ?></p>
	<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=zodiacpress-settings&tab=misc' ) ); ?>" class="button-primary"><?php _e( 'Gehe zum Setup', 'zodiacpress' ); ?></a> <button id="zp-skip-setup" class="button-secondary"><?php _e( 'Setup überspringen', 'zodiacpress' ); ?></button></p>
</div>
