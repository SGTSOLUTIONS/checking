@extends('layouts.corporation-auth')

@section('title', 'Login | Corporation Portal')

@section('form-content')
<div id="loginFormContainer">
    <div class="mb-4">
        <h4 class="fw-bold" style="color: #32012F;">Welcome to SRIS</h4>
        {{-- <p class="text-secondary small">Sign in to access property tax, licenses, complaints, and more</p> --}}
    </div>

    <form id="loginForm" method="POST" action="{{ route('corporation.login.submit') }}">
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
        <p>
            Don't have an account?
            <a href="{{ route('corporation.register') }}" class="text-decoration-none fw-bold" style="color:#F97300;">Register here</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let isSubmitting = false;

        function clearErrors() {
            $('#loginEmail, #loginPassword').removeClass('is-invalid');
            $('#loginEmailError, #loginPasswordError').text('');
        }

        $('#loginEmail, #loginPassword').on('input', function () {
            $(this).removeClass('is-invalid');
            $('#' + this.id + 'Error').text('');
        });

        $('#loginForm').on('submit', function (e) {
            e.preventDefault();

            if (isSubmitting) {
                return false;
            }

            clearErrors();

            const $form = $(this);
            const $btn = $('#loginSubmitBtn');
            const originalBtnHtml = $btn.html();
            const email = $('#loginEmail').val().trim();
            const password = $('#loginPassword').val();

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
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Signing in...');
            showLoader();

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (response) {
                    hideLoader();

                    if (response.status === 'success') {
                        showToast('success', 'Welcome!', response.message, 1200);
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1200);
                    } else {
                        isSubmitting = false;
                        $btn.prop('disabled', false).html(originalBtnHtml);
                        showToast('error', 'Login Failed', response.message || 'Something went wrong.', 4000);
                    }
                },
                error: function (xhr) {
                    hideLoader();
                    isSubmitting = false;
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    if (xhr.status === 401) {
                        $('#loginPassword').addClass('is-invalid');
                        $('#loginPasswordError').text(xhr.responseJSON?.message || 'Invalid password.');
                        showToast('error', 'Authentication Failed', xhr.responseJSON?.message || 'Invalid credentials.', 4000);
                    } else if (xhr.status === 404) {
                        $('#loginEmail').addClass('is-invalid');
                        $('#loginEmailError').text(xhr.responseJSON?.message || 'Account not found.');
                        showToast('error', 'Account Not Found', xhr.responseJSON?.message || 'No account found with this email.', 4000);
                    } else if (xhr.status === 403) {
                        showToast('error', 'Account Issue', xhr.responseJSON?.message || 'Your account is not active.', 4000);
                    } else if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};

                        if (errors.email) {
                            $('#loginEmail').addClass('is-invalid');
                            $('#loginEmailError').text(errors.email[0]);
                        }

                        if (errors.password) {
                            $('#loginPassword').addClass('is-invalid');
                            $('#loginPasswordError').text(errors.password[0]);
                        }

                        showToast('error', 'Validation Error', 'Please check the form.', 4000);
                    } else if (xhr.status === 419) {
                        showToast('error', 'Session Expired', 'Please refresh the page and try again.', 4000);
                    } else {
                        showToast('error', 'Error', 'Something went wrong. Please try again.', 4000);
                    }
                }
            });

            return false;
        });
    });
</script>
@endpush
