@extends('layouts.authLayout')

@section('title', 'Forgot Password')

@section('content')
<h3>Reset Your Password</h3>

<div id="alert-container"></div>

<form id="forgotPasswordForm">
    @csrf
    
    <div class="mb-4 text-center">
        <i class="fas fa-key fa-3x text-primary mb-3"></i>
        <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
        </div>
        <div class="invalid-feedback" id="email_error"></div>
    </div>

    {{-- Submit --}}
    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <span id="btnText">Send Reset Link</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
        </button>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i>Back to Login
        </a>
    </div>
</form>
@endsection

@section('js')
<script>
$(function () {
    $('#forgotPasswordForm').on('submit', function (e) {
        e.preventDefault();

        $('#alert-container').html('');
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $('#btnText').text('Sending...');
        $('#btnSpinner').removeClass('d-none');
        $('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: "{{ route('password.email') }}",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (res) {
                $('button[type="submit"]').prop('disabled', false);
                $('#btnText').text('Send Reset Link');
                $('#btnSpinner').addClass('d-none');

                if (res.status === 'success') {
                    showToast('success', 'Email Sent!', res.message, 5000);
                    // Optionally clear the form
                    $('#email').val('');
                } else {
                    showToast('error', 'Error!', res.message, 5000);
                }
            },
            error: function (xhr) {
                $('button[type="submit"]').prop('disabled', false);
                $('#btnText').text('Send Reset Link');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error!', 'Please check your email address.', 5000);
                } else if (xhr.status === 404) {
                    showToast('error', 'Email Not Found!', xhr.responseJSON.message || 'No account found with this email.', 5000);
                } else {
                    showToast('error', 'Error!', 'Something went wrong. Please try again.', 5000);
                }
            }
        });
    });
});
</script>
@endsection