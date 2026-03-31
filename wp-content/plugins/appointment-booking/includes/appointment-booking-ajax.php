<?php
/**
 * AJAX-related functionality (fetching times, etc.).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function appointment_booking_ajax_init() {
	add_action( 'wp_ajax_fetch_appointment_times', 'fetch_appointment_times' );
	add_action( 'wp_ajax_nopriv_fetch_appointment_times', 'fetch_appointment_times' );
	
}



/**
 * AJAX callback to fetch available appointment times
 */
function fetch_appointment_times() {
	global $wpdb;
	$selected_date     = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : '';
	$available_times   = array( '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00' );

	$appointments_table = $wpdb->prefix . 'appointments';
	$vacation_table     = $wpdb->prefix . 'vacation_dates';

	// Get booked times for the selected date
	$booked_times = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT appointment_time FROM $appointments_table WHERE appointment_date = %s",
			$selected_date
		)
	);

	// Get vacation dates
	$vacation_dates = $wpdb->get_results( "SELECT vacation_start_date, vacation_end_date FROM $vacation_table", ARRAY_A );

	// Check if the selected date is within any vacation range
	foreach ( $vacation_dates as $vacation ) {
		if ( $selected_date >= $vacation['vacation_start_date'] && $selected_date <= $vacation['vacation_end_date'] ) {
			// Return empty array if the selected date is on vacation
			wp_send_json( array() );
		}
	}

	// Filter out booked times
	$available_times = array_diff( $available_times, $booked_times );

	wp_send_json( array_values( $available_times ) );
}

/**
 * Helper function to check availability
 */
function check_availability( $date, $time ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'appointments';

	// Check if an appointment exists for the given date/time
	$existing_appointment = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM $table_name WHERE appointment_date = %s AND appointment_time = %s",
			$date,
			$time
		)
	);

	return ( $existing_appointment ) ? 'unavailable' : 'available';


}

