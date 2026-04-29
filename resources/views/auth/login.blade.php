@extends('layouts.authLayout')

@section('title', 'Login - Taxpayer Access')

@section('content')
    <div class="form-header">
        <h2>Taxpayer Access</h2>
        <p>Sign in to view property tax, file returns & download e-receipts</p>
    </div>

    <div id="alert-container"></div>

    <form id="loginForm" method="POST" >
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
$(document).ready(function() {
    // Make sure to unbind any existing handlers
    $('#loginForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

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

        console.log('Sending AJAX request to login...');

        $.ajax({
            url: "{{ route('login.post') }}",
            method: "POST",
            data: formData,
            dataType: "json",
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(res) {
                console.log('Login response:', res);

                if (res.status === 'success') {
                    showToast('success', 'Success!', res.message, 1500);

                    // Handle redirect
                    if (res.redirect) {
                        console.log('Redirecting to:', res.redirect);
                        setTimeout(function() {
                            window.location.href = res.redirect;
                        }, 1500);
                    } else {
                        console.error('No redirect URL in response');
                        resetLoginButton();
                    }
                } else {
                    resetLoginButton();
                    showToast('error', 'Error!', res.message || 'Invalid credentials', 5000);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });

                resetLoginButton();

                // Try to parse error response
                if (xhr.responseJSON) {
                    const errorMsg = xhr.responseJSON.message || 'Authentication failed';
                    showToast('error', 'Error', errorMsg, 5000);

                    // Handle validation errors
                    if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.email) {
                            $('#email').addClass('is-invalid');
                            $('#email_error').text(errors.email[0]);
                        }
                        if (errors.password) {
                            $('#password').addClass('is-invalid');
                            $('#password_error').text(errors.password[0]);
                        }
                    }
                } else {
                    showToast('error', 'Error', 'Unable to connect to server. Please try again.', 5000);
                }
            }
        });

        function resetLoginButton() {
            $('#loginBtn').prop('disabled', false);
            $('#btnSpinner').addClass('d-none');
            $('#btnText').html('<i class="fas fa-file-invoice-dollar"></i> Access Tax Dashboard');
        }
    });

    // SSO Buttons
    $('#ssoGoogleBtn, #ssoOktaBtn, #ssoMsftBtn').on('click', function() {
        let service = $(this).text().trim();
        showToast("info", service, "Redirecting to " + service, 2000);
        setTimeout(() => {
            window.location.href = "{{ route('login') }}?sso=" + service.toLowerCase().replace(/\s+/g, '');
        }, 1500);
    });

    // Demo credentials on double-click
    $('.form-header').on('dblclick', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
        $('#email').val('taxpayer@tn.gov.in');
        $('#password').val('TNtax2025');
        showToast("success", "Demo Credentials", "Sample taxpayer account loaded", 2200);
    });
});
</script>
@endsection
