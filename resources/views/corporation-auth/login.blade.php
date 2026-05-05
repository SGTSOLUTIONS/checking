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
$(document).ready(function() {
    alert('hello');
    console.log('jQuery loaded successfully');

    let isSubmitting = false;

    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('Form submitted via jQuery');

        if (isSubmitting) return false;

        // Reset errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const email = $('#loginEmail').val().trim();
        const password = $('#loginPassword').val();
        const remember = $('#rememberCheck').is(':checked') ? 1 : 0;

        let hasError = false;

        if (!email) {
            $('#loginEmail').addClass('is-invalid');
            $('#loginEmailError').text('Email address is required.');
            hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#loginEmail').addClass('is-invalid');
            $('#loginEmailError').text('Enter a valid email address.');
            hasError = true;
        }

        if (!password) {
            $('#loginPassword').addClass('is-invalid');
            $('#loginPasswordError').text('Password is required.');
            hasError = true;
        }

        if (hasError) {
            showToast('error', 'Validation Error', 'Please check the form.', 3000);
            return false;
        }

        isSubmitting = true;
        const $btn = $('#loginSubmitBtn');
        const originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Signing in...').prop('disabled', true);

        $.ajax({
            url: "{{ route('corporation.login.submit') }}",
            type: "POST",
            data: {
                email: email,
                password: password,
                remember: remember,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function(response) {
                console.log('AJAX Success:', response);
                if (response.status === 'success') {
                    showToast('success', 'Welcome!', response.message, 1500);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    isSubmitting = false;
                    $btn.html(originalText).prop('disabled', false);
                    showToast('error', 'Error', response.message || 'Something went wrong.', 4000);
                }
            },
            error: function(xhr) {
                console.log('AJAX Error:', xhr);
                isSubmitting = false;
                $btn.html(originalText).prop('disabled', false);

                if (xhr.status === 401) {
                    $('#loginPassword').addClass('is-invalid');
                    $('#loginPasswordError').text(xhr.responseJSON?.message || 'Invalid password.');
                    showToast('error', 'Authentication Failed', xhr.responseJSON?.message, 4000);
                } else if (xhr.status === 404) {
                    $('#loginEmail').addClass('is-invalid');
                    $('#loginEmailError').text(xhr.responseJSON?.message || 'Account not found.');
                    showToast('error', 'Account Not Found', xhr.responseJSON?.message, 4000);
                } else if (xhr.status === 403) {
                    showToast('error', 'Account Issue', xhr.responseJSON?.message, 4000);
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        if (errors.email) {
                            $('#loginEmail').addClass('is-invalid');
                            $('#loginEmailError').text(errors.email[0]);
                        }
                        if (errors.password) {
                            $('#loginPassword').addClass('is-invalid');
                            $('#loginPasswordError').text(errors.password[0]);
                        }
                    }
                    showToast('error', 'Validation Error', 'Please check the form.', 4000);
                } else {
                    showToast('error', 'Error', 'Something went wrong. Please try again.', 4000);
                }
            }
        });
    });

    // Clear validation on input
    $('#loginEmail, #loginPassword').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
        $(`#${this.id}Error`).text('');
    });
});
</script>
@endsection
