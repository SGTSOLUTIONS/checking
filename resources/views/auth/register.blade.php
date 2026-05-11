@extends('layouts.authLayout')

@section('title', 'Surveyor Registration | Spatial Revenue Intelligent System')

@section('content')
<div class="auth-card">

    <!-- LEFT: Branding & Surveyor Info -->
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
                Register as an authorized GIS surveyor to manage spatial property data,
                building inspections, geo-tagging, and municipal survey operations
                within the SRIS platform.
            </p>

            <div class="trust-badge">

                <div class="trust-item">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>GIS Enabled</span>
                </div>

                <div class="trust-item">
                    <i class="fas fa-building"></i>
                    <span>Property Mapping</span>
                </div>

                <div class="trust-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Access</span>
                </div>

            </div>
        </div>

        <div class="quote-area">
            <div class="quote">
                “Digital surveying improves transparency, taxation accuracy, and smart city planning.”
            </div>
        </div>

    </div>

    <!-- RIGHT: Registration Form -->
    <div class="login-form-section">

        <div class="form-header">
            <h2>Create Surveyor Account</h2>
            <p>
                Fill your details to access GIS survey tools and field operations
            </p>
        </div>

        <form
            id="registerForm"
            method="POST"
            action="{{ route('register.post') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <!-- Profile Picture -->
            <div class="mb-3">

                <label class="input-label">
                    <i class="fas fa-id-card me-2"></i>
                    Surveyor Photo ID
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
                    Secure Password
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

            <!-- DOB -->
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

                <i class="fas fa-map-marked-alt"></i>

                <span id="btnText">
                    Register Surveyor Account
                </span>

                <span
                    id="btnSpinner"
                    class="spinner-border spinner-border-sm d-none ms-2"
                ></span>

            </button>

            <div class="register-prompt mt-3">

                Already registered?

                <a href="{{ route('login') }}">
                    Login to Dashboard
                </a>

            </div>

            <div class="text-center mt-3">

                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Secured GIS Survey Management System
                </small>

            </div>

        </form>

    </div>
</div>
@endsection
