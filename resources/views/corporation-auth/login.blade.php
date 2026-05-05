@extends('layouts.corporation-auth')

@section('title', 'Login | Corporation Portal')

@section('form-content')
<div id="loginFormContainer">
    <div class="mb-4">
        <h4 class="fw-bold" style="color: #32012F;">Welcome to Municipal e-Services</h4>
        <p class="text-secondary small">Sign in to access property tax, licenses, complaints, and more</p>
    </div>

    <form id="loginForm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="loginEmail" name="email" placeholder="your@email.com" required>
            </div>
            <div class="invalid-feedback" id="loginEmailError"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required>
                <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="loginPassword" style="border-left:0; background:#fff6eb;">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <div class="invalid-feedback" id="loginPasswordError"></div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberCheck" name="remember" style="border-color:#F97300;">
                <label class="form-check-label small" for="rememberCheck">Remember me</label>
            </div>
            <a href="{{ route('corporation.password.request') }}" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary-custom" id="loginSubmitBtn">
            <i class="fas fa-arrow-right-to-bracket me-2"></i> Access Corporation Dashboard
        </button>
    </form>

    <hr class="my-4">

    <div class="text-center">
        <p>Don't have an account?
            <a href="{{ route('corporation.register') }}" class="text-decoration-none fw-bold" style="color:#F97300;">Register here</a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Use pure JavaScript to ensure it runs after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded successfully');

    let isSubmitting = false;

    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('Form submitted');

            if (isSubmitting) return false;

            // Reset errors
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
            });

            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;
            const remember = document.getElementById('rememberCheck').checked ? 1 : 0;

            let hasError = false;

            if (!email) {
                document.getElementById('loginEmail').classList.add('is-invalid');
                document.getElementById('loginEmailError').textContent = 'Email address is required.';
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('loginEmail').classList.add('is-invalid');
                document.getElementById('loginEmailError').textContent = 'Enter a valid email address.';
                hasError = true;
            }

            if (!password) {
                document.getElementById('loginPassword').classList.add('is-invalid');
                document.getElementById('loginPasswordError').textContent = 'Password is required.';
                hasError = true;
            }

            if (hasError) {
                if (typeof showToast === 'function') {
                    showToast('error', 'Validation Error', 'Please check the form.', 3000);
                } else {
                    alert('Please check the form');
                }
                return false;
            }

            isSubmitting = true;
            const $btn = document.getElementById('loginSubmitBtn');
            const originalText = $btn.innerHTML;
            $btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Signing in...';
            $btn.disabled = true;

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('corporation.login.submit') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: remember,
                    _token: csrfToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (typeof showToast === 'function') {
                        showToast('success', 'Welcome!', data.message, 1500);
                    }
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    isSubmitting = false;
                    $btn.innerHTML = originalText;
                    $btn.disabled = false;

                    if (data.message) {
                        if (typeof showToast === 'function') {
                            showToast('error', 'Error', data.message, 4000);
                        } else {
                            alert(data.message);
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                isSubmitting = false;
                $btn.innerHTML = originalText;
                $btn.disabled = false;

                if (typeof showToast === 'function') {
                    showToast('error', 'Error', 'Something went wrong. Please try again.', 4000);
                } else {
                    alert('Something went wrong. Please try again.');
                }
            });
        });
    }

    // Clear validation on input
    const emailInput = document.getElementById('loginEmail');
    const passwordInput = document.getElementById('loginPassword');

    if (emailInput) {
        emailInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            document.getElementById('loginEmailError').textContent = '';
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            document.getElementById('loginPasswordError').textContent = '';
        });
    }
});
</script>
@endsection
