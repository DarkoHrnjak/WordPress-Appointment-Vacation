<?php
ob_start();
error_reporting( E_ALL & ~E_DEPRECATED );

/**
 * Admin-related functionality (menus, pages, admin scripts).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function appointment_booking_safe_redirect( $args = array(), $fallback = '' ) {
	$referer = trim( (string) wp_get_referer() );
	if ( empty( $referer ) ) {
		$referer = ! empty( $fallback ) ? $fallback : admin_url('admin.php?page=appointment-booking-vacation');
	}
	$redirect_url = add_query_arg( $args, $referer );
	if ( ob_get_length() ) { ob_clean(); }
	wp_redirect( $redirect_url );
	exit;
}


/**
 * Initialize Admin
 */
function appointment_booking_admin_init() {

	// Add admin menu items
	add_action( 'admin_menu', 'appointment_booking_add_admin_menu' );
	// Enqueue admin scripts and styles
	add_action( 'admin_enqueue_scripts', 'appointment_booking_admin_enqueue_scripts' );
}
add_action('init', 'appointment_booking_admin_init');

/**
 * Enqueue scripts/styles in admin area
 */
function appointment_booking_admin_enqueue_scripts( $hook ) {
	// Optionally, only load on our plugin pages.
	wp_enqueue_style(
		'appointment-booking-admin-css',
		APPOINTMENT_BOOKING_PLUGIN_URL . 'assets/css/appointment-booking-admin.css',
		array(),
		APPOINTMENT_BOOKING_VERSION
	);
	wp_enqueue_script(
		'appointment-booking-admin-script',
		APPOINTMENT_BOOKING_PLUGIN_URL . 'assets/js/appointment-booking.js',
		array( 'jquery' ),
		APPOINTMENT_BOOKING_VERSION,
		true
	);
	wp_localize_script(
		'appointment-booking-admin-script',
		'appointment_booking_params',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);
}

/**
 * Add top-level and sub-level menu pages
 */

function appointment_booking_add_admin_menu() {
	// Main menu page.
	add_menu_page(
		__( 'Appointment Booking', 'appointment-booking' ),
		__( 'Appointment Booking', 'appointment-booking' ),
		'manage_options',
		'appointment-booking',
		'appointment_booking_admin_page',
		'dashicons-calendar'
	);
	// Appointments page.
	add_submenu_page(
		'appointment-booking',
		__( 'Appointments', 'appointment-booking' ),
		__( 'Appointments', 'appointment-booking' ),
		'manage_options',
		'appointment-booking-appointments',
		'appointment_booking_appointments_page_content'
	);
	// Vacation page.
	add_submenu_page(
		'appointment-booking',
		__( 'Vacation Booking', 'appointment-booking' ),
		__( 'Vacation Booking', 'appointment-booking' ),
		'manage_options',
		'appointment-booking-vacation',
		'appointment_booking_vacation_page_content'
	);
	// Hidden edit pages (registered with capability "read" so any logged-in user can access).
	add_submenu_page(
		null,
		__( 'Edit Appointment', 'appointment-booking' ),
		__( 'Edit Appointment', 'appointment-booking' ),
		'read',  // using 'read' so that any logged-in user can access it
		'appointment-booking-edit-appointment',
		'appointment_booking_edit_appointment_page'
	);

	add_submenu_page(
		null,
		__( 'Edit Vacation', 'appointment-booking' ),
		__( 'Edit Vacation', 'appointment-booking' ),
		'read',  // using 'read' for unrestricted access
		'appointment-booking-edit-vacation',
		'appointment_booking_edit_vacation_page'
	);


}

/**
 * Main Admin Page
 */
function appointment_booking_admin_page() {
	?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-calendar-alt" style="font-size: 32px; width: 32px; height: 32px; color: var(--primary-color);"></span>
            <?php esc_html_e( 'Appointment Booking', 'appointment-booking' ); ?>
        </h1>
        <div class="premium-card">
            <p style="color: var(--text-muted); margin-bottom: 25px;"><?php esc_html_e( 'Manage your appointment settings here.', 'appointment-booking' ); ?></p>
		    <?php echo do_shortcode('[appointment_booking_form]'); ?>
        </div>
    </div>
	<?php

}

/**
 * Appointments Page
 */
