@extends('layouts.corporation-auth')

@section('title', 'Register | Corporation Portal')

@section('form-content')
<div id="registerFormContainer" class="form-container fade-in">
    <div class="mb-4 text-center text-md-start">
        <h4 class="fw-bold" style="color: #32012F;">Citizen Registration</h4>
        <p class="text-secondary small" style="color: #524C42 !important;">Register for Municipal e-Services - Property Tax, Trade License, Grievances</p>
    </div>

    <form id="registerForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="regFullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="regFullName" name="name" placeholder="As per Aadhaar" required>
                <div class="invalid-feedback" id="nameError"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="regEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="regEmail" name="email" placeholder="you@example.com" required>
                <div class="invalid-feedback" id="emailError"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="regPhone" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="regPhone" name="phone" placeholder="10-digit mobile" maxlength="10" required>
                <div class="invalid-feedback" id="phoneError"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="regGender" class="form-label">Gender <span class="text-danger">*</span></label>
                <select class="form-control" id="regGender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <div class="invalid-feedback" id="genderError"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="regDob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="regDob" name="date_of_birth" required>
                <div class="invalid-feedback" id="dobError"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="regCity" class="form-label">City <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="regCity" name="city" placeholder="Enter your city" required>
                <div class="invalid-feedback" id="cityError"></div>
            </div>
        </div>

        <div class="mb-3">
            <label for="regCorporation" class="form-label">Corporation <span class="text-danger">*</span></label>
            <select class="form-control" id="regCorporation" name="corporation_id" required>
                <option value="">Select Corporation</option>
                @foreach($corporations as $corporation)
                    <option value="{{ $corporation->id }}">{{ $corporation->name }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="corporationError"></div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="regPassword" class="form-label">Create Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="regPassword" name="password" placeholder="Min 8 characters" required>
                    <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="regPassword" style="background:#fff6eb;">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div class="strength-meter mt-2">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <div class="invalid-feedback" id="passwordError"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="regConfirmPwd" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                    <input type="password" class="form-control" id="regConfirmPwd" name="password_confirmation" placeholder="Re-enter password" required>
                </div>
                <div class="invalid-feedback" id="confirmPasswordError"></div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <div class="file-upload-area" id="fileUploadArea">
                <div class="text-center">
                    <i class="fas fa-cloud-upload-alt fa-2x" style="color: #F97300;"></i>
                    <div class="file-upload-text mt-2">
                        <div class="primary fw-bold">Click or drag to upload profile picture</div>
                        <div class="secondary small text-muted">JPG, PNG, GIF (Max 2MB)</div>
                    </div>
                </div>
            </div>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">
            <div id="filePreview" class="file-preview" style="display: none;">
                <img id="previewImage" src="" alt="Profile Preview">
                <div class="mt-2">
                    <span id="fileName" class="small"></span>
                    <button type="button" class="file-remove" id="removeFile">Remove</button>
                </div>
            </div>
            <div class="invalid-feedback" id="profileError"></div>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="termsCheck" required style="border-color:#F97300;">
            <label class="form-check-label small" for="termsCheck" style="color:#32012F;">
                I agree to the <a href="#" class="text-decoration-none" style="color:#F97300;">Municipal Terms of Service</a> and <a href="#" class="text-decoration-none" style="color:#F97300;">Privacy Policy</a>
            </label>
            <div class="invalid-feedback" id="termsError"></div>
        </div>

        <button type="submit" class="btn btn-primary-custom" id="registerSubmitBtn">
            <i class="fas fa-user-plus me-2"></i> Register for Municipal Services
        </button>
    </form>

    <hr class="my-4">

    <div class="text-center">
        <p class="signup-text" style="color:#524C42;">Already a registered citizen?
            <a href="{{ route('corporation.login') }}" class="text-decoration-none fw-bold" style="color:#F97300;">Sign in here</a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let isSubmitting = false;

    $('#registerForm').on('submit', function(e) {
        e.preventDefault();

        if (isSubmitting) return false;

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const formData = new FormData(this);

        const name = $('#regFullName').val().trim();
        const email = $('#regEmail').val().trim();
        const phone = $('#regPhone').val().trim();
        const gender = $('#regGender').val();
        const dob = $('#regDob').val();
        const city = $('#regCity').val().trim();
        const corporation = $('#regCorporation').val();
        const password = $('#regPassword').val();
        const confirmPwd = $('#regConfirmPwd').val();
        const terms = $('#termsCheck').is(':checked');

        let hasError = false;

        if (!name) {
            $('#regFullName').addClass('is-invalid');
            $('#nameError').text('Full name is required.');
            hasError = true;
        }

        if (!email) {
            $('#regEmail').addClass('is-invalid');
            $('#emailError').text('Email address is required.');
            hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#regEmail').addClass('is-invalid');
            $('#emailError').text('Enter a valid email address.');
            hasError = true;
        }

        if (!phone) {
            $('#regPhone').addClass('is-invalid');
            $('#phoneError').text('Phone number is required.');
            hasError = true;
        } else if (!/^\d{10}$/.test(phone)) {
            $('#regPhone').addClass('is-invalid');
            $('#phoneError').text('Enter a valid 10-digit phone number.');
            hasError = true;
        }

        if (!gender) {
            $('#regGender').addClass('is-invalid');
            $('#genderError').text('Please select gender.');
            hasError = true;
        }

        if (!dob) {
            $('#regDob').addClass('is-invalid');
            $('#dobError').text('Date of birth is required.');
            hasError = true;
        }

        if (!city) {
            $('#regCity').addClass('is-invalid');
            $('#cityError').text('City is required.');
            hasError = true;
        }

        if (!corporation) {
            $('#regCorporation').addClass('is-invalid');
            $('#corporationError').text('Please select a corporation.');
            hasError = true;
        }

        if (!password) {
            $('#regPassword').addClass('is-invalid');
            $('#passwordError').text('Password is required.');
            hasError = true;
        } else if (password.length < 6) {
            $('#regPassword').addClass('is-invalid');
            $('#passwordError').text('Password must be at least 6 characters.');
            hasError = true;
        }

        if (password !== confirmPwd) {
            $('#regConfirmPwd').addClass('is-invalid');
            $('#confirmPasswordError').text('Passwords do not match.');
            hasError = true;
        }

        if (!terms) {
            $('#termsCheck').addClass('is-invalid');
            $('#termsError').text('You must accept the terms and conditions.');
            hasError = true;
        }

        if (hasError) {
            showToast('error', 'Validation Error', 'Please check the form for errors.', 3000);
            return false;
        }

        isSubmitting = true;
        $('#registerSubmitBtn').html('<i class="fas fa-spinner fa-spin me-2"></i> Registering...').prop('disabled', true);

        $.ajax({
            url: "{{ route('corporation.register.submit') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Registration Successful!', response.message, 2000);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#registerSubmitBtn').html('<i class="fas fa-user-plus me-2"></i> Register for Municipal Services').prop('disabled', false);

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        let fieldId = '';
                        if (key === 'name') fieldId = 'regFullName';
                        else if (key === 'email') fieldId = 'regEmail';
                        else if (key === 'phone') fieldId = 'regPhone';
                        else if (key === 'gender') fieldId = 'regGender';
                        else if (key === 'date_of_birth') fieldId = 'regDob';
                        else if (key === 'city') fieldId = 'regCity';
                        else if (key === 'corporation_id') fieldId = 'regCorporation';
                        else if (key === 'password') fieldId = 'regPassword';
                        else fieldId = 'reg' + key.charAt(0).toUpperCase() + key.slice(1);

                        $(`#${fieldId}`).addClass('is-invalid');
                        $(`#${key}Error`).text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error', 'Please check the form for errors.', 4000);
                } else {
                    showToast('error', 'Error', xhr.responseJSON?.message || 'Registration failed. Please try again.', 4000);
                }
            }
        });
    });

    $('#regConfirmPwd').on('keyup', function() {
        const password = $('#regPassword').val();
        const confirm = $(this).val();
        if (password !== confirm && confirm.length > 0) {
            $(this).addClass('is-invalid');
            $('#confirmPasswordError').text('Passwords do not match.');
        } else {
            $(this).removeClass('is-invalid');
            $('#confirmPasswordError').text('');
        }
    });

    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
        const id = $(this).attr('id');
        if (id === 'regFullName') $('#nameError').text('');
        else if (id === 'regEmail') $('#emailError').text('');
        else if (id === 'regPhone') $('#phoneError').text('');
        else if (id === 'regGender') $('#genderError').text('');
        else if (id === 'regDob') $('#dobError').text('');
        else if (id === 'regCity') $('#cityError').text('');
        else if (id === 'regCorporation') $('#corporationError').text('');
        else if (id === 'regPassword') $('#passwordError').text('');
    });
});
</script>
@endsection
