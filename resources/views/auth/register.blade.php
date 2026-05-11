@extends('layouts.authLayout')

@section('title', 'Surveyor Registration | Spatial Revenue Intelligent System')

@section('content')
<div class="auth-card">

    <!-- LEFT SIDE -->
    <div class="login-hero">

        <div class="brand">
            <div class="brand-icon">
                <i class="fas fa-user-plus"></i>
            </div>

            <div>
                <div class="brand-text">
                    Spatial Revenue Intelligent System
                </div>

                <div class="brand-sub">
                    GIS Surveyor Registration Portal
                </div>
            </div>
        </div>

        <div class="hero-content">

            <h1>
                New Surveyor<br>
                <span class="hero-highlight">Registration</span>
            </h1>

            <p class="hero-description">
                Register as an authorized GIS surveyor to manage property inspections,
                geo-tagging, building mapping, spatial data collection, and municipal
                survey operations across the SRIS platform.
            </p>

            <div class="trust-badge">

                <div class="trust-item">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>GIS Mapping</span>
                </div>

                <div class="trust-item">
                    <i class="fas fa-building"></i>
                    <span>Property Survey</span>
                </div>

                <div class="trust-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Access</span>
                </div>

            </div>
        </div>

        <div class="quote-area">
            <div class="quote">
                “Digital surveying improves urban governance and tax transparency.”
            </div>
        </div>

    </div>

    <!-- RIGHT SIDE -->
    <div class="login-form-section">

        <div class="form-header">
            <h2>Create Surveyor Account</h2>

            <p>
                Register to access GIS layers, property inspections &
                field survey management tools
            </p>
        </div>

        <form
            id="registerForm"
            method="POST"
            action="{{ route('register.post') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <!-- Profile Upload -->
            <div class="mb-3">

                <label class="input-label">
                    <i class="fas fa-id-card me-2"></i>
                    Surveyor Profile Photo
                </label>

                <div class="file-upload-container">

                    <div class="file-upload-area" id="fileUploadArea">

                        <div class="file-upload-icon">
                            <i class="fas fa-cloud-upload-alt fa-2x"></i>
                        </div>

                        <div class="file-upload-text">
                            <div class="primary">
                                Upload profile photo
                            </div>

                            <div class="secondary">
                                PNG, JPG, JPEG up to 2MB
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn btn-outline-success mt-2 file-upload-btn"
                        >
                            <i class="fas fa-folder-open me-2"></i>
                            Choose File
                        </button>

                        <input
                            type="file"
                            class="file-input"
                            id="profile_picture"
                            name="profile_picture"
                            accept=".png,.jpg,.jpeg"
                        >

                    </div>

                    <div class="file-preview" id="filePreview"></div>

                </div>

                <div class="invalid-feedback" id="profile_picture_error"></div>

            </div>

            <!-- Full Name -->
            <div class="mb-3">

                <label class="input-label" for="name">
                    <i class="fas fa-user me-2"></i>
                    Full Name
                </label>

                <div class="input-field">

                    <i class="fas fa-user"></i>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter full name"
                        value="{{ old('name') }}"
                    >

                </div>

                <div class="invalid-feedback" id="name_error"></div>

            </div>

            <!-- Email -->
            <div class="mb-3">

                <label class="input-label" for="email">
                    <i class="fas fa-envelope me-2"></i>
                    Official Email Address
                </label>

                <div class="input-field">

                    <i class="fas fa-envelope"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="surveyor@sris.gov.in"
                        value="{{ old('email') }}"
                    >

                </div>

                <div class="invalid-feedback" id="email_error"></div>

            </div>

            <!-- Password -->
            <div class="mb-3">

                <label class="input-label" for="password">
                    <i class="fas fa-lock me-2"></i>
                    Password
                </label>

                <div class="input-field">

                    <i class="fas fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Create password"
                    >

                </div>

                <div class="invalid-feedback" id="password_error"></div>

            </div>

            <!-- Confirm Password -->
            <div class="mb-3">

                <label class="input-label" for="password_confirmation">
                    <i class="fas fa-check-circle me-2"></i>
                    Confirm Password
                </label>

                <div class="input-field">

                    <i class="fas fa-check-circle"></i>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Re-enter password"
                    >

                </div>

                <div class="invalid-feedback" id="password_confirmation_error"></div>

            </div>

            <!-- Gender -->
            <div class="mb-3">

                <label class="input-label" for="gender">
                    <i class="fas fa-venus-mars me-2"></i>
                    Gender
                </label>

                <div class="input-field">

                    <i class="fas fa-venus-mars"></i>

                    <select
                        id="gender"
                        name="gender"
                        class="form-select-custom"
                    >
                        <option value="" selected disabled>
                            Select gender
                        </option>

                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>

                </div>

                <div class="invalid-feedback" id="gender_error"></div>

            </div>

            <!-- Phone -->
            <div class="mb-3">

                <label class="input-label" for="phone">
                    <i class="fas fa-phone-alt me-2"></i>
                    Mobile Number
                </label>

                <div class="input-field">

                    <i class="fas fa-phone-alt"></i>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="10-digit mobile number"
                        value="{{ old('phone') }}"
                    >

                </div>

                <div class="invalid-feedback" id="phone_error"></div>

            </div>

            <!-- Date Of Birth -->
            <div class="mb-3">

                <label class="input-label" for="date_of_birth">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Date of Birth
                </label>

                <div class="input-field">

                    <i class="fas fa-calendar-alt"></i>

                    <input
                        type="date"
                        id="date_of_birth"
                        name="date_of_birth"
                        value="{{ old('date_of_birth') }}"
                    >

                </div>

                <div class="invalid-feedback" id="date_of_birth_error"></div>

            </div>

            <!-- Zone -->
            <div class="mb-4">

                <label class="input-label" for="city">
                    <i class="fas fa-map-pin me-2"></i>
                    Survey Zone / City
                </label>

                <div class="input-field">

                    <i class="fas fa-map-pin"></i>

                    <input
                        type="text"
                        id="city"
                        name="city"
                        placeholder="Eg: Chennai Zone 1"
                        value="{{ old('city') }}"
                    >

                </div>

                <div class="invalid-feedback" id="city_error"></div>

            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="login-btn"
                id="registerBtn"
            >

                <i class="fas fa-user-plus"></i>

                <span id="btnText">
                    Register Surveyor
                </span>

                <span
                    id="btnSpinner"
                    class="spinner-border spinner-border-sm d-none ms-2"
                ></span>

            </button>

            <div class="register-prompt mt-3">

                Already have an account?

                <a href="{{ route('login') }}">
                    Login Dashboard
                </a>

            </div>

            <div class="text-center mt-3">

                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Protected by SRIS Security System
                </small>

            </div>

        </form>

    </div>

