<?php
/**
 * Plugin Name: THC Detox Calculator
 * Plugin URI: https://example.com/
 * Description: Calculadora orientativa de ventana de detección de THC para orina y sangre.
 * Version: 1.0.0
 * Author: CorsoCoder
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: thc-detox-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

define( 'THC_DETOX_CALCULATOR_VERSION', '1.0.0' );
define( 'THC_DETOX_CALCULATOR_FILE', __FILE__ );
define( 'THC_DETOX_CALCULATOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'THC_DETOX_CALCULATOR_URL', plugin_dir_url( __FILE__ ) );

require_once THC_DETOX_CALCULATOR_PATH . 'includes/class-thc-detox-calculator-estimator.php';
require_once THC_DETOX_CALCULATOR_PATH . 'includes/class-thc-detox-calculator-calendar.php';
require_once THC_DETOX_CALCULATOR_PATH . 'includes/class-thc-detox-calculator-shortcode.php';
require_once THC_DETOX_CALCULATOR_PATH . 'includes/class-thc-detox-calculator-plugin.php';

THC_Detox_Calculator_Plugin::instance();
