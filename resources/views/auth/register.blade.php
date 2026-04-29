<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>TN Municipal | Property Tax - New Taxpayer Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a3c2c 0%, #0f2b1f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Tamil Nadu Heritage Background Overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(255, 215, 120, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 215, 120, 0.04) 1px, transparent 1px);
            background-size: 45px 45px;
            pointer-events: none;
            z-index: 0;
        }

        /* Decorative Blobs */
        .bg-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.2;
            pointer-events: none;
            z-index: 0;
        }

        .blob-1 {
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, #e67e22, #f39c12);
            top: -20%;
            right: -10%;
        }

        .blob-2 {
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, #28a745, #20c997);
            bottom: -20%;
            left: -10%;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(230, 126, 34, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 560px;
            position: relative;
            z-index: 10;
            animation: fadeSlideUp 0.5s ease-out;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-header {
            background: linear-gradient(135deg, #1e5a3c 0%, #0f3b26 100%);
            color: white;
            padding: 28px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-header::after {
            content: "🏛️";
            position: absolute;
            bottom: 5px;
            right: 15px;
            font-size: 55px;
            opacity: 0.1;
            pointer-events: none;
        }

        .auth-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .auth-header p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .gov-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 0.7rem;
            margin-top: 12px;
            backdrop-filter: blur(4px);
        }

        .auth-body {
            padding: 28px 30px;
        }

        /* File Upload Area (Enhanced) */
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
            border: 2px dashed #cbd5e1;
            padding: 25px;
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
        }

        .file-upload-text .secondary {
            color: #7e8b9e;
            font-size: 12px;
        }

        .file-preview {
            margin-top: 15px;
            text-align: center;
        }

        .file-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 20px;
            border: 3px solid #e67e22;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .file-info {
            margin-top: 10px;
            font-size: 13px;
        }

        .file-remove {
            border: none;
            background: transparent;
            color: #dc3545;
            margin-top: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: color 0.3s ease;
        }

        .file-remove:hover {
            color: #c82333;
            text-decoration: underline;
        }

        /* Form Elements */
        .form-label {
            font-weight: 600;
            color: #1e3a5f;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #e67e22;
            box-shadow: 0 0 0 4px rgba(230, 126, 34, 0.15);
        }

        .btn-success {
            background: linear-gradient(95deg, #e67e22, #f39c12);
            border: none;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 44px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -8px rgba(230, 126, 34, 0.4);
            background: linear-gradient(95deg, #d47118, #e68a2e);
        }

        .btn-outline-success {
            border: 1px solid #e67e22;
            color: #e67e22;
            background: white;
            border-radius: 40px;
            padding: 8px 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-outline-success:hover {
            background: #e67e22;
            color: white;
            border-color: #e67e22;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.75rem;
            margin-top: 5px;
            color: #dc3545;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
        }

        /* Alert styling */
        .alert-custom {
            border-radius: 16px;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success-custom {
            background: #e8f5e9;
            border-left-color: #28a745;
            color: #155724;
        }

        .alert-error-custom {
            background: #fee2e2;
            border-left-color: #dc3545;
            color: #721c24;
        }

        /* Link */
        .auth-link {
            text-align: center;
            margin-top: 20px;
        }

        .auth-link a {
            color: #e67e22;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        /* Ripple effect preserved */
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0));
            transform: scale(0);
            animation: rippleAnim 0.5s ease-out;
            pointer-events: none;
        }

        @keyframes rippleAnim {
            to {
                transform: scale(6);
                opacity: 0;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }

            .auth-body {
                padding: 20px;
            }

            .auth-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Decorative Tamil Nadu heritage blobs -->
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <div class="auth-container">
        <div class="auth-header">
            <h2><i class="fas fa-landmark me-2"></i>Taxpayer Registration</h2>
            <p>Greater Chennai Corporation • Tamil Nadu</p>
            <div class="gov-badge">
                <i class="fas fa-file-certificate"></i>
                <span>e-Governance | Tax Recognition Portal</span>
            </div>
        </div>

        <div class="auth-body">
            <div id="alert-container"></div>

            <form id="registerForm" enctype="multipart/form-data">
                @csrf

                {{-- Profile Picture Upload --}}
                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fas fa-id-card me-2"></i>Taxpayer Photo (for
                        e-Recognition)</label>
                    <div class="file-upload-container">
                        <div class="file-upload-area" id="fileUploadArea">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt fa-3x"></i>
                            </div>
                            <div class="file-upload-text">
                                <div class="primary">Upload passport size photo</div>
                                <div class="secondary">PNG, JPG, JPEG, GIF up to 2MB</div>
                            </div>
                            <button type="button" class="btn btn-outline-success mt-3 file-upload-btn">
                                <i class="fas fa-folder-open me-2"></i>Choose File
                            </button>
                            <input type="file" class="file-input" id="profile_picture" name="profile_picture"
                                accept=".png,.jpg,.jpeg,.gif">
                        </div>
                        <div class="file-preview" id="filePreview"></div>
                    </div>
                    <div class="invalid-feedback d-block mt-2" id="profile_picture_error"></div>
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold"><i class="fas fa-user me-2"></i>Full Name (as per
                        ID proof)</label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter your full name">
                    <div class="invalid-feedback" id="name_error"></div>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold"><i class="fas fa-envelope me-2"></i>Email
                        Address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="taxpayer@tn.gov.in">
                    <div class="invalid-feedback" id="email_error"></div>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold"><i class="fas fa-lock me-2"></i>Portal
                        Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Create a strong password">
                    <div class="invalid-feedback" id="password_error"></div>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-bold"><i
                            class="fas fa-check-circle me-2"></i>Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        placeholder="Re-enter password">
                    <div class="invalid-feedback" id="password_confirmation_error"></div>
                </div>

                {{-- Gender --}}
                <div class="mb-3">
                    <label for="gender" class="form-label fw-bold"><i
                            class="fas fa-venus-mars me-2"></i>Gender</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="" selected disabled>Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="invalid-feedback" id="gender_error"></div>
                </div>

                {{-- Phone --}}
                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold"><i class="fas fa-phone-alt me-2"></i>Mobile
                        Number</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        placeholder="10-digit mobile number">
                    <div class="invalid-feedback" id="phone_error"></div>
                </div>

                {{-- Date of Birth --}}
                <div class="mb-3">
                    <label for="date_of_birth" class="form-label fw-bold"><i
                            class="fas fa-calendar-alt me-2"></i>Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                    <div class="invalid-feedback" id="date_of_birth_error"></div>
                </div>

                {{-- City --}}
                <div class="mb-4">
                    <label for="city" class="form-label fw-bold"><i class="fas fa-city me-2"></i>City /
                        Corporation Zone</label>
                    <input type="text" class="form-control" id="city" name="city"
                        placeholder="Eg: Chennai, Coimbatore, Madurai">
                    <div class="invalid-feedback" id="city_error"></div>
                </div>

                {{-- Submit --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        <span id="btnText">Register for Tax Recognition</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>

                <div class="auth-link">
                    <p class="mb-0">Already have a property tax account?
                        <a href="{{ route('login') }}">Login to Dashboard</a>
                    </p>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted"><i class="fas fa-shield-alt me-1"></i>Secured by Tamil Nadu
                        e-Governance</small>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function() {
            // ========== ALL ORIGINAL FUNCTIONS PRESERVED ==========
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
                reader.onload = function(e) {
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

            function showAlert(type, message) {
                const alertClass = type === 'error' ? 'alert-error-custom' : 'alert-success-custom';
                const icon = type === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle';

                $('#alert-container').html(`
                <div class="alert ${alertClass} alert-dismissible fade show d-flex align-items-center" role="alert" style="border-radius: 16px;">
                    <i class="fas ${icon} me-2"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            `);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 5000);
            }

            // Submit form (ORIGINAL LOGIC PRESERVED)
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                $('#alert-container').html('');
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#profile_picture_error').text('');

                $('#btnText').text('Registering...');
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
                    success: function(res) {
                        $('button[type="submit"]').prop('disabled', false);
                        $('#btnText').text('Register for Tax Recognition');
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
                    error: function(xhr) {
                        $('button[type="submit"]').prop('disabled', false);
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
                            showAlert('error',
                                'Please check the form for errors and try again.');
                        } else {
                            showAlert('error',
                                'An unexpected error occurred. Please try again.');
                        }
                    }
                });
            });

            // Add some interactive form validation
            $('input, select').on('focus', function() {
                $(this).removeClass('is-invalid');
            });

            // Ripple effect on buttons (enhancement without breaking functions)
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-success, .btn-outline-success, .file-remove');
                if (btn) {
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple-effect');
                    const rect = btn.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
                    ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
                    ripple.style.position = 'absolute';
                    ripple.style.background =
                        'radial-gradient(circle, rgba(255,255,255,0.6), rgba(255,255,255,0))';
                    ripple.style.borderRadius = '50%';
                    ripple.style.pointerEvents = 'none';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.transition = 'transform 0.4s, opacity 0.6s';
                    btn.style.position = 'relative';
                    btn.style.overflow = 'hidden';
                    btn.appendChild(ripple);
                    setTimeout(() => {
                        ripple.style.transform = 'scale(5)';
                        ripple.style.opacity = '0';
                    }, 10);
                    setTimeout(() => ripple.remove(), 500);
                }
            });
        });
    </script>
</body>

</html>
