<?php
require_once 'wp-load.php';
global $wpdb;
$selected_date = '2026-04-10';
$available_times   = array( '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00' );

$appointments_table = $wpdb->prefix . 'appointments';
$vacation_table     = $wpdb->prefix . 'vacation_dates';

$booked_times = $wpdb->get_col(
    $wpdb->prepare(
        "SELECT appointment_time FROM $appointments_table WHERE appointment_date = %s",
        $selected_date
    )
);

$vacation_dates = $wpdb->get_results( "SELECT vacation_start_date, vacation_end_date FROM $vacation_table", ARRAY_A );

echo "Booked Times:\n";
print_r($booked_times);
echo "Vacation Dates:\n";
print_r($vacation_dates);

foreach ( $vacation_dates as $vacation ) {
    if ( $selected_date >= $vacation['vacation_start_date'] && $selected_date <= $vacation['vacation_end_date'] ) {
        echo "MATCHED VACATION DATE. returning empty array\n";
    }
}

$available_times = array_diff( $available_times, $booked_times );
echo "Available Times:\n";
print_r(array_values($available_times));
