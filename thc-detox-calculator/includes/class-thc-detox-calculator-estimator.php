<?php

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class THC_Detox_Calculator_Estimator {

/**
 * Reglas base orientativas (heurística conservadora) tomando como referencia:
 * - Healthline (How Long Does Weed Stay in Your System?, 2026)
 * - Zamnesia THC Detox Calculator
 * - NCBI StatPearls (Cannabis Use Disorder)
 * - PMC: Extended Urinary Δ9-THC Excretion in Chronic Cannabis Users
 * - HHS/Federal Register cutoffs para THCA en testing laboral
 *
 * @var array<string, array<string, float>>
 */
private $base_days = array(
'uso_aislado'                => array( 'urine' => 3.0, 'blood' => 1.0 ),
'algunos_dias'               => array( 'urine' => 6.0, 'blood' => 1.8 ),
'varias_veces_semana'        => array( 'urine' => 12.0, 'blood' => 2.8 ),
'casi_diario'                => array( 'urine' => 20.0, 'blood' => 4.0 ),
'diario'                     => array( 'urine' => 30.0, 'blood' => 5.5 ),
'varias_veces_dia'           => array( 'urine' => 45.0, 'blood' => 7.0 ),
);

/**
 * @param array<string, mixed> $data
 *
 * @return array<string, mixed>
 */
public function estimate( array $data ) {
$sanitized = $this->validate_and_sanitize( $data );

if ( is_wp_error( $sanitized ) ) {
return array(
'success' => false,
'errors'  => $sanitized->get_error_messages(),
);
}

$base      = $this->base_days[ $sanitized['frequency'] ];
$multi     = $this->build_multiplier( $sanitized );
$multi_bld = 1 + ( $multi - 1 ) * 0.65;

$urine_probable = $base['urine'] * $multi;
$blood_probable = $base['blood'] * $multi_bld;

$urine_window = $this->build_window_days( $urine_probable, 1.0 );
$blood_window = $this->build_window_days( $blood_probable, 0.5 );

$last_use_date = new DateTimeImmutable( $sanitized['last_use'] );

$urine_dates = $this->build_window_dates( $last_use_date, $urine_window );
$blood_dates = $this->build_window_dates( $last_use_date, $blood_window );

$influences = $this->build_influences( $sanitized, $multi );

return array(
'success'     => true,
'normalized'  => $sanitized,
'disclaimer'  => __( 'Esta herramienta ofrece una estimación aproximada y no garantiza un resultado negativo en un test de drogas.', 'thc-detox-calculator' ),
'urine'       => array(
'label'           => __( 'Ventana estimada para orina', 'thc-detox-calculator' ),
'window_days'     => $urine_window,
'window_readable' => $this->human_window( $urine_window ),
'dates'           => $urine_dates,
'tags'            => $this->build_tags( $urine_dates ),
),
'blood'       => array(
'label'           => __( 'Ventana estimada para sangre', 'thc-detox-calculator' ),
'window_days'     => $blood_window,
'window_readable' => $this->human_window( $blood_window ),
'dates'           => $blood_dates,
'tags'            => $this->build_tags( $blood_dates ),
),
'influences'  => $influences,
);
}

/**
 * @param array<string, mixed> $data
 *
 * @return array<string, mixed>|WP_Error
 */
private function validate_and_sanitize( array $data ) {
$errors = new WP_Error();

$allowed_gender = array( 'hombre', 'mujer', 'no_binario', 'prefiero_no_decir' );
$allowed_rhythm = array( 'muy_pausado', 'lento', 'algo_lento', 'normal', 'algo_rapido', 'rapido', 'muy_rapido' );
$allowed_move   = array( 'casi_nada', '1_2_mes', '1_semana', '2_semana', '3_4_semana', 'casi_diario', 'diario' );
$allowed_freq   = array_keys( $this->base_days );
$allowed_qty    = array( '0.1', '0.15', '0.25', '0.5', '0.75', '1', 'mas_1', 'concentrados' );
$allowed_power  = array( 'muy_suave', 'suave', 'media', 'alta', 'muy_alta', 'concentrado' );

$gender = isset( $data['gender'] ) ? sanitize_key( wp_unslash( $data['gender'] ) ) : '';
if ( ! in_array( $gender, $allowed_gender, true ) ) {
$errors->add( 'gender', __( 'Selecciona una opción válida en género.', 'thc-detox-calculator' ) );
}

$age = isset( $data['age'] ) ? absint( $data['age'] ) : 0;
if ( $age < 18 || $age > 122 ) {
$errors->add( 'age', __( 'La edad debe estar entre 18 y 122 años.', 'thc-detox-calculator' ) );
}

$weight_unit = isset( $data['weight_unit'] ) ? sanitize_key( wp_unslash( $data['weight_unit'] ) ) : '';
if ( ! in_array( $weight_unit, array( 'kg', 'lb' ), true ) ) {
$errors->add( 'weight_unit', __( 'La unidad de peso no es válida.', 'thc-detox-calculator' ) );
}

$weight_raw = isset( $data['weight'] ) ? str_replace( ',', '.', sanitize_text_field( wp_unslash( $data['weight'] ) ) ) : '';
$weight     = is_numeric( $weight_raw ) ? (float) $weight_raw : 0.0;

if ( 'kg' === $weight_unit && ( $weight < 30 || $weight > 240 ) ) {
$errors->add( 'weight', __( 'Si usas kg, el peso debe estar entre 30 y 240.', 'thc-detox-calculator' ) );
}

if ( 'lb' === $weight_unit && ( $weight < 66 || $weight > 529 ) ) {
$errors->add( 'weight', __( 'Si usas lb, el peso debe estar entre 66 y 529.', 'thc-detox-calculator' ) );
}

$rhythm = isset( $data['body_rhythm'] ) ? sanitize_key( wp_unslash( $data['body_rhythm'] ) ) : '';
if ( ! in_array( $rhythm, $allowed_rhythm, true ) ) {
$errors->add( 'body_rhythm', __( 'Selecciona una opción válida en ritmo corporal.', 'thc-detox-calculator' ) );
}

$movement = isset( $data['movement'] ) ? sanitize_key( wp_unslash( $data['movement'] ) ) : '';
if ( ! in_array( $movement, $allowed_move, true ) ) {
$errors->add( 'movement', __( 'Selecciona una opción válida en movimiento semanal.', 'thc-detox-calculator' ) );
}

$frequency = isset( $data['frequency'] ) ? sanitize_key( wp_unslash( $data['frequency'] ) ) : '';
if ( ! in_array( $frequency, $allowed_freq, true ) ) {
$errors->add( 'frequency', __( 'Selecciona una frecuencia habitual válida.', 'thc-detox-calculator' ) );
}

$quantity = isset( $data['quantity'] ) ? sanitize_key( wp_unslash( $data['quantity'] ) ) : '';
if ( ! in_array( $quantity, $allowed_qty, true ) ) {
$errors->add( 'quantity', __( 'Selecciona una cantidad por sesión válida.', 'thc-detox-calculator' ) );
}

$potency = isset( $data['potency'] ) ? sanitize_key( wp_unslash( $data['potency'] ) ) : '';
if ( ! in_array( $potency, $allowed_power, true ) ) {
$errors->add( 'potency', __( 'Selecciona una intensidad del producto válida.', 'thc-detox-calculator' ) );
}

$last_use = isset( $data['last_use'] ) ? sanitize_text_field( wp_unslash( $data['last_use'] ) ) : '';
if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $last_use ) ) {
$errors->add( 'last_use', __( 'La fecha de última toma no es válida.', 'thc-detox-calculator' ) );
} else {
$last_use_dt = DateTimeImmutable::createFromFormat( 'Y-m-d', $last_use );
$today       = new DateTimeImmutable( 'today', wp_timezone() );
if ( false === $last_use_dt || $last_use_dt->format( 'Y-m-d' ) !== $last_use ) {
$errors->add( 'last_use', __( 'La fecha de última toma no tiene un formato correcto.', 'thc-detox-calculator' ) );
} elseif ( $last_use_dt > $today ) {
$errors->add( 'last_use', __( 'La fecha de última toma no puede estar en el futuro.', 'thc-detox-calculator' ) );
}
}

