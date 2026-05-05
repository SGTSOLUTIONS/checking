@extends('layouts.corporation-auth-layout')

@section('title', 'Register | Corporation Portal')

@section('content')
<div class="auth-card" style="max-width: 1000px;">
    <div class="login-form-section" style="width: 100%;">
        <div class="mobile-header">
            <div class="brand-icon mx-auto"><i class="fas fa-building"></i></div>
            <div class="brand-text">Corporation Portal</div>
            <div class="brand-sub">Property Tax Management System</div>
        </div>

        <div class="form-header">
            <h2>Create Account</h2>
            <p>Register as a corporation user to access the system</p>
        </div>

        <form id="registerForm" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="input-label">Full Name</label>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" placeholder="Enter your full name" required>
                    </div>
                    <div class="invalid-feedback" id="name_error"></div>
                </div>

                <div class="col-md-6">
                    <label class="input-label">Email Address</label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <div class="invalid-feedback" id="email_error"></div>
                </div>

                <div class="col-md-6">
                    <label class="input-label">Phone Number</label>
                    <div class="input-field">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" placeholder="Enter phone number" required>
                    </div>
                    <div class="invalid-feedback" id="phone_error"></div>

                </div>

                <div class="col-md-6">
                    <label class="input-label">Gender</label>
                    <div class="input-field">
                        <i class="fas fa-venus-mars"></i>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="invalid-feedback" id="gender_error"></div>
                </div>

                <div class="col-md-6">
                    <label class="input-label">Date of Birth</label>
                    <div class="input-field">
                        <i class="fas fa-calendar"></i>
                        <input type="date" name="date_of_birth" required>
                    </div>
                    <div class="invalid-feedback" id="date_of_birth_error"></div>
                </div>

                <div class="col-md-6">
                    <label class="input-label">City</label>
                    <div class="input-field">
                        <i class="fas fa-city"></i>
                        <input type="text" name="city" placeholder="Enter your city" required>
                    </div>
                    <div class="invalid-feedback" id="city_error"></div>
                </div>

                <div class="col-md-6">
                    <label class="input-label">Corporation</label>
                    <div class="input-field">
                        <i class="fas fa-building"></i>
                        <select name="corporation_id" required>
                            <option value="">Select Corporation</option>
                            @foreach($corporations as $corporation)
                                <option value="{{ $corporation->id }}">{{ $corporation->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="invalid-feedback" id="corporation_id_error"></div>
                </div>

                <div class="col-md-6">
                    <label class="input-label">Password</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Create a password" required>
                    </div>
                    <div class="invalid-feedback" id="password_error"></div>
                </div>

                <div class="col-md-6">
                    <label class="input-label">Confirm Password</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" placeholder="Confirm password" required>
                    </div>
                    <div class="invalid-feedback" id="password_confirmation_error"></div>
                </div>

                <div class="col-md-12">
                    <label class="input-label">Profile Picture</label>
                    <div class="file-upload-container">
                        <div class="file-upload-area" id="fileUploadArea">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                            </div>
                            <div class="file-upload-text">
                                <div class="primary">Click or drag to upload profile picture</div>
                                <div class="secondary">JPG, PNG, GIF (Max 2MB)</div>
                            </div>
                        </div>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">
                        <div id="filePreview" class="file-preview" style="display: none;">
                            <img id="previewImage" src="" alt="Profile Preview">
                            <div class="file-info">
                                <span id="fileName"></span>
                                <button type="button" class="file-remove" id="removeFile">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback" id="profile_picture_error"></div>
                </div>
            </div>

            <div class="form-options mt-3">
                <label class="checkbox">
                    <input type="checkbox" id="termsCheck" required>
                    <span>I agree to the <a href="#" class="text-primary">Terms and Conditions</a></span>
                </label>
            </div>

            <button type="submit" class="login-btn mt-3" id="registerBtn">
                <i class="fas fa-user-plus"></i>
                <span id="btnText">Create Account</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
            </button>
        </form>

        <div class="back-to-login">
            <a href="{{ route('corporation.login') }}"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let isSubmitting = false;

    // File upload handling
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('profile_picture');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    const removeFile = document.getElementById('removeFile');

    fileUploadArea.addEventListener('click', () => fileInput.click());
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });
    fileUploadArea.addEventListener('dragleave', () => fileUploadArea.classList.remove('dragover'));
    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length) {
            handleFileSelect(this.files[0]);
        }
    });

    function handleFileSelect(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                filePreview.style.display = 'block';
                fileUploadArea.style.display = 'none';
                fileName.textContent = file.name;
            };
            reader.readAsDataURL(file);
        }
    }

    removeFile.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.style.display = 'none';
        fileUploadArea.style.display = 'block';
        previewImage.src = '';
    });

    // Form submission
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isSubmitting) return false;

        // Reset validation
        $('.invalid-feedback').text('');
        $('.input-field input, .input-field select').removeClass('is-invalid');

        let formData = new FormData(this);

        // Terms validation
        if (!$('#termsCheck').is(':checked')) {
            showToast('error', 'Validation Error', 'Please accept the terms and conditions.', 3000);
            return false;
        }

        isSubmitting = true;
        $('#btnText').text('Creating Account...');
        $('#btnSpinner').removeClass('d-none');
        $('#registerBtn').prop('disabled', true);

        $.ajax({
            url: "{{ route('corporation.register.submit') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Welcome!', response.message, 2000);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#registerBtn').prop('disabled', false);
                $('#btnText').text('Create Account');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        $(`[name="${key}"]`).addClass('is-invalid');
                        $(`#${key}_error`).text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error', 'Please check the form for errors.', 4000);
                } else {
                    showToast('error', 'Error', xhr.responseJSON?.message || 'Something went wrong.', 4000);
                }
            }
        });
    });

    // Password confirmation validation
    $('input[name="password"], input[name="password_confirmation"]').on('keyup', function() {
        const password = $('input[name="password"]').val();
        const confirm = $('input[name="password_confirmation"]').val();
        if (password !== confirm && confirm.length > 0) {
            $('input[name="password_confirmation"]').addClass('is-invalid');
            $('#password_confirmation_error').text('Passwords do not match.');
        } else {
            $('input[name="password_confirmation"]').removeClass('is-invalid');
            $('#password_confirmation_error').text('');
        }
    });

    // Clear validation on input
    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
        $(`#${$(this).attr('name')}_error`).text('');
    });
});
</script>
@endsection
