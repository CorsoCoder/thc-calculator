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
return $this->render_calculator( array() );
}

/**
 * @param array<string,mixed> $attributes
 * @return string
 */
public function render_block( $attributes = array() ) {
return $this->render_calculator( is_array( $attributes ) ? $attributes : array() );
}

/**
 * @param array<string,mixed> $attributes
 * @return string
 */
private function render_calculator( array $attributes ) {
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

$view_data = $this->build_view_data( $attributes );

ob_start();
require THC_DETOX_CALCULATOR_PATH . 'templates/calculator-form.php';

return (string) ob_get_clean();
}

/**
 * @param array<string,mixed> $attributes
 * @return array<string,mixed>
 */
private function build_view_data( array $attributes ) {
$default_data = array(
'title'                => __( 'Calculadora de desintoxicación THC', 'thc-detox-calculator' ),
'description'          => __( 'Estima tu ventana orientativa para test de orina y sangre con un enfoque conservador.', 'thc-detox-calculator' ),
'disclaimer'           => __( 'Esta herramienta ofrece una estimación aproximada y no garantiza un resultado negativo en un test de drogas.', 'thc-detox-calculator' ),
'previous_button'      => __( 'Anterior', 'thc-detox-calculator' ),
'next_button'          => __( 'Siguiente', 'thc-detox-calculator' ),
'submit_button'        => __( 'Calcular ventana estimada', 'thc-detox-calculator' ),
'submit_loading'       => __( 'Calculando...', 'thc-detox-calculator' ),
'max_width'            => 920,
'font_size'            => 1,
'border_radius'        => 20,
'border_width'         => 0,
'background_color'     => '',
'text_color'           => '#eef2ff',
'primary_color'        => '#6f7fff',
'accent_color'         => '#16c79a',
'border_color'         => '#252c4a',
'inline_style'         => '',
);

$sanitized_data = array(
'title'           => sanitize_text_field( isset( $attributes['title'] ) ? $attributes['title'] : $default_data['title'] ),
'description'     => sanitize_text_field( isset( $attributes['description'] ) ? $attributes['description'] : $default_data['description'] ),
'disclaimer'      => sanitize_text_field( isset( $attributes['disclaimer'] ) ? $attributes['disclaimer'] : $default_data['disclaimer'] ),
'previous_button' => sanitize_text_field( isset( $attributes['previousButtonLabel'] ) ? $attributes['previousButtonLabel'] : $default_data['previous_button'] ),
'next_button'     => sanitize_text_field( isset( $attributes['nextButtonLabel'] ) ? $attributes['nextButtonLabel'] : $default_data['next_button'] ),
'submit_button'   => sanitize_text_field( isset( $attributes['submitButtonLabel'] ) ? $attributes['submitButtonLabel'] : $default_data['submit_button'] ),
'submit_loading'  => sanitize_text_field( isset( $attributes['submitLoadingLabel'] ) ? $attributes['submitLoadingLabel'] : $default_data['submit_loading'] ),
);

$sanitized_data['max_width'] = max( 480, min( 1400, absint( isset( $attributes['maxWidth'] ) ? $attributes['maxWidth'] : $default_data['max_width'] ) ) );
$sanitized_data['font_size'] = max( 0.8, min( 1.4, (float) ( isset( $attributes['fontSize'] ) ? $attributes['fontSize'] : $default_data['font_size'] ) ) );
$sanitized_data['border_radius'] = max( 0, min( 48, absint( isset( $attributes['borderRadius'] ) ? $attributes['borderRadius'] : $default_data['border_radius'] ) ) );
$sanitized_data['border_width'] = max( 0, min( 12, absint( isset( $attributes['borderWidth'] ) ? $attributes['borderWidth'] : $default_data['border_width'] ) ) );
$sanitized_data['background_color'] = sanitize_hex_color( isset( $attributes['backgroundColor'] ) ? $attributes['backgroundColor'] : '' ) ?: $default_data['background_color'];
$sanitized_data['text_color'] = sanitize_hex_color( isset( $attributes['textColor'] ) ? $attributes['textColor'] : '' ) ?: $default_data['text_color'];
$sanitized_data['primary_color'] = sanitize_hex_color( isset( $attributes['primaryColor'] ) ? $attributes['primaryColor'] : '' ) ?: $default_data['primary_color'];
$sanitized_data['accent_color'] = sanitize_hex_color( isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '' ) ?: $default_data['accent_color'];
$sanitized_data['border_color'] = sanitize_hex_color( isset( $attributes['borderColor'] ) ? $attributes['borderColor'] : '' ) ?: $default_data['border_color'];

$font_size_string = number_format( $sanitized_data['font_size'], 2, '.', '' );
$font_size_string = rtrim( rtrim( $font_size_string, '0' ), '.' );

$style_vars = array(
'--thc-text'         => $sanitized_data['text_color'],
'--thc-primary'      => $sanitized_data['primary_color'],
'--thc-accent'       => $sanitized_data['accent_color'],
'--thc-border-color' => $sanitized_data['border_color'],
'--thc-border-width' => $sanitized_data['border_width'] . 'px',
'--thc-radius'       => $sanitized_data['border_radius'] . 'px',
'--thc-max-width'    => $sanitized_data['max_width'] . 'px',
'--thc-font-size'    => $font_size_string . 'rem',
);

if ( ! empty( $sanitized_data['background_color'] ) ) {
$style_vars['--thc-bg'] = $sanitized_data['background_color'];
}

$inline_style = '';
foreach ( $style_vars as $key => $value ) {
$inline_style .= $key . ':' . $value . ';';
}

$sanitized_data['inline_style'] = $inline_style;

return $sanitized_data;
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
