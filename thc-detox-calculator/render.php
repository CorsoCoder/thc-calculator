<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$block_type = WP_Block_Type_Registry::get_instance()->get_registered( 'thc-detox/calculator' );
if ( $block_type && ! empty( $block_type->view_script_handles ) && is_array( $block_type->view_script_handles ) ) {
	$view_script_handle = reset( $block_type->view_script_handles );
	if ( is_string( $view_script_handle ) && '' !== $view_script_handle ) {
		wp_localize_script(
			$view_script_handle,
			'THCDetoxCalculatorData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'thc_detox_calculator_nonce' ),
			)
		);
	}
}

$default_data = array(
	'title'            => __( 'Calculadora de desintoxicación THC', 'thc-detox-calculator' ),
	'description'      => __( 'Estima tu ventana orientativa para test de orina y sangre con un enfoque conservador.', 'thc-detox-calculator' ),
	'disclaimer'       => __( 'Esta herramienta ofrece una estimación aproximada y no garantiza un resultado negativo en un test de drogas.', 'thc-detox-calculator' ),
	'previous_button'  => __( 'Anterior', 'thc-detox-calculator' ),
	'next_button'      => __( 'Siguiente', 'thc-detox-calculator' ),
	'submit_button'    => __( 'Calcular ventana estimada', 'thc-detox-calculator' ),
	'submit_loading'   => __( 'Calculando...', 'thc-detox-calculator' ),
	'max_width'        => 920,
	'font_size'        => 1,
	'border_radius'    => 20,
	'border_width'     => 0,
	'background_color' => '',
	'text_color'       => '#eef2ff',
	'primary_color'    => '#6f7fff',
	'accent_color'     => '#16c79a',
	'border_color'     => '#252c4a',
);

if ( class_exists( 'THC_Detox_Calculator_Shortcode' ) && method_exists( 'THC_Detox_Calculator_Shortcode', 'get_default_view_data' ) ) {
	$default_data = THC_Detox_Calculator_Shortcode::get_default_view_data();
}

$view_data = array(
	'title'           => sanitize_text_field( isset( $attributes['title'] ) ? $attributes['title'] : $default_data['title'] ),
	'description'     => sanitize_text_field( isset( $attributes['description'] ) ? $attributes['description'] : $default_data['description'] ),
	'disclaimer'      => sanitize_text_field( isset( $attributes['disclaimer'] ) ? $attributes['disclaimer'] : $default_data['disclaimer'] ),
	'previous_button' => sanitize_text_field( isset( $attributes['previousButtonLabel'] ) ? $attributes['previousButtonLabel'] : $default_data['previous_button'] ),
	'next_button'     => sanitize_text_field( isset( $attributes['nextButtonLabel'] ) ? $attributes['nextButtonLabel'] : $default_data['next_button'] ),
	'submit_button'   => sanitize_text_field( isset( $attributes['submitButtonLabel'] ) ? $attributes['submitButtonLabel'] : $default_data['submit_button'] ),
	'submit_loading'  => sanitize_text_field( isset( $attributes['submitLoadingLabel'] ) ? $attributes['submitLoadingLabel'] : $default_data['submit_loading'] ),
);

$view_data['max_width']        = max( 480, min( 1400, absint( isset( $attributes['maxWidth'] ) ? $attributes['maxWidth'] : $default_data['max_width'] ) ) );
$view_data['font_size']        = max( 0.8, min( 1.4, (float) ( isset( $attributes['fontSize'] ) ? $attributes['fontSize'] : $default_data['font_size'] ) ) );
$view_data['border_radius']    = max( 0, min( 48, absint( isset( $attributes['borderRadius'] ) ? $attributes['borderRadius'] : $default_data['border_radius'] ) ) );
$view_data['border_width']     = max( 0, min( 12, absint( isset( $attributes['borderWidth'] ) ? $attributes['borderWidth'] : $default_data['border_width'] ) ) );
$view_data['background_color'] = sanitize_hex_color( isset( $attributes['backgroundColor'] ) ? $attributes['backgroundColor'] : '' ) ?: $default_data['background_color'];
$view_data['text_color']       = sanitize_hex_color( isset( $attributes['textColor'] ) ? $attributes['textColor'] : '' ) ?: $default_data['text_color'];
$view_data['primary_color']    = sanitize_hex_color( isset( $attributes['primaryColor'] ) ? $attributes['primaryColor'] : '' ) ?: $default_data['primary_color'];
$view_data['accent_color']     = sanitize_hex_color( isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '' ) ?: $default_data['accent_color'];
$view_data['border_color']     = sanitize_hex_color( isset( $attributes['borderColor'] ) ? $attributes['borderColor'] : '' ) ?: $default_data['border_color'];

$font_size_string    = number_format( $view_data['font_size'], 2, '.', '' );
$font_size_formatted = preg_replace( '/\.?0+$/', '', $font_size_string );
$font_size_formatted = '' === $font_size_formatted ? '1' : $font_size_formatted;

$style_vars = array(
	'--thc-text'         => $view_data['text_color'],
	'--thc-primary'      => $view_data['primary_color'],
	'--thc-accent'       => $view_data['accent_color'],
	'--thc-border-color' => $view_data['border_color'],
	'--thc-border-width' => $view_data['border_width'] . 'px',
	'--thc-radius'       => $view_data['border_radius'] . 'px',
	'--thc-max-width'    => $view_data['max_width'] . 'px',
	'--thc-font-size'    => $font_size_formatted . 'rem',
);

if ( ! empty( $view_data['background_color'] ) ) {
	$style_vars['--thc-bg'] = $view_data['background_color'];
}

$inline_style = '';
foreach ( $style_vars as $key => $value ) {
	$inline_style .= $key . ':' . $value . ';';
}

$view_data['inline_style'] = $inline_style;

ob_start();
require __DIR__ . '/templates/calculator-form.php';

return (string) ob_get_clean();
