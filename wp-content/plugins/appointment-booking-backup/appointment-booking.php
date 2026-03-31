<?php
/**
 * Plugin Name:       Appointment Booking
 * Plugin URI:        https://example.com
 * Description:       Custom appointment booking functionality.
 * Version:           1.0
 * Author:            Darko
 * Author URI:        https://example.com
 * Text Domain:       appointment-booking
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Define constants
 */
define( 'APPOINTMENT_BOOKING_VERSION', '1.0' );
define( 'APPOINTMENT_BOOKING_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APPOINTMENT_BOOKING_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Include necessary files
 */
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/class-appointment-booking-activator.php';
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/class-appointment-booking-deactivator.php';
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/appointment-booking-admin.php';
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/appointment-booking-frontend.php';
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/appointment-booking-ajax.php';

/**
 * Register activation and deactivation hooks
 */
register_activation_hook( __FILE__, array( 'Appointment_Booking_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Appointment_Booking_Deactivator', 'deactivate' ) );

/**
 * Plugin initialization
 */
function run_appointment_booking() {
	// Admin setup
	appointment_booking_admin_init();

	// Frontend setup
	appointment_booking_frontend_init();

	// AJAX setup
	appointment_booking_ajax_init();
}
run_appointment_booking();

