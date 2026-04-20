@extends('layouts.authLayout')

@section('title', 'Login')

@section('content')
<h3>Login to Your Account</h3>

<div id="alert-container"></div>

<form id="loginForm">
    @csrf

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label">Email addres</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
        <div class="invalid-feedback" id="email_error"></div>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
        <div class="invalid-feedback" id="password_error"></div>
    </div>

    {{-- Submit --}}
    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <span id="btnText">Login</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
        </button>
    </div>
<div class="text-center mt-3">
    <a href="{{ route('password.request') }}">Forgot your password?</a>
</div>
    <div class="text-center mt-3">
        <a href="{{ route('register') }}">Don’t have an account? Register</a>
    </div>
</form>
@endsection

@section('js')
<script>
$(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        $('#alert-container').html('');
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $('#btnText').text('Logging in...');
        $('#btnSpinner').removeClass('d-none');
        $('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: "{{ route('login.post') }}",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (res) {
                $('button[type="submit"]').prop('disabled', false);
                $('#btnText').text('Login');
                $('#btnSpinner').addClass('d-none');

                if (res.status === 'success') {
                    showToast('success', 'Success!', res.message, 3000);
                    setTimeout(() => window.location.href = res.redirect, 1500);
                } else {
                    showToast('error', 'Error!', res.message, 5000);
                }
            },
            error: function (xhr) {
                $('button[type="submit"]').prop('disabled', false);
                $('#btnText').text('Login');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error!', 'Please check the form for errors.', 5000);
                } else if (xhr.status === 401) {
                    showToast('error', 'Authentication Failed!', xhr.responseJSON.message || 'Invalid credentials.', 5000);
                } else {
                    showToast('error', 'Error!', 'Something went wrong. Please try again.', 5000);
                }
            }
        });
    });
});
</script>
@endsection
