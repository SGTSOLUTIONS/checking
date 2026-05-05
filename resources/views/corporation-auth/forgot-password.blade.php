@extends('layouts.corporation-auth')

@section('title', 'Forgot Password | Corporation Portal')

@section('form-content')
<div id="forgotPasswordContainer" class="form-container fade-in">
    <div class="mb-4 text-center">
        <i class="fas fa-key fa-3x" style="color: #2d6a4f;"></i>
        <h4 class="fw-bold mt-3" style="color: #1a3c5c;">Forgot Password?</h4>
        <p class="text-secondary small">Enter your email address and we'll send you a link to reset your password.</p>
    </div>

    <form id="forgotPasswordForm">
        @csrf
        <div class="mb-3">
            <label for="resetEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="resetEmail" name="email" placeholder="your@email.com" required>
            </div>
            <div class="invalid-feedback" id="emailError"></div>
        </div>

        <button type="submit" class="btn btn-primary-custom" id="resetBtn">
            <i class="fas fa-paper-plane me-2"></i> Send Reset Link
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

    $('#forgotPasswordForm').on('submit', function(e) {
        e.preventDefault();

        if (isSubmitting) return false;

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const email = $('#resetEmail').val().trim();

        if (!email) {
            $('#resetEmail').addClass('is-invalid');
            $('#emailError').text('Email address is required.');
            showToast('error', 'Validation Error', 'Please enter your email address.', 3000);
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            $('#resetEmail').addClass('is-invalid');
            $('#emailError').text('Please enter a valid email address.');
            showToast('error', 'Validation Error', 'Please enter a valid email address.', 3000);
            return false;
        }

        isSubmitting = true;
        $('#resetBtn').html('<i class="fas fa-spinner fa-spin me-2"></i> Sending...').prop('disabled', true);

        $.ajax({
            url: "{{ route('corporation.password.email') }}",
            method: "POST",
            data: {
                email: email,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Email Sent!', response.message, 5000);
                    setTimeout(function() {
                        window.location.href = "{{ route('corporation.login') }}";
                    }, 3000);
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                $('#resetBtn').html('<i class="fas fa-paper-plane me-2"></i> Send Reset Link').prop('disabled', false);

                if (xhr.status === 422) {
                    $('#resetEmail').addClass('is-invalid');
                    $('#emailError').text(xhr.responseJSON?.message);
                    showToast('error', 'Error', xhr.responseJSON?.message, 4000);
                } else {
                    showToast('error', 'Error', xhr.responseJSON?.message || 'Something went wrong.', 4000);
                }
            }
        });
    });

    $('#resetEmail').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#emailError').text('');
    });
});
</script>
@endsection
