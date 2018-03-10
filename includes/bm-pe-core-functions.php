<?php
/**
 * Press Events Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author Burn Media Ltd
 * @package PressEvents/Functions
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include core functions
 */
include( BM_PE_ABSPATH . 'includes/bm-pe-conditional-functions.php' );
include( BM_PE_ABSPATH . 'includes/bm-pe-event-functions.php' );
include( BM_PE_ABSPATH . 'includes/bm-pe-formatting-functions.php' );
include( BM_PE_ABSPATH . 'includes/bm-pe-location-functions.php' );
include( BM_PE_ABSPATH . 'includes/bm-pe-organiser-functions.php' );
include( BM_PE_ABSPATH . 'includes/bm-pe-term-functions.php' );

/**
 * Get saved setting from given section
 */
function bm_pe_get_option( $option, $section, $default = '' ) {
	$options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

/**
 * Get permalink settings for things like events and taxonomies.
 *
 * @since  1.0.0
 * @return array
 */
function bm_pe_get_permalink_structure() {
	$saved_permalinks = (array) get_option( 'press_events_permalinks', array() );

	$permalinks = wp_parse_args( array_filter( $saved_permalinks ), array(
		'event_archive' => _x( 'events', 'slug', 'press-events' ),
		'event_base' => _x( 'event', 'slug', 'press-events' ),
		'category_base' => _x( 'event-category', 'slug', 'press-events' ),
		'tag_base' => _x( 'event-tag', 'slug', 'press-events' ),
	) );

	if ( $saved_permalinks !== $permalinks ) {
		update_option( 'press_events_permalinks', $permalinks );
	}

	$permalinks['event_archive_slug'] = untrailingslashit( $permalinks['event_archive'] );
	$permalinks['event_rewrite_slug'] = untrailingslashit( $permalinks['event_base'] );
	$permalinks['category_rewrite_slug'] = untrailingslashit( $permalinks['category_base'] );
	$permalinks['tag_rewrite_slug'] = untrailingslashit( $permalinks['tag_base'] );

	return $permalinks;
}

/**
 * Get event template parts
 */
function bm_bm_pe_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/press-events/slug-name.php
	if ( $name && ! BM_PE_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}-{$name}.php", BM_Press_Events()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( BM_Press_Events()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = BM_Press_Events()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/press-events/slug.php
	if ( ! $template && ! BM_PE_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}.php", BM_Press_Events()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'bm_bm_pe_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get event template
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function bm_pe_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( !empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = bm_pe_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		return false;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'bm_pe_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'press_events_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'press_events_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme /	$template_path / $template_name
 *		yourtheme /	$template_name
 *		$default_path /	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function bm_pe_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = BM_Press_Events()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = BM_Press_Events()->plugin_path() . '/templates/';
	}

	// Look within the theme
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	return apply_filters( 'press_event_locate_template', $template, $template_name, $template_path );
}

/**
 * Get Base Currency Code.
 *
 * @return string
 */
function bm_pe_get_currency() {
	return apply_filters( 'press_events_currency', bm_pe_get_option( 'code', 'pe-general-currency', 'GBP' ) );
}

/**
 * Get full list of currency codes.
 *
 * @return array
 */
