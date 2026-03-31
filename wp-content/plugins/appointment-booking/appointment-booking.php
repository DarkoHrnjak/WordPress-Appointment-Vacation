<?php
/**
 * Plugin Name:       Appointment Booking
 * Plugin URI:        https://github.com/DarkoHrnjak/WordPress-Appointment-Vacation
 * Description:       A premium appointment booking system with vacation management, AJAX time slots, and modern glassmorphic UI.
 * Version:           2.0.0
 * Author:            Darko Hrnjak
 * Author URI:        https://github.com/DarkoHrnjak
 * Text Domain:       appointment-booking
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 5.6
 *
 * @package AppointmentBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin constants.
 */
define( 'APPOINTMENT_BOOKING_VERSION', '2.0.0' );
define( 'APPOINTMENT_BOOKING_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APPOINTMENT_BOOKING_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoload plugin files.
 */
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/class-appointment-booking-activator.php';
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/class-appointment-booking-deactivator.php';
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/appointment-booking-admin.php';
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/appointment-booking-frontend.php';
require_once APPOINTMENT_BOOKING_PLUGIN_DIR . 'includes/appointment-booking-ajax.php';

/**
 * Activation & deactivation hooks.
 */
register_activation_hook( __FILE__, array( 'Appointment_Booking_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Appointment_Booking_Deactivator', 'deactivate' ) );

/**
 * Bootstrap the plugin.
 *
 * Registers all admin, frontend, and AJAX hooks.
 *
 * @return void
 */
function appointment_booking_run() {
	appointment_booking_admin_init();
	appointment_booking_frontend_init();
	appointment_booking_ajax_init();
}
appointment_booking_run();
