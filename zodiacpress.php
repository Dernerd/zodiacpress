<?php
/*
Plugin Name: ZodiacPress
Plugin URI: https://n3rds.work/piestingtal_source/zodiacpress-by-psource/
Description: Generiere astrologische Geburtsberichte mit Deinen benutzerdefinierten Interpretationen.
Version: 2.0.6
Author: DerN3rd
Author URI: https://n3rds.work
License: GNU GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: zodiacpress
Domain Path: /languages

Copyright 2016-2020 DerN3rd

This file is part of ZodiacPress.

ZodiacPress is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

ZodiacPress is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with ZodiacPress. If not, see <http://www.gnu.org/licenses/>.
*/
require 'includes/psource-plugin-update/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://n3rds.work//wp-update-server/?action=get_metadata&slug=zodiacpress', 
	__FILE__, 
	'zodiacpress' 
);


if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'ZodiacPress' ) ) {
final class ZodiacPress {
	private static $instance;
	/**
	 * Main ZodiacPress Instance.
	 *
	 * Insures that only one instance of ZodiacPress exists in memory at any one
	 * time.
	 *
	 * @return object|ZodiacPress The one true ZodiacPress
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ZodiacPress ) ) {
			self::$instance = new ZodiacPress;
			self::$instance->setup_constants();
			add_action( 'plugins_loaded', array( self::$instance, 'plugin_loaded' ) );
			add_action( 'init', array( self::$instance, 'languages' ) );
			self::$instance->includes();
		}
		return self::$instance;
	}
	private function setup_constants() {
		if ( ! defined( 'ZODIACPRESS_VERSION' ) ) {
			define( 'ZODIACPRESS_VERSION', '2.0.6' );// @todo update
		}
		if ( ! defined( 'ZODIACPRESS_URL' ) ) {
			define( 'ZODIACPRESS_URL', plugin_dir_url( __FILE__ ) );
		}
		if ( ! defined( 'ZODIACPRESS_PATH' ) ) {
			define( 'ZODIACPRESS_PATH', plugin_dir_path( __FILE__ ) );
		}
	}
	private function includes() {
		global $zodiacpress_options;
		include_once ZODIACPRESS_PATH . 'includes/admin/settings/register-settings.php';
		$zodiacpress_options = get_option( 'zodiacpress_settings' );
		include_once ZODIACPRESS_PATH . 'includes/ajax-functions.php';
		include_once ZODIACPRESS_PATH . 'includes/astro-functions.php';
		include_once ZODIACPRESS_PATH . 'includes/class-zp-birth-report.php';
		include_once ZODIACPRESS_PATH . 'includes/class-zp-chart.php';
		include_once ZODIACPRESS_PATH . 'includes/class-zp-ephemeris.php';
		include_once ZODIACPRESS_PATH . 'includes/misc-functions.php';
		include_once ZODIACPRESS_PATH . 'includes/scripts.php';
		include_once ZODIACPRESS_PATH . 'includes/time-functions.php';
		include_once ZODIACPRESS_PATH . 'includes/form/template-functions.php';
		include_once ZODIACPRESS_PATH . 'includes/form/template.php';
		include_once ZODIACPRESS_PATH . 'includes/shortcode.php';
		// anything that handles ajax scripts must be loaded both in front and back.
		include_once ZODIACPRESS_PATH . 'includes/form/validation.php';
		include_once ZODIACPRESS_PATH . 'includes/chart-drawing.php';
		include_once ZODIACPRESS_PATH . 'includes/class-zp-customize.php';
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			include_once ZODIACPRESS_PATH . 'includes/admin/settings/register-interpretations.php';
			include_once ZODIACPRESS_PATH . 'includes/admin/settings/display-interpretations.php';
			include_once ZODIACPRESS_PATH . 'includes/admin/settings/display-settings.php';
			include_once ZODIACPRESS_PATH . 'includes/admin/admin-functions.php';
			include_once ZODIACPRESS_PATH . 'includes/admin/admin-pages.php';
			include_once ZODIACPRESS_PATH . 'includes/admin/tools.php';
		}
	}
	public function languages() {
		load_plugin_textdomain( 'zodiacpress', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
	public function plugin_loaded() {
		zp_is_sweph_executable();// Set necessary file permissions
	}
	/**
	 * Fired when the plugin is activated.
	 *
	 * @param bool $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide = false ) {
		global $wpdb;
		if ( is_multisite() && $network_wide ) {
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				self::single_activate();
				restore_current_blog();
			}
		} else {
			self::single_activate();
		}
	}
	/**
	 * Fired for each blog when the plugin is activated.
	 */
	private static function single_activate() {
		// If no existing settings, set up default ones.
		if ( false == get_option( 'zodiacpress_settings' ) ) {
			$options = array();
			foreach( zp_get_registered_settings() as $tab => $sections ) {	
				foreach( $sections as $section => $settings) {
					foreach ( $settings as $option ) {
						if ( ! empty( $option['std'] ) ) {
							$options[ $option['id'] ] = $option['std'];
						}
					}
				}
			}
			update_option( 'zodiacpress_settings', $options );
		}
		$admin = get_role( 'administrator' );
		if ( null != $admin ) {
			$admin->add_cap( 'manage_zodiacpress_settings' );
			$admin->add_cap( 'manage_zodiacpress_interps' );
		}
		set_transient( 'zodiacpress_activating', true, 5 );
	}
}
} // End class_exists check.
ZodiacPress::instance();
register_activation_hook( __FILE__, array( 'ZodiacPress', 'activate' ) );
function zp_deactivate() {
    delete_option( 'zp_cleanup_deprecated_options_v19' );
}
register_deactivation_hook( __FILE__, 'zp_deactivate' );
