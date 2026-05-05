@extends('layouts.corporation-auth')

@section('title', 'Reset Password | Corporation Portal')

@section('form-content')
<div id="resetPasswordContainer" class="form-container fade-in">
    <div class="mb-4 text-center">
        <i class="fas fa-lock fa-3x" style="color: #2d6a4f;"></i>
        <h4 class="fw-bold mt-3" style="color: #1a3c5c;">Reset Password</h4>
        <p class="text-secondary small">Create a new password for your account.</p>
    </div>

    <form id="resetPasswordForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-3">
            <label for="newPassword" class="form-label">New Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="newPassword" name="password" placeholder="Enter new password" required>
                <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="newPassword">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <div class="invalid-feedback" id="passwordError"></div>
        </div>

        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder="Confirm new password" required>
            </div>
            <div class="invalid-feedback" id="confirmPasswordError"></div>
        </div>

        <button type="submit" class="btn btn-primary-custom" id="resetPasswordBtn">
            <i class="fas fa-save me-2"></i> Reset Password
        </button>
    </form>

    <hr class="my-4">

    <div class="text-center">
        <p class="signup-text" style="color:#5c6e7e;">Remember your password?
            <a href="{{ route('corporation.login') }}" class="text-decoration-none fw-bold" style="color:#2d6a4f;">Back to Login</a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let isSubmitting = false;

    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();

        if (isSubmitting) return false;

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const password = $('#newPassword').val();
        const confirmPassword = $('#confirmPassword').val();

        let hasError = false;

        if (!password) {
            $('#newPassword').addClass('is-invalid');
            $('#passwordError').text('Password is required.');
            hasError = true;
        } else if (password.length < 6) {
            $('#newPassword').addClass('is-invalid');
            $('#passwordError').text('Password must be at least 6 characters.');
            hasError = true;
        }

        if (password !== confirmPassword) {
            $('#confirmPassword').addClass('is-invalid');
            $('#confirmPasswordError').text('Passwords do not match.');
            hasError = true;
        }

        if (hasError) {
            showToast('error', 'Validation Error', 'Please check the form for errors.', 3000);
            return false;
        }

        isSubmitting = true;
        $('#resetPasswordBtn').html('<i class="fas fa-spinner fa-spin me-2"></i> Resetting...').prop('disabled', true);

        $.ajax({
            url: "{{ route('corporation.password.update') }}",
            method: "POST",
            data: {
                token: $('input[name="token"]').val(),
                email: $('input[name="email"]').val(),
                password: password,
                password_confirmation: confirmPassword,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Password Reset!', response.message, 2000);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#resetPasswordBtn').html('<i class="fas fa-save me-2"></i> Reset Password').prop('disabled', false);

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        if (key === 'password') {
                            $('#newPassword').addClass('is-invalid');
                            $('#passwordError').text(errors[key][0]);
                        } else if (key === 'email') {
                            showToast('error', 'Error', errors[key][0], 4000);
                        }
                    }
                } else {
                    showToast('error', 'Error', xhr.responseJSON?.message || 'Failed to reset password.', 4000);
                }
            }
        });
    });

    // Password confirmation validation
    $('#confirmPassword').on('keyup', function() {
        const password = $('#newPassword').val();
        const confirm = $(this).val();
        if (password !== confirm && confirm.length > 0) {
            $(this).addClass('is-invalid');
            $('#confirmPasswordError').text('Passwords do not match.');
        } else {
            $(this).removeClass('is-invalid');
            $('#confirmPasswordError').text('');
        }
    });

    $('#newPassword, #confirmPassword').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });
});
</script>
@endsection
