@extends('layouts.authLayout')

@section('title', 'Login - Taxpayer Access')

@section('content')
    <div class="form-header">
        <h2>Taxpayer Access</h2>
        <p>Sign in to view property tax, file returns & download e-receipts</p>
    </div>

    <div id="alert-container"></div>

    <form id="loginForm" action="javascript:void(0);" method="POST">
        @csrf

        <div class="mb-3">
            <label class="input-label" for="email">Email address</label>
            <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" class=""
                    placeholder="yourname@municipality.tn.gov.in" autocomplete="email" value="{{ old('email') }}">
            </div>
            <div class="invalid-feedback-custom" id="email_error"></div>
        </div>

        <div class="mb-3">
            <label class="input-label" for="password">Password</label>
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" class="" placeholder="Enter password"
                    autocomplete="current-password">
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
    <div class="register-prompt">New taxpayer? <a href="{{ route('register') }}">Register your property for tax
            recognition</a></div>
@endsection

@section('scripts')
    <script>
        // Make sure everything runs after page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, setting up login handler');

            // Get form element
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const rememberCheck = document.getElementById('rememberCheck');
            const emailError = document.getElementById('email_error');
            const passwordError = document.getElementById('password_error');
            const alertContainer = document.getElementById('alert-container');

            // Remove any existing event listeners by replacing the form
            const newForm = loginForm.cloneNode(true);
            loginForm.parentNode.replaceChild(newForm, loginForm);

            // Get the new form reference
            const finalForm = document.getElementById('loginForm');

            // Add submit event listener
            finalForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Form submitted via AJAX');

                // Clear previous errors
                if (alertContainer) alertContainer.innerHTML = '';
                emailInput.classList.remove('is-invalid');
                passwordInput.classList.remove('is-invalid');
                if (emailError) emailError.textContent = '';
                if (passwordError) passwordError.textContent = '';

                // Show loading state
                if (btnText) btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
                if (btnSpinner) btnSpinner.classList.remove('d-none');
                if (loginBtn) loginBtn.disabled = true;

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                'content');

                // Prepare form data
                const formData = new FormData();
                formData.append('email', emailInput.value.trim());
                formData.append('password', passwordInput.value);
                formData.append('_token', csrfToken);
                formData.append('remember', rememberCheck.checked ? '1' : '0');

                // Make AJAX request
                fetch("{{ route('login.post') }}", {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json().then(data => ({
                            status: response.status,
                            data
                        }));
                    })
                    .then(({
                        status,
                        data
                    }) => {
                        console.log('Response data:', data);

                        if (data.status === 'success') {
                            // Show success message
                            if (window.showToast) {
                                window.showToast('success', 'Success!', data.message, 1500);
                            } else {
                                console.log('Success:', data.message);
                            }

                            // Redirect after short delay
                            if (data.redirect) {
                                setTimeout(function() {
                                    window.location.href = data.redirect;
                                }, 1500);
                            } else {
                                resetButton();
                                if (window.showToast) {
                                    window.showToast('error', 'Error!', 'No redirect URL provided',
                                        3000);
                                }
                            }
                        } else {
                            resetButton();
                            if (window.showToast) {
                                window.showToast('error', 'Error!', data.message || 'Login failed',
                                    4000);
                            } else {
                                alert(data.message || 'Login failed');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        resetButton();
                        if (window.showToast) {
                            window.showToast('error', 'Connection Error',
                                'Unable to connect to server. Please try again.', 5000);
                        } else {
                            alert('Unable to connect to server. Please try again.');
                        }
                    });

                function resetButton() {
                    if (loginBtn) loginBtn.disabled = false;
                    if (btnSpinner) btnSpinner.classList.add('d-none');
                    if (btnText) btnText.innerHTML =
                        '<i class="fas fa-file-invoice-dollar"></i> Access Tax Dashboard';
                }
            });

            // SSO Buttons
            const ssoGoogle = document.getElementById('ssoGoogleBtn');
            const ssoOkta = document.getElementById('ssoOktaBtn');
            const ssoMsft = document.getElementById('ssoMsftBtn');

            if (ssoGoogle) {
                ssoGoogle.addEventListener('click', function() {
                    if (window.showToast) window.showToast("info", "TN e-Seva",
                        "Redirecting to Tamil Nadu Single Sign-On", 2000);
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}?sso=tnseva";
                    }, 1500);
                });
            }

            if (ssoOkta) {
                ssoOkta.addEventListener('click', function() {
                    if (window.showToast) window.showToast("info", "UMANG Platform",
                        "Connecting to Unified Mobile App", 2000);
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}?sso=umang";
                    }, 1500);
                });
            }

            if (ssoMsft) {
                ssoMsft.addEventListener('click', function() {
                    if (window.showToast) window.showToast("info", "DigiLocker",
                        "Authenticating via DigiLocker issued documents", 2000);
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}?sso=digilocker";
                    }, 1500);
                });
            }

            // Demo credentials on double-click
            const formHeader = document.querySelector('.form-header');
            if (formHeader) {
                formHeader.addEventListener('dblclick', function(e) {
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
                    if (emailInput) emailInput.value = 'taxpayer@tn.gov.in';
                    if (passwordInput) passwordInput.value = 'TNtax2025';
                    if (window.showToast) window.showToast("success", "Demo Credentials",
                        "Sample taxpayer account loaded", 2200);
                });
            }
        });

        // Reset button function for global use
        function resetLoginButton() {
            const loginBtn = document.getElementById('loginBtn');
            const btnSpinner = document.getElementById('btnSpinner');
            const btnText = document.getElementById('btnText');

            if (loginBtn) loginBtn.disabled = false;
            if (btnSpinner) btnSpinner.classList.add('d-none');
            if (btnText) btnText.innerHTML = '<i class="fas fa-file-invoice-dollar"></i> Access Tax Dashboard';
        }
    </script>
@endsection