if ( $errors->has_errors() ) {
return $errors;
}

$weight_kg = ( 'lb' === $weight_unit ) ? round( $weight * 0.45359237, 1 ) : round( $weight, 1 );

return array(
'gender'      => $gender,
'age'         => $age,
'weight'      => $weight,
'weight_unit' => $weight_unit,
'weight_kg'   => $weight_kg,
'body_rhythm' => $rhythm,
'movement'    => $movement,
'frequency'   => $frequency,
'quantity'    => $quantity,
'potency'     => $potency,
'last_use'    => $last_use,
);
}

/**
 * @param array<string, mixed> $sanitized
 *
 * @return float
 */
private function build_multiplier( array $sanitized ) {
$qty_map = array(
'0.1'          => 0.90,
'0.15'         => 0.97,
'0.25'         => 1.00,
'0.5'          => 1.12,
'0.75'         => 1.22,
'1'            => 1.33,
'mas_1'        => 1.48,
'concentrados' => 1.70,
);

$potency_map = array(
'muy_suave'   => 0.90,
'suave'       => 0.98,
'media'       => 1.05,
'alta'        => 1.15,
'muy_alta'    => 1.25,
'concentrado' => 1.42,
);

$rhythm_map = array(
'muy_pausado' => 1.20,
'lento'       => 1.12,
'algo_lento'  => 1.06,
'normal'      => 1.00,
'algo_rapido' => 0.95,
'rapido'      => 0.90,
'muy_rapido'  => 0.85,
);

$movement_map = array(
'casi_nada'   => 1.08,
'1_2_mes'     => 1.05,
'1_semana'    => 1.02,
'2_semana'    => 1.00,
'3_4_semana'  => 0.97,
'casi_diario' => 0.94,
'diario'      => 0.92,
);

$age_modifier = 0.0;
if ( $sanitized['age'] >= 60 ) {
$age_modifier = 0.07;
} elseif ( $sanitized['age'] >= 45 ) {
$age_modifier = 0.04;
} elseif ( $sanitized['age'] <= 24 ) {
$age_modifier = -0.03;
}

$weight_modifier = 0.0;
if ( $sanitized['weight_kg'] >= 110 ) {
$weight_modifier = 0.05;
} elseif ( $sanitized['weight_kg'] >= 90 ) {
$weight_modifier = 0.03;
} elseif ( $sanitized['weight_kg'] <= 55 ) {
$weight_modifier = -0.03;
}

$combined = $qty_map[ $sanitized['quantity'] ] * $potency_map[ $sanitized['potency'] ] * $rhythm_map[ $sanitized['body_rhythm'] ] * $movement_map[ $sanitized['movement'] ];
$combined = $combined * ( 1 + $age_modifier + $weight_modifier );

return max( 0.55, min( 2.80, round( $combined, 4 ) ) );
}

