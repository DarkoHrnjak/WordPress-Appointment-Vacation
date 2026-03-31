<?php
/**
 * Frontend — shortcode rendering, script enqueuing, and form submission.
 *
 * @package AppointmentBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap all frontend hooks.
 *
 * @return void
 */
function appointment_booking_frontend_init() {
	add_shortcode( 'appointment_booking_form', 'appointment_booking_form_shortcode' );
	add_action( 'init', 'appointment_booking_handle_submission' );
	add_action( 'wp_enqueue_scripts', 'appointment_booking_enqueue_frontend_assets' );
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function appointment_booking_enqueue_frontend_assets() {
	wp_enqueue_style(
		'appointment-booking-frontend-css',
		APPOINTMENT_BOOKING_PLUGIN_URL . 'assets/css/appointment-booking-frontend.css',
		array(),
		APPOINTMENT_BOOKING_VERSION
	);

	wp_enqueue_script(
		'appointment-booking-script',
		APPOINTMENT_BOOKING_PLUGIN_URL . 'assets/js/appointment-booking.js',
		array( 'jquery' ),
		APPOINTMENT_BOOKING_VERSION,
		true
	);

	wp_localize_script(
		'appointment-booking-script',
		'appointment_booking_params',
		array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) )
	);
}

/**
 * Render the public booking form via [appointment_booking_form] shortcode.
 *
 * @return string  HTML output.
 */
function appointment_booking_form_shortcode() {
	ob_start();
	?>
	<div class="appointment-booking-form">
		<h2><?php esc_html_e( 'Book an Appointment', 'appointment-booking' ); ?></h2>
		<p class="subtitle"><?php esc_html_e( 'Available times are between 9 AM and 4 PM.', 'appointment-booking' ); ?></p>

		<form method="post" action="">
			<div class="form-group">
				<label for="appointment_name"><?php esc_html_e( 'Name', 'appointment-booking' ); ?></label>
				<input type="text" id="appointment_name" name="appointment_name" placeholder="<?php esc_attr_e( 'Your full name', 'appointment-booking' ); ?>" required>
			</div>

			<div class="form-group">
				<label for="appointment_phone"><?php esc_html_e( 'Phone Number', 'appointment-booking' ); ?></label>
				<input
					type="tel"
					id="appointment_phone"
					name="appointment_phone"
					class="appointment-phone-number"
					inputmode="tel"
					autocomplete="tel"
					pattern="[0-9+\-\s()]+"
					title="<?php esc_attr_e( 'Digits, spaces, dashes, and parentheses only', 'appointment-booking' ); ?>"
					placeholder="<?php esc_attr_e( '+1 (555) 123-4567', 'appointment-booking' ); ?>"
					required
				>
			</div>

			<div class="form-group">
				<label for="appointment_date"><?php esc_html_e( 'Date', 'appointment-booking' ); ?></label>
				<input type="date" id="appointment_date" name="appointment_date" min="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>" required>
			</div>

			<div class="form-group">
				<label for="appointment_time"><?php esc_html_e( 'Time', 'appointment-booking' ); ?></label>
				<select id="appointment_time" name="appointment_time" required disabled>
					<option value=""><?php esc_html_e( 'Select a date first', 'appointment-booking' ); ?></option>
				</select>
			</div>

			<div class="form-group">
				<label for="appointment_comments"><?php esc_html_e( 'Comments', 'appointment-booking' ); ?></label>
				<textarea id="appointment_comments" name="appointment_comments" rows="4" placeholder="<?php esc_attr_e( 'Any additional notes…', 'appointment-booking' ); ?>"></textarea>
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
 * Process the frontend booking form submission.
 *
 * Validates input, checks availability, inserts the record,
 * and redirects to prevent duplicate submissions.
 *
 * @return void
 */
function appointment_booking_handle_submission() {
	if ( ! isset( $_POST['submit_appointment'] ) ) {
		return;
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'appointments';

	// Sanitise all inputs.
	$date     = sanitize_text_field( $_POST['appointment_date'] );
	$time     = sanitize_text_field( $_POST['appointment_time'] );
	$name     = sanitize_text_field( $_POST['appointment_name'] );
	$phone    = sanitize_text_field( $_POST['appointment_phone'] );
	$comments = sanitize_textarea_field( $_POST['appointment_comments'] );
	$email    = get_option( 'admin_email' );

	// Validate phone — digits and phone-formatting characters only.
	if ( ! preg_match( '/^[0-9+\-\s()]+$/', $phone ) ) {
		echo '<div class="error">' . esc_html__( 'Please enter a valid phone number.', 'appointment-booking' ) . '</div>';
		return;
	}

	// Reject past dates.
	if ( strtotime( $date ) < strtotime( date( 'Y-m-d' ) ) ) {
		echo '<div class="error">' . esc_html__( 'You cannot book an appointment for a past date.', 'appointment-booking' ) . '</div>';
		return;
	}

	// Check slot availability.
	if ( 'available' !== appointment_booking_check_availability( $date, $time ) ) {
		echo '<div class="error">' . esc_html__( 'This time slot is no longer available. Please choose another.', 'appointment-booking' ) . '</div>';
		return;
	}

	// Insert the new appointment.
	$wpdb->insert(
		$table_name,
		array(
			'appointment_date'     => $date,
			'appointment_time'     => $time,
			'appointment_name'     => $name,
			'appointment_phone'    => $phone,
			'appointment_comments' => $comments,
			'user_email'           => $email,
		)
	);

	// Redirect to prevent double-submission on refresh.
	wp_redirect( add_query_arg( 'appointment', 'success', wp_get_referer() ) );
	exit;
}
