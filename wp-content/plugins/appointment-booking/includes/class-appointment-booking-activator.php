<?php
/**
 * Plugin activator — creates database tables on activation.
 *
 * @package AppointmentBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Appointment_Booking_Activator
 *
 * Handles one-time setup when the plugin is activated.
 */
class Appointment_Booking_Activator {

	/**
	 * Run on plugin activation.
	 *
	 * Creates the `appointments` and `vacation_dates` tables
	 * using WordPress's dbDelta for safe, idempotent migrations.
	 *
	 * @return void
	 */
	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// -- Appointments table --
		$appointments_table = $wpdb->prefix . 'appointments';
		$sql_appointments   = "CREATE TABLE IF NOT EXISTS $appointments_table (
			id            MEDIUMINT(9)  NOT NULL AUTO_INCREMENT,
			appointment_date    DATE          NOT NULL,
			appointment_time    VARCHAR(50)   NOT NULL,
			appointment_name    VARCHAR(100)  NOT NULL,
			appointment_phone   VARCHAR(20)   NOT NULL,
			appointment_comments TEXT,
			user_email          VARCHAR(100)  NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta( $sql_appointments );

		// -- Vacation dates table --
		$vacation_table = $wpdb->prefix . 'vacation_dates';
		$sql_vacation   = "CREATE TABLE IF NOT EXISTS $vacation_table (
			id                  MEDIUMINT(9)  NOT NULL AUTO_INCREMENT,
			vacation_start_date DATE          NOT NULL,
			vacation_end_date   DATE          NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta( $sql_vacation );
	}
}