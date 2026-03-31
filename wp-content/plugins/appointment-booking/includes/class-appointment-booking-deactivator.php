<?php
/**
 * Plugin deactivator — cleanup on deactivation.
 *
 * @package AppointmentBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Appointment_Booking_Deactivator
 *
 * Handles cleanup when the plugin is deactivated.
 * Tables are intentionally preserved so data is not lost.
 */
class Appointment_Booking_Deactivator {

	/**
	 * Run on plugin deactivation.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Reserved for future cleanup (cron jobs, transients, etc.).
	}
}