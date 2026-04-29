@extends('layouts.authLayout')

@section('title', 'Login - Taxpayer Access')

@section('content')
    <div class="form-header">
        <h2>Taxpayer Access</h2>
        <p>Sign in to view property tax, file returns & download e-receipts</p>
    </div>

    <form id="loginForm">
        @csrf

        <div class="mb-3">
            <label class="input-label" for="email">Email address</label>
            <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="yourname@municipality.tn.gov.in" autocomplete="email">
            </div>
            <div class="invalid-feedback-custom" id="email_error"></div>
        </div>

        <div class="mb-3">
            <label class="input-label" for="password">Password</label>
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Enter password" autocomplete="current-password">
                <button type="button" class="toggle-password" style="position: absolute; right: 12px; background: transparent; border: none; color: #e67e22;">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback-custom" id="password_error"></div>
        </div>

        <div class="form-options">
            <label class="checkbox">
                <input type="checkbox" name="remember" id="rememberCheck"> <span>Keep me signed in (secured device)</span>
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
    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        const input = $('#password');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#loginForm').on('submit', function(e) {
        e.preventDefault();

        $('#email, #password').removeClass('is-invalid');
        $('#email_error, #password_error').text('');

        $('#btnText').html('<i class="fas fa-spinner fa-spin"></i> Authenticating...');
        $('#btnSpinner').removeClass('d-none');
        $('#loginBtn').prop('disabled', true);

        $.ajax({
            url: "{{ route('login.post') }}",
            method: "POST",
            data: {
                email: $('#email').val().trim(),
                password: $('#password').val(),
                remember: $('#rememberCheck').is(':checked') ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function(res) {
                if (res.status === 'success') {
                    showToast('success', 'Success!', res.message, 1500);
                    setTimeout(function() {
                        window.location.href = res.redirect;
                    }, 1500);
                } else {
                    resetLoginButton();
                    showToast('error', 'Error!', res.message || 'Invalid credentials', 5000);
                }
            },
            error: function(xhr) {
                resetLoginButton();
                if (xhr.responseJSON) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors.email) {
                            $('#email').addClass('is-invalid');
                            $('#email_error').text(xhr.responseJSON.errors.email[0]);
                        }
                        if (xhr.responseJSON.errors.password) {
                            $('#password').addClass('is-invalid');
                            $('#password_error').text(xhr.responseJSON.errors.password[0]);
                        }
                    } else {
                        showToast('error', 'Error', xhr.responseJSON.message || 'Authentication failed', 5000);
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
