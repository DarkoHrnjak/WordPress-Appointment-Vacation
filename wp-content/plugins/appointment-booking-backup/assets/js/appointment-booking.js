jQuery(document).ready(function($) {
    // On date change, fetch available times
    $('#appointment_date').on('change', function() {
        var selectedDate = $(this).val();
        var data = {
            action: 'fetch_appointment_times',
            date: selectedDate
        };

        $.post(appointment_booking_params.ajaxUrl, data, function(response) {
            var $timeSelect = $('#appointment_time');
            $timeSelect.empty(); // Clear old options
            $timeSelect.append('<option value="">' + 'Select Time' + '</option>');

            if (response.length > 0) {
                $.each(response, function(index, time) {
                    $timeSelect.append('<option value="' + time + '">' + time + '</option>');
                });
            } else {
                // If no times are available, you could show a message or disable the dropdown
                $timeSelect.append('<option value="">' + 'No Times Available' + '</option>');
            }
        });
    });
});

