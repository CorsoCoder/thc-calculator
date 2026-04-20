<?php
if ( ! defined( 'ABSPATH' ) ) {
exit;
}

$view_data = isset( $view_data ) && is_array( $view_data ) ? $view_data : array();
$default_data = THC_Detox_Calculator_Shortcode::get_default_view_data();
$view_data = wp_parse_args( $view_data, $default_data );
?>
<div class="thc-detox-calculator" data-thc-detox-calculator style="<?php echo esc_attr( $view_data['inline_style'] ); ?>">
<form class="thc-detox-calculator__form" novalidate>
<div class="thc-detox-calculator__header">
<h2><?php echo esc_html( $view_data['title'] ); ?></h2>
<p><?php echo esc_html( $view_data['description'] ); ?></p>
</div>

<div class="thc-detox-calculator__alert thc-detox-calculator__alert--disclaimer" role="note">
<?php echo esc_html( $view_data['disclaimer'] ); ?>
</div>

<div class="thc-detox-calculator__errors" role="alert" aria-live="polite" hidden></div>

<section class="thc-detox-step is-active" data-step="1">
<h3><?php echo esc_html__( 'Paso 1 · Tu perfil', 'thc-detox-calculator' ); ?></h3>
<div class="thc-detox-grid">
<label>
<span><?php echo esc_html__( 'Género', 'thc-detox-calculator' ); ?></span>
<select name="gender" required>
<option value=""><?php echo esc_html__( 'Selecciona', 'thc-detox-calculator' ); ?></option>
<option value="hombre"><?php echo esc_html__( 'Hombre', 'thc-detox-calculator' ); ?></option>
<option value="mujer"><?php echo esc_html__( 'Mujer', 'thc-detox-calculator' ); ?></option>
<option value="no_binario"><?php echo esc_html__( 'No binario / Otro', 'thc-detox-calculator' ); ?></option>
<option value="prefiero_no_decir"><?php echo esc_html__( 'Prefiero no decirlo', 'thc-detox-calculator' ); ?></option>
</select>
</label>
<label>
<span><?php echo esc_html__( 'Edad', 'thc-detox-calculator' ); ?></span>
<input type="number" name="age" min="18" max="122" required />
</label>
<div class="thc-detox-inline">
<label>
<span><?php echo esc_html__( 'Peso', 'thc-detox-calculator' ); ?></span>
<input type="number" name="weight" min="30" max="240" step="0.1" required />
</label>
<label>
<span><?php echo esc_html__( 'Unidad', 'thc-detox-calculator' ); ?></span>
<select name="weight_unit" required>
<option value="kg">kg</option>
<option value="lb">lb</option>
</select>
</label>
</div>
</div>
</section>

<section class="thc-detox-step" data-step="2">
<h3><?php echo esc_html__( 'Paso 2 · Cómo responde tu cuerpo', 'thc-detox-calculator' ); ?></h3>
<div class="thc-detox-grid">
<label>
<span><?php echo esc_html__( 'Ritmo corporal', 'thc-detox-calculator' ); ?></span>
<select name="body_rhythm" required>
<option value=""><?php echo esc_html__( 'Selecciona', 'thc-detox-calculator' ); ?></option>
<option value="muy_pausado"><?php echo esc_html__( 'Muy pausado', 'thc-detox-calculator' ); ?></option>
<option value="lento"><?php echo esc_html__( 'Más lento de lo normal', 'thc-detox-calculator' ); ?></option>
<option value="algo_lento"><?php echo esc_html__( 'Algo lento', 'thc-detox-calculator' ); ?></option>
<option value="normal"><?php echo esc_html__( 'Normal', 'thc-detox-calculator' ); ?></option>
<option value="algo_rapido"><?php echo esc_html__( 'Algo rápido', 'thc-detox-calculator' ); ?></option>
<option value="rapido"><?php echo esc_html__( 'Rápido', 'thc-detox-calculator' ); ?></option>
<option value="muy_rapido"><?php echo esc_html__( 'Muy rápido', 'thc-detox-calculator' ); ?></option>
</select>
</label>
</div>

<fieldset>
<legend><?php echo esc_html__( 'Movimiento semanal', 'thc-detox-calculator' ); ?></legend>
<div class="thc-chip-group">
<label class="thc-chip"><input type="radio" name="movement" value="casi_nada" required /><span><?php echo esc_html__( 'Casi nada', 'thc-detox-calculator' ); ?></span></label>
<label class="thc-chip"><input type="radio" name="movement" value="1_2_mes" /><span><?php echo esc_html__( '1–2 veces al mes', 'thc-detox-calculator' ); ?></span></label>
<label class="thc-chip"><input type="radio" name="movement" value="1_semana" /><span><?php echo esc_html__( '1 vez por semana', 'thc-detox-calculator' ); ?></span></label>
<label class="thc-chip"><input type="radio" name="movement" value="2_semana" /><span><?php echo esc_html__( '2 veces por semana', 'thc-detox-calculator' ); ?></span></label>
<label class="thc-chip"><input type="radio" name="movement" value="3_4_semana" /><span><?php echo esc_html__( '3–4 veces por semana', 'thc-detox-calculator' ); ?></span></label>
<label class="thc-chip"><input type="radio" name="movement" value="casi_diario" /><span><?php echo esc_html__( 'Casi a diario', 'thc-detox-calculator' ); ?></span></label>
<label class="thc-chip"><input type="radio" name="movement" value="diario" /><span><?php echo esc_html__( 'Diario', 'thc-detox-calculator' ); ?></span></label>
</div>
</fieldset>
</section>

