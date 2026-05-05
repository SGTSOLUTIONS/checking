@extends('layouts.corporation-auth-layout')

@section('title', 'Reset Password | Corporation Portal')

@section('content')
<div class="auth-card" style="max-width: 600px;">
    <div class="login-form-section" style="width: 100%;">
        <div class="mobile-header">
            <div class="brand-icon mx-auto"><i class="fas fa-building"></i></div>
            <div class="brand-text">Corporation Portal</div>
            <div class="brand-sub">Property Tax Management System</div>
        </div>

        <div class="form-header text-center">
            <i class="fas fa-lock fa-3x text-primary mb-3"></i>
            <h2>Reset Password</h2>
            <p>Enter your new password below.</p>
        </div>

        <form id="resetPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="input-label">New Password</label>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter new password" required>
                </div>
                <div class="invalid-feedback" id="password_error"></div>
            </div>

            <div class="mb-3">
                <label class="input-label">Confirm Password</label>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password_confirmation" placeholder="Confirm new password" required>
                </div>
                <div class="invalid-feedback" id="password_confirmation_error"></div>
            </div>

            <button type="submit" class="login-btn" id="resetBtn">
                <i class="fas fa-save"></i>
                <span id="btnText">Reset Password</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
            </button>
        </form>

        <div class="back-to-login mt-3">
            <a href="{{ route('corporation.login') }}"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let isSubmitting = false;

    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isSubmitting) return false;

        $('.invalid-feedback').text('');
        $('.input-field input').removeClass('is-invalid');

        const password = $('input[name="password"]').val();
        const confirm = $('input[name="password_confirmation"]').val();

        if (!password) {
            $('input[name="password"]').addClass('is-invalid');
            $('#password_error').text('Password is required.');
            showToast('error', 'Validation Error', 'Please enter a password.', 3000);
            return false;
        }

        if (password.length < 6) {
            $('input[name="password"]').addClass('is-invalid');
            $('#password_error').text('Password must be at least 6 characters.');
            showToast('error', 'Validation Error', 'Password must be at least 6 characters.', 3000);
            return false;
        }

        if (password !== confirm) {
            $('input[name="password_confirmation"]').addClass('is-invalid');
            $('#password_confirmation_error').text('Passwords do not match.');
            showToast('error', 'Validation Error', 'Passwords do not match.', 3000);
            return false;
        }

        isSubmitting = true;
        $('#btnText').text('Resetting...');
        $('#btnSpinner').removeClass('d-none');
        $('#resetBtn').prop('disabled', true);

        const formData = {
            token: $('input[name="token"]').val(),
            email: $('input[name="email"]').val(),
            password: password,
            password_confirmation: confirm,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "{{ route('corporation.password.update') }}",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Password Reset', response.message, 3000);
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 2000);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#resetBtn').prop('disabled', false);
                $('#btnText').text('Reset Password');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        $(`input[name="${key}"]`).addClass('is-invalid');
                        $(`#${key}_error`).text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error', 'Please check the form.', 4000);
                } else {
                    showToast('error', 'Error', xhr.responseJSON?.message || 'Failed to reset password.', 4000);
                }
            }
        });
    });

    $('input').on('input', function() {
        $(this).removeClass('is-invalid');
        $(`#${$(this).attr('name')}_error`).text('');
    });
});
</script>
@endsection