function bm_pe_get_currencies() {
	static $currencies;

	if ( !isset( $currencies ) ) {
		$currencies = array_unique(
			apply_filters( 'press_events_currencies',
				array(
					'AED' => __( 'United Arab Emirates dirham', 'press-events' ),
					'AFN' => __( 'Afghan afghani', 'press-events' ),
					'ALL' => __( 'Albanian lek', 'press-events' ),
					'AMD' => __( 'Armenian dram', 'press-events' ),
					'ANG' => __( 'Netherlands Antillean guilder', 'press-events' ),
					'AOA' => __( 'Angolan kwanza', 'press-events' ),
					'ARS' => __( 'Argentine peso', 'press-events' ),
					'AUD' => __( 'Australian dollar', 'press-events' ),
					'AWG' => __( 'Aruban florin', 'press-events' ),
					'AZN' => __( 'Azerbaijani manat', 'press-events' ),
					'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'press-events' ),
					'BBD' => __( 'Barbadian dollar', 'press-events' ),
					'BDT' => __( 'Bangladeshi taka', 'press-events' ),
					'BGN' => __( 'Bulgarian lev', 'press-events' ),
					'BHD' => __( 'Bahraini dinar', 'press-events' ),
					'BIF' => __( 'Burundian franc', 'press-events' ),
					'BMD' => __( 'Bermudian dollar', 'press-events' ),
					'BND' => __( 'Brunei dollar', 'press-events' ),
					'BOB' => __( 'Bolivian boliviano', 'press-events' ),
					'BRL' => __( 'Brazilian real', 'press-events' ),
					'BSD' => __( 'Bahamian dollar', 'press-events' ),
					'BTC' => __( 'Bitcoin', 'press-events' ),
					'BTN' => __( 'Bhutanese ngultrum', 'press-events' ),
					'BWP' => __( 'Botswana pula', 'press-events' ),
					'BYR' => __( 'Belarusian ruble (old)', 'press-events' ),
					'BYN' => __( 'Belarusian ruble', 'press-events' ),
					'BZD' => __( 'Belize dollar', 'press-events' ),
					'CAD' => __( 'Canadian dollar', 'press-events' ),
					'CDF' => __( 'Congolese franc', 'press-events' ),
					'CHF' => __( 'Swiss franc', 'press-events' ),
					'CLP' => __( 'Chilean peso', 'press-events' ),
					'CNY' => __( 'Chinese yuan', 'press-events' ),
					'COP' => __( 'Colombian peso', 'press-events' ),
					'CRC' => __( 'Costa Rican col&oacute;n', 'press-events' ),
					'CUC' => __( 'Cuban convertible peso', 'press-events' ),
					'CUP' => __( 'Cuban peso', 'press-events' ),
					'CVE' => __( 'Cape Verdean escudo', 'press-events' ),
					'CZK' => __( 'Czech koruna', 'press-events' ),
					'DJF' => __( 'Djiboutian franc', 'press-events' ),
					'DKK' => __( 'Danish krone', 'press-events' ),
					'DOP' => __( 'Dominican peso', 'press-events' ),
					'DZD' => __( 'Algerian dinar', 'press-events' ),
					'EGP' => __( 'Egyptian pound', 'press-events' ),
					'ERN' => __( 'Eritrean nakfa', 'press-events' ),
					'ETB' => __( 'Ethiopian birr', 'press-events' ),
					'EUR' => __( 'Euro', 'press-events' ),
					'FJD' => __( 'Fijian dollar', 'press-events' ),
					'FKP' => __( 'Falkland Islands pound', 'press-events' ),
					'GBP' => __( 'Pound sterling', 'press-events' ),
					'GEL' => __( 'Georgian lari', 'press-events' ),
					'GGP' => __( 'Guernsey pound', 'press-events' ),
					'GHS' => __( 'Ghana cedi', 'press-events' ),
					'GIP' => __( 'Gibraltar pound', 'press-events' ),
					'GMD' => __( 'Gambian dalasi', 'press-events' ),
					'GNF' => __( 'Guinean franc', 'press-events' ),
					'GTQ' => __( 'Guatemalan quetzal', 'press-events' ),
					'GYD' => __( 'Guyanese dollar', 'press-events' ),
					'HKD' => __( 'Hong Kong dollar', 'press-events' ),
					'HNL' => __( 'Honduran lempira', 'press-events' ),
					'HRK' => __( 'Croatian kuna', 'press-events' ),
					'HTG' => __( 'Haitian gourde', 'press-events' ),
					'HUF' => __( 'Hungarian forint', 'press-events' ),
					'IDR' => __( 'Indonesian rupiah', 'press-events' ),
					'ILS' => __( 'Israeli new shekel', 'press-events' ),
					'IMP' => __( 'Manx pound', 'press-events' ),
					'INR' => __( 'Indian rupee', 'press-events' ),
					'IQD' => __( 'Iraqi dinar', 'press-events' ),
					'IRR' => __( 'Iranian rial', 'press-events' ),
					'IRT' => __( 'Iranian toman', 'press-events' ),
					'ISK' => __( 'Icelandic kr&oacute;na', 'press-events' ),
					'JEP' => __( 'Jersey pound', 'press-events' ),
					'JMD' => __( 'Jamaican dollar', 'press-events' ),
					'JOD' => __( 'Jordanian dinar', 'press-events' ),
					'JPY' => __( 'Japanese yen', 'press-events' ),
					'KES' => __( 'Kenyan shilling', 'press-events' ),
					'KGS' => __( 'Kyrgyzstani som', 'press-events' ),
					'KHR' => __( 'Cambodian riel', 'press-events' ),
					'KMF' => __( 'Comorian franc', 'press-events' ),
					'KPW' => __( 'North Korean won', 'press-events' ),
					'KRW' => __( 'South Korean won', 'press-events' ),
					'KWD' => __( 'Kuwaiti dinar', 'press-events' ),
					'KYD' => __( 'Cayman Islands dollar', 'press-events' ),
					'KZT' => __( 'Kazakhstani tenge', 'press-events' ),
					'LAK' => __( 'Lao kip', 'press-events' ),
					'LBP' => __( 'Lebanese pound', 'press-events' ),
					'LKR' => __( 'Sri Lankan rupee', 'press-events' ),
					'LRD' => __( 'Liberian dollar', 'press-events' ),
					'LSL' => __( 'Lesotho loti', 'press-events' ),
					'LYD' => __( 'Libyan dinar', 'press-events' ),
					'MAD' => __( 'Moroccan dirham', 'press-events' ),
					'MDL' => __( 'Moldovan leu', 'press-events' ),
					'MGA' => __( 'Malagasy ariary', 'press-events' ),
					'MKD' => __( 'Macedonian denar', 'press-events' ),
					'MMK' => __( 'Burmese kyat', 'press-events' ),
					'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'press-events' ),
					'MOP' => __( 'Macanese pataca', 'press-events' ),
					'MRO' => __( 'Mauritanian ouguiya', 'press-events' ),
					'MUR' => __( 'Mauritian rupee', 'press-events' ),
					'MVR' => __( 'Maldivian rufiyaa', 'press-events' ),
					'MWK' => __( 'Malawian kwacha', 'press-events' ),
					'MXN' => __( 'Mexican peso', 'press-events' ),
					'MYR' => __( 'Malaysian ringgit', 'press-events' ),
					'MZN' => __( 'Mozambican metical', 'press-events' ),
					'NAD' => __( 'Namibian dollar', 'press-events' ),
					'NGN' => __( 'Nigerian naira', 'press-events' ),
					'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'press-events' ),
					'NOK' => __( 'Norwegian krone', 'press-events' ),
					'NPR' => __( 'Nepalese rupee', 'press-events' ),
					'NZD' => __( 'New Zealand dollar', 'press-events' ),
					'OMR' => __( 'Omani rial', 'press-events' ),
					'PAB' => __( 'Panamanian balboa', 'press-events' ),
					'PEN' => __( 'Peruvian nuevo sol', 'press-events' ),
					'PGK' => __( 'Papua New Guinean kina', 'press-events' ),
					'PHP' => __( 'Philippine peso', 'press-events' ),
					'PKR' => __( 'Pakistani rupee', 'press-events' ),
					'PLN' => __( 'Polish z&#x142;oty', 'press-events' ),
					'PRB' => __( 'Transnistrian ruble', 'press-events' ),
					'PYG' => __( 'Paraguayan guaran&iacute;', 'press-events' ),
					'QAR' => __( 'Qatari riyal', 'press-events' ),
					'RON' => __( 'Romanian leu', 'press-events' ),
					'RSD' => __( 'Serbian dinar', 'press-events' ),
					'RUB' => __( 'Russian ruble', 'press-events' ),
					'RWF' => __( 'Rwandan franc', 'press-events' ),
					'SAR' => __( 'Saudi riyal', 'press-events' ),
					'SBD' => __( 'Solomon Islands dollar', 'press-events' ),
					'SCR' => __( 'Seychellois rupee', 'press-events' ),
					'SDG' => __( 'Sudanese pound', 'press-events' ),
					'SEK' => __( 'Swedish krona', 'press-events' ),
					'SGD' => __( 'Singapore dollar', 'press-events' ),
					'SHP' => __( 'Saint Helena pound', 'press-events' ),
					'SLL' => __( 'Sierra Leonean leone', 'press-events' ),
					'SOS' => __( 'Somali shilling', 'press-events' ),
					'SRD' => __( 'Surinamese dollar', 'press-events' ),
					'SSP' => __( 'South Sudanese pound', 'press-events' ),
					'STD' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'press-events' ),
					'SYP' => __( 'Syrian pound', 'press-events' ),
					'SZL' => __( 'Swazi lilangeni', 'press-events' ),
					'THB' => __( 'Thai baht', 'press-events' ),
					'TJS' => __( 'Tajikistani somoni', 'press-events' ),
					'TMT' => __( 'Turkmenistan manat', 'press-events' ),
					'TND' => __( 'Tunisian dinar', 'press-events' ),
					'TOP' => __( 'Tongan pa&#x2bb;anga', 'press-events' ),
					'TRY' => __( 'Turkish lira', 'press-events' ),
					'TTD' => __( 'Trinidad and Tobago dollar', 'press-events' ),
					'TWD' => __( 'New Taiwan dollar', 'press-events' ),
					'TZS' => __( 'Tanzanian shilling', 'press-events' ),
					'UAH' => __( 'Ukrainian hryvnia', 'press-events' ),
					'UGX' => __( 'Ugandan shilling', 'press-events' ),
					'USD' => __( 'United States dollar', 'press-events' ),
					'UYU' => __( 'Uruguayan peso', 'press-events' ),
					'UZS' => __( 'Uzbekistani som', 'press-events' ),
					'VEF' => __( 'Venezuelan bol&iacute;var', 'press-events' ),
					'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'press-events' ),
					'VUV' => __( 'Vanuatu vatu', 'press-events' ),
					'WST' => __( 'Samoan t&#x101;l&#x101;', 'press-events' ),
					'XAF' => __( 'Central African CFA franc', 'press-events' ),
					'XCD' => __( 'East Caribbean dollar', 'press-events' ),
					'XOF' => __( 'West African CFA franc', 'press-events' ),
					'XPF' => __( 'CFP franc', 'press-events' ),
					'YER' => __( 'Yemeni rial', 'press-events' ),
					'ZAR' => __( 'South African rand', 'press-events' ),
					'ZMW' => __( 'Zambian kwacha', 'press-events' ),
				)
			)
		);
	}

	return $currencies;
}