<section class="thc-detox-step" data-step="3">
<h3><?php echo esc_html__( 'Paso 3 · Tu patrón de consumo', 'thc-detox-calculator' ); ?></h3>
<div class="thc-detox-grid">
<label>
<span><?php echo esc_html__( 'Frecuencia habitual', 'thc-detox-calculator' ); ?></span>
<select name="frequency" required>
<option value=""><?php echo esc_html__( 'Selecciona', 'thc-detox-calculator' ); ?></option>
<option value="uso_aislado"><?php echo esc_html__( 'Uso aislado', 'thc-detox-calculator' ); ?></option>
<option value="algunos_dias"><?php echo esc_html__( 'Algunos días', 'thc-detox-calculator' ); ?></option>
<option value="varias_veces_semana"><?php echo esc_html__( 'Varias veces por semana', 'thc-detox-calculator' ); ?></option>
<option value="casi_diario"><?php echo esc_html__( 'Casi diario', 'thc-detox-calculator' ); ?></option>
<option value="diario"><?php echo esc_html__( 'Diario', 'thc-detox-calculator' ); ?></option>
<option value="varias_veces_dia"><?php echo esc_html__( 'Varias veces al día', 'thc-detox-calculator' ); ?></option>
</select>
</label>
<label>
<span><?php echo esc_html__( 'Cantidad por sesión', 'thc-detox-calculator' ); ?></span>
<select name="quantity" required>
<option value=""><?php echo esc_html__( 'Selecciona', 'thc-detox-calculator' ); ?></option>
<option value="0.1">0.1 g</option>
<option value="0.15">0.15 g</option>
<option value="0.25">0.25 g</option>
<option value="0.5">0.5 g</option>
<option value="0.75">0.75 g</option>
<option value="1">1 g</option>
<option value="mas_1"><?php echo esc_html__( 'Más de 1 g', 'thc-detox-calculator' ); ?></option>
<option value="concentrados"><?php echo esc_html__( 'Concentrados / dabs / extractos', 'thc-detox-calculator' ); ?></option>
</select>
</label>
<label>
<span><?php echo esc_html__( 'Intensidad del producto', 'thc-detox-calculator' ); ?></span>
<select name="potency" required>
<option value=""><?php echo esc_html__( 'Selecciona', 'thc-detox-calculator' ); ?></option>
<option value="muy_suave"><?php echo esc_html__( 'Muy suave', 'thc-detox-calculator' ); ?></option>
<option value="suave"><?php echo esc_html__( 'Suave', 'thc-detox-calculator' ); ?></option>
<option value="media"><?php echo esc_html__( 'Media', 'thc-detox-calculator' ); ?></option>
<option value="alta"><?php echo esc_html__( 'Alta', 'thc-detox-calculator' ); ?></option>
<option value="muy_alta"><?php echo esc_html__( 'Muy alta', 'thc-detox-calculator' ); ?></option>
<option value="concentrado"><?php echo esc_html__( 'Concentrado', 'thc-detox-calculator' ); ?></option>
</select>
</label>
</div>
</section>

<section class="thc-detox-step" data-step="4">
<h3><?php echo esc_html__( 'Paso 4 · Última toma', 'thc-detox-calculator' ); ?></h3>
<div class="thc-detox-grid">
<label>
<span><?php echo esc_html__( 'Fecha de última toma', 'thc-detox-calculator' ); ?></span>
<input type="date" name="last_use" max="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" required />
</label>
</div>
</section>

<section class="thc-detox-step" data-step="5">
<h3><?php echo esc_html__( 'Paso 5 · Tu ventana estimada', 'thc-detox-calculator' ); ?></h3>
<div class="thc-detox-results" data-results hidden></div>
</section>

<div class="thc-detox-calculator__footer">
<button type="button" class="thc-btn thc-btn--ghost" data-prev><?php echo esc_html( $view_data['previous_button'] ); ?></button>
<button type="button" class="thc-btn" data-next><?php echo esc_html( $view_data['next_button'] ); ?></button>
<button type="submit" class="thc-btn thc-btn--accent" data-submit data-loading-text="<?php echo esc_attr( $view_data['submit_loading'] ); ?>" data-default-text="<?php echo esc_attr( $view_data['submit_button'] ); ?>" hidden><?php echo esc_html( $view_data['submit_button'] ); ?></button>
</div>
</form>
</div>