function appointment_booking_appointments_page_content() {
	// Process deletion before output.
	if ( isset( $_POST['delete_appointment'] ) ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'appointments';
		$appointment_id = intval( $_POST['delete_appointment'] );
		if ( $appointment_id > 0 ) {
			$wpdb->delete( $table_name, array( 'id' => $appointment_id ), array( '%d' ) );
		}
		if ( ! ob_start() ) { ob_start(); }
		if ( ob_get_length() ) { ob_clean(); }
		wp_redirect( admin_url( 'admin.php?page=appointment-booking-appointments&delete=success' ) );
		exit;
	}

	global $wpdb;
	$table_name   = $wpdb->prefix . 'appointments';
	$search_date  = isset( $_POST['search_date'] ) ? sanitize_text_field( $_POST['search_date'] ) : '';
	$search_name  = isset( $_POST['search_name'] ) ? sanitize_text_field( $_POST['search_name'] ) : '';
	$search_phone = isset( $_POST['search_phone'] ) ? sanitize_text_field( $_POST['search_phone'] ) : '';

	$query = "SELECT * FROM $table_name WHERE 1=1";
	if ( ! empty( $search_date ) ) {
		$query .= $wpdb->prepare( ' AND appointment_date = %s', $search_date );
	}
	if ( ! empty( $search_name ) ) {
		$query .= $wpdb->prepare( ' AND appointment_name LIKE %s', '%' . $wpdb->esc_like( $search_name ) . '%' );
	}
	if ( ! empty( $search_phone ) ) {
		$query .= $wpdb->prepare( ' AND appointment_phone LIKE %s', '%' . $wpdb->esc_like( $search_phone ) . '%' );
	}

	$appointments = $wpdb->get_results( $query, ARRAY_A );
	?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-list-view" style="font-size: 32px; width: 32px; height: 32px; color: var(--primary-color);"></span>
            <?php esc_html_e( 'Appointments', 'appointment-booking' ); ?>
        </h1>
        <p style="color: var(--text-muted); margin-bottom: 25px;"><?php esc_html_e( 'View appointments here.', 'appointment-booking' ); ?></p>
        <form method="post" action="" class="appointment-search-form">
            <input type="date" name="search_date" value="<?php echo esc_attr( $search_date ); ?>" placeholder="<?php esc_attr_e( 'Search by Date', 'appointment-booking' ); ?>">
            <input type="text" name="search_name" value="<?php echo esc_attr( $search_name ); ?>" placeholder="<?php esc_attr_e( 'Search by Name', 'appointment-booking' ); ?>">
            <input type="text" name="search_phone" value="<?php echo esc_attr( $search_phone ); ?>" placeholder="<?php esc_attr_e( 'Search by Phone', 'appointment-booking' ); ?>">
            <button type="submit" class="button button-primary"><?php esc_html_e( 'Search', 'appointment-booking' ); ?></button>
        </form>
        <div class="premium-card" style="padding: 0;">
            <table class="appointments-table" style="margin-top: 0;">
                <thead>
                <tr>
                    <th><?php esc_html_e( 'Date', 'appointment-booking' ); ?></th>
                    <th><?php esc_html_e( 'Time', 'appointment-booking' ); ?></th>
                    <th><?php esc_html_e( 'Name', 'appointment-booking' ); ?></th>
                    <th><?php esc_html_e( 'Phone', 'appointment-booking' ); ?></th>
                    <th><?php esc_html_e( 'Comments', 'appointment-booking' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'appointment-booking' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ( ! empty( $appointments ) ) : ?>
                    <?php foreach ( $appointments as $appointment ) : ?>
                        <tr>
                            <td><?php echo esc_html( $appointment['appointment_date'] ); ?></td>
                            <td><?php echo esc_html( $appointment['appointment_time'] ); ?></td>
                            <td><?php echo esc_html( $appointment['appointment_name'] ); ?></td>
                            <td><?php echo esc_html( $appointment['appointment_phone'] ); ?></td>
                            <td><?php echo esc_html( $appointment['appointment_comments'] ); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=appointment-booking-edit-appointment&id=' . $appointment['id']); ?>" class="button button-primary" style="margin-right: 5px;">Edit</a>
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="delete_appointment" value="<?php echo esc_attr($appointment['id']); ?>">
                                    <button type="submit" class="button button-primary" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);"><?php esc_html_e( 'No appointments found.', 'appointment-booking' ); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
	<?php
}

/**
 * Hidden Edit Appointment Page
 */