/**
 * Get Currency symbol.
 *
 * @param string $currency Currency. (default: '').
 * @return string
 */
function bm_pe_get_currency_symbol( $currency = '' ) {
	if ( ! $currency ) {
		$currency = bm_pe_get_currency();
	}

	$symbols = apply_filters( 'press_events_currency_symbols', array(
		'AED' => '&#x62f;.&#x625;',
		'AFN' => '&#x60b;',
		'ALL' => 'L',
		'AMD' => 'AMD',
		'ANG' => '&fnof;',
		'AOA' => 'Kz',
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => 'Afl.',
		'AZN' => 'AZN',
		'BAM' => 'KM',
		'BBD' => '&#36;',
		'BDT' => '&#2547;&nbsp;',
		'BGN' => '&#1083;&#1074;.',
		'BHD' => '.&#x62f;.&#x628;',
		'BIF' => 'Fr',
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => 'Bs.',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTC' => '&#3647;',
		'BTN' => 'Nu.',
		'BWP' => 'P',
		'BYR' => 'Br',
		'BYN' => 'Br',
		'BZD' => '&#36;',
		'CAD' => '&#36;',
		'CDF' => 'Fr',
		'CHF' => '&#67;&#72;&#70;',
		'CLP' => '&#36;',
		'CNY' => '&yen;',
		'COP' => '&#36;',
		'CRC' => '&#x20a1;',
		'CUC' => '&#36;',
		'CUP' => '&#36;',
		'CVE' => '&#36;',
		'CZK' => '&#75;&#269;',
		'DJF' => 'Fr',
		'DKK' => 'DKK',
		'DOP' => 'RD&#36;',
		'DZD' => '&#x62f;.&#x62c;',
		'EGP' => 'EGP',
		'ERN' => 'Nfk',
		'ETB' => 'Br',
		'EUR' => '&euro;',
		'FJD' => '&#36;',
		'FKP' => '&pound;',
		'GBP' => '&pound;',
		'GEL' => '&#x10da;',
		'GGP' => '&pound;',
		'GHS' => '&#x20b5;',
		'GIP' => '&pound;',
		'GMD' => 'D',
		'GNF' => 'Fr',
		'GTQ' => 'Q',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => 'L',
		'HRK' => 'Kn',
		'HTG' => 'G',
		'HUF' => '&#70;&#116;',
		'IDR' => 'Rp',
		'ILS' => '&#8362;',
		'IMP' => '&pound;',
		'INR' => '&#8377;',
		'IQD' => '&#x639;.&#x62f;',
		'IRR' => '&#xfdfc;',
		'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
		'ISK' => 'kr.',
		'JEP' => '&pound;',
		'JMD' => '&#36;',
		'JOD' => '&#x62f;.&#x627;',
		'JPY' => '&yen;',
		'KES' => 'KSh',
		'KGS' => '&#x441;&#x43e;&#x43c;',
		'KHR' => '&#x17db;',
		'KMF' => 'Fr',
		'KPW' => '&#x20a9;',
		'KRW' => '&#8361;',
		'KWD' => '&#x62f;.&#x643;',
		'KYD' => '&#36;',
		'KZT' => 'KZT',
		'LAK' => '&#8365;',
		'LBP' => '&#x644;.&#x644;',
		'LKR' => '&#xdbb;&#xdd4;',
		'LRD' => '&#36;',
		'LSL' => 'L',
		'LYD' => '&#x644;.&#x62f;',
		'MAD' => '&#x62f;.&#x645;.',
		'MDL' => 'MDL',
		'MGA' => 'Ar',
		'MKD' => '&#x434;&#x435;&#x43d;',
		'MMK' => 'Ks',
		'MNT' => '&#x20ae;',
		'MOP' => 'P',
		'MRO' => 'UM',
		'MUR' => '&#x20a8;',
		'MVR' => '.&#x783;',
		'MWK' => 'MK',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => 'MT',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => 'C&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#x631;.&#x639;.',
		'PAB' => 'B/.',
		'PEN' => 'S/.',
		'PGK' => 'K',
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PRB' => '&#x440;.',
		'PYG' => '&#8370;',
		'QAR' => '&#x631;.&#x642;',
		'RMB' => '&yen;',
		'RON' => 'lei',
		'RSD' => '&#x434;&#x438;&#x43d;.',
		'RUB' => '&#8381;',
		'RWF' => 'Fr',
		'SAR' => '&#x631;.&#x633;',
		'SBD' => '&#36;',
		'SCR' => '&#x20a8;',
		'SDG' => '&#x62c;.&#x633;.',
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&pound;',
		'SLL' => 'Le',
		'SOS' => 'Sh',
		'SRD' => '&#36;',
		'SSP' => '&pound;',
		'STD' => 'Db',
		'SYP' => '&#x644;.&#x633;',
		'SZL' => 'L',
		'THB' => '&#3647;',
		'TJS' => '&#x405;&#x41c;',
		'TMT' => 'm',
		'TND' => '&#x62f;.&#x62a;',
		'TOP' => 'T&#36;',
		'TRY' => '&#8378;',
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => 'Sh',
		'UAH' => '&#8372;',
		'UGX' => 'UGX',
		'USD' => '&#36;',
		'UYU' => '&#36;',
		'UZS' => 'UZS',
		'VEF' => 'Bs F',
		'VND' => '&#8363;',
		'VUV' => 'Vt',
		'WST' => 'T',
		'XAF' => 'CFA',
		'XCD' => '&#36;',
		'XOF' => 'CFA',
		'XPF' => 'Fr',
		'YER' => '&#xfdfc;',
		'ZAR' => '&#82;',
		'ZMW' => 'ZK',
	) );

	$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

	return apply_filters( 'press_events_currency_symbol', $currency_symbol, $currency );
}

/**
 * Define a constant if it is not already defined.
 *
 * @since 1.0.0
 * @param string $name  Constant name.
 * @param string $value Value.
 */
function bm_pe_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}
