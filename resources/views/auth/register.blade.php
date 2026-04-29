@extends('layouts.authLayout')

@section('title', 'Register | TN Municipal Property Tax Portal')

@section('content')
    <div class="auth-card">
        <!-- LEFT: Branding & municipal info -->
        <div class="login-hero">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-user-plus"></i></div>
                <div>
                    <div class="brand-text">Greater Chennai Corporation</div>
                    <div class="brand-sub">Tamil Nadu • Tax Recognition Portal</div>
                </div>
            </div>
            <div class="hero-content">
                <h1>New Taxpayer<br><span class="hero-highlight">Registration</span></h1>
                <p class="hero-description">
                    Join Tamil Nadu's digital property tax ecosystem. Register your property,
                    get instant tax recognition, and pay taxes online seamlessly.
                </p>
                <div class="trust-badge">
                    <div class="trust-item"><i class="fas fa-shield-alt"></i> <span>Data Security</span></div>
                    <div class="trust-item"><i class="fas fa-file-certificate"></i> <span>Digital Recognition</span></div>
                    <div class="trust-item"><i class="fas fa-hand-holding-usd"></i> <span>Easy Payments</span></div>
                </div>
            </div>
            <div class="quote-area">
                <div class="quote">“வரி செலுத்துவோரின் நலன் காக்கும் தமிழ்நாடு அரசு” — Tamil Nadu Government</div>
            </div>
        </div>

        <!-- RIGHT: Registration Form -->
        <div class="login-form-section">
            <div class="form-header">
                <h2>Create Tax Account</h2>
                <p>Fill your details to register for property tax services</p>
            </div>

            <form id="registerForm" method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data">
                @csrf

                <!-- Profile Picture Upload -->
                <div class="mb-3">
                    <label class="input-label">
                        <i class="fas fa-id-card me-2"></i>Taxpayer Photo (for e-Recognition)
                    </label>
                    <div class="file-upload-container">
                        <div class="file-upload-area" id="fileUploadArea">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                            </div>
                            <div class="file-upload-text">
                                <div class="primary">Upload passport size photo</div>
                                <div class="secondary">PNG, JPG, JPEG up to 2MB</div>
                            </div>
                            <button type="button" class="btn btn-outline-success mt-2 file-upload-btn">
                                <i class="fas fa-folder-open me-2"></i>Choose File
                            </button>
                            <input type="file" class="file-input" id="profile_picture" name="profile_picture"
                                accept=".png,.jpg,.jpeg">
                        </div>
                        <div class="file-preview" id="filePreview"></div>
                    </div>
                    <div class="invalid-feedback" id="profile_picture_error"></div>
                </div>

                <!-- Full Name -->
                <div class="mb-3">
                    <label class="input-label" for="name">
                        <i class="fas fa-user me-2"></i>Full Name (as per ID proof)
                    </label>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" id="name" name="name" placeholder="Enter your full name"
                            value="{{ old('name') }}">
                    </div>
                    <div class="invalid-feedback" id="name_error"></div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="input-label" for="email">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="taxpayer@tn.gov.in"
                            value="{{ old('email') }}">
                    </div>
                    <div class="invalid-feedback" id="email_error"></div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="input-label" for="password">
                        <i class="fas fa-lock me-2"></i>Portal Password
                    </label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Create a strong password">
                    </div>
                    <div class="invalid-feedback" id="password_error"></div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label class="input-label" for="password_confirmation">
                        <i class="fas fa-check-circle me-2"></i>Confirm Password
                    </label>
                    <div class="input-field">
                        <i class="fas fa-check-circle"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Re-enter password">
                    </div>
                    <div class="invalid-feedback" id="password_confirmation_error"></div>
                </div>

                <!-- Gender -->
                <div class="mb-3">
                    <label class="input-label" for="gender">
                        <i class="fas fa-venus-mars me-2"></i>Gender
                    </label>
                    <div class="input-field">
                        <i class="fas fa-venus-mars"></i>
                        <select id="gender" name="gender" class="form-select-custom">
                            <option value="" selected disabled>Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="invalid-feedback" id="gender_error"></div>
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label class="input-label" for="phone">
                        <i class="fas fa-phone-alt me-2"></i>Mobile Number
                    </label>
                    <div class="input-field">
                        <i class="fas fa-phone-alt"></i>
                        <input type="tel" id="phone" name="phone" placeholder="10-digit mobile number"
                            value="{{ old('phone') }}">
                    </div>
                    <div class="invalid-feedback" id="phone_error"></div>
                </div>

                <!-- Date of Birth -->
                <div class="mb-3">
                    <label class="input-label" for="date_of_birth">
                        <i class="fas fa-calendar-alt me-2"></i>Date of Birth
                    </label>
                    <div class="input-field">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                            value="{{ old('date_of_birth') }}">
                    </div>
                    <div class="invalid-feedback" id="date_of_birth_error"></div>
                </div>

                <!-- City -->
                <div class="mb-4">
                    <label class="input-label" for="city">
                        <i class="fas fa-city me-2"></i>City / Corporation Zone
                    </label>
                    <div class="input-field">
                        <i class="fas fa-city"></i>
                        <input type="text" id="city" name="city"
                            placeholder="Eg: Chennai, Coimbatore, Madurai" value="{{ old('city') }}">
                    </div>
                    <div class="invalid-feedback" id="city_error"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn" id="registerBtn">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span id="btnText">Register for Tax Recognition</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                </button>

                <div class="register-prompt mt-3">
                    Already have a property tax account?
                    <a href="{{ route('login') }}">Login to Dashboard</a>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>Secured by Tamil Nadu e-Governance
                    </small>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Custom styles for registration form */
        .file-upload-container {
            position: relative;
        }

        .file-input {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
            pointer-events: none;
        }

        .file-upload-area {
            cursor: pointer;
            border: 2px dashed #e2e8f0;
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s ease;
            background: #fef9f0;
        }

        .file-upload-area:hover {
            border-color: #e67e22;
            background: #fff6ea;
        }

        .file-upload-area.dragover {
            border-color: #e67e22;
            background: #fef0e0;
            transform: scale(1.01);
        }

        .file-upload-icon {
            margin-bottom: 12px;
            color: #e67e22;
        }

        .file-upload-text .primary {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c4c6e;
            font-size: 0.9rem;
        }

        .file-upload-text .secondary {
            color: #7e8b9e;
            font-size: 11px;
        }

        .file-preview {
            margin-top: 15px;
            text-align: center;
        }

        .file-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e67e22;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .file-info {
            margin-top: 10px;
            font-size: 12px;
        }

        .file-remove {
            border: none;
            background: transparent;
            color: #dc3545;
            margin-top: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: color 0.3s ease;
        }

        .file-remove:hover {
            color: #c82333;
            text-decoration: underline;
        }

        .btn-outline-success {
            border: 1px solid #e67e22;
            color: #e67e22;
            background: white;
            border-radius: 40px;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background: #e67e22;
            color: white;
            border-color: #e67e22;
        }

        .form-select-custom {
            width: 100%;
            padding: 11px 14px 11px 44px;
            font-size: 0.9rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.2s;
            outline: none;
            font-family: 'Inter', 'Poppins', sans-serif;
        }

        .form-select-custom:focus {
            border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.15);
        }

        .form-select-custom.is-invalid {
            border-color: #e74c3c;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let isSubmitting = false;

            // File upload handling
            const fileInput = $('#profile_picture');
            const fileUploadArea = $('#fileUploadArea');
            const filePreview = $('#filePreview');
            const fileUploadBtn = $('.file-upload-btn');

            // When clicking "Choose File" button
            fileUploadBtn.on('click', function(e) {
                e.stopPropagation();
                fileInput[0].click();
            });

            // When clicking anywhere in the upload area
            fileUploadArea.on('click', function(e) {
                if (!$(e.target).closest('.file-remove, .file-upload-btn').length) {
                    fileInput[0].click();
                }
            });

            // Prevent file input click from bubbling up
            fileInput.on('click', function(e) {
                e.stopPropagation();
            });

            // Drag & drop handling
            fileUploadArea.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileUploadArea.addClass('dragover');
            });

            fileUploadArea.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileUploadArea.removeClass('dragover');
            });

            fileUploadArea.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileUploadArea.removeClass('dragover');

                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelection(files[0]);
                }
            });

            // Handle file selection
            fileInput.on('change', function(e) {
                e.stopPropagation();
                if (this.files && this.files[0]) {
                    handleFileSelection(this.files[0]);
                }
            });

            function handleFileSelection(file) {
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    showToast('error', 'Invalid File', 'Please select a PNG, JPG, or JPEG image.', 3000);
                    return;
                }

                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    showToast('error', 'File Too Large', 'Please select an image smaller than 2MB.', 3000);
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    filePreview.html(`
                <div class="d-flex align-items-center justify-content-center gap-3 p-2">
                    <img src="${e.target.result}" alt="Preview">
                    <div class="text-start">
                        <div class="file-info">
                            <div class="fw-bold text-dark">${file.name}</div>
                            <div class="text-muted">${(file.size / 1024).toFixed(2)} KB</div>
                        </div>
                        <button type="button" class="file-remove" id="removeFile">
                            <i class="fas fa-times me-1"></i> Remove
                        </button>
                    </div>
                </div>
            `);

                    $('#removeFile').on('click', function(ev) {
                        ev.preventDefault();
                        ev.stopPropagation();
                        removeFile();
                    });
                };
                reader.readAsDataURL(file);
            }

            function removeFile() {
                fileInput.val('');
                filePreview.html('');
            }

            // Form submission
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (isSubmitting) {
                    return false;
                }

                // Reset validation errors
                $('.invalid-feedback').text('');
                $('.input-field input, .form-select-custom').removeClass('is-invalid');

                // Get form data
                const formData = new FormData(this);

                // Client-side validation
                let hasError = false;

                const name = $('#name').val().trim();
                if (!name) {
                    $('#name').addClass('is-invalid');
                    $('#name_error').text('Full name is required.');
                    hasError = true;
                }

                const email = $('#email').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email) {
                    $('#email').addClass('is-invalid');
                    $('#email_error').text('Email address is required.');
                    hasError = true;
                } else if (!emailRegex.test(email)) {
                    $('#email').addClass('is-invalid');
                    $('#email_error').text('Please enter a valid email address.');
                    hasError = true;
                }

                const password = $('#password').val();
                if (!password) {
                    $('#password').addClass('is-invalid');
                    $('#password_error').text('Password is required.');
                    hasError = true;
                } else if (password.length < 6) {
                    $('#password').addClass('is-invalid');
                    $('#password_error').text('Password must be at least 6 characters.');
                    hasError = true;
                }

                const passwordConfirmation = $('#password_confirmation').val();
                if (password !== passwordConfirmation) {
                    $('#password_confirmation').addClass('is-invalid');
                    $('#password_confirmation_error').text('Passwords do not match.');
                    hasError = true;
                }

                const phone = $('#phone').val().trim();
                const phoneRegex = /^[0-9]{10}$/;
                if (!phone) {
                    $('#phone').addClass('is-invalid');
                    $('#phone_error').text('Mobile number is required.');
                    hasError = true;
                } else if (!phoneRegex.test(phone)) {
                    $('#phone').addClass('is-invalid');
                    $('#phone_error').text('Please enter a valid 10-digit mobile number.');
                    hasError = true;
                }

                const city = $('#city').val().trim();
                if (!city) {
                    $('#city').addClass('is-invalid');
                    $('#city_error').text('City is required.');
                    hasError = true;
                }

                if (hasError) {
                    showToast('error', 'Validation Error', 'Please check the form for errors.', 3000);
                    return false;
                }

                // Show loading state
                isSubmitting = true;
                $('#btnText').text('Registering...');
                $('#btnSpinner').removeClass('d-none');
                $('#registerBtn').prop('disabled', true);

                // AJAX request
                $.ajax({
                    url: "{{ route('register.post') }}",
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        isSubmitting = false;
                        $('#registerBtn').prop('disabled', false);
                        $('#btnText').text('Register for Tax Recognition');
                        $('#btnSpinner').addClass('d-none');

                        if (response.status === 'success') {
                            showToast('success', 'Registration Successful!', response.message,
                                2000);
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 2000);
                        } else {
                            showToast('error', 'Registration Failed', response.message ||
                                'Please try again.', 4000);
                        }
                    },
                    error: function(xhr) {
                        isSubmitting = false;
                        $('#registerBtn').prop('disabled', false);
                        $('#btnText').text('Register for Tax Recognition');
                        $('#btnSpinner').addClass('d-none');

                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                if (key === 'profile_picture') {
                                    $('#profile_picture_error').text(errors[key][0]);
                                } else {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}_error`).text(errors[key][0]);
                                }
                            }
                            showToast('error', 'Validation Error',
                                'Please check the form for errors.', 4000);
                        } else if (xhr.status === 409) {
                            const message = xhr.responseJSON?.message ||
                                'Email or phone already registered.';
                            showToast('error', 'Account Exists', message, 4000);
                            if (message.toLowerCase().includes('email')) {
                                $('#email').addClass('is-invalid');
                                $('#email_error').text(message);
                            }
                            if (message.toLowerCase().includes('phone')) {
                                $('#phone').addClass('is-invalid');
                                $('#phone_error').text(message);
                            }
                        } else {
                            const message = xhr.responseJSON?.message ||
                                'Something went wrong. Please try again later.';
                            showToast('error', 'Error', message, 4000);
                        }
                    }
                });

                return false;
            });

            // Clear validation on input
            $('#name, #email, #password, #password_confirmation, #phone, #city, #gender, #date_of_birth').on(
                'input change',
                function() {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').text('');
                });

            // Welcome message
            setTimeout(() => {
                showToast('info', '📜 Tamil Nadu Municipal Tax', 'Register for property tax e-services',
                    4000);
            }, 500);
        });
    </script>
@endsection
