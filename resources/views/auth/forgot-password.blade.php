{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.authLayout')

@section('title', 'Forgot Password | TN Municipal Property Tax Portal')

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
                <h1>Password<br><span class="hero-highlight">Recovery</span></h1>
                <p class="hero-description">
                    Don't worry! Enter your registered email address and we'll send you a secure link to reset your
                    password.
                </p>
                <div class="trust-badge">
                    <div class="trust-item"><i class="fas fa-shield-alt"></i> <span>Secure Reset</span></div>
                    <div class="trust-item"><i class="fas fa-clock"></i> <span>60 Min Expiry</span></div>
                    <div class="trust-item"><i class="fas fa-envelope"></i> <span>Email Link</span></div>
                </div>
            </div>
            <div class="quote-area">
                <div class="quote">“Forgot password? Reset it securely in minutes.” — TN e-Governance</div>
            </div>
        </div>

        <!-- RIGHT: Forgot Password Form -->
        <div class="login-form-section">
            <!-- Mobile Header -->
            <div class="mobile-header">
                <div class="brand-icon" style="margin: 0 auto 10px;"><i class="fas fa-key"></i></div>
                <div class="brand-text">Password Recovery</div>
                <div class="brand-sub">Reset your account password</div>
            </div>

            <div class="form-header">
                <h2>Reset Your Password</h2>
                <p>We'll send you a secure link to reset your password</p>
            </div>

            <div id="alert-container"></div>

            <form id="forgotPasswordForm">
                @csrf

                <div class="mb-4 text-center">
                    <div
                        style="background: #fef5e8; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-key fa-2x" style="color: #e67e22;"></i>
                    </div>
                    <p class="text-muted" style="font-size: 0.85rem;">
                        Enter your registered email address and we'll send you a password reset link.
                    </p>
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="input-label" for="email">
                        <i class="fas fa-envelope me-2"></i>Registered Email Address
                    </label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class=""
                            placeholder="yourname@municipality.tn.gov.in" autocomplete="email" value="{{ old('email') }}">
                    </div>
                    <div class="invalid-feedback" id="email_error"></div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="login-btn" id="resetBtn">
                    <span id="btnText"><i class="fas fa-paper-plane me-2"></i> Send Reset Link</span>
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

            $('#forgotPasswordForm').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (isSubmitting) return false;

                // Clear previous errors
                $('#alert-container').html('');
                $('#email').removeClass('is-invalid');
                $('#email_error').text('');

                const email = $('#email').val().trim();

                // Client-side validation
                if (!email) {
                    $('#email').addClass('is-invalid');
                    $('#email_error').text('Email address is required.');
                    showToast('error', 'Validation Error', 'Please enter your email address.', 3000);
                    return false;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    $('#email').addClass('is-invalid');
                    $('#email_error').text('Please enter a valid email address.');
                    showToast('error', 'Validation Error', 'Please enter a valid email address.', 3000);
                    return false;
                }

                // Show loading state
                isSubmitting = true;
                $('#btnText').html('<i class="fas fa-paper-plane me-2"></i> Sending...');
                $('#btnSpinner').removeClass('d-none');
                $('#resetBtn').prop('disabled', true);

                const formData = {
                    email: email,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: "{{ route('password.email') }}",
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(res) {
                        isSubmitting = false;
                        $('#resetBtn').prop('disabled', false);
                        $('#btnText').html(
                            '<i class="fas fa-paper-plane me-2"></i> Send Reset Link');
                        $('#btnSpinner').addClass('d-none');

                        if (res.status === 'success') {
                            showToast('success', 'Reset Link Sent!', res.message ||
                                'Check your email for password reset instructions.', 5000);
                            $('#email').val('');

                            setTimeout(() => {
                                showToast('info', 'Important Note',
                                    'The reset link expires in 60 minutes.', 4000);
                            }, 3000);
                        } else {
                            showToast('error', 'Error!', res.message ||
                                'Something went wrong. Please try again.', 5000);
                        }
                    },
                    error: function(xhr) {
                        isSubmitting = false;
                        $('#resetBtn').prop('disabled', false);
                        $('#btnText').html(
                            '<i class="fas fa-paper-plane me-2"></i> Send Reset Link');
                        $('#btnSpinner').addClass('d-none');

                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.email) {
                                $('#email').addClass('is-invalid');
                                $('#email_error').text(errors.email[0]);
                            }
                            showToast('error', 'Validation Error!',
                                'Please enter a valid email address.', 5000);
                        } else if (xhr.status === 404) {
                            $('#email').addClass('is-invalid');
                            $('#email_error').text('No account found with this email address.');
                            showToast('error', 'Email Not Found!',
                                'No taxpayer account exists with this email.', 5000);
                        } else if (xhr.status === 429) {
                            showToast('warning', 'Too Many Attempts',
                                'Please wait before trying again.', 5000);
                        } else {
                            let message = xhr.responseJSON?.message ||
                                'Unable to process your request. Please try again later.';
                            showToast('error', 'Error!', message, 5000);
                        }
                    }
                });

                return false;
            });

            // Clear validation on focus
            $('#email').on('focus', function() {
                $(this).removeClass('is-invalid');
                $('#email_error').text('');
            });

            // Demo mode - double click to populate demo email
            $('.login-form-section').on('dblclick', function(e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
                $('#email').val('taxpayer@tn.gov.in');
                showToast("info", "Demo Email", "Demo email address loaded for testing", 2200);
            });
        });
    </script>
@endsection
