<?php

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class THC_Detox_Calculator_Plugin {

/**
 * @var THC_Detox_Calculator_Plugin|null
 */
private static $instance = null;

/**
 * @var THC_Detox_Calculator_Shortcode
 */
private $shortcode;

/**
 * @return THC_Detox_Calculator_Plugin
 */
public static function instance() {
if ( null === self::$instance ) {
self::$instance = new self();
}

return self::$instance;
}

private function __construct() {
$this->shortcode = new THC_Detox_Calculator_Shortcode( new THC_Detox_Calculator_Estimator(), new THC_Detox_Calculator_Calendar() );

add_action( 'init', array( $this, 'register_shortcode' ) );
add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
add_action( 'wp_ajax_thc_detox_calculate', array( $this->shortcode, 'handle_ajax_calculation' ) );
add_action( 'wp_ajax_nopriv_thc_detox_calculate', array( $this->shortcode, 'handle_ajax_calculation' ) );
}

/**
 * Registra shortcode y callback de render.
 *
 * @return void
 */
public function register_shortcode() {
add_shortcode( 'thc_detox_calculator', array( $this->shortcode, 'render_shortcode' ) );
}

/**
 * Registra assets del frontend.
 *
 * @return void
 */
public function register_assets() {
wp_register_style(
'thc-detox-calculator',
THC_DETOX_CALCULATOR_URL . 'assets/css/thc-detox-calculator.css',
array(),
THC_DETOX_CALCULATOR_VERSION
);

wp_register_script(
'thc-detox-calculator',
THC_DETOX_CALCULATOR_URL . 'assets/js/thc-detox-calculator.js',
array(),
THC_DETOX_CALCULATOR_VERSION,
true
);
}
}
