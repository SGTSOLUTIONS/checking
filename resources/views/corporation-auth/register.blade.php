@extends('layouts.corporation-auth')

@section('title', 'Register | Corporation Portal')

@section('form-content')
<div id="registerFormContainer">
    <div class="mb-4">
        <h4 class="fw-bold" style="color: #32012F;">Create Corporation Account</h4>
        <p class="text-secondary small">Register to access SRIS dashboard</p>
    </div>

    <form id="registerForm" method="POST" action="{{ route('corporation.register.submit') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="regName" name="name" placeholder="Enter full name">
                </div>
                <div class="invalid-feedback" id="regNameError"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="regEmail" name="email" placeholder="Enter email address">
                </div>
                <div class="invalid-feedback" id="regEmailError"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text" class="form-control" id="regPhone" name="phone" placeholder="Enter phone number">
                </div>
                <div class="invalid-feedback" id="regPhoneError"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Gender <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                    <select class="form-control" id="regGender" name="gender">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="invalid-feedback" id="regGenderError"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <input type="date" class="form-control" id="regDob" name="date_of_birth">
                </div>
                <div class="invalid-feedback" id="regDobError"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">City <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                    <input type="text" class="form-control" id="regCity" name="city" placeholder="Enter city">
                </div>
                <div class="invalid-feedback" id="regCityError"></div>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Corporation <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <select class="form-control" id="regCorporation" name="corporation_id">
                        <option value="">Select corporation</option>
                        @foreach($corporations as $corporation)
                            <option value="{{ $corporation->id }}">{{ $corporation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="invalid-feedback" id="regCorporationError"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="regPassword" name="password" placeholder="Enter password">
                    <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="regPassword" style="border-left:0; background:#fff6eb;">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="regPasswordError"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="regPasswordConfirmation" name="password_confirmation" placeholder="Confirm password">
                    <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="regPasswordConfirmation" style="border-left:0; background:#fff6eb;">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="regPasswordConfirmationError"></div>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Profile Picture</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-image"></i></span>
                    <input type="file" class="form-control" id="regProfilePicture" name="profile_picture" accept="image/*">
                </div>
                <div class="invalid-feedback" id="regProfilePictureError"></div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom" id="registerSubmitBtn">
            <i class="fas fa-user-plus me-2"></i> Create Account
        </button>
    </form>

    <hr class="my-4">

    <div class="text-center">
        <p>
            Already have an account?
            <a href="{{ route('corporation.login') }}" class="text-decoration-none fw-bold" style="color:#F97300;">Login here</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let isRegisterSubmitting = false;

        function clearRegisterErrors() {
            $('#registerForm .form-control').removeClass('is-invalid');
            $('#regNameError, #regEmailError, #regPhoneError, #regGenderError, #regDobError, #regCityError, #regCorporationError, #regPasswordError, #regPasswordConfirmationError, #regProfilePictureError').text('');
        }

        $('#registerForm input, #registerForm select').on('input change', function () {
            $(this).removeClass('is-invalid');
        });

        $('#registerForm').on('submit', function (e) {
            e.preventDefault();

            if (isRegisterSubmitting) {
                return false;
            }

            clearRegisterErrors();

            const form = document.getElementById('registerForm');
            const formData = new FormData(form);
            const $btn = $('#registerSubmitBtn');
            const originalBtnHtml = $btn.html();

            let hasError = false;
            const email = $('#regEmail').val().trim();
            const password = $('#regPassword').val();
            const confirmPassword = $('#regPasswordConfirmation').val();

            if (!$('#regName').val().trim()) {
                $('#regName').addClass('is-invalid');
                $('#regNameError').text('Full name is required.');
                hasError = true;
            }

            if (!email) {
                $('#regEmail').addClass('is-invalid');
                $('#regEmailError').text('Email is required.');
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $('#regEmail').addClass('is-invalid');
                $('#regEmailError').text('Enter a valid email address.');
                hasError = true;
            }

            if (!$('#regPhone').val().trim()) {
                $('#regPhone').addClass('is-invalid');
                $('#regPhoneError').text('Phone number is required.');
                hasError = true;
            }

            if (!$('#regGender').val()) {
                $('#regGender').addClass('is-invalid');
                $('#regGenderError').text('Gender is required.');
                hasError = true;
            }

            if (!$('#regDob').val()) {
                $('#regDob').addClass('is-invalid');
                $('#regDobError').text('Date of birth is required.');
                hasError = true;
            }

            if (!$('#regCity').val().trim()) {
                $('#regCity').addClass('is-invalid');
                $('#regCityError').text('City is required.');
                hasError = true;
            }

            if (!$('#regCorporation').val()) {
                $('#regCorporation').addClass('is-invalid');
                $('#regCorporationError').text('Corporation is required.');
                hasError = true;
            }

            if (!password) {
                $('#regPassword').addClass('is-invalid');
                $('#regPasswordError').text('Password is required.');
                hasError = true;
            } else if (password.length < 6) {
                $('#regPassword').addClass('is-invalid');
                $('#regPasswordError').text('Password must be at least 6 characters.');
                hasError = true;
            }

            if (!confirmPassword) {
                $('#regPasswordConfirmation').addClass('is-invalid');
                $('#regPasswordConfirmationError').text('Confirm password is required.');
                hasError = true;
            } else if (password !== confirmPassword) {
                $('#regPasswordConfirmation').addClass('is-invalid');
                $('#regPasswordConfirmationError').text('Passwords do not match.');
                hasError = true;
            }

            if (hasError) {
                showToast('error', 'Validation Error', 'Please check the registration form.', 3000);
                return false;
            }

            isRegisterSubmitting = true;
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Registering...');
            showLoader();

            $.ajax({
                url: $('#registerForm').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    hideLoader();

                    if (response.status === 'success') {
                        showToast('success', 'Registration Successful', response.message, 1500);
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else {
                        isRegisterSubmitting = false;
                        $btn.prop('disabled', false).html(originalBtnHtml);
                        showToast('error', 'Error', response.message || 'Something went wrong.', 4000);
                    }
                },
                error: function (xhr) {
                    hideLoader();
                    isRegisterSubmitting = false;
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};

                        if (errors.name) {
                            $('#regName').addClass('is-invalid');
                            $('#regNameError').text(errors.name[0]);
                        }
                        if (errors.email) {
                            $('#regEmail').addClass('is-invalid');
                            $('#regEmailError').text(errors.email[0]);
                        }
                        if (errors.phone) {
                            $('#regPhone').addClass('is-invalid');
                            $('#regPhoneError').text(errors.phone[0]);
                        }
                        if (errors.gender) {
                            $('#regGender').addClass('is-invalid');
                            $('#regGenderError').text(errors.gender[0]);
                        }
                        if (errors.date_of_birth) {
                            $('#regDob').addClass('is-invalid');
                            $('#regDobError').text(errors.date_of_birth[0]);
                        }
                        if (errors.city) {
                            $('#regCity').addClass('is-invalid');
                            $('#regCityError').text(errors.city[0]);
                        }
                        if (errors.corporation_id) {
                            $('#regCorporation').addClass('is-invalid');
                            $('#regCorporationError').text(errors.corporation_id[0]);
                        }
                        if (errors.password) {
                            $('#regPassword').addClass('is-invalid');
                            $('#regPasswordError').text(errors.password[0]);
                        }
                        if (errors.profile_picture) {
                            $('#regProfilePicture').addClass('is-invalid');
                            $('#regProfilePictureError').text(errors.profile_picture[0]);
                        }

                        showToast('error', 'Validation Error', 'Please correct the highlighted fields.', 4000);
                    } else if (xhr.status === 419) {
                        showToast('error', 'Session Expired', 'Please refresh the page and try again.', 4000);
                    } else {
                        showToast('error', 'Error', xhr.responseJSON?.message || 'Something went wrong. Please try again.', 4000);
                    }
                }
            });

            return false;
        });
    });
</script>
@endpush
