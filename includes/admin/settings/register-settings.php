<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Add all settings sections and fields
 *
 * @return void
*/
function zp_register_settings() {
	if ( false == get_option( 'zodiacpress_settings' ) ) {
		add_option( 'zodiacpress_settings' );
	}

	foreach ( zp_get_registered_settings() as $tab => $sections ) {
		foreach ( $sections as $section => $settings) {

			add_settings_section(
				'zodiacpress_settings_' . $tab . '_' . $section,
				__return_null(),
				'__return_false',
				'zodiacpress_settings_' . $tab . '_' . $section
			);

			foreach ( $settings as $option ) {

				$name = isset( $option['name'] ) ? $option['name'] : '';
				add_settings_field(
					'zodiacpress_settings[' . $option['id'] . ']',
					$name,
					function_exists( 'zp_' . $option['type'] . '_callback' ) ? 'zp_' . $option['type'] . '_callback' : 'zp_missing_callback',
					'zodiacpress_settings_' . $tab . '_' . $section,
					'zodiacpress_settings_' . $tab . '_' . $section,
					array(
						'section'     => $section,
						'id'          => isset( $option['id'] )          ? $option['id']          : null,
						'desc'        => ! empty( $option['desc'] )      ? $option['desc']        : '',
						'name'        => isset( $option['name'] )        ? $option['name']        : null,
						'size'        => isset( $option['size'] )        ? $option['size']        : null,
						'options'     => isset( $option['options'] )     ? $option['options']     : '',
						'std'         => isset( $option['std'] )         ? $option['std']         : '',
						'class'		=> isset( $option['class'] )	? $option['class'] : '',
					)
				);
			}
		}

	}
	register_setting( 'zodiacpress_settings', 'zodiacpress_settings', 'zp_settings_sanitize' );
}
add_action( 'admin_init', 'zp_register_settings' );