</div>

<style>

.file-upload-container{
    position:relative;
}

.file-input{
    position:absolute;
    width:0;
    height:0;
    opacity:0;
    pointer-events:none;
}

.file-upload-area{
    cursor:pointer;
    border:2px dashed #dbe4f0;
    padding:20px;
    border-radius:20px;
    text-align:center;
    transition:0.3s ease;
    background:#f8fbff;
}

.file-upload-area:hover{
    border-color:#2563eb;
    background:#eff6ff;
}

.file-upload-area.dragover{
    border-color:#2563eb;
    background:#dbeafe;
    transform:scale(1.01);
}

.file-upload-icon{
    margin-bottom:12px;
    color:#2563eb;
}

.file-upload-text .primary{
    font-weight:600;
    margin-bottom:5px;
    color:#1e3a5f;
    font-size:0.9rem;
}

.file-upload-text .secondary{
    color:#7b8794;
    font-size:11px;
}

.file-preview{
    margin-top:15px;
    text-align:center;
}

.file-preview img{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:50%;
    border:3px solid #2563eb;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

.file-info{
    margin-top:10px;
    font-size:12px;
}

.file-remove{
    border:none;
    background:transparent;
    color:#dc3545;
    margin-top:6px;
    cursor:pointer;
    font-size:12px;
}

.btn-outline-success{
    border:1px solid #2563eb;
    color:#2563eb;
    background:white;
    border-radius:40px;
    padding:6px 16px;
    font-size:0.75rem;
    font-weight:600;
}

.btn-outline-success:hover{
    background:#2563eb;
    color:white;
}

.form-select-custom{
    width:100%;
    padding:11px 14px 11px 44px;
    font-size:0.9rem;
    border:1.5px solid #e2e8f0;
    border-radius:16px;
    background:#ffffff;
    outline:none;
}

.form-select-custom:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,0.15);
}

.form-select-custom.is-invalid{
    border-color:#dc3545;
}

</style>
@endsection

@section('scripts')

<script>

