{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.authLayout')

@section('title', 'Reset Password | TN Municipal Property Tax Portal')

@section('content')
    <div class="auth-card">
        <!-- LEFT: Branding & Info -->
        <div class="login-hero">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-landmark"></i></div>
                <div>
                    <div class="brand-text">Greater Chennai Corporation</div>
                    <div class="brand-sub">Tamil Nadu • Tax Recognition Portal</div>
                </div>
            </div>
            <div class="hero-content">
                <h1>Create<br><span class="hero-highlight">New Password</span></h1>
                <p class="hero-description">
                    Your new password must be different from previously used passwords.
                    Choose a strong password to keep your account secure.
                </p>
                <div class="trust-badge">
                    <div class="trust-item"><i class="fas fa-lock"></i> <span>Strong Password</span></div>
                    <div class="trust-item"><i class="fas fa-check-circle"></i> <span>8+ Characters</span></div>
                    <div class="trust-item"><i class="fas fa-shield-alt"></i> <span>Encrypted</span></div>
                </div>
            </div>
            <div class="quote-area">
                <div class="quote">“Secure your property tax account with a strong password.” — TN e-Governance</div>
            </div>
        </div>

        <!-- RIGHT: Reset Password Form -->
        <div class="login-form-section">
            <!-- Mobile Header -->
            <div class="mobile-header">
                <div class="brand-icon" style="margin: 0 auto 10px;"><i class="fas fa-lock"></i></div>
                <div class="brand-text">Create New Password</div>
                <div class="brand-sub">Reset your account password</div>
            </div>

            <div class="form-header">
                <h2>Create New Password</h2>
                <p>Please enter your new password below</p>
            </div>

            <div id="alert-container"></div>

            <form id="resetPasswordForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token ?? '' }}">
                <input type="hidden" name="email" value="{{ $email ?? '' }}">

                {{-- New Password --}}
                <div class="mb-4">
                    <label class="input-label" for="password">
                        <i class="fas fa-lock me-2"></i>New Password
                    </label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class=""
                            placeholder="Enter new password" autocomplete="new-password">
                    </div>
                    <div class="invalid-feedback" id="password_error"></div>
                    <small class="text-muted" style="font-size: 0.7rem;">
                        <i class="fas fa-info-circle"></i> Minimum 8 characters with at least 1 uppercase, 1 lowercase, 1
                        number
                    </small>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label class="input-label" for="password_confirmation">
                        <i class="fas fa-check-circle me-2"></i>Confirm Password
                    </label>
                    <div class="input-field">
                        <i class="fas fa-check-circle"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" class=""
                            placeholder="Confirm new password" autocomplete="new-password">
                    </div>
                    <div class="invalid-feedback" id="password_confirmation_error"></div>
                </div>

                {{-- Password Strength Indicator --}}
                <div class="mb-4">
                    <div class="progress" style="height: 4px; border-radius: 4px;">
                        <div id="passwordStrength" class="progress-bar" role="progressbar"
                            style="width: 0%; transition: all 0.3s ease;"></div>
                    </div>
                    <small id="strengthText" class="text-muted" style="font-size: 0.7rem;">Password strength: <span
                            id="strengthLabel">Not entered</span></small>
                </div>

                {{-- Submit --}}
                <button type="submit" class="login-btn" id="resetBtn">
                    <span id="btnText"><i class="fas fa-save me-2"></i> Reset Password</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                </button>

                <div class="register-prompt mt-4">
                    <a href="{{ route('login') }}" class="forgot-link">
                        <i class="fas fa-arrow-left me-2"></i>Back to Login
                    </a>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>Secured by Tamil Nadu e-Governance
                    </small>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let isSubmitting = false;

            // Password strength checker
            function checkPasswordStrength(password) {
                let strength = 0;
                let strengthText = '';
                let strengthColor = '';
                let width = 0;

                if (password.length === 0) {
                    $('#strengthLabel').text('Not entered');
                    $('#passwordStrength').css('width', '0%');
                    $('#passwordStrength').removeClass('bg-success bg-warning bg-danger bg-info');
                    return;
                }

                if (password.length >= 8) strength += 25;
                if (password.match(/[a-z]/)) strength += 25;
                if (password.match(/[A-Z]/)) strength += 25;
                if (password.match(/[0-9]/)) strength += 25;

                if (strength <= 25) {
                    strengthText = 'Weak';
                    strengthColor = 'bg-danger';
                    width = 25;
                } else if (strength <= 50) {
                    strengthText = 'Fair';
                    strengthColor = 'bg-warning';
                    width = 50;
                } else if (strength <= 75) {
                    strengthText = 'Good';
                    strengthColor = 'bg-info';
                    width = 75;
                } else {
                    strengthText = 'Strong';
                    strengthColor = 'bg-success';
                    width = 100;
                }

                $('#strengthLabel').text(strengthText);
                $('#passwordStrength').removeClass('bg-danger bg-warning bg-info bg-success').addClass(
                    strengthColor);
                $('#passwordStrength').css('width', width + '%');
            }

            $('#password').on('input', function() {
                checkPasswordStrength($(this).val());
            });

            $('#resetPasswordForm').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (isSubmitting) return false;

                // Clear previous errors
                $('#alert-container').html('');
                $('#password').removeClass('is-invalid');
                $('#password_confirmation').removeClass('is-invalid');
                $('#password_error').text('');
                $('#password_confirmation_error').text('');

                const password = $('#password').val();
                const passwordConfirmation = $('#password_confirmation').val();
                const token = $('input[name="token"]').val();
                const email = $('input[name="email"]').val();

                // Client-side validation
                if (!password) {
                    $('#password').addClass('is-invalid');
                    $('#password_error').text('Password is required.');
                    showToast('error', 'Validation Error', 'Please enter your new password.', 3000);
                    return false;
                }

                if (password.length < 8) {
                    $('#password').addClass('is-invalid');
                    $('#password_error').text('Password must be at least 8 characters.');
                    showToast('error', 'Validation Error', 'Password must be at least 8 characters.', 3000);
                    return false;
                }

                if (!password.match(/[a-z]/)) {
                    $('#password').addClass('is-invalid');
                    $('#password_error').text('Password must contain at least one lowercase letter.');
                    showToast('error', 'Validation Error',
                        'Password must contain at least one lowercase letter.', 3000);
                    return false;
                }

                if (!password.match(/[A-Z]/)) {
                    $('#password').addClass('is-invalid');
                    $('#password_error').text('Password must contain at least one uppercase letter.');
                    showToast('error', 'Validation Error',
                        'Password must contain at least one uppercase letter.', 3000);
                    return false;
                }

                if (!password.match(/[0-9]/)) {
                    $('#password').addClass('is-invalid');
                    $('#password_error').text('Password must contain at least one number.');
                    showToast('error', 'Validation Error', 'Password must contain at least one number.',
                        3000);
                    return false;
                }

                if (password !== passwordConfirmation) {
                    $('#password_confirmation').addClass('is-invalid');
                    $('#password_confirmation_error').text('Passwords do not match.');
                    showToast('error', 'Validation Error', 'Passwords do not match. Please try again.',
                        3000);
                    return false;
                }

                if (!token || !email) {
                    showToast('error', 'Invalid Request',
                        'Reset token is missing or invalid. Please request a new reset link.', 5000);
                    return false;
                }

                // Show loading state
                isSubmitting = true;
                $('#btnText').html('<i class="fas fa-save me-2"></i> Resetting...');
                $('#btnSpinner').removeClass('d-none');
                $('#resetBtn').prop('disabled', true);

                const formData = {
                    token: token,
                    email: email,
                    password: password,
                    password_confirmation: passwordConfirmation,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: "{{ route('password.update') }}",
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(res) {
                        isSubmitting = false;
                        $('#resetBtn').prop('disabled', false);
                        $('#btnText').html('<i class="fas fa-save me-2"></i> Reset Password');
                        $('#btnSpinner').addClass('d-none');

                        if (res.status === 'success') {
                            showToast('success', 'Success!',
                                'Password reset successfully! Redirecting to login...', 2000
                                );
                            setTimeout(function() {
                                window.location.href = res.redirect ||
                                    "{{ route('login') }}";
                            }, 2000);
                        } else {
                            showToast('error', 'Error!', res.message ||
                                'Failed to reset password.', 5000);
                        }
                    },
                    error: function(xhr) {
                        isSubmitting = false;
                        $('#resetBtn').prop('disabled', false);
                        $('#btnText').html('<i class="fas fa-save me-2"></i> Reset Password');
                        $('#btnSpinner').addClass('d-none');

                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.password) {
                                $('#password').addClass('is-invalid');
                                $('#password_error').text(errors.password[0]);
                            }
                            if (errors.password_confirmation) {
                                $('#password_confirmation').addClass('is-invalid');
                                $('#password_confirmation_error').text(errors
                                    .password_confirmation[0]);
                            }
                            showToast('error', 'Validation Error!',
                                'Please check your password requirements.', 5000);
                        } else if (xhr.status === 400) {
                            showToast('error', 'Invalid Token',
                                'The reset link has expired or is invalid. Please request a new one.',
                                5000);
                            setTimeout(function() {
                                window.location.href =
                                "{{ route('password.request') }}";
                            }, 3000);
                        } else {
                            let message = xhr.responseJSON?.message ||
                                'Failed to reset password. Please try again.';
                            showToast('error', 'Error!', message, 5000);
                        }
                    }
                });

                return false;
            });

            // Clear validation on focus/input
            $('#password, #password_confirmation').on('focus input', function() {
                $(this).removeClass('is-invalid');
                $('#password_error').text('');
                $('#password_confirmation_error').text('');
            });
        });
    </script>
@endsection
