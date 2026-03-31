<?php
/**
 * AJAX handlers — fetches available time slots and checks availability.
 *
 * @package AppointmentBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register AJAX actions for both logged-in and guest users.
 *
 * @return void
 */
function appointment_booking_ajax_init() {
	add_action( 'wp_ajax_fetch_appointment_times', 'appointment_booking_fetch_times' );
	add_action( 'wp_ajax_nopriv_fetch_appointment_times', 'appointment_booking_fetch_times' );
}

/**
 * AJAX callback — returns available appointment times for a given date.
 *
 * Checks the date against existing bookings and vacation ranges,
 * then returns a JSON array of remaining open time slots.
 *
 * @return void  Outputs JSON and terminates.
 */
function appointment_booking_fetch_times() {
	global $wpdb;

	$selected_date   = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : '';
	$available_times = array( '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00' );

	$appointments_table = $wpdb->prefix . 'appointments';
	$vacation_table     = $wpdb->prefix . 'vacation_dates';

	// Fetch already-booked times for the selected date.
	$booked_times = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT appointment_time FROM $appointments_table WHERE appointment_date = %s",
			$selected_date
		)
	);

	// Check if the date falls within any vacation range.
	$vacation_dates = $wpdb->get_results(
		"SELECT vacation_start_date, vacation_end_date FROM $vacation_table",
		ARRAY_A
	);

	foreach ( $vacation_dates as $vacation ) {
		if ( $selected_date >= $vacation['vacation_start_date'] && $selected_date <= $vacation['vacation_end_date'] ) {
			wp_send_json( array() );
		}
	}

	// Remove booked times from the pool.
	$available_times = array_diff( $available_times, $booked_times );

	wp_send_json( array_values( $available_times ) );
}

/**
 * Check whether a specific date + time slot is still available.
 *
 * @param  string $date  Date in Y-m-d format.
 * @param  string $time  Time in H:i format.
 * @return string        'available' or 'unavailable'.
 */
function appointment_booking_check_availability( $date, $time ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'appointments';

	$existing = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id FROM $table_name WHERE appointment_date = %s AND appointment_time = %s",
			$date,
			$time
		)
	);

	return $existing ? 'unavailable' : 'available';
}
