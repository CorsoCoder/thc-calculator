<?php

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class THC_Detox_Calculator_Shortcode {

/**
 * @var THC_Detox_Calculator_Estimator
 */
private $estimator;

/**
 * @var THC_Detox_Calculator_Calendar
 */
private $calendar;

/**
 * @param THC_Detox_Calculator_Estimator $estimator
 * @param THC_Detox_Calculator_Calendar  $calendar
 */
public function __construct( THC_Detox_Calculator_Estimator $estimator, THC_Detox_Calculator_Calendar $calendar ) {
$this->estimator = $estimator;
$this->calendar  = $calendar;
}

/**
 * @return string
 */
public function render_shortcode() {
wp_enqueue_style( 'thc-detox-calculator' );
wp_enqueue_script( 'thc-detox-calculator' );

wp_localize_script(
'thc-detox-calculator',
'THCDetoxCalculatorData',
array(
'ajaxUrl' => admin_url( 'admin-ajax.php' ),
'nonce'   => wp_create_nonce( 'thc_detox_calculator_nonce' ),
)
);

ob_start();
require THC_DETOX_CALCULATOR_PATH . 'templates/calculator-form.php';

return (string) ob_get_clean();
}

/**
 * @return void
 */
public function handle_ajax_calculation() {
if ( ! check_ajax_referer( 'thc_detox_calculator_nonce', 'nonce', false ) ) {
wp_send_json_error(
array(
'errors' => array( __( 'No se pudo validar la solicitud. Recarga la página e inténtalo de nuevo.', 'thc-detox-calculator' ) ),
),
403
);
}

$result = $this->estimator->estimate( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

if ( empty( $result['success'] ) ) {
wp_send_json_error(
array(
'errors' => isset( $result['errors'] ) && is_array( $result['errors'] ) ? array_map( 'wp_kses_post', $result['errors'] ) : array(),
),
422
);
}

$urine_calendar = $this->calendar->build_calendar_data( 'urine', $result['urine']['dates']['conservative']['iso'] );
$blood_calendar = $this->calendar->build_calendar_data( 'blood', $result['blood']['dates']['conservative']['iso'] );

$response = array(
'disclaimer' => wp_kses_post( $result['disclaimer'] ),
'influences' => array_map( 'wp_kses_post', $result['influences'] ),
'urine'      => array(
'label'           => wp_kses_post( $result['urine']['label'] ),
'window_readable' => wp_kses_post( $result['urine']['window_readable'] ),
'tags'            => array_map( 'wp_kses_post', $result['urine']['tags'] ),
'dates'           => $result['urine']['dates'],
'calendar'        => $urine_calendar,
),
'blood'      => array(
'label'           => wp_kses_post( $result['blood']['label'] ),
'window_readable' => wp_kses_post( $result['blood']['window_readable'] ),
'tags'            => array_map( 'wp_kses_post', $result['blood']['tags'] ),
'dates'           => $result['blood']['dates'],
'calendar'        => $blood_calendar,
),
);

wp_send_json_success( $response );
}
}
