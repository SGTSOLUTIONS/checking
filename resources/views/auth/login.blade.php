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
<script>
    $(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();

            $('#alert-container').html('');
            $('#email, #password').removeClass('is-invalid');
            $('#email_error, #password_error').text('');

            $('#btnText').html('<i class="fas fa-file-invoice-dollar"></i> Access Tax Dashboard');
            $('#btnSpinner').removeClass('d-none');
            $('#loginBtn').prop('disabled', true);

            const formData = {
                email: $('#email').val().trim(),
                password: $('#password').val(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                remember: $('#rememberCheck').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: "{{ route('login.post') }}",
                method: "POST",
                data: formData,
                dataType: "json",
                success: function(res) {
                    $('#loginBtn').prop('disabled', false);
                    $('#btnSpinner').addClass('d-none');
                    $('#btnText').html('<i class="fas fa-file-invoice-dollar"></i> Access Tax Dashboard');

                    if (res.status === 'success') {
                        showToast('success', 'Success!', res.message, 3000);
                        setTimeout(() => {
                            if (res.redirect) window.location.href = res.redirect;
                            else showToast('info', 'Redirect', 'Dashboard loading...', 2000);
                        }, 1500);
                    } else {
                        showToast('error', 'Error!', res.message || 'Invalid credentials', 5000);
                    }
                },
                error: function(xhr) {
                    $('#loginBtn').prop('disabled', false);
                    $('#btnSpinner').addClass('d-none');
                    $('#btnText').html('<i class="fas fa-file-invoice-dollar"></i> Access Tax Dashboard');

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
                        showToast('error', 'Authentication Failed!', xhr.responseJSON?.message || 'Invalid credentials.', 5000);
                    } else {
                        showToast('error', 'Connection issue', 'Unable to reach server. Please try again.', 5000);
                    }
                }
            });
        });

        // SSO Buttons Demo
        $('#ssoGoogleBtn').on('click', function() {
            showToast("info", "TN e-Seva", "Redirecting to Tamil Nadu Single Sign-On", 2800);
            setTimeout(() => showToast("success", "SSO Connected", "Taxpayer records fetched", 2400), 1400);
        });
        $('#ssoOktaBtn').on('click', function() {
            showToast("info", "UMANG Platform", "Connecting to Unified Mobile App", 2700);
            setTimeout(() => showToast("success", "Verified", "Property tax summary available", 2300), 1300);
        });
        $('#ssoMsftBtn').on('click', function() {
            showToast("info", "DigiLocker", "Authenticating via DigiLocker issued documents", 2800);
            setTimeout(() => showToast("success", "Authorized", "Tax certificates accessible", 2200), 1400);
        });

        // Demo credentials on double-click
        $('.login-form-section').on('dblclick', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
            $('#email').val('taxpayer@tn.gov.in');
            $('#password').val('TNtax2025');
            showToast("success", "Demo Credentials", "Sample taxpayer account loaded (for preview)", 2200);
        });
    });
</script>
@endsection
