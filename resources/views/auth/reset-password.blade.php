@extends('layouts.authLayout')

@section('title', 'Reset Password - Create New Password')

@section('content')
    <div class="form-header">
        <h2>Create New Password</h2>
        <p>Please enter your new password below</p>
    </div>

    <div id="alert-container"></div>

    <form id="resetPasswordForm" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        {{-- New Password --}}
        <div class="mb-4">
            <label class="input-label" for="password">
                <i class="fas fa-lock me-2"></i>New Password
            </label>
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" class=""
                       placeholder="Enter new password" autocomplete="new-password">
            </div>
            <div class="invalid-feedback-custom" id="password_error"></div>
            <small class="text-muted">Minimum 8 characters with at least 1 uppercase, 1 lowercase, 1 number</small>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label class="input-label" for="password_confirmation">
                <i class="fas fa-check-circle me-2"></i>Confirm Password
            </label>
            <div class="input-field">
                <i class="fas fa-check-circle"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" class=""
                       placeholder="Confirm new password" autocomplete="new-password">
            </div>
            <div class="invalid-feedback-custom" id="password_confirmation_error"></div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="login-btn" id="resetBtn">
            <span id="btnText"><i class="fas fa-save"></i> Reset Password</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
        </button>

        <div class="register-prompt mt-4">
            <a href="{{ route('login') }}" class="forgot-link">
                <i class="fas fa-arrow-left me-2"></i>Back to Login
            </a>
        </div>
    </form>
@endsection

@section('scripts')
<script>
$(function () {
    $('#resetPasswordForm').on('submit', function (e) {
        e.preventDefault();

        $('#alert-container').html('');
        $('#password').removeClass('is-invalid');
        $('#password_confirmation').removeClass('is-invalid');
        $('#password_error').text('');
        $('#password_confirmation_error').text('');

        $('#btnText').html('<i class="fas fa-save"></i> Resetting...');
        $('#btnSpinner').removeClass('d-none');
        $('#resetBtn').prop('disabled', true);

        const formData = {
            token: $('input[name="token"]').val(),
            email: $('input[name="email"]').val(),
            password: $('#password').val(),
            password_confirmation: $('#password_confirmation').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "{{ route('password.update') }}",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (res) {
                if (res.status === 'success') {
                    showToast('success', 'Success!', 'Password reset successfully! Redirecting to login...', 2000);
                    setTimeout(() => {
                        window.location.href = res.redirect || "{{ route('login') }}";
                    }, 2000);
                }
            },
            error: function (xhr) {
                $('#resetBtn').prop('disabled', false);
                $('#btnText').html('<i class="fas fa-save"></i> Reset Password');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.password) {
                        $('#password').addClass('is-invalid');
                        $('#password_error').text(errors.password[0]);
                    }
                    if (errors.password_confirmation) {
                        $('#password_confirmation').addClass('is-invalid');
                        $('#password_confirmation_error').text(errors.password_confirmation[0]);
                    }
                    showToast('error', 'Validation Error!', 'Please check your password requirements.', 5000);
                } else {
                    let message = xhr.responseJSON?.message || 'Failed to reset password. Please try again.';
                    showToast('error', 'Error!', message, 5000);
                }
            }
        });
    });

    $('#password, #password_confirmation').on('focus', function() {
        $(this).removeClass('is-invalid');
        $('#password_error').text('');
        $('#password_confirmation_error').text('');
    });
});
</script>
@endsection
