@extends('layouts.authLayout')

@section('title', 'Reset Password')

@section('content')
<h3>Create New Password</h3>

<div id="alert-container"></div>

<form id="resetPasswordForm">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    
    <div class="mb-4 text-center">
        <i class="fas fa-lock fa-3x text-success mb-3"></i>
        <p class="text-muted">Create a new password for your account.</p>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" class="form-control" id="email" name="email" value="{{ $email ?? old('email') }}" readonly>
        </div>
        <div class="invalid-feedback" id="email_error"></div>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
            <button type="button" class="input-group-text toggle-password">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <div class="invalid-feedback" id="password_error"></div>
        <div class="form-text">Password must be at least 8 characters long.</div>
    </div>

    {{-- Confirm Password --}}
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
            <button type="button" class="input-group-text toggle-password">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <div class="invalid-feedback" id="password_confirmation_error"></div>
    </div>

    {{-- Password Strength Meter --}}
    <div class="mb-3">
        <div class="password-strength">
            <div class="strength-bar">
                <div class="strength-fill" id="strengthFill"></div>
            </div>
            <small class="strength-text" id="strengthText">Password strength</small>
        </div>
    </div>

    {{-- Submit --}}
    <div class="d-grid">
        <button type="submit" class="btn btn-success">
            <span id="btnText">Reset Password</span>
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

@section('css')
<style>
.password-strength {
    margin-top: 10px;
}

.strength-bar {
    height: 5px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 5px;
}

.strength-fill {
    height: 100%;
    width: 0%;
    border-radius: 3px;
    transition: all 0.3s ease;
}

.strength-weak {
    background: #dc3545;
    width: 25%;
}

.strength-fair {
    background: #fd7e14;
    width: 50%;
}

.strength-good {
    background: #ffc107;
    width: 75%;
}

.strength-strong {
    background: #28a745;
    width: 100%;
}

.strength-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.toggle-password {
    cursor: pointer;
    background: #fff;
    border: 1px solid #ced4da;
}

.toggle-password:hover {
    background: #f8f9fa;
}
</style>
@endsection

@section('js')
<script>
$(function () {
    // Password visibility toggle
    $('.toggle-password').on('click', function() {
        const input = $(this).closest('.input-group').find('input');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password strength meter
    $('#password').on('input', function() {
        const password = $(this).val();
        const strengthFill = $('#strengthFill');
        const strengthText = $('#strengthText');
        
        let strength = 0;
        let text = 'Password strength';
        let className = '';

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        switch(strength) {
            case 0:
                text = 'Very Weak';
                className = 'strength-weak';
                break;
            case 1:
                text = 'Weak';
                className = 'strength-weak';
                break;
            case 2:
                text = 'Fair';
                className = 'strength-fair';
                break;
            case 3:
                text = 'Good';
                className = 'strength-good';
                break;
            case 4:
                text = 'Strong';
                className = 'strength-strong';
                break;
        }

        strengthFill.removeClass().addClass('strength-fill ' + className);
        strengthText.text(text);
    });

    // Form submission
    $('#resetPasswordForm').on('submit', function (e) {
        e.preventDefault();

        $('#alert-container').html('');
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $('#btnText').text('Resetting...');
        $('#btnSpinner').removeClass('d-none');
        $('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: "{{ route('password.update') }}",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (res) {
                $('button[type="submit"]').prop('disabled', false);
                $('#btnText').text('Reset Password');
                $('#btnSpinner').addClass('d-none');

                if (res.status === 'success') {
                    showToast('success', 'Password Reset!', res.message, 5000);
                    setTimeout(() => window.location.href = res.redirect, 2000);
                } else {
                    showToast('error', 'Error!', res.message, 5000);
                }
            },
            error: function (xhr) {
                $('button[type="submit"]').prop('disabled', false);
                $('#btnText').text('Reset Password');
                $('#btnSpinner').addClass('d-none');

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').text(errors[key][0]);
                    }
                    showToast('error', 'Validation Error!', 'Please check the form for errors.', 5000);
                } else if (xhr.status === 400) {
                    showToast('error', 'Invalid Token!', xhr.responseJSON.message || 'The reset link is invalid or expired.', 5000);
                } else {
                    showToast('error', 'Error!', 'Something went wrong. Please try again.', 5000);
                }
            }
        });
    });
});
</script>
@endsection