jQuery(document).ready(function($) {
    // On date change, fetch available times
    $('#appointment_date').on('change', function() {
        var selectedDate = $(this).val();
        var $timeSelect = $('#appointment_time');

        if (!selectedDate) {
            $timeSelect.empty();
            $timeSelect.append('<option value="">Please select a Date first</option>');
            $timeSelect.prop('disabled', true);
            return;
        }

        // Show loading state
        $timeSelect.empty();
        $timeSelect.append('<option value="">Loading times...</option>');
        $timeSelect.prop('disabled', true);

        var data = {
            action: 'fetch_appointment_times',
            date: selectedDate
        };

        $.post(appointment_booking_params.ajaxUrl, data, function(response) {
            $timeSelect.empty(); // Clear old options

            if (response && response.length > 0) {
                $timeSelect.append('<option value="">Select Time</option>');
                $.each(response, function(index, time) {
                    $timeSelect.append('<option value="' + time + '">' + time + '</option>');
                });
                $timeSelect.prop('disabled', false);
                $timeSelect.css('opacity', 0).animate({opacity: 1}, 300);
            } else {
                // If no times are available
                $timeSelect.append('<option value="">Fully Booked / Vacation</option>');
                $timeSelect.prop('disabled', true);
            }
        }).fail(function() {
            $timeSelect.empty();
            $timeSelect.append('<option value="">Error loading times</option>');
            $timeSelect.prop('disabled', true);
        });
    });

    // ── Phone Number: Instant character stripping ──
    // This is the absolute most reliable method: catch everything, at any time.
    $(document).on('input keyup paste change blur', '#appointment_phone, .appointment-phone-number, input[type="tel"]', function (e) {
        var input = this;
        var start = input.selectionStart;
        var val = input.value;
        var sanitized = val.replace(/[^0-9+\-\s()]/g, '');

        if (val !== sanitized) {
            input.value = sanitized;
            // Best effort cursor management
            if (start !== null) {
                // If we removed characters before the current cursor position, shift it
                var removedPrefixCount = (val.substring(0, start).match(/[^0-9+\-\s()]/g) || []).length;
                input.setSelectionRange(start - removedPrefixCount, start - removedPrefixCount);
            }
        }
    });

    // ── Date Validation: Fully custom message ──
    $(document).on('invalid', '#appointment_date, [name="appointment_date"], [name="vacation_start_date"], [name="vacation_end_date"], input[type="date"]', function(e) {
        var input = e.target;
        // Reset to allow re-evaluation
        input.setCustomValidity(''); 

        if (input.validity.rangeUnderflow) {
            var minVal = $(input).attr('min');
            if (minVal) {
                // Format YYYY-MM-DD to more readable date format
                var dateParts = minVal.split('-');
                var displayDate = dateParts[2] + '.' + dateParts[1] + '.' + dateParts[0];
                input.setCustomValidity('Date must be ' + displayDate + ' or later.');
            } else {
                input.setCustomValidity('Date must be today or later.');
            }
        } else if (input.validity.valueMissing) {
            input.setCustomValidity('Please select a date.');
        }
    });

    // Reset when the user starts typing/picking again
    $(document).on('input change', '#appointment_date, [name="appointment_date"], [name="vacation_start_date"], [name="vacation_end_date"], input[type="date"]', function(e) {
        e.target.setCustomValidity('');
    });
});
