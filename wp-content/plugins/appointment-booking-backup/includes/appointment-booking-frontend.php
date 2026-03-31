<?php
/**
 * Frontend-related functionality (shortcodes, form handling, scripts).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Initialize Frontend
 */
function appointment_booking_frontend_init() {
	// Register shortcode
	add_shortcode( 'appointment_booking_form', 'appointment_booking_form_shortcode' );

	// Handle form submission
	add_action( 'init', 'handle_appointment_form_submission' );

	// Enqueue front-end scripts & styles
	add_action( 'wp_enqueue_scripts', 'appointment_booking_enqueue_scripts' );
}

/**
 * Enqueue scripts/styles on the front-end
 */
function appointment_booking_enqueue_scripts() {
	// Enqueue frontend CSS
	wp_enqueue_style(
		'appointment-booking-frontend-css',
		APPOINTMENT_BOOKING_PLUGIN_URL . 'assets/css/appointment-booking-frontend.css',
		array(),
		APPOINTMENT_BOOKING_VERSION
	);

	// Enqueue frontend JS
	wp_enqueue_script(
		'appointment-booking-script',
		APPOINTMENT_BOOKING_PLUGIN_URL . 'assets/js/appointment-booking.js',
		array( 'jquery' ),
		APPOINTMENT_BOOKING_VERSION,
		true
	);

	// Localize script data
	wp_localize_script(
		'appointment-booking-script',
		'appointment_booking_params',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);
}

/**
 * Shortcode for appointment booking form
 */
function appointment_booking_form_shortcode() {
	ob_start(); ?>
	<div class="appointment-booking-form">
		<h2><?php esc_html_e( 'Book an Appointment', 'appointment-booking' ); ?></h2>
		<form method="post" action="">
			<div class="form-group">
				<label for="appointment_name"><?php esc_html_e( 'Name:', 'appointment-booking' ); ?></label>
				<input type="text" id="appointment_name" name="appointment_name" required>
			</div>

			<div class="form-group">
				<label for="appointment_phone"><?php esc_html_e( 'Phone Number:', 'appointment-booking' ); ?></label>
				<input type="text" id="appointment_phone" name="appointment_phone" required>
			</div>

			<div class="form-group">
				<label for="appointment_date"><?php esc_html_e( 'Select Date:', 'appointment-booking' ); ?></label>
				<input type="date" id="appointment_date" name="appointment_date" required>
			</div>

			<div class="form-group">
				<label for="appointment_time"><?php esc_html_e( 'Select Time:', 'appointment-booking' ); ?></label>
				<select id="appointment_time" name="appointment_time" required>
					<option value=""><?php esc_html_e( 'Select Time', 'appointment-booking' ); ?></option>
					<!-- Dynamically generated times handled via AJAX -->
				</select>
			</div>

			<div class="form-group">
				<label for="appointment_comments"><?php esc_html_e( 'Additional Comments:', 'appointment-booking' ); ?></label>
				<textarea id="appointment_comments" name="appointment_comments" rows="4"></textarea>
			</div>

			<button type="submit" name="submit_appointment">
				<?php esc_html_e( 'Book Appointment', 'appointment-booking' ); ?>
			</button>
		</form>
	</div>


	<?php
	return ob_get_clean();
}

/**
 * Handle form submission
 */
function handle_appointment_form_submission() {
	if ( isset( $_POST['submit_appointment'] ) ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'appointments';

		$appointment_date     = sanitize_text_field( $_POST['appointment_date'] );
		$appointment_time     = sanitize_text_field( $_POST['appointment_time'] );
		$appointment_name     = sanitize_text_field( $_POST['appointment_name'] );
		$appointment_phone    = sanitize_text_field( $_POST['appointment_phone'] );
		$appointment_comments = sanitize_textarea_field( $_POST['appointment_comments'] );
		$user_email           = get_option( 'admin_email' );

		// Check availability
		$availability = check_availability( $appointment_date, $appointment_time );

		if ( 'available' === $availability ) {
			$wpdb->insert(
				$table_name,
				array(
					'appointment_date'     => $appointment_date,
					'appointment_time'     => $appointment_time,
					'appointment_name'     => $appointment_name,
					'appointment_phone'    => $appointment_phone,
					'appointment_comments' => $appointment_comments,
					'user_email'           => $user_email,
				)
			);
			// Redirect to avoid resubmission
			wp_redirect( add_query_arg( 'appointment', 'success', wp_get_referer() ) );
			exit;
		} else {
			// Show error if date/time not available
			echo '<div class="error">' . esc_html__( 'Sorry, the selected date and time are not available. Please choose another.', 'appointment-booking' ) . '</div>';
		}
	}
}