function appointment_booking_edit_appointment_page() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'appointments';
	$appointment_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
	if ( $appointment_id <= 0 ) {
		echo '<div class="notice notice-error"><p>Invalid appointment ID.</p></div>';
		return;
	}
	if ( isset($_POST['update_appointment']) ) {
		$appointment_date    = sanitize_text_field( $_POST['appointment_date'] );
		$appointment_time    = sanitize_text_field( $_POST['appointment_time'] );
		$appointment_name    = sanitize_text_field( $_POST['appointment_name'] );
		$appointment_phone    = sanitize_text_field( $_POST['appointment_phone'] );
		$appointment_comments = sanitize_textarea_field( $_POST['appointment_comments'] );

		// Phone number validation server-side
		if ( !preg_match('/^[0-9+\-\s()]+$/', $appointment_phone) ) {
			echo '<div class="notice notice-error"><p>Please enter a valid phone number.</p></div>';
			return;
		}

		if ( strtotime( $appointment_date ) < strtotime( date( 'Y-m-d' ) ) ) {
			echo '<div class="notice notice-error"><p>Sorry, you cannot update an appointment to a past date.</p></div>';
			return;
		}

		$wpdb->update(
			$table_name,
			array(
				'appointment_date'    => $appointment_date,
				'appointment_time'    => $appointment_time,
				'appointment_name'    => $appointment_name,
				'appointment_phone'   => $appointment_phone,
				'appointment_comments'=> $appointment_comments,
			),
			array( 'id' => $appointment_id )
		);
		if ( ! ob_start() ) { ob_start(); }
		wp_redirect( admin_url('admin.php?page=appointment-booking-appointments&update=success') );
		exit;
	}
	$appointment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $appointment_id ), ARRAY_A );
	if ( ! $appointment ) {
		echo '<div class="notice notice-error"><p>Appointment not found.</p></div>';
		return;
	}
	?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-edit-large" style="font-size: 32px; width: 32px; height: 32px; color: var(--primary-color);"></span>
            Edit Appointment
        </h1>
        <div class="premium-card" style="max-width: 600px;">
            <form method="post" action="">
                <div class="form-group">
                    <label for="appointment_date">Date</label>
                    <input type="date" name="appointment_date" id="appointment_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo esc_attr( $appointment['appointment_date'] ); ?>" required>
                </div>
                <div class="form-group">
                    <label for="appointment_time">Time</label>
                    <input type="time" name="appointment_time" id="appointment_time" min="09:00" max="16:00" value="<?php echo esc_attr( $appointment['appointment_time'] ); ?>" required>
                </div>
                <div class="form-group">
                    <label for="appointment_name">Name</label>
                    <input type="text" name="appointment_name" id="appointment_name" value="<?php echo esc_attr( $appointment['appointment_name'] ); ?>" required>
                </div>
                <div class="form-group">
                    <label for="appointment_phone">Phone</label>
                    <input type="tel" name="appointment_phone" id="appointment_phone" class="appointment-phone-number" value="<?php echo esc_attr( $appointment['appointment_phone'] ); ?>" pattern="[0-9+\-\s()]+" title="Please enter a valid phone number" required>
                </div>
                <div class="form-group">
                    <label for="appointment_comments">Comments</label>
                    <textarea name="appointment_comments" id="appointment_comments" rows="4"><?php echo esc_textarea( $appointment['appointment_comments'] ); ?></textarea>
                </div>
                <p class="submit" style="margin-bottom: 0;">
                    <button type="submit" name="update_appointment" class="button button-primary">Update Appointment</button>
                </p>
            </form>
        </div>
    </div>
	<?php
}

/**
 * Vacation Page
 */
