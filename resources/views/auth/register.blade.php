@extends('layouts.authLayout')

@section('title', 'Register - New Taxpayer Registration')

@section('content')
    <div class="form-header">
        <h2>Taxpayer Registration</h2>
        <p>Create your account to access property tax services</p>
    </div>

    <div id="alert-container"></div>

    <form id="registerForm" enctype="multipart/form-data">
        @csrf

        {{-- Profile Picture Upload --}}
        <div class="mb-3">
            <label class="input-label"><i class="fas fa-id-card me-2"></i>Taxpayer Photo (for e-Recognition)</label>
            <div class="file-upload-mini" id="fileUploadArea">
                <i class="fas fa-cloud-upload-alt"></i> Click or drag photo (PNG/JPG up to 2MB)
                <input type="file" id="profile_picture" name="profile_picture" accept=".png,.jpg,.jpeg,.gif" style="display:none">
            </div>
            <div id="filePreview" class="file-preview-mini"></div>
            <div class="invalid-feedback-custom" id="profile_picture_error"></div>
        </div>

        {{-- Name --}}
        <div class="mb-3">
            <label class="input-label"><i class="fas fa-user me-2"></i>Full Name (as per ID proof)</label>
            <div class="input-field">
                <i class="fas fa-user"></i>
                <input type="text" id="name" name="name" placeholder="Enter your full name" value="{{ old('name') }}">
            </div>
            <div class="invalid-feedback-custom" id="name_error"></div>
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label class="input-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
            <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="taxpayer@tn.gov.in" value="{{ old('email') }}">
            </div>
            <div class="invalid-feedback-custom" id="email_error"></div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label class="input-label"><i class="fas fa-lock me-2"></i>Portal Password</label>
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Create a strong password">
            </div>
            <div class="invalid-feedback-custom" id="password_error"></div>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
            <label class="input-label"><i class="fas fa-check-circle me-2"></i>Confirm Password</label>
            <div class="input-field">
                <i class="fas fa-check-circle"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password">
            </div>
            <div class="invalid-feedback-custom" id="password_confirmation_error"></div>
        </div>

        {{-- Gender --}}
        <div class="mb-3">
            <label class="input-label"><i class="fas fa-venus-mars me-2"></i>Gender</label>
            <div class="input-field">
                <i class="fas fa-venus-mars"></i>
                <select id="gender" name="gender">
                    <option value="" selected disabled>Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="invalid-feedback-custom" id="gender_error"></div>
        </div>

        {{-- Phone --}}
        <div class="mb-3">
            <label class="input-label"><i class="fas fa-phone-alt me-2"></i>Mobile Number</label>
            <div class="input-field">
                <i class="fas fa-phone-alt"></i>
                <input type="text" id="phone" name="phone" placeholder="10-digit mobile number" value="{{ old('phone') }}">
            </div>
            <div class="invalid-feedback-custom" id="phone_error"></div>
        </div>

        {{-- Date of Birth --}}
        <div class="mb-3">
            <label class="input-label"><i class="fas fa-calendar-alt me-2"></i>Date of Birth</label>
            <div class="input-field">
                <i class="fas fa-calendar-alt"></i>
                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
            </div>
            <div class="invalid-feedback-custom" id="date_of_birth_error"></div>
        </div>

        {{-- City --}}
        <div class="mb-4">
            <label class="input-label"><i class="fas fa-city me-2"></i>City / Corporation Zone</label>
            <div class="input-field">
                <i class="fas fa-city"></i>
                <input type="text" id="city" name="city" placeholder="Eg: Chennai, Coimbatore, Madurai" value="{{ old('city') }}">
            </div>
            <div class="invalid-feedback-custom" id="city_error"></div>
        </div>

        {{-- Submit --}}
        <div class="d-grid">
            <button type="submit" class="login-btn" id="registerBtn">
                <span id="btnText"><i class="fas fa-file-invoice-dollar"></i> Register for Tax Recognition</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </div>

        <div class="register-prompt mt-3">
            Already have a property tax account? <a href="{{ route('login') }}">Login to Dashboard</a>
        </div>
        <div class="text-center mt-3">
            <small class="text-muted"><i class="fas fa-shield-alt me-1"></i>Secured by Tamil Nadu e-Governance</small>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    $(function() {
        // File upload handling
        let selectedFile = null;

        $('#fileUploadArea').on('click', function() {
            $('#profile_picture').trigger('click');
        });

        $('#profile_picture').on('change', function(e) {
            if (this.files && this.files[0]) {
                handleFileSelection(this.files[0]);
            }
        });

        // Drag & drop
        const uploadArea = $('#fileUploadArea')[0];
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('border-danger');
        });
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('border-danger');
        });
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('border-danger');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelection(files[0]);
            }
        });

        function handleFileSelection(file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showToast('error', 'Invalid file type!', 'Please select a PNG, JPG, JPEG, or GIF image.', 4000);
                return;
            }

            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                showToast('error', 'File too large!', 'Please select an image smaller than 2MB.', 4000);
                return;
            }

            selectedFile = file;
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#filePreview').html(`
                    <img src="${e.target.result}" class="preview-img">
                    <button type="button" class="btn btn-sm btn-link text-danger" id="removeFileBtn">
                        <i class="fas fa-times"></i> Remove
                    </button>
                `);
                $('#removeFileBtn').on('click', function() {
                    $('#profile_picture').val('');
                    selectedFile = null;
                    $('#filePreview').empty();
                });
            };
            reader.readAsDataURL(file);
        }

        // Form submission
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();

            $('#alert-container').html('');
            $('.input-field input, .input-field select').removeClass('is-invalid');
            $('.invalid-feedback-custom').text('');

            $('#btnText').text('Registering...');
            $('#btnSpinner').removeClass('d-none');
            $('#registerBtn').prop('disabled', true);

            const formData = new FormData(this);

            // Replace with actual file if selected via drag-drop
            if (selectedFile && $('#profile_picture')[0].files.length === 0) {
                formData.set('profile_picture', selectedFile);
            }

            $.ajax({
                url: "{{ route('register.post') }}",
                method: "POST",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#registerBtn').prop('disabled', false);
                    $('#btnText').text('Register for Tax Recognition');
                    $('#btnSpinner').addClass('d-none');

                    if (res.status === 'success') {
                        showToast('success', 'Registration Successful!', res.message, 3000);
                        setTimeout(() => {
                            if (res.redirect) window.location.href = res.redirect;
                            else window.location.href = "{{ route('login') }}";
                        }, 2000);
                    } else {
                        showToast('error', 'Registration Failed!', res.message || 'Please try again.', 5000);
                    }
                },
                error: function(xhr) {
                    $('#registerBtn').prop('disabled', false);
                    $('#btnText').text('Register for Tax Recognition');
                    $('#btnSpinner').addClass('d-none');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            if (key === 'profile_picture') {
                                $('#profile_picture_error').text(errors[key][0]);
                            } else {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '_error').text(errors[key][0]);
                            }
                        }
                        showToast('error', 'Validation Error!', 'Please check the form for errors.', 5000);
                    } else if (xhr.status === 409) {
                        showToast('error', 'Account Exists!', xhr.responseJSON?.message || 'Email already registered.', 5000);
                    } else {
                        showToast('error', 'Error!', 'Something went wrong. Please try again.', 5000);
                    }
                }
            });
        });

        // Clear validation on focus
        $('input, select').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback-custom').text('');
        });
    });
</script>
@endsection