/**
 * Retrieve the array of plugin settings
 *
 * @return array
*/
function zp_get_registered_settings() {
	/**
	 * 'Whitelisted' ZP settings, filters are provided for each settings
	 * section to allow extensions to add their own settings
	 */
	$zp_settings = array(
		/** Natal Settings */
		'natal' => apply_filters( 'zp_settings_natal',
			array(
				'main' => array(
					'planet_settings' => array(
						'id'	=> 'planet_settings',
						'name'	=> '<h3>' . __( 'Planeten- und Punkteinstellungen', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					'enable_planet_signs' => array(
						'id'		=> 'enable_planet_signs',
						'name'		=> __( 'Aktiviere Planeten (und Punkte) in Zeichen', 'zodiacpress' ),
						'desc'		=> __( 'Wähle aus, welche im Abschnitt "In den Schildern" des Berichts angezeigt werden sollen.', 'zodiacpress' ),
						'type'		=> 'multicheck',
						'options'	=> zp_get_planets(),
						'std'		=> zp_get_planets( false, 7 )
					),
					'enable_planet_houses' => array(
						'id'		=> 'enable_planet_houses',
						'name'		=> __( 'Aktiviere Planeten (und Punkte) in Häusern', 'zodiacpress' ),
						'desc'		=> __( 'Wähle aus, welche im Abschnitt "In den Häusern" des Berichts angezeigt werden sollen.', 'zodiacpress' ),
						'type'		=> 'multicheck',
						'options'	=> zp_get_planets( true ),
						'std'		=> zp_get_planets( true )
					),
				),
				'aspects' => array(
					'aspects_settings' => array(
						'id'	=> 'aspects_settings',
						'name'	=> '<h3>' . __( 'Aspekteinstellungen', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					'enable_aspects' => array(
						'id'		=> 'enable_aspects',
						'name'		=> __( 'Aspekte aktivieren', 'zodiacpress' ),
						'desc'		=> __( 'Wähle aus, welche Aspekte im Bericht angezeigt werden sollen.', 'zodiacpress' ),
						'type'		=> 'multicheck',
						'options'	=> zp_get_aspects(),
						'std'		=> zp_get_aspects()
					),
					'enable_planet_aspects' => array(
						'id'		=> 'enable_planet_aspects',
						'name'		=> __( 'Aspekte zu Planeten (und Punkten)', 'zodiacpress' ),
						'desc'		=> __( 'Wähle aus, für welche Planeten/Punkte Aspekte berechnet werden sollen.', 'zodiacpress' ),
						'type'		=> 'multicheck',
						'options'	=> zp_get_planets(),
						'std'		=> zp_get_planets( false, 10 )
					),
				),
				'report'	=> array(
					'report_settings' => array(
						'id'	=> 'report_settings',
						'name'	=> '<h3>' . __( 'Bildschirmeinstellungen', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					'add_drawing_to_birthreport' => array(
								'id'	=> 'add_drawing_to_birthreport',
								'name'	=> __( 'Diagrammrad zum Natal-Bericht hinzufügen', 'zodiacpress' ),
								'type'	=> 'select',
								'desc'	=> __( 'Möchtest Du die Diagrammzeichnung zum Geburtsbericht hinzufügen?', 'zodiacpress' ),
								'options'	=> array(
									'no' => __( 'nicht hinzufügen', 'zodiacpress' ),
									'bottom' => __( 'Zum Ende des Berichts hinzufügen', 'zodiacpress' ),
									'top' => __( 'Zum Anfang des Berichts hinzufügen', 'zodiacpress' ),
								),
								'std'		=> 'no'
					),
					'birthreport_intro' => array(
						'id'	=> 'birthreport_intro',
						'name'	=> __( 'Geburtsbericht Intro', 'zodiacpress' ),
						'type'	=> 'textarea',
						'desc'	=> __( 'Optionaler "Einführungstext" für den Geburtsbericht.', 'zodiacpress' )
					),
					'birthreport_closing' => array(
						'id'	=> 'birthreport_closing',
						'name'	=> __( 'Abschluss des Geburtsberichts', 'zodiacpress' ),
						'type'	=> 'textarea',
						'desc'	=> __( 'Optionaler "Schließen"-Text für den Geburtsbericht. Dies wird am Ende des Berichts angezeigt.', 'zodiacpress' )
					),
					'hide_empty_titles' => array(
						'id'	=> 'hide_empty_titles',
						'name'	=> __( 'Leere Titel ausblenden', 'zodiacpress' ),
						'type'	=> 'checkbox',
						'desc'	=> __( 'Titel für Stücke ausblenden, die keinen Interpretationstext enthalten.', 'zodiacpress' ),
						'class' => 'zp-setting-checkbox-label'
					),
				),

				'technical'	=> array(
					'tech_settings' => array(
						'id'	=> 'tech_settings',
						'name'	=> '<h3>' . __( 'Technische Einstellungen', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					'birthreport_allow_unknown_bt' => array(
						'id'	=> 'birthreport_allow_unknown_bt',
						'name'	=> __( 'Unbekannte Geburtszeit zulassen', 'zodiacpress' ),
						'type'	=> 'checkbox',
						'desc'	=> __( 'Ermögliche Personen mit unbekannten Geburtszeiten, einen Geburtsbericht zu erstellen. Wenn diese Option aktiviert ist, können sie einen Basisbericht erstellen, der Elemente ausschließt, für die eine Geburtszeit erforderlich ist (d.H. Häuser, Mond, Aszendent, Midheaven, Vertex und Teil des Glücks).', 'zodiacpress' ),
						'class' => 'zp-setting-checkbox-label'
					),
				)

			)
		),
		'drawing' => apply_filters( 'zp_settings_drawing',
			array(
				'main' => array(
					'drawing_allow_unknown_bt' => array(
								'id'	=> 'drawing_allow_unknown_bt',
								'name'	=> __( 'Unbekannte Geburtszeit zulassen', 'zodiacpress' ),
								'type'	=> 'checkbox',
								'std'	=> 1,
								'desc'	=> __( 'Ermögliche Personen mit unbekannten Geburtszeiten, eine Diagrammradzeichnung zu erhalten. Ihr Diagramm wird für 12:00 Uhr gezeichnet. In ihrer Karte werden Mond, Aszendent, Midheaven, Teil von Fortune und Vertex weggelassen.', 'zodiacpress' ),
								'class' => 'zp-setting-checkbox-label'
					),
				)
			)
		),
		'misc' => apply_filters( 'zp_settings_misc',
			array(
				'main' => array(
					'atlas_header' => array(
						'id'	=> 'atlas_header',
						'name'	=> '<h3>' . __( 'Atlas', 'zodiacpress' ) . '</h3>',
						'type'	=> 'header',
						'desc'	=> '<hr />'
					),
					'geonames_user'	=> array(
						'id'	=> 'geonames_user',
						'name'	=> __( 'GeoNames Benutzername', 'zodiacpress' ),
						'desc'	=> sprintf( __( 'Dein Benutzername von GeoNames.org wird benötigt, um Zeitzoneninformationen von ihrem Webservice zu erhalten. (%1$sKostenlosen Account erstellen%2$s)', 'zodiacpress' ), '<a href="http://www.geonames.org/login" target="_blank" rel="noopener">', '</a>' ),
						'type'	=> 'text',
						'size'	=> 'medium',
						'std'	=> ''
					),
					'uninstall_header' => array(
						'id'	=> 'uninstall_header',
						'name'	=> '<h3>' . __( 'Deinstallieren', 'zodiacpress' ) . '</h3>',
						'type'	=> 'header',
						'desc'	=> '<hr />'
					),
					'remove_data' => array(
						'id'	=> 'remove_data',
						'name'	=> __( 'Daten bei der Deinstallation entfernen', 'zodiacpress' ),
						'type'	=> 'checkbox',
						'desc'	=> __( 'Aktiviere dieses Kontrollkästchen, wenn z.B. beim Löschen des Plugins alle Daten (EINSCHLIESSLICH INTERPRETATIONSTEXT) vollständig entfernen soll.', 'zodiacpress' ),
						'class' => 'zp-setting-checkbox-label'
					),
				)
			)
		)
	);
	return apply_filters( 'zp_registered_settings', $zp_settings );
}

/**
 * Settings Sanitization
 *
 * Adds a settings updated notice
 *
 * @param array $input The value inputted in the field
 *
 * @return string $input Sanitizied value
 */
function zp_settings_sanitize( $input = array() ) {
	global $zodiacpress_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = zp_get_registered_settings();
	$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'natal';
	$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';

	$input 					= $input ? $input : array();
	$zodiacpress_options 	= $zodiacpress_options ? $zodiacpress_options : array();

	$input = apply_filters( 'zodiacpress_settings_' . $tab . '-' . $section . '_sanitize', $input );

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {

		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[ $tab ][ $section ][ $key ]['type'] ) ? $settings[ $tab ][ $section ][ $key ]['type'] : false;

		if ( $type ) {
			// Field type specific filter
			$input[$key] = apply_filters( 'zp_settings_sanitize_' . $type, $value, $key );
		}

		// General filter
		$input[ $key ] = apply_filters( 'zp_settings_sanitize', $input[ $key ], $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	$section_settings = ! empty( $settings[ $tab ][ $section ] ) ? $settings[ $tab ][ $section ] : array();

	if ( ! empty( $section_settings ) ) {
		foreach ( $section_settings as $key => $value ) {
			if ( empty( $input[ $key ] ) ) {
				unset( $zodiacpress_options[ $key ] );
			}
		}
	}

	// Merge new settings with the existing
	$output = array_merge( $zodiacpress_options, $input );

	add_settings_error( 'zp-notices', '', __( 'Einstellungen aktualisiert.', 'zodiacpress' ), 'updated' );

	return $output;
}

/**
 * Sanitize text fields
 *
 * @param string $input The field value
 * @param string $key The field id
 * @return string $input Sanitizied value
 */
function zp_sanitize_text_field( $input, $key ) {
	// Sanitize orb fields. Must be numeric.
	if ( 0 === strpos( $key, 'orb_' ) ) {
		if ( ! is_numeric( $input ) ) {
			return 8;
		} else {
			return abs( $input );// not negative
		}
	}
	return sanitize_text_field( $input );
}
add_filter( 'zp_settings_sanitize_text', 'zp_sanitize_text_field', 10, 2 );

/**
 * Sanitize multicheck fields
 *
 * @param array $input The field value
 * @param string $key The field id
 * @return array Sanitizied value
 */
function zp_sanitize_multicheck_field( $input, $key ) {
	foreach ( $input as $k => $v ) {
		$out[] = array( 'id' => $k, 'label' => $v );
	}

	return $out;
}
add_filter( 'zp_settings_sanitize_multicheck', 'zp_sanitize_multicheck_field', 10, 2 );

/**
 * Retrieve settings tabs
 *
 * @return array $tabs
 */
function zp_get_settings_tabs() {
	$settings = zp_get_registered_settings();
	$tabs = array(
		'natal'		=> __( 'Geburtsberichts', 'zodiacpress' ),
		'drawing'	=> __( 'Nur Diagrammzeichnungsbericht', 'zodiacpress' ),
		'misc'		=> __( 'Sonstiges', 'zodiacpress' )
	);
	return apply_filters( 'zp_settings_tabs', $tabs );
}
/**
 * Retrieve settings tab sections
 *
 * @return array $section
 */
function zp_get_settings_tab_sections( $tab = false ) {
	$tabs     = array();
	$sections = zp_get_registered_settings_sections();
	if ( $tab && ! empty( $sections[ $tab ] ) ) {
		$tabs = $sections[ $tab ];
	}
	return $tabs;
}
/**
 * Get the settings sections for each tab
 * Uses a static to avoid running the filters on every request to this function
 *
 * @return array Array of tabs and sections
 */
function zp_get_registered_settings_sections() {
	static $sections = false;

	if ( false !== $sections ) {
		return $sections;
	}
	$sections = array(
		'natal'		=> apply_filters( 'zp_settings_sections_natal', array(
			'main'		=> __( 'Planeten und Punkte', 'zodiacpress' ),
			'aspects'	=> __( 'Aspekte', 'zodiacpress' ),
			'orbs'		=> __( 'Gestirn', 'zodiacpress' ),
			'report'	=> __( 'Anzeige', 'zodiacpress' ),
			'technical'	=> __( 'Technisch', 'zodiacpress' )
		) ),
		'drawing'		=> apply_filters( 'zp_settings_sections_drawing', array(
			'main'		=>  __( 'Berichtseinstellungen "Nur Diagrammzeichnung"', 'zodiacpress' )
		) ),
		'misc'		=> apply_filters( 'zp_settings_sections_misc', array(
			'main'		=>  __( 'Sonstige Einstellungen', 'zodiacpress' )
		) ),
	);

	$sections = apply_filters( 'zodiacpress_settings_sections', $sections );

	return $sections;
}
// renders the header field
function zp_header_callback( $args ) {
	echo empty( $args['desc'] ) ? '' : $args['desc'];
}
// renders checkbox setting
function zp_checkbox_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );
	$checked = isset( $options[ $args['id'] ] ) ? checked( 1, $options[ $args['id'] ], false ) : '';
	$html = '<input type="checkbox" id="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" value="1" ' . $checked . '/>';
	$html .= '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';
	echo $html;
}
// renders multicheck setting
function zp_multicheck_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );
	if ( ! empty( $args['options'] ) ) {
		echo '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
		$enabled_options = array();
		if ( is_array( $options ) ) {
			$plucked_keys	= array();
			$plucked_values	= array();
			foreach ( $options as $k => $v ) {
				if ( is_array( $v ) && isset( $v[0]['id'] ) ) {
					$plucked_keys[] = $k;
					$plucked_values[] = array_column( $v, 'id', 'id' );
				}
			}
			$enabled_options = array_combine( $plucked_keys, $plucked_values );
		}
		foreach( $args['options'] as $option ):
			$enabled = isset( $enabled_options[$args['id']][ $option['id'] ] ) ? $option['id'] : NULL;
			echo '<input name="zodiacpress_settings[' . esc_attr( $args['id'] ) . '][' . $option['id'] . ']" id="zodiacpress_settings[' . esc_attr( $args['id'] ) . '][' . $option['id'] . ']" type="checkbox" value="' . esc_attr( $option['label'] ) . '" ' . checked($option['id'], $enabled, false) . '/>&nbsp;';
			echo '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . '][' . $option['id'] . ']">' . wp_kses_post( $option['label'] ) . '</label><br/>';
		endforeach;
	}
}
// renders text settings
function zp_text_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );

	if ( isset( $options[ $args['id'] ] ) ) {
		$value = $options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
	$name = 'name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"';
	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html     = '<input type="text" class="' . esc_attr( $size ) . '-text" id="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '" />';
	$html    .= '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';
	echo $html;
}
// alert if a callback is missing for a setting
function zp_missing_callback($args) {
	printf(
		__( 'Die für die Einstellung %s verwendete Rückruffunktion fehlt.', 'zodiacpress' ),
		'<strong>' . esc_attr( $args['id'] ) . '</strong>'
	);
}
// renders select field
function zp_select_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );
	if ( isset( $options[ $args['id'] ] ) ) {
		$value = $options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
	$html = '<select id="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" />';
	foreach ( $args['options'] as $option => $name ) {
		$selected = ( $option === $value ) ? ' selected' : '';
		$html .= '<option value="' . esc_attr( $option ) . '"' . $selected . '>' . esc_html( $name ) . '</option>';
	}
	$html .= '</select>';
	$html .= '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';
	echo $html;
}
// renders textarea setting
function zp_textarea_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );
	$value = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
	$html = '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
	$html .= '<textarea class="large-text" cols="50" rows="5" id="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	echo $html;
}
/**
 * Set manage_zodiacpress_settings as the cap required to save ZP settings
 * @return string capability required
 */
