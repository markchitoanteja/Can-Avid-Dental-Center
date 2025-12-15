$(document).ready(function () {
    if (notification && Object.keys(notification).length > 0) {
        showNotification(notification);
    }

    toggleLoader(false);

    $('[data-toggle="tooltip"]').tooltip();

    $(".dataTable").DataTable({
        responsive: true,
        autoWidth: false,
        lengthChange: false,
        paging: true,
        searching: true,
        ordering: false,
        info: true,
    });

    $('.logoutBtn').on('click', function (e) {
        $.ajax({
            url: base_url + 'logout',
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    location.href = base_url;
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    $('#my_profile_image').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);

        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#my_profile_preview_image').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    $('#my_profile_form').submit(function (e) {
        e.preventDefault(); // Prevent default submit

        const name = $('#my_profile_name').val().trim();
        const email = $('#my_profile_email').val().trim();
        const password = $('#my_profile_password').val();
        const confirmPassword = $('#my_profile_confirm_password').val();
        const image = $('#my_profile_image')[0].files[0]; // get selected file

        // Clear old errors
        $('.error-message').remove();
        $('.is-invalid').removeClass('is-invalid');

        // Password validation
        if (password !== confirmPassword) {
            $('#my_profile_password, #my_profile_confirm_password').addClass('is-invalid');
            $('#my_profile_password').after('<small class="error-message text-danger">Passwords do not match.</small>');
            return;
        }

        $('#my_profile_submit').prop('disabled', true).text('Updating...');

        var formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);
        if (image) {
            formData.append('image', image); // append image
        }

        $.ajax({
            url: base_url + 'admin/update_profile',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    if (response.error_type === 'email_exists') {
                        $('#my_profile_email').addClass('is-invalid').after('<small class="error-message text-danger">This email already exists.</small>');
                    } else if (response.error_type === 'invalid_image') {
                        $('#my_profile_image').addClass('is-invalid').after('<small class="error-message text-danger">Invalid image file.</small>');
                    }

                    $('#my_profile_submit').prop('disabled', false).text('Save changes');
                }
            },
            error: function (_, _, error) {
                console.error(error);
                $('#my_profile_submit').prop('disabled', false).text('Save changes');
            }
        });
    });

    $('#my_profile_password, #my_profile_confirm_password').on('input', function () {
        $('#my_profile_password, #my_profile_confirm_password').removeClass('is-invalid');
        $('#my_profile_password').siblings('.error-message').remove();
    });

    $('#my_profile_email').on('input', function () {
        $(this).removeClass('is-invalid');
        $(this).siblings('.error-message').remove();
    });

    $(document).on('click', '.loadable', function () {
        toggleLoader(true);
    });

    $('#add_appointment_client_id').on('change', function () {
        const userId = $(this).val();

        if (!userId) return;

        const formData = new FormData();

        formData.append('user_id', userId);

        $.ajax({
            url: base_url + 'admin/get_user_profile',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response && response.phone) {
                    $('#add_appointment_phone').val(response.phone);
                } else {
                    $('#add_appointment_phone').val('');
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    $('#add_appointment_form').submit(function (e) {
        $('#add_appointment_form .is-invalid').removeClass('is-invalid');
        $('#add_appointment_form .invalid-feedback').remove();

        const service = $('#add_appointment_service').val();
        const client_id = $('#add_appointment_client_id').val();
        const phone = $('#add_appointment_phone').val().trim();
        const appointment_date = $('#add_appointment_date').val();
        const appointment_time = $('#add_appointment_time').val();

        let hasError = false;

        // ---- Date Validation ----
        const selectedDate = new Date(appointment_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const minDate = new Date(today);
        minDate.setDate(today.getDate() + 1);

        if (!appointment_date || selectedDate < minDate) {
            $('#add_appointment_date')
                .addClass('is-invalid')
                .after('<div class="invalid-feedback">Appointment date must be at least 1 day ahead of today.</div>');
            hasError = true;
        }

        // ---- Time Validation ----
        if (!appointment_time) {
            $('#add_appointment_time')
                .addClass('is-invalid')
                .after('<div class="invalid-feedback">Please select an appointment time.</div>');
            hasError = true;
        } else {
            const [hours, minutes] = appointment_time.split(':').map(Number);
            const dayOfWeek = selectedDate.getDay(); // 0 = Sun, 6 = Sat
            let openTime, closeTime;

            // Define working hours
            if (dayOfWeek >= 1 && dayOfWeek <= 5) {
                openTime = 8 * 60;   // Weekdays 8:00 AM
                closeTime = 19 * 60; // Weekdays 7:00 PM
            } else if (dayOfWeek === 6) {
                openTime = 10 * 60;  // Saturday 10:00 AM
                closeTime = 17 * 60; // Saturday 5:00 PM
            } else {
                openTime = 10 * 60;  // Sunday 10:00 AM
                closeTime = 16 * 60; // Sunday 4:00 PM
            }

            const selectedMinutes = hours * 60 + minutes;

            if (selectedMinutes < openTime || selectedMinutes > closeTime) {
                let message = '';
                switch (dayOfWeek) {
                    case 0:
                        message = 'Sunday hours are from 10:00 AM to 4:00 PM.';
                        break;
                    case 6:
                        message = 'Saturday hours are from 10:00 AM to 5:00 PM.';
                        break;
                    default:
                        message = 'Weekday hours are from 8:00 AM to 7:00 PM.';
                }
                $('#add_appointment_time')
                    .addClass('is-invalid')
                    .after(`<div class="invalid-feedback">${message}</div>`);
                hasError = true;
            }
        }

        if (hasError) return;

        toggleLoader(true);

        var formData = new FormData();

        formData.append('service', service);
        formData.append('client_id', client_id);
        formData.append('phone', phone);
        formData.append('appointment_date', appointment_date);
        formData.append('appointment_time', appointment_time);

        $.ajax({
            url: base_url + 'admin/add_appointment',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    $('#add_appointment_form').on('change input', 'input, select', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });

    $('#edit_appointment_form').on('change input', 'input, select', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });

    $(document).on('click', '.cancel_appointment', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, cancel it!"
        }).then((result) => {
            if (result.isConfirmed) {
                toggleLoader(true);

                var formData = new FormData();

                formData.append('id', id);

                $.ajax({
                    url: base_url + 'admin/cancel_appointment',
                    data: formData,
                    type: 'POST',
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function (_, _, error) {
                        console.error(error);
                    }
                });
            }
        });
    });

    $(document).on('click', '.edit_appointment', function () {
        const id = $(this).data('id');

        $('#edit_appointment_form .is-invalid').removeClass('is-invalid');
        $('#edit_appointment_form .invalid-feedback').remove();

        $('#edit_appointment_modal').modal('show');
        $('#edit_appointment_submit').prop('disabled', true).text('Please wait...');

        var formData = new FormData();

        formData.append('id', id);

        $.ajax({
            url: base_url + 'admin/get_appointment',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const appointment = response.appointment;

                    // --- Format the date ---
                    // Convert "10/07/2025" → "2025-10-07"
                    const [month, day, year] = appointment.appointment_date.split('/');
                    const formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;

                    // --- Format the time ---
                    // Convert "8:00am" or "8:00 pm" → "08:00" or "20:00"
                    let timeStr = appointment.appointment_time.trim().toLowerCase(); // "8:00am"
                    let hours = parseInt(timeStr.split(':')[0]);
                    let minutes = timeStr.split(':')[1].replace(/[^0-9]/g, ''); // remove am/pm
                    const isPM = timeStr.includes('pm');

                    if (isPM && hours < 12) hours += 12;
                    if (!isPM && hours === 12) hours = 0;

                    const formattedTime = `${String(hours).padStart(2, '0')}:${minutes.padStart(2, '0')}`;

                    // --- Set values into the form ---
                    $('#edit_appointment_id').val(appointment.id);
                    $('#edit_appointment_service').val(appointment.service);
                    $('#edit_appointment_client_id').val(appointment.client_id).trigger('change');
                    $('#edit_appointment_phone').val(appointment.phone);
                    $('#edit_appointment_date').val(formattedDate);
                    $('#edit_appointment_time').val(formattedTime);

                    $('#edit_appointment_submit').prop('disabled', false).text('Save changes');
                    $('#edit_appointment_form .main-form').removeClass('d-none');
                    $('#edit_appointment_form .loading').removeClass('d-flex').addClass('d-none');
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    $('#edit_appointment_client_id').on('change', function () {
        const userId = $(this).val();

        if (!userId) return;

        const formData = new FormData();

        formData.append('user_id', userId);

        $.ajax({
            url: base_url + 'admin/get_user_profile',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response && response.phone) {
                    $('#edit_appointment_phone').val(response.phone);
                } else {
                    $('#edit_appointment_phone').val('');
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    $('#edit_appointment_form').submit(function (e) {
        $('#edit_appointment_form .is-invalid').removeClass('is-invalid');
        $('#edit_appointment_form .invalid-feedback').remove();

        const id = $('#edit_appointment_id').val();
        const service = $('#edit_appointment_service').val();
        const client_id = $('#edit_appointment_client_id').val();
        const phone = $('#edit_appointment_phone').val().trim();
        const appointment_date = $('#edit_appointment_date').val();
        const appointment_time = $('#edit_appointment_time').val();

        let hasError = false;

        // ---- Date Validation ----
        const selectedDate = new Date(appointment_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const minDate = new Date(today);
        minDate.setDate(today.getDate() + 1);

        if (!appointment_date || selectedDate < minDate) {
            $('#edit_appointment_date')
                .addClass('is-invalid')
                .after('<div class="invalid-feedback">Appointment date must be at least 1 day ahead of today.</div>');
            hasError = true;
        }

        // ---- Time Validation ----
        if (!appointment_time) {
            $('#edit_appointment_time')
                .addClass('is-invalid')
                .after('<div class="invalid-feedback">Please select an appointment time.</div>');
            hasError = true;
        } else {
            const [hours, minutes] = appointment_time.split(':').map(Number);
            const dayOfWeek = selectedDate.getDay(); // 0 = Sun, 6 = Sat
            let openTime, closeTime;

            // Define working hours
            if (dayOfWeek >= 1 && dayOfWeek <= 5) {
                openTime = 8 * 60;   // Weekdays 8:00 AM
                closeTime = 19 * 60; // Weekdays 7:00 PM
            } else if (dayOfWeek === 6) {
                openTime = 10 * 60;  // Saturday 10:00 AM
                closeTime = 17 * 60; // Saturday 5:00 PM
            } else {
                openTime = 10 * 60;  // Sunday 10:00 AM
                closeTime = 16 * 60; // Sunday 4:00 PM
            }

            const selectedMinutes = hours * 60 + minutes;

            if (selectedMinutes < openTime || selectedMinutes > closeTime) {
                let message = '';
                switch (dayOfWeek) {
                    case 0:
                        message = 'Sunday hours are from 10:00 AM to 4:00 PM.';
                        break;
                    case 6:
                        message = 'Saturday hours are from 10:00 AM to 5:00 PM.';
                        break;
                    default:
                        message = 'Weekday hours are from 8:00 AM to 7:00 PM.';
                }
                $('#edit_appointment_time')
                    .addClass('is-invalid')
                    .after(`<div class="invalid-feedback">${message}</div>`);
                hasError = true;
            }
        }

        if (hasError) return;

        toggleLoader(true);

        var formData = new FormData();

        formData.append('id', id);
        formData.append('service', service);
        formData.append('client_id', client_id);
        formData.append('phone', phone);
        formData.append('appointment_date', appointment_date);
        formData.append('appointment_time', appointment_time);

        $.ajax({
            url: base_url + 'admin/update_appointment',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    $('#add_client_image, #edit_client_image').on('change', function (e) {
        const file = e.target.files[0];
        const previewId = $(this).attr('id') === 'add_client_image'
            ? '#add_client_image_preview'
            : '#edit_client_image_preview';

        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => $(previewId).attr('src', event.target.result);
            reader.readAsDataURL(file);
        }
    });

    $('#addClientForm').on('submit', function (e) {
        $('.invalid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid');

        const password = $('#add_client_password').val().trim();
        const confirmPassword = $('#add_client_confirm_password').val().trim();
        const phone = $('#add_client_phone').val().trim();
        const email = $('#add_client_email').val().trim();

        let isValid = true;

        if (password !== confirmPassword) {
            $('#add_client_password, #add_client_confirm_password').addClass('is-invalid');
            $('#add_client_confirm_password').after('<div class="invalid-feedback password-error d-block">Passwords do not match.</div>');

            isValid = false;
        }

        const phonePattern = /^09\d{9}$/;
        if (phone !== '' && !phonePattern.test(phone)) {
            $('#add_client_phone').addClass('is-invalid').after('<div class="invalid-feedback phone-error d-block">Please enter a valid Philippine phone number (e.g., 09123456789).</div>');
            isValid = false;
        }

        // === Email Format Validation ===
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            $('#add_client_email').addClass('is-invalid')
                .after('<div class="invalid-feedback email-error d-block">Please enter a valid email address.</div>');
            isValid = false;
        }

        if (!isValid) return;

        toggleLoader(true);

        var formData = new FormData();

        formData.append('image', $('#add_client_image')[0].files[0] || '');
        formData.append('name', $('#add_client_name').val().trim());
        formData.append('email', email);
        formData.append('password', password);
        formData.append('phone', phone);
        formData.append('birthdate', $('#add_client_birthdate').val());
        formData.append('gender', $('#add_client_gender').val());
        formData.append('address', $('#add_client_address').val());

        $.ajax({
            url: base_url + 'admin/add_client',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (!response.success) {
                    if (response.error && response.error === 'email_exists') {
                        $('#add_client_email').addClass('is-invalid').after('<div class="invalid-feedback email-error d-block">This email is already registered.</div>');
                    } else {
                        alert('Something went wrong. Please try again.');
                    }

                    toggleLoader(false);
                }

                location.reload();
            },
            error: function (_, _, error) {
                console.error(error);
                alert('An error occurred while submitting the form.');
            }
        });
    });

    $('#add_client_password, #add_client_confirm_password').on('input', function () {
        $('#add_client_password, #add_client_confirm_password').removeClass('is-invalid');
        $('.invalid-feedback.password-error').remove();
    });

    $('#add_client_phone').on('input', function () {
        $(this).removeClass('is-invalid');
        $('.invalid-feedback.phone-error').remove();
    });

    $('#add_client_email').on('input', function () {
        $(this).removeClass('is-invalid');
        $('.invalid-feedback.email-error').remove();
    });

    $(document).on('click', '.delete_client', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "This client will be permanently deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                toggleLoader(true);

                const formData = new FormData();
                formData.append('id', id);

                $.ajax({
                    url: base_url + 'admin/delete_client',
                    data: formData,
                    type: 'POST',
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong while deleting the client.",
                                icon: "error"
                            });
                        }
                    },
                    error: function (_, _, error) {
                        toggleLoader(false);
                        console.error(error);
                        Swal.fire("Error!", "An unexpected error occurred.", "error");
                    }
                });
            }
        });
    });

    $(document).on('click', '.edit_client', function () {
        const id = $(this).data('id');

        // Safely get the form element
        const $form = $('#edit_client_form');
        const formEl = $form[0];

        // Reset form state only if it exists
        if (formEl) formEl.reset();

        // Remove validation styles and messages
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();

        // Show modal and set loading state
        $('#edit_client_modal').modal('show');
        $('#edit_client_submit').prop('disabled', true).text('Please wait...');
        $form.find('.main-form').addClass('d-none');
        $form.find('.loading').removeClass('d-none').addClass('d-flex');

        const formData = new FormData();
        formData.append('id', id);

        $.ajax({
            url: base_url + 'admin/get_client',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const client = response.client;

                    // --- Fill form fields ---
                    $('#edit_client_id').val(client.id);
                    $('#edit_client_name').val(client.name);
                    $('#edit_client_email').val(client.email);
                    $('#edit_client_phone').val(client.phone);
                    $('#edit_client_birthdate').val(client.birthdate);
                    $('#edit_client_gender').val(client.gender);
                    $('#edit_client_address').val(client.address);

                    // --- Image preview ---
                    const imageUrl = client.image
                        ? base_url + 'public/dist/admin/img/uploads/' + client.image
                        : base_url + 'public/dist/admin/img/uploads/default-user-image.webp';
                    $('#edit_client_image_preview').attr('src', imageUrl);

                    // --- UI Updates ---
                    $('#edit_client_submit').prop('disabled', false).text('Save changes');
                    $form.find('.main-form').removeClass('d-none');
                    $form.find('.loading').removeClass('d-flex').addClass('d-none');
                } else {
                    Swal.fire('Error!', 'Failed to load client details.', 'error');
                }
            },
            error: function (_, _, error) {
                console.error(error);
                Swal.fire('Error!', 'An unexpected error occurred.', 'error');
            }
        });
    });

    $(document).on('submit', '#edit_client_form', function (e) {
        const $form = $(this);

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();

        // Get form values
        const id = $('#edit_client_id').val();
        const name = $('#edit_client_name').val().trim();
        const email = $('#edit_client_email').val().trim();
        const phone = $('#edit_client_phone').val().trim();
        const birthdate = $('#edit_client_birthdate').val();
        const gender = $('#edit_client_gender').val();
        const address = $('#edit_client_address').val().trim();
        const password = $('#edit_client_password').val() || '';
        const confirmPassword = $('#edit_client_confirm_password').val() || '';
        const image = $('#edit_client_image')[0].files[0];

        let isValid = true;

        // --- Validate email format ---
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            $('#edit_client_email')
                .addClass('is-invalid')
                .after('<div class="invalid-feedback d-block">Please enter a valid email address.</div>');
            isValid = false;
        }

        // --- Validate phone format (Philippines) ---
        const phonePattern = /^09\d{9}$/;
        if (phone !== '' && !phonePattern.test(phone)) {
            $('#edit_client_phone')
                .addClass('is-invalid')
                .after('<div class="invalid-feedback d-block">Please enter a valid Philippine phone number (e.g., 09123456789).</div>');
            isValid = false;
        }

        // --- Validate password match if changed ---
        if (password !== '' || confirmPassword !== '') {
            if (password !== confirmPassword) {
                $('#edit_client_password, #edit_client_confirm_password').addClass('is-invalid');
                $('#edit_client_confirm_password').after('<div class="invalid-feedback d-block">Passwords do not match.</div>');
                isValid = false;
            }
        }

        if (!isValid) return;

        toggleLoader(true);

        // Build FormData
        const formData = new FormData();
        formData.append('id', id);
        formData.append('name', name);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('birthdate', birthdate);
        formData.append('gender', gender);
        formData.append('address', address);

        if (password) formData.append('password', password);
        if (image) formData.append('image', image);

        $.ajax({
            url: base_url + 'admin/update_client',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    toggleLoader(false);

                    if (response.error_type === 'email_exists') {
                        $('#edit_client_email').addClass('is-invalid').after('<div class="invalid-feedback d-block">This email is already in use.</div>');
                    } else {
                        Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                    }

                    $('#edit_client_submit').prop('disabled', false).text('Save changes');
                }
            },
            error: function (_, _, error) {
                console.error(error);
                Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                $('#edit_client_submit').prop('disabled', false).text('Save changes');
            }
        });
    });

    $(document).on('submit', '#addServiceForm', function (e) {
        const name = $('#add_service_name').val().trim();
        const category = $('#add_service_category').val().trim();

        $('#add_service_name, #add_service_category').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        let isValid = true;

        if (name === '') {
            $('#add_service_name').addClass('is-invalid').after('<div class="invalid-feedback d-block">Please enter a service name.</div>');

            isValid = false;
        }

        if (category === '') {
            $('#add_service_category').addClass('is-invalid').after('<div class="invalid-feedback d-block">Please enter a category.</div>');

            isValid = false;
        }

        if (!isValid) return;

        toggleLoader(true);

        const formData = new FormData();

        formData.append('name', name);
        formData.append('category', category);

        $.ajax({
            url: base_url + 'admin/add_service',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    toggleLoader(false);

                    Swal.fire('Error!', 'Failed to add service.', 'error');
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    $(document).on('click', '.delete_service', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "This service will be permanently deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                toggleLoader(true);

                var formData = new FormData();
                formData.append('id', id);

                $.ajax({
                    url: base_url + 'admin/delete_service',
                    data: formData,
                    type: 'POST',
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            Swal.fire("Error!", "Failed to delete service.", "error");
                        }
                    },
                    error: function (_, _, error) {
                        toggleLoader(false);
                        console.error(error);
                        Swal.fire("Error!", "An unexpected error occurred.", "error");
                    }
                });
            }
        });
    });

    $(document).on('click', '.edit_service', function () {
        const id = $(this).data('id');

        $('#editServiceForm .is-invalid').removeClass('is-invalid');
        $('#editServiceForm .invalid-feedback').remove();
        $('#editServiceForm')[0].reset();

        $('#editServiceModal').modal('show');
        $('#edit_service_submit').prop('disabled', true).text('Please wait...');

        const formData = new FormData();
        formData.append('id', id);

        $.ajax({
            url: base_url + 'admin/get_service',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const service = response.service;
                    $('#edit_service_id').val(service.id);
                    $('#edit_service_name').val(service.name);
                    $('#edit_service_category').val(service.category);

                    $('#edit_service_submit').prop('disabled', false).text('Save Changes');
                } else {
                    Swal.fire('Error!', 'Failed to load service details.', 'error');
                }
            },
            error: function (_, _, error) {
                console.error(error);
                Swal.fire('Error!', 'An unexpected error occurred.', 'error');
            }
        });
    });

    $('#editServiceForm').on('submit', function () {
        const id = $('#edit_service_id').val();
        const name = $('#edit_service_name').val().trim();
        const category = $('#edit_service_category').val();

        if (!name || !category) {
            Swal.fire('Validation Error', 'Please fill out all fields.', 'warning');
            return;
        }

        toggleLoader(true);

        const formData = new FormData();

        formData.append('id', id);
        formData.append('name', name);
        formData.append('category', category);

        $.ajax({
            url: base_url + 'admin/update_service',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    toggleLoader(false);

                    Swal.fire('Error!', 'Failed to update service.', 'error');
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    $(document).on('click', '#add_misc_service', function () {
        const newItem = `
        <div class="input-group mb-2 misc-item">
            <input type="text" name="misc_service[]" class="form-control misc_desc" placeholder="Enter miscellaneous service">
            <input type="number" name="misc_amount[]" class="form-control misc_amount" placeholder="Amount (₱)" step="0.01" min="0">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger remove-misc"><i class="fas fa-times"></i></button>
            </div>
        </div>`;
        $('#misc_services_wrapper').append(newItem);
    });

    $(document).on('click', '.remove-misc', function () {
        if ($('.misc-item').length > 1) {
            $(this).closest('.misc-item').remove();
            calculateTotal();
        } else {
            Swal.fire('Notice', 'You must keep at least one miscellaneous row.', 'info');
        }
    });

    $(document).on('input', '.misc_amount', function () {
        calculateTotal();
    });

    $(document).on('input', '#service_admin_amount', function () {
        calculateTotal();
    });

    function calculateTotal() {
        let total = 0;

        // Get service admin amount
        const adminAmt = parseFloat($('#service_admin_amount').val());
        if (!isNaN(adminAmt)) total += adminAmt;

        // Add all misc amounts
        $('.misc_amount').each(function () {
            const val = parseFloat($(this).val());
            if (!isNaN(val)) total += val;
        });

        // Update the total field
        $('#payment_total').val(total.toFixed(2));
    }

    $(document).on('submit', '#add_billing_form', function (e) {
        $('#add_billing_form .is-invalid').removeClass('is-invalid');
        $('#add_billing_form .invalid-feedback').remove();

        const formData = new FormData(this);

        const miscDescriptions = [];
        const miscAmounts = [];

        $('.misc-item').each(function () {
            const desc = $(this).find('.misc_desc').val().trim();
            const amt = $(this).find('.misc_amount').val().trim();

            if (desc !== '' && amt !== '') {
                miscDescriptions.push(desc);
                miscAmounts.push(amt);
            }
        });

        formData.delete('misc_service[]');
        formData.delete('misc_amount[]');

        miscDescriptions.forEach(d => formData.append('misc_service[]', d));
        miscAmounts.forEach(a => formData.append('misc_amount[]', a));

        toggleLoader(true);

        $.ajax({
            url: base_url + 'admin/add_billing',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    });

    function toggleLoader(show) {
        if (show) {
            $("#loadingOverlay").fadeIn(200);
        } else {
            $("#loadingOverlay").fadeOut(200);
        }
    }

    function showNotification(notification) {
        Swal.fire({
            title: notification.title,
            text: notification.text,
            icon: notification.icon
        });
    }
});