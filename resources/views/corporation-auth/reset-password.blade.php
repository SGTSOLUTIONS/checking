@extends('layouts.corporation-auth')

@section('title', 'Reset Password | Corporation Portal')

@section('form-content')
<div id="resetPasswordContainer">
    <div class="mb-4">
        <h4 class="fw-bold" style="color: #32012F;">Reset Password</h4>
        <p class="text-secondary small">Enter your new password below to complete the reset process</p>
    </div>

    <form id="resetPasswordForm" method="POST" action="{{ route('corporation.password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="resetEmail" name="email" value="{{ $email }}" placeholder="Enter email address">
            </div>
            <div class="invalid-feedback" id="resetEmailError"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">New Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="resetPassword" name="password" placeholder="Enter new password">
                <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="resetPassword" style="border-left:0; background:#fff6eb;">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <div class="invalid-feedback" id="resetPasswordError"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="resetPasswordConfirmation" name="password_confirmation" placeholder="Confirm new password">
                <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="resetPasswordConfirmation" style="border-left:0; background:#fff6eb;">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <div class="invalid-feedback" id="resetPasswordConfirmationError"></div>
        </div>

        <button type="submit" class="btn btn-primary-custom" id="resetSubmitBtn">
            <i class="fas fa-key me-2"></i> Reset Password
        </button>
    </form>

    <hr class="my-4">

    <div class="text-center">
        <p>
            Back to
            <a href="{{ route('corporation.login') }}" class="text-decoration-none fw-bold" style="color:#F97300;">Login</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let isResetSubmitting = false;

        function clearResetErrors() {
            $('#resetEmail, #resetPassword, #resetPasswordConfirmation').removeClass('is-invalid');
            $('#resetEmailError, #resetPasswordError, #resetPasswordConfirmationError').text('');
        }

        $('#resetEmail, #resetPassword, #resetPasswordConfirmation').on('input', function () {
            $(this).removeClass('is-invalid');
            $('#' + this.id + 'Error').text('');
        });

        $('#resetPasswordForm').on('submit', function (e) {
            e.preventDefault();

            if (isResetSubmitting) {
                return false;
            }

            clearResetErrors();

            const email = $('#resetEmail').val().trim();
            const password = $('#resetPassword').val();
            const confirmPassword = $('#resetPasswordConfirmation').val();
            const $btn = $('#resetSubmitBtn');
            const originalBtnHtml = $btn.html();

            let hasError = false;

            if (!email) {
                $('#resetEmail').addClass('is-invalid');
                $('#resetEmailError').text('Email is required.');
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $('#resetEmail').addClass('is-invalid');
                $('#resetEmailError').text('Enter a valid email address.');
                hasError = true;
            }

            if (!password) {
                $('#resetPassword').addClass('is-invalid');
                $('#resetPasswordError').text('Password is required.');
                hasError = true;
            } else if (password.length < 6) {
                $('#resetPassword').addClass('is-invalid');
                $('#resetPasswordError').text('Password must be at least 6 characters.');
                hasError = true;
            }

            if (!confirmPassword) {
                $('#resetPasswordConfirmation').addClass('is-invalid');
                $('#resetPasswordConfirmationError').text('Confirm password is required.');
                hasError = true;
            } else if (password !== confirmPassword) {
                $('#resetPasswordConfirmation').addClass('is-invalid');
                $('#resetPasswordConfirmationError').text('Passwords do not match.');
                hasError = true;
            }

            if (hasError) {
                showToast('error', 'Validation Error', 'Please check the reset password form.', 3000);
                return false;
            }

            isResetSubmitting = true;
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Resetting...');
            showLoader();

            $.ajax({
                url: $('#resetPasswordForm').attr('action'),
                type: 'POST',
                data: $('#resetPasswordForm').serialize(),
                dataType: 'json',
                success: function (response) {
                    hideLoader();
                    isResetSubmitting = false;
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    if (response.status === 'success') {
                        showToast('success', 'Password Reset', response.message, 1500);
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else {
                        showToast('error', 'Error', response.message || 'Unable to reset password.', 4000);
                    }
                },
                error: function (xhr) {
                    hideLoader();
                    isResetSubmitting = false;
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};

                        if (errors.email) {
                            $('#resetEmail').addClass('is-invalid');
                            $('#resetEmailError').text(errors.email[0]);
                        }
                        if (errors.password) {
                            $('#resetPassword').addClass('is-invalid');
                            $('#resetPasswordError').text(errors.password[0]);
                        }

                        if (!Object.keys(errors).length) {
                            showToast('error', 'Reset Failed', xhr.responseJSON?.message || 'Invalid or expired reset token.', 4000);
                        } else {
                            showToast('error', 'Validation Error', 'Please correct the highlighted fields.', 4000);
                        }
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