$(document).ready(function(){

    let isSubmitting = false;

    const fileInput = $('#profile_picture');
    const fileUploadArea = $('#fileUploadArea');
    const filePreview = $('#filePreview');
    const fileUploadBtn = $('.file-upload-btn');

    // File Button
    fileUploadBtn.on('click', function(e){
        e.stopPropagation();
        fileInput[0].click();
    });

    // Upload Area Click
    fileUploadArea.on('click', function(e){

        if(!$(e.target).closest('.file-remove, .file-upload-btn').length){
            fileInput[0].click();
        }

    });

    // Drag Over
    fileUploadArea.on('dragover', function(e){

        e.preventDefault();
        fileUploadArea.addClass('dragover');

    });

    // Drag Leave
    fileUploadArea.on('dragleave', function(e){

        e.preventDefault();
        fileUploadArea.removeClass('dragover');

    });

    // Drop
    fileUploadArea.on('drop', function(e){

        e.preventDefault();
        fileUploadArea.removeClass('dragover');

        const files = e.originalEvent.dataTransfer.files;

        if(files.length > 0){
            handleFileSelection(files[0]);
        }

    });

    // Change
    fileInput.on('change', function(){

        if(this.files && this.files[0]){
            handleFileSelection(this.files[0]);
        }

    });

    // File Preview
    function handleFileSelection(file){

        const validTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png'
        ];

        if(!validTypes.includes(file.type)){

            showToast(
                'error',
                'Invalid File',
                'Only PNG, JPG, JPEG allowed',
                3000
            );

            return;
        }

        if(file.size > 2 * 1024 * 1024){

            showToast(
                'error',
                'Large File',
                'Image must be below 2MB',
                3000
            );

            return;
        }

        const reader = new FileReader();

        reader.onload = function(e){

            filePreview.html(`
                <div class="d-flex align-items-center justify-content-center gap-3 p-2">
                    <img src="${e.target.result}" alt="Preview">

                    <div class="text-start">

                        <div class="file-info">
                            <div class="fw-bold text-dark">${file.name}</div>
                            <div class="text-muted">
                                ${(file.size / 1024).toFixed(2)} KB
                            </div>
                        </div>

                        <button
                            type="button"
                            class="file-remove"
                            id="removeFile"
                        >
                            <i class="fas fa-times me-1"></i>
                            Remove
                        </button>

                    </div>
                </div>
            `);

            $('#removeFile').on('click', function(){

                fileInput.val('');
                filePreview.html('');

            });

        };

        reader.readAsDataURL(file);

    }

    // Submit
    $('#registerForm').on('submit', function(e){

        e.preventDefault();

        if(isSubmitting){
            return false;
        }

        $('.invalid-feedback').text('');
        $('.input-field input, .form-select-custom')
            .removeClass('is-invalid');

        let hasError = false;

        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        const phone = $('#phone').val().trim();
        const city = $('#city').val().trim();

        // Name
        if(!name){

            $('#name').addClass('is-invalid');
            $('#name_error').text('Full name required');

            hasError = true;
        }

        // Email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if(!email){

            $('#email').addClass('is-invalid');
            $('#email_error').text('Email required');

            hasError = true;

        }else if(!emailRegex.test(email)){

            $('#email').addClass('is-invalid');
            $('#email_error').text('Invalid email');

            hasError = true;
        }

        // Password
        if(password.length < 6){

            $('#password').addClass('is-invalid');
            $('#password_error')
                .text('Minimum 6 characters required');

            hasError = true;
        }

        // Confirm Password
        if(password !== confirmPassword){

            $('#password_confirmation')
                .addClass('is-invalid');

            $('#password_confirmation_error')
                .text('Passwords do not match');

            hasError = true;
        }

        // Phone
        const phoneRegex = /^[0-9]{10}$/;

        if(!phoneRegex.test(phone)){

            $('#phone').addClass('is-invalid');
            $('#phone_error')
                .text('Enter valid 10 digit mobile');

            hasError = true;
        }

        // City
        if(!city){

            $('#city').addClass('is-invalid');
            $('#city_error')
                .text('Survey zone required');

            hasError = true;
        }

        if(hasError){

            showToast(
                'error',
                'Validation Error',
                'Please check all fields',
                3000
            );

            return false;
        }

        isSubmitting = true;

        $('#btnText').text('Registering...');
        $('#btnSpinner').removeClass('d-none');

        $('#registerBtn').prop('disabled', true);

        const formData = new FormData(this);

        $.ajax({

            url: "{{ route('register.post') }}",
            method: "POST",
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,

            success: function(response){

                isSubmitting = false;

                $('#registerBtn').prop('disabled', false);

                $('#btnText').text('Register Surveyor');
                $('#btnSpinner').addClass('d-none');

                if(response.status === 'success'){

                    showToast(
                        'success',
                        'Registration Successful',
                        response.message,
                        2000
                    );

                    setTimeout(function(){

                        window.location.href =
                            response.redirect;

                    }, 2000);

                }else{

                    showToast(
                        'error',
                        'Registration Failed',
                        response.message,
                        4000
                    );
                }

            },

            error: function(xhr){

                isSubmitting = false;

                $('#registerBtn').prop('disabled', false);

                $('#btnText').text('Register Surveyor');
                $('#btnSpinner').addClass('d-none');

                if(xhr.status === 422 &&
                    xhr.responseJSON.errors){

                    const errors =
                        xhr.responseJSON.errors;

                    for(let key in errors){

                        $(`#${key}`)
                            .addClass('is-invalid');

                        $(`#${key}_error`)
                            .text(errors[key][0]);
                    }

                    showToast(
                        'error',
                        'Validation Error',
                        'Please check form errors',
                        4000
                    );

                }else{

                    showToast(
                        'error',
                        'Error',
                        'Something went wrong',
                        4000
                    );
                }

            }

        });

    });

    // Clear Validation
    $('#registerForm input, #registerForm select')
        .on('input change', function(){

        $(this).removeClass('is-invalid');

        $(this)
            .siblings('.invalid-feedback')
            .text('');

    });

    // Welcome Message
    setTimeout(() => {

        showToast(
            'info',
            '🗺️ SRIS Survey Portal',
            'Register your surveyor account',
            4000
        );

    }, 500);

});

</script>

@endsection