function zp_set_settings_cap( $cap ) {
	return 'manage_zodiacpress_settings';
}
add_filter( 'option_page_capability_zodiacpress_settings', 'zp_set_settings_cap' );
/**
 * Display help text at the top of the Orbs settings
 */
function zp_orbs_settings_help_text() {
	static $done_ran;
	if ( ! empty( $done_ran ) ) {
		return;
	}
	echo '<p class="clear zp-helptext">' . __( 'Stelle für jeden Aspekt das Gestirn ein, die für jeden Planeten verwendet werden soll. Wenn leer, wird die Standardeinstellung (8) verwendet.', 'zodiacpress' ) . '</p>';
	$done_ran = true;
}
/**
 * Add granular Orbs settings
 */
function zp_orbs_add_orb_settings( $settings ) {
	$planets = zp_get_planets();
	$aspects = zp_get_aspects();
	foreach ( $aspects as $asp ) {
		$asp_id		= $asp['id'];
		$header_key	= $asp_id . '_orbs';
		$settings['orbs'][ $header_key ] = array(
					'id'	=> $header_key,
					'name'	=> '<h3>' . $asp['label'] . '</h3>',
					'type'	=> 'header',
					'desc'	=> '<hr />'
		);		
		foreach ( $planets as $p ) {
			$p_id	= $p['id'];
			$key 	= 'orb_' . $asp_id . '_' . $p_id;
			$settings['orbs'][ $key ] = array(
					'id'		=> $key,
					'name'		=> '',
					'type'		=> 'text',
					'desc'		=> $p['label'],
					'size'	=> 'small',
					'std'	=> '8'
			);
		}
	}
	return $settings;
}
add_action( 'zodiacpress_settings_tab_top_natal_orbs', 'zp_orbs_settings_help_text' );
add_action( 'zp_settings_natal', 'zp_orbs_add_orb_settings' );