/**
 * @param float $probable
 * @param float $min_floor
 *
 * @return array<string, float>
 */
private function build_window_days( $probable, $min_floor ) {
$optimistic   = max( $min_floor, round( $probable * 0.78, 1 ) );
$probable_day = max( $optimistic, round( $probable, 1 ) );
$conservative = max( $probable_day, round( $probable * 1.35, 1 ) );

return array(
'optimistic'   => $optimistic,
'probable'     => $probable_day,
'conservative' => $conservative,
);
}

/**
 * @param DateTimeImmutable    $last_use
 * @param array<string, float> $window
 *
 * @return array<string, array<string, string>>
 */
private function build_window_dates( DateTimeImmutable $last_use, array $window ) {
$locale = get_locale();
if ( empty( $locale ) ) {
$locale = 'es_ES';
}

return array(
'optimistic'   => $this->format_result_date( $last_use, $window['optimistic'], $locale ),
'probable'     => $this->format_result_date( $last_use, $window['probable'], $locale ),
'conservative' => $this->format_result_date( $last_use, $window['conservative'], $locale ),
);
}

/**
 * @param DateTimeImmutable $base_date
 * @param float             $days
 * @param string            $locale
 *
 * @return array<string, string>
 */
private function format_result_date( DateTimeImmutable $base_date, $days, $locale ) {
$target = $base_date->modify( '+' . (int) ceil( $days ) . ' days' );

return array(
'iso'   => $target->format( 'Y-m-d' ),
'label' => wp_date( 'd M Y', $target->getTimestamp() ),
);
}

/**
 * @param array<string, array<string, string>> $dates
 *
 * @return array<string, string>
 */
private function build_tags( array $dates ) {
return array(
'optimistic'   => sprintf( __( 'Optimista: %s', 'thc-detox-calculator' ), $dates['optimistic']['label'] ),
'probable'     => sprintf( __( 'Probable: %s', 'thc-detox-calculator' ), $dates['probable']['label'] ),
'conservative' => sprintf( __( 'Conservadora: %s', 'thc-detox-calculator' ), $dates['conservative']['label'] ),
);
}

/**
 * @param array<string, float> $window
 *
 * @return string
 */
private function human_window( array $window ) {
return sprintf(
/* translators: 1: optimistic days, 2: conservative days */
__( 'Entre %1$s y %2$s días (estimación orientativa).', 'thc-detox-calculator' ),
number_format_i18n( $window['optimistic'], 1 ),
number_format_i18n( $window['conservative'], 1 )
);
}

/**
 * @param array<string, mixed> $sanitized
 * @param float                $multiplier
 *
 * @return array<int, string>
 */
private function build_influences( array $sanitized, $multiplier ) {
$lines   = array();
$base    = $this->base_days[ $sanitized['frequency'] ]['urine'];
$base_hi = $base >= 20;

if ( $base_hi ) {
$lines[] = __( 'Tu frecuencia de uso es el factor con mayor impacto en esta estimación.', 'thc-detox-calculator' );
} else {
$lines[] = __( 'La frecuencia habitual sigue siendo el factor más relevante para estimar la ventana.', 'thc-detox-calculator' );
}

if ( in_array( $sanitized['potency'], array( 'alta', 'muy_alta', 'concentrado' ), true ) ) {
$lines[] = __( 'La intensidad del producto indicada puede alargar la ventana de detección.', 'thc-detox-calculator' );
}

if ( in_array( $sanitized['quantity'], array( '1', 'mas_1', 'concentrados' ), true ) ) {
$lines[] = __( 'La cantidad por sesión incrementa el margen conservador de la estimación.', 'thc-detox-calculator' );
}

if ( $multiplier > 1.2 ) {
$lines[] = __( 'Tu combinación de ritmo corporal y patrón de consumo sugiere una eliminación más lenta.', 'thc-detox-calculator' );
} elseif ( $multiplier < 0.9 ) {
$lines[] = __( 'Tu ritmo corporal y actividad semanal podrían favorecer una ventana algo más corta.', 'thc-detox-calculator' );
}

$lines[] = __( 'El test en sangre suele detectar consumo más reciente que el test de orina.', 'thc-detox-calculator' );

return array_slice( array_values( array_unique( $lines ) ), 0, 4 );
}
}
