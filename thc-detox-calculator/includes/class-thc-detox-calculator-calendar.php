<?php

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class THC_Detox_Calculator_Calendar {

/**
 * @param string $test_type
 * @param string $date_iso
 *
 * @return array<string, string>
 */
public function build_calendar_data( $test_type, $date_iso ) {
$event_title = 'urine' === $test_type
? __( 'Fecha estimada de ventana THC - orina', 'thc-detox-calculator' )
: __( 'Fecha estimada de ventana THC - sangre', 'thc-detox-calculator' );

$start = new DateTimeImmutable( $date_iso . ' 09:00:00', wp_timezone() );
$end   = $start->modify( '+1 hour' );

$google_url = add_query_arg(
array(
'action'   => 'TEMPLATE',
'text'     => rawurlencode( $event_title ),
'dates'    => $this->to_google_date( $start ) . '/' . $this->to_google_date( $end ),
'details'  => rawurlencode( __( 'Fecha estimada orientativa de ventana de detección. No garantiza resultado negativo.', 'thc-detox-calculator' ) ),
'location' => rawurlencode( __( 'Recordatorio personal', 'thc-detox-calculator' ) ),
),
'https://calendar.google.com/calendar/render'
);

$ics_content = $this->build_ics(
$event_title,
$start,
$end,
__( 'Fecha estimada orientativa de ventana de detección de THC. Herramienta sin validez médica, legal ni forense.', 'thc-detox-calculator' )
);

return array(
'title'    => $event_title,
'google'   => esc_url_raw( $google_url ),
'ics_name' => sanitize_file_name( strtolower( str_replace( ' ', '-', remove_accents( $event_title ) ) ) . '.ics' ),
'ics'      => base64_encode( $ics_content ),
);
}

/**
 * @param DateTimeImmutable $date
 *
 * @return string
 */
private function to_google_date( DateTimeImmutable $date ) {
return gmdate( 'Ymd\THis\Z', $date->getTimestamp() );
}

/**
 * @param string            $title
 * @param DateTimeImmutable $start
 * @param DateTimeImmutable $end
 * @param string            $description
 *
 * @return string
 */
private function build_ics( $title, DateTimeImmutable $start, DateTimeImmutable $end, $description ) {
$uid      = wp_generate_uuid4() . '@thc-detox-calculator';
$dt_stamp = gmdate( 'Ymd\THis\Z' );
$dt_start = gmdate( 'Ymd\THis\Z', $start->getTimestamp() );
$dt_end   = gmdate( 'Ymd\THis\Z', $end->getTimestamp() );

$lines = array(
'BEGIN:VCALENDAR',
'VERSION:2.0',
'PRODID:-//THC Detox Calculator//ES',
'CALSCALE:GREGORIAN',
'BEGIN:VEVENT',
'UID:' . $uid,
'DTSTAMP:' . $dt_stamp,
'DTSTART:' . $dt_start,
'DTEND:' . $dt_end,
'SUMMARY:' . $this->escape_ics_text( $title ),
'DESCRIPTION:' . $this->escape_ics_text( $description ),
'END:VEVENT',
'END:VCALENDAR',
);

return implode( "\r\n", $lines ) . "\r\n";
}

/**
 * @param string $text
 *
 * @return string
 */
private function escape_ics_text( $text ) {
$text = str_replace( '\\', '\\\\', $text );
$text = str_replace( ';', '\\;', $text );
$text = str_replace( ',', '\\,', $text );

return str_replace( array( "\r\n", "\n", "\r" ), '\\n', $text );
}
}
