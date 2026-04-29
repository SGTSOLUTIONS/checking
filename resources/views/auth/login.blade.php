@extends('layouts.authLayout')

@section('title', 'Login - Taxpayer Access')

@section('content')
    <div class="form-header">
        <h2>Taxpayer Access</h2>
        <p>Sign in to view property tax, file returns & download e-receipts</p>
    </div>

    <div id="alert-container"></div>

    <form id="loginForm" method="POST">
        @csrf

        <div class="mb-3">
            <label class="input-label" for="email">Email address</label>
            <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" class="" placeholder="yourname@municipality.tn.gov.in" autocomplete="email" value="{{ old('email') }}">
            </div>
            <div class="invalid-feedback-custom" id="email_error"></div>
        </div>

        <div class="mb-3">
            <label class="input-label" for="password">Password</label>
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" class="" placeholder="Enter password" autocomplete="current-password">
            </div>
            <div class="invalid-feedback-custom" id="password_error"></div>
        </div>

        <div class="form-options">
            <label class="checkbox">
                <input type="checkbox" id="rememberCheck" name="remember"> <span>Keep me signed in (secured device)</span>
            </label>
            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password / PID?</a>
        </div>

        <button type="submit" class="login-btn" id="loginBtn">
            <span id="btnText"><i class="fas fa-file-invoice-dollar"></i> Access Tax Dashboard</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
        </button>
    </form>

    <div class="divider"><span>OR SIGN IN WITH</span></div>
    <div class="sso-buttons">
        <button class="sso-btn" id="ssoGoogleBtn"><i class="fab fa-google"></i> TN e-Seva</button>
        <button class="sso-btn" id="ssoOktaBtn"><i class="fas fa-building"></i> UMANG SSO</button>
        <button class="sso-btn" id="ssoMsftBtn"><i class="fas fa-database"></i> DigiLocker</button>
    </div>
    <div class="register-prompt">New taxpayer? <a href="{{ route('register') }}">Register your property for tax recognition</a></div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();

            // Reset error states
            $('#alert-container').html('');
            $('#email, #password').removeClass('is-invalid');
            $('#email_error, #password_error').text('');

            // Show loading state
            $('#btnText').html('<i class="fas fa-spinner fa-spin"></i> Authenticating...');
            $('#btnSpinner').removeClass('d-none');
            $('#loginBtn').prop('disabled', true);

            const formData = {
                email: $('#email').val().trim(),
                password: $('#password').val(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                remember: $('#rememberCheck').is(':checked') ? 1 : 0
            };
            alert("hi");
            $.ajax({
                url: "{{ route('login.post') }}",
                method: "POST",
                data: formData,
                dataType: "json",
                success: function(res) {
                    console.log('Login response:', res);

                    if (res.status === 'success') {
                        showToast('success', 'Success!', res.message, 2000);

                        // IMPORTANT FIX: Handle redirect properly
                        if (res.redirect) {
                            // Check if redirect URL is absolute or relative
                            let redirectUrl = res.redirect;

                            // If it's an absolute URL but you need relative, extract the path
                            if (redirectUrl.startsWith('http')) {
                                try {
                                    const url = new URL(redirectUrl);
                                    redirectUrl = url.pathname + url.search;
                                } catch(e) {
                                    console.error('Invalid URL:', e);
                                }
                            }

                            setTimeout(function() {
                                window.location.href = redirectUrl;
                            }, 1500);
                        } else {
                            // Fallback redirect - try to determine based on role
                            setTimeout(function() {
                                window.location.href = "{{ route('admin.dashboard') }}";
                            }, 1500);
                        }
                    } else {
                        resetLoginButton();
                        showToast('error', 'Error!', res.message || 'Invalid credentials', 5000);
                    }
                },
                error: function(xhr) {
                    resetLoginButton();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.email) {
                            $('#email').addClass('is-invalid');
                            $('#email_error').text(errors.email[0]);
                        }
                        if (errors.password) {
                            $('#password').addClass('is-invalid');
                            $('#password_error').text(errors.password[0]);
                        }
                        showToast('error', 'Validation Error!', 'Please check the form for errors.', 5000);
                    } else if (xhr.status === 401) {
                        showToast('error', 'Authentication Failed!', xhr.responseJSON?.message || 'Invalid password.', 5000);
                    } else if (xhr.status === 403) {
                        showToast('error', 'Account Inactive!', xhr.responseJSON?.message || 'Your account is not active.', 5000);
                    } else if (xhr.status === 404) {
                        showToast('error', 'Account Not Found!', xhr.responseJSON?.message || 'No account found with this email.', 5000);
                    } else {
                        let errorMsg = 'Unable to reach server. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showToast('error', 'Connection issue', errorMsg, 5000);
                    }
                }
            });
        });

        function resetLoginButton() {
            $('#loginBtn').prop('disabled', false);
            $('#btnSpinner').addClass('d-none');
            $('#btnText').html('<i class="fas fa-file-invoice-dollar"></i> Access Tax Dashboard');
        }

        // SSO Buttons Demo
        $('#ssoGoogleBtn').on('click', function() {
            showToast("info", "TN e-Seva", "Redirecting to Tamil Nadu Single Sign-On", 2800);
            setTimeout(() => {
                window.location.href = "{{ route('login') }}?sso=tnseva";
            }, 1500);
        });

        $('#ssoOktaBtn').on('click', function() {
            showToast("info", "UMANG Platform", "Connecting to Unified Mobile App", 2700);
            setTimeout(() => {
                window.location.href = "{{ route('login') }}?sso=umang";
            }, 1500);
        });

        $('#ssoMsftBtn').on('click', function() {
            showToast("info", "DigiLocker", "Authenticating via DigiLocker issued documents", 2800);
            setTimeout(() => {
                window.location.href = "{{ route('login') }}?sso=digilocker";
            }, 1500);
        });

        // Demo credentials on double-click
        $('.form-header').on('dblclick', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
            $('#email').val('taxpayer@tn.gov.in');
            $('#password').val('TNtax2025');
            showToast("success", "Demo Credentials", "Sample taxpayer account loaded (for preview)", 2200);
        });
    });
</script>
@endsection
