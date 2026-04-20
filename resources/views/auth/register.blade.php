<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        .auth-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .auth-body {
            padding: 30px;
        }
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
            border: 2px dashed #dee2e6;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .file-upload-area:hover {
            border-color: #28a745;
            background: #f0fff4;
        }
        .file-upload-area.dragover {
            border-color: #28a745;
            background: #e8f5e8;
            transform: scale(1.02);
        }
        .file-upload-icon {
            margin-bottom: 15px;
            color: #28a745;
        }
        .file-upload-text .primary {
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
        }
        .file-upload-text .secondary {
            color: #6c757d;
            font-size: 14px;
        }
        .file-preview {
            margin-top: 15px;
            text-align: center;
        }
        .file-preview img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid #28a745;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .file-info {
            margin-top: 10px;
            font-size: 14px;
        }
        .file-remove {
            border: none;
            background: transparent;
            color: #dc3545;
            margin-top: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        .file-remove:hover {
            color: #c82333;
        }
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2 class="mb-2">Create New Account</h2>
            <p class="mb-0">Join our community today</p>
        </div>
        
        <div class="auth-body">
            <div id="alert-container"></div>

            <form id="registerForm" enctype="multipart/form-data">
                @csrf

                {{-- Profile Picture Upload --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Profile Picture</label>
                    <div class="file-upload-container">
                        <div class="file-upload-area" id="fileUploadArea">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt fa-3x"></i>
                            </div>
                            <div class="file-upload-text">
                                <div class="primary">Click to upload or drag & drop</div>
                                <div class="secondary">PNG, JPG, JPEG, GIF up to 2MB</div>
                            </div>
                            <button type="button" class="btn btn-outline-success mt-3 file-upload-btn">
                                <i class="fas fa-folder-open me-2"></i>Choose File
                            </button>
                            <input type="file" class="file-input" id="profile_picture" name="profile_picture" accept=".png,.jpg,.jpeg,.gif">
                        </div>
                        <div class="file-preview" id="filePreview"></div>
                    </div>
                    <div class="invalid-feedback d-block mt-2" id="profile_picture_error"></div>
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name">
                    <div class="invalid-feedback" id="name_error"></div>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                    <div class="invalid-feedback" id="email_error"></div>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                    <div class="invalid-feedback" id="password_error"></div>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password">
                    <div class="invalid-feedback" id="password_confirmation_error"></div>
                </div>

                {{-- Gender --}}
                <div class="mb-3">
                    <label for="gender" class="form-label fw-bold">Gender</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="" selected disabled>Select your gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="invalid-feedback" id="gender_error"></div>
                </div>

                {{-- Phone --}}
                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
                    <div class="invalid-feedback" id="phone_error"></div>
                </div>

                {{-- Date of Birth --}}
                <div class="mb-3">
                    <label for="date_of_birth" class="form-label fw-bold">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                    <div class="invalid-feedback" id="date_of_birth_error"></div>
                </div>

                {{-- City --}}
                <div class="mb-4">
                    <label for="city" class="form-label fw-bold">City</label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="Enter your city">
                    <div class="invalid-feedback" id="city_error"></div>
                </div>

                {{-- Submit --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">
                        <span id="btnText">Create Account</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>

                <div class="text-center mt-4">
                    <p class="mb-0">Already have an account? 
                        <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(function () {
        const fileInput = $('#profile_picture');
        const fileUploadArea = $('#fileUploadArea');
        const filePreview = $('#filePreview');
        const fileUploadBtn = $('.file-upload-btn');

        // When clicking "Choose File" button
        fileUploadBtn.on('click', function (e) {
            e.stopPropagation();
            fileInput[0].click();
        });

        // When clicking anywhere in the upload area
        fileUploadArea.on('click', function (e) {
            if (!$(e.target).closest('.file-remove, .file-upload-btn').length) {
                fileInput[0].click();
            }
        });

        // Prevent file input click from bubbling up
        fileInput.on('click', function (e) {
            e.stopPropagation();
        });

        // Drag & drop handling
        fileUploadArea.on('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fileUploadArea.addClass('dragover');
        });

        fileUploadArea.on('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fileUploadArea.removeClass('dragover');
        });

        fileUploadArea.on('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fileUploadArea.removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelection(files[0]);
            }
        });

        // Handle file selection
        fileInput.on('change', function (e) {
            e.stopPropagation();
            if (this.files && this.files[0]) {
                handleFileSelection(this.files[0]);
            }
        });

        function handleFileSelection(file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showAlert('error', 'Invalid file type! Please select a PNG, JPG, JPEG, or GIF image.');
                return;
            }

            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                showAlert('error', 'File too large! Please select an image smaller than 2MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                filePreview.html(`
                    <div class="d-flex align-items-center justify-content-center gap-4 p-3 bg-light rounded">
                        <img src="${e.target.result}" alt="Preview">
                        <div class="text-start">
                            <div class="file-info">
                                <div class="fw-bold text-dark">${file.name}</div>
                                <div class="text-muted">${(file.size / 1024).toFixed(2)} KB</div>
                            </div>
                            <button type="button" class="file-remove btn btn-link text-danger p-0 mt-2 text-decoration-none" id="removeFile">
                                <i class="fas fa-times me-1"></i> Remove File
                            </button>
                        </div>
                    </div>
                `);

                $('#removeFile').on('click', function (ev) {
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

        function showAlert(type, message) {
            const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
            const icon = type === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle';
            
            $('#alert-container').html(`
                <div class="alert ${alertClass} alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas ${icon} me-2"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        // Submit form
        $('#registerForm').on('submit', function (e) {
            e.preventDefault();

            $('#alert-container').html('');
            $('.form-control, .form-select').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#profile_picture_error').text('');

            $('#btnText').text('Creating Account...');
            $('#btnSpinner').removeClass('d-none');
            $('button[type="submit"]').prop('disabled', true);

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('register.post') }}",
                method: "POST",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (res) {
                    $('button[type="submit"]').prop('disabled', false);
                    $('#btnText').text('Create Account');
                    $('#btnSpinner').addClass('d-none');

                    if (res.status === 'success') {
                        showAlert('success', res.message);
                        setTimeout(() => {
                            window.location.href = res.redirect;
                        }, 2000);
                    } else {
                        showAlert('error', 'Registration Failed: ' + res.message);
                    }
                },
                error: function (xhr) {
                    $('button[type="submit"]').prop('disabled', false);
                    $('#btnText').text('Create Account');
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
                        showAlert('error', 'Please check the form for errors and try again.');
                    } else {
                        showAlert('error', 'An unexpected error occurred. Please try again.');
                    }
                }
            });
        });

        // Add some interactive form validation
        $('input, select').on('focus', function() {
            $(this).removeClass('is-invalid');
        });
    });
    </script>
</body>
</html>