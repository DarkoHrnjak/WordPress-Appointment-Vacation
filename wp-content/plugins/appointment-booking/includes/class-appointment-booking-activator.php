<?php
/**
 * Handles plugin activation (database table creation).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Appointment_Booking_Activator {

	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// Create appointments table
		$appointments_table = $wpdb->prefix . 'appointments';
		$sql_appointments   = "CREATE TABLE IF NOT EXISTS $appointments_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            appointment_date date NOT NULL,
            appointment_time varchar(50) NOT NULL,
            appointment_name varchar(100) NOT NULL,
            appointment_phone varchar(20) NOT NULL,
            appointment_comments text,
            user_email varchar(100) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql_appointments );

		// Create vacation_dates table (if not exists)
		$vacation_table = $wpdb->prefix . 'vacation_dates';
		$sql_vacation   = "CREATE TABLE IF NOT EXISTS $vacation_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            vacation_start_date date NOT NULL,
            vacation_end_date date NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
		dbDelta( $sql_vacation );
	}
}