function appointment_booking_vacation_page_content() {
	global $wpdb;
	$vacation_table_name = $wpdb->prefix . 'vacation_dates';

	// Handle form submission for adding vacation dates
	if ( isset( $_POST['submit_vacation'] ) ) {
		$vacation_start_date = sanitize_text_field( $_POST['vacation_start_date'] );
		$vacation_end_date   = sanitize_text_field( $_POST['vacation_end_date'] );

		if ( strtotime( $vacation_start_date ) < strtotime( date( 'Y-m-d' ) ) || strtotime( $vacation_end_date ) < strtotime( date( 'Y-m-d' ) ) ) {
			echo '<div class="notice notice-error"><p>Sorry, you cannot set a vacation for a past date.</p></div>';
			return;
		}

		$wpdb->insert(
			$vacation_table_name,
			array(
				'vacation_start_date' => $vacation_start_date,
				'vacation_end_date'   => $vacation_end_date,
			)
		);
		$referer = wp_get_referer();
		if ( empty( $referer ) ) {
			$referer = admin_url('admin.php?page=appointment-booking-vacation');
		}
		//appointment_booking_safe_redirect( array('vacation' => 'success'), $referer );
		$redirect_url = add_query_arg('vacation', 'success', (string)$referer);
		if ( ob_get_length() ) { ob_clean(); }
		if ( ! ob_start() ) { ob_start(); }
		wp_redirect( $redirect_url );
		exit;
	}

	// Handle delete action
	if ( isset( $_POST['delete_vacation'] ) ) {
		global $wpdb;
		$vacation_table_name = $wpdb->prefix . 'vacation_dates';
		$vacation_id = intval( $_POST['delete_vacation'] );
		if ( $vacation_id > 0 ) {
			$wpdb->delete( $vacation_table_name, array( 'id' => $vacation_id ), array( '%d' ) );
		}
		appointment_booking_safe_redirect( array('delete' => 'success'), admin_url('admin.php?page=appointment-booking-vacation') );
		exit;
	}


	$vacation_dates = $wpdb->get_results( "SELECT * FROM $vacation_table_name" );
	?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-palmtree" style="font-size: 32px; width: 32px; height: 32px; color: var(--primary-color);"></span>
            <?php esc_html_e( 'Set Vacation Dates', 'appointment-booking' ); ?>
        </h1>
        <p style="color: var(--text-muted); margin-bottom: 25px;">Block out specific dates when no appointments can be scheduled.</p>
        <div class="premium-card" style="max-width: 600px;">
            <form method="post" action="" id="vacation-form" style="padding:0; border:none; box-shadow:none; margin:0;">
                <div class="form-group">
                    <label for="vacation_start_date"><?php esc_html_e( 'Vacation Start Date:', 'appointment-booking' ); ?></label>
                <input type="date" id="vacation_start_date" name="vacation_start_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label for="vacation_end_date"><?php esc_html_e( 'Vacation End Date:', 'appointment-booking' ); ?></label>
                <input type="date" id="vacation_end_date" name="vacation_end_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
                <button type="submit" name="submit_vacation" class="button button-primary"><?php esc_html_e( 'Set Vacation', 'appointment-booking' ); ?></button>
            </form>
        </div>

        <h2 style="margin-top: 40px; font-weight: 800;"><?php esc_html_e( 'Existing Vacations', 'appointment-booking' ); ?></h2>
        <div class="premium-card" style="padding: 0;">
            <table class="vacations-table" style="margin-top: 0;">
                <thead>
                <tr>
                    <th><?php esc_html_e( 'Start Date', 'appointment-booking' ); ?></th>
                    <th><?php esc_html_e( 'End Date', 'appointment-booking' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'appointment-booking' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ( ! empty( $vacation_dates ) ) : ?>
                    <?php foreach ( $vacation_dates as $vacation ) : ?>
                        <tr>
                            <td><?php echo esc_html( $vacation->vacation_start_date ); ?></td>
                            <td><?php echo esc_html( $vacation->vacation_end_date ); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <a href="<?php echo admin_url('admin.php?page=appointment-booking-edit-vacation&id=' . $vacation->id); ?>" class="button button-primary" style="margin-right: 5px;">Edit</a>
                                <!-- Delete Button -->
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="delete_vacation" value="<?php echo esc_attr($vacation->id); ?>">
                                    <button type="submit" class="button button-primary" onclick="return confirm('Are you sure you want to delete this vacation?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px; color: var(--text-muted);"><?php esc_html_e( 'No vacation dates set.', 'appointment-booking' ); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
	<?php
}
function appointment_booking_edit_vacation_page() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'vacation_dates';
	$vacation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ( $vacation_id <= 0 ) {
		echo '<div class="notice notice-error"><p>Invalid vacation ID.</p></div>';
		return;
	}

	// Process form submission for updating the vacation.
	if ( isset($_POST['update_vacation']) ) {
		$vacation_start_date = sanitize_text_field( $_POST['vacation_start_date'] );
		$vacation_end_date   = sanitize_text_field( $_POST['vacation_end_date'] );

		if ( strtotime( $vacation_start_date ) < strtotime( date( 'Y-m-d' ) ) || strtotime( $vacation_end_date ) < strtotime( date( 'Y-m-d' ) ) ) {
			echo '<div class="notice notice-error"><p>Sorry, you cannot update a vacation to a past date.</p></div>';
			return;
		}

		$wpdb->update(
			$table_name,
			array(
				'vacation_start_date' => $vacation_start_date,
				'vacation_end_date'   => $vacation_end_date,
			),
			array( 'id' => $vacation_id )
		);

		wp_redirect( admin_url('admin.php?page=appointment-booking-vacation&update=success') );
		exit;
	}

	// Retrieve the vacation record.
	$vacation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $vacation_id ), ARRAY_A );
	if ( ! $vacation ) {
		echo '<div class="notice notice-error"><p>Vacation not found.</p></div>';
		return;
	}
	?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-edit-large" style="font-size: 32px; width: 32px; height: 32px; color: var(--primary-color);"></span>
            Edit Vacation
        </h1>
        <div class="premium-card" style="max-width: 600px;">
            <form method="post" action="">
                <div class="form-group">
                    <label for="vacation_start_date">Start Date</label>
                    <input type="date" name="vacation_start_date" id="vacation_start_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo esc_attr( $vacation['vacation_start_date'] ); ?>" required>
                </div>
                <div class="form-group">
                    <label for="vacation_end_date">End Date</label>
                    <input type="date" name="vacation_end_date" id="vacation_end_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo esc_attr( $vacation['vacation_end_date'] ); ?>" required>
                </div>
                <p class="submit" style="margin-bottom: 0;">
                    <button type="submit" name="update_vacation" class="button button-primary">Update Vacation</button>
                </p>
            </form>
        </div>
    </div>
	<?php
}

