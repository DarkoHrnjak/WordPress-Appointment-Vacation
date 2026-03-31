<?php
/**
 * Handles plugin deactivation.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Appointment_Booking_Deactivator {

	public static function deactivate() {
		// Place any deactivation code here,
		// such as removing cron jobs, flushing rewrite rules, etc.
		// e.g.: flush_rewrite_rules();
	}
}