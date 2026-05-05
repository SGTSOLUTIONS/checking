@extends('layouts.corporation-auth')

@section('title', 'Forgot Password | Corporation Portal')

@section('form-content')
<div id="forgotPasswordContainer">
    <div class="mb-4">
        <h4 class="fw-bold" style="color: #32012F;">Forgot Password</h4>
        <p class="text-secondary small">Enter your registered email address to receive a password reset link</p>
    </div>

    <form id="forgotPasswordForm" method="POST" action="{{ route('corporation.password.email') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="forgotEmail" name="email" placeholder="Enter your registered email">
            </div>
            <div class="invalid-feedback" id="forgotEmailError"></div>
        </div>

        <button type="submit" class="btn btn-primary-custom" id="forgotSubmitBtn">
            <i class="fas fa-paper-plane me-2"></i> Send Reset Link
        </button>
    </form>

    <hr class="my-4">

    <div class="text-center">
        <p>
            Remember your password?
            <a href="{{ route('corporation.login') }}" class="text-decoration-none fw-bold" style="color:#F97300;">Back to login</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let isForgotSubmitting = false;

        $('#forgotEmail').on('input', function () {
            $(this).removeClass('is-invalid');
            $('#forgotEmailError').text('');
        });

        $('#forgotPasswordForm').on('submit', function (e) {
            e.preventDefault();

            if (isForgotSubmitting) {
                return false;
            }

            $('#forgotEmail').removeClass('is-invalid');
            $('#forgotEmailError').text('');

            const email = $('#forgotEmail').val().trim();
            const $btn = $('#forgotSubmitBtn');
            const originalBtnHtml = $btn.html();

            if (!email) {
                $('#forgotEmail').addClass('is-invalid');
                $('#forgotEmailError').text('Email address is required.');
                showToast('error', 'Validation Error', 'Please enter your email address.', 3000);
                return false;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $('#forgotEmail').addClass('is-invalid');
                $('#forgotEmailError').text('Enter a valid email address.');
                showToast('error', 'Validation Error', 'Please enter a valid email.', 3000);
                return false;
            }

            isForgotSubmitting = true;
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Sending...');
            showLoader();

            $.ajax({
                url: $('#forgotPasswordForm').attr('action'),
                type: 'POST',
                data: $('#forgotPasswordForm').serialize(),
                dataType: 'json',
                success: function (response) {
                    hideLoader();
                    isForgotSubmitting = false;
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    if (response.status === 'success') {
                        showToast('success', 'Email Sent', response.message, 4000);
                        $('#forgotPasswordForm')[0].reset();
                    } else {
                        showToast('error', 'Error', response.message || 'Unable to send reset link.', 4000);
                    }
                },
                error: function (xhr) {
                    hideLoader();
                    isForgotSubmitting = false;
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};

                        if (errors.email) {
                            $('#forgotEmail').addClass('is-invalid');
                            $('#forgotEmailError').text(errors.email[0]);
                        } else {
                            $('#forgotEmail').addClass('is-invalid');
                            $('#forgotEmailError').text(xhr.responseJSON?.message || 'Invalid email address.');
                        }

                        showToast('error', 'Validation Error', xhr.responseJSON?.message || 'Please check your email address.', 4000);
                    } else if (xhr.status === 500) {
                        showToast('error', 'Mail Error', xhr.responseJSON?.message || 'Failed to send email. Please try again later.', 4000);
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
