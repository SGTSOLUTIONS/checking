@extends('layouts.surveyor-layout')

@section('content')
    <div id="flash-message-container"></div>

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Edit Point Data - GIS ID: {{ $gisid }}</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('surveyor.mapview') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Map
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @foreach($pointData as $index => $point)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Point Record #{{ $index + 1 }} - Assessment: {{ $point->assessment ?? 'N/A' }}</h5>
                </div>
                <div class="card-body">
                    <form class="point-form" data-point-id="{{ $point->id }}" data-point-index="{{ $index }}">
                        @csrf
                        <div class="row">
                            @php
                                $excludeFields = ['id', 'created_at', 'updated_at', 'deleted_at', 'building_data_id', 'shops', 'worker_name'];
                                $fields = array_keys((array)$point);
                                $displayFields = array_filter($fields, function($field) use ($excludeFields) {
                                    return !in_array($field, $excludeFields);
                                });
                            @endphp

                            @foreach($displayFields as $field)
                                <div class="col-md-4 mb-3">
                                    <label for="field_{{ $point->id }}_{{ $field }}" class="form-label">
                                        {{ ucwords(str_replace('_', ' ', $field)) }}
                                        @if(in_array($field, ['owner_name', 'point_gisid', 'floor', 'no_of_shop']))
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if($field === 'bill_usage')
                                        <select name="{{ $field }}" id="field_{{ $point->id }}_{{ $field }}"
                                                class="form-control form-control-sm point-field"
                                                data-point-id="{{ $point->id }}"
                                                data-field="{{ $field }}"
                                                {{ !canEdit($surveyor->name, $point->worker_name) ? 'disabled' : '' }}>
                                            <option value="">Select Usage</option>
                                            <option value="COMMERCIAL" {{ ($point->$field ?? '') == 'COMMERCIAL' ? 'selected' : '' }}>COMMERCIAL</option>
                                            <option value="EDUCATIONAL INSTITUTIONS" {{ ($point->$field ?? '') == 'EDUCATIONAL INSTITUTIONS' ? 'selected' : '' }}>EDUCATIONAL INSTITUTIONS</option>
                                            <option value="GOVERNMENT BUILDING" {{ ($point->$field ?? '') == 'GOVERNMENT BUILDING' ? 'selected' : '' }}>GOVERNMENT BUILDING</option>
                                            <option value="INDUSTRIAL" {{ ($point->$field ?? '') == 'INDUSTRIAL' ? 'selected' : '' }}>INDUSTRIAL</option>
                                            <option value="OFFICE / LODGE / THEATER / RESTAURANTS" {{ ($point->$field ?? '') == 'OFFICE / LODGE / THEATER / RESTAURANTS' ? 'selected' : '' }}>OFFICE / LODGE / THEATER / RESTAURANTS</option>
                                            <option value="RESIDENTIAL" {{ ($point->$field ?? '') == 'RESIDENTIAL' ? 'selected' : '' }}>RESIDENTIAL</option>
                                            <option value="STAR HOTEL" {{ ($point->$field ?? '') == 'STAR HOTEL' ? 'selected' : '' }}>STAR HOTEL</option>
                                        </select>
                                    @elseif($field === 'assessment_type')
                                        <select name="{{ $field }}" id="field_{{ $point->id }}_{{ $field }}"
                                                class="form-control form-control-sm point-field"
                                                data-point-id="{{ $point->id }}"
                                                data-field="{{ $field }}"
                                                {{ !canEdit($surveyor->name, $point->worker_name) ? 'disabled' : '' }}>
                                            <option value="OLD" {{ ($point->$field ?? '') == 'OLD' ? 'selected' : '' }}>OLD</option>
                                            <option value="NEW" {{ ($point->$field ?? '') == 'NEW' ? 'selected' : '' }}>NEW</option>
                                            <option value="OTHER" {{ ($point->$field ?? '') == 'OTHER' ? 'selected' : '' }}>OTHER</option>
                                            <option value="NO_TAX" {{ ($point->$field ?? '') == 'NO_TAX' ? 'selected' : '' }}>NO TAX</option>
                                            <option value="VACCAND" {{ ($point->$field ?? '') == 'VACCAND' ? 'selected' : '' }}>VACCAND</option>
                                        </select>
                                    @elseif(in_array($field, ['no_of_shop', 'floor', 'no_of_persons', 'trade_income']))
                                        <input type="number"
                                               name="{{ $field }}"
                                               id="field_{{ $point->id }}_{{ $field }}"
                                               class="form-control form-control-sm point-field"
                                               value="{{ $point->$field ?? '' }}"
                                               data-point-id="{{ $point->id }}"
                                               data-field="{{ $field }}"
                                               {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                    @else
                                        <input type="text"
                                               name="{{ $field }}"
                                               id="field_{{ $point->id }}_{{ $field }}"
                                               class="form-control form-control-sm point-field"
                                               value="{{ $point->$field ?? '' }}"
                                               data-point-id="{{ $point->id }}"
                                               data-field="{{ $field }}"
                                               {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                    @endif

                                    <div class="invalid-feedback"></div>
                                </div>
                            @endforeach
                        </div>

                        @if(canEdit($surveyor->name, $point->worker_name))
                            <div class="row mt-2">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary update-point-btn" data-point-id="{{ $point->id }}">
                                        <i class="fas fa-save"></i> Update Point
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-secondary mt-2">
                                <i class="fas fa-lock"></i> Read Only - Created by: {{ $point->worker_name ?? 'Unknown' }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Shops Section for this Point -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">Shops for Point {{ $point->assessment ?? $point->point_gisid }}</h6>
                </div>
                <div class="card-body">
                    @if(canEdit($surveyor->name, $point->worker_name))
                        <button type="button" class="btn btn-success mb-3 add-shop-btn" data-point-id="{{ $point->id }}">
                            <i class="fas fa-plus"></i> Add New Shop
                        </button>
                    @endif

                    <div id="shops-container-{{ $point->id }}">
                        @if(isset($point->shops) && count($point->shops) > 0)
                            @foreach($point->shops as $shop)
                                <div class="card mb-3 shop-card" data-shop-id="{{ $shop->id }}">
                                    <div class="card-header bg-light">
                                        <strong>Shop: {{ $shop->shop_name ?? 'Unnamed' }}</strong>
                                        @if(canEdit($surveyor->name, $point->worker_name))
                                            <button type="button" class="btn btn-danger btn-sm float-end delete-shop-btn" data-shop-id="{{ $shop->id }}" data-point-id="{{ $point->id }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Shop Floor</label>
                                                <input type="text" class="form-control form-control-sm shop-field"
                                                       value="{{ $shop->shop_floor ?? '' }}"
                                                       data-shop-id="{{ $shop->id }}"
                                                       data-field="shop_floor"
                                                       {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Shop Name</label>
                                                <input type="text" class="form-control form-control-sm shop-field"
                                                       value="{{ $shop->shop_name ?? '' }}"
                                                       data-shop-id="{{ $shop->id }}"
                                                       data-field="shop_name"
                                                       {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Owner Name</label>
                                                <input type="text" class="form-control form-control-sm shop-field"
                                                       value="{{ $shop->shop_owner_name ?? '' }}"
                                                       data-shop-id="{{ $shop->id }}"
                                                       data-field="shop_owner_name"
                                                       {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Category</label>
                                                <input type="text" class="form-control form-control-sm shop-field"
                                                       value="{{ $shop->shop_category ?? '' }}"
                                                       data-shop-id="{{ $shop->id }}"
                                                       data-field="shop_category"
                                                       {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Mobile</label>
                                                <input type="text" class="form-control form-control-sm shop-field"
                                                       value="{{ $shop->shop_mobile ?? '' }}"
                                                       data-shop-id="{{ $shop->id }}"
                                                       data-field="shop_mobile"
                                                       {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">License</label>
                                                <input type="text" class="form-control form-control-sm shop-field"
                                                       value="{{ $shop->license ?? '' }}"
                                                       data-shop-id="{{ $shop->id }}"
                                                       data-field="license"
                                                       {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">No. of Employees</label>
                                                <input type="number" class="form-control form-control-sm shop-field"
                                                       value="{{ $shop->number_of_employee ?? 0 }}"
                                                       data-shop-id="{{ $shop->id }}"
                                                       data-field="number_of_employee"
                                                       {{ !canEdit($surveyor->name, $point->worker_name) ? 'readonly' : '' }}>
                                            </div>
                                        </div>
                                        @if(canEdit($surveyor->name, $point->worker_name))
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-primary btn-sm update-shop-btn" data-shop-id="{{ $shop->id }}">
                                                        <i class="fas fa-save"></i> Update Shop
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">No shops added yet.</div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        $(document).ready(function() {
            var surveyor = @json($surveyor);

            // Helper function to check edit permission
            function canEdit(surveyorName, workerName) {
                var adminUsers = ['sgt', 'malaqc', 'mala57sgtqc', 'MALA QC SGT', 'malasgt', 'mala51qc', 'sir', 'Anand', 'anandnew', 'malanew', 'anandnew91', 'Anandnew55', 'SGT', 'ward90', 'anandnew89', 'malanew51', 'officeqc52', 'Anandnew51'];

                if (adminUsers.includes(surveyorName)) {
                    return true;
                }

                if (workerName && surveyorName && workerName.toLowerCase() === surveyorName.toLowerCase()) {
                    return true;
                }

                if (workerName && surveyorName && workerName.toLowerCase().includes(surveyorName.toLowerCase())) {
                    return true;
                }

                return false;
            }

            function showMessage(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
                var html = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>';

                $("#flash-message-container").html(html);

                setTimeout(function() {
                    $(".alert").fadeOut('slow');
                }, 5000);
            }

            // Update Point
            $(document).on("click", ".update-point-btn", function() {
                var pointId = $(this).data("point-id");
                var form = $(this).closest("form");
                var pointData = {};

                form.find(".point-field").each(function() {
                    var field = $(this).data("field");
                    var value = $(this).val();
                    if (field) {
                        pointData[field] = value;
                    }
                });

                // Remove validation error styles
                form.find(".is-invalid").removeClass("is-invalid");
                form.find(".invalid-feedback").text("");

                $.ajax({
                    url: "{{ route('surveyor.updatePointRecord') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: pointId,
                        type: 'point',
                        data: pointData,
                        corp: {{ $corp }},
                        zone: '{{ $zone }}',
                        ward_no: {{ $wardNo }}
                    },
                    success: function(response) {
                        if(response.success) {
                            showMessage('success', 'Point data updated successfully');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showMessage('error', response.error || 'Error updating point data');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON) {
                            // Handle validation errors
                            var errors = xhr.responseJSON;
                            if (errors.error) {
                                showMessage('error', errors.error);
                            }
                        } else {
                            showMessage('error', 'Error updating point data');
                        }
                    }
                });
            });

            // Update Shop
            $(document).on("click", ".update-shop-btn", function() {
                var shopId = $(this).data("shop-id");
                var shopCard = $(this).closest(".shop-card");
                var shopData = {};

                shopCard.find(".shop-field").each(function() {
                    var field = $(this).data("field");
                    var value = $(this).val();
                    if (field) {
                        shopData[field] = value;
                    }
                });

                $.ajax({
                    url: "{{ route('surveyor.updatePointRecord') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: shopId,
                        type: 'shop',
                        data: shopData,
                        corp: {{ $corp }},
                        zone: '{{ $zone }}',
                        ward_no: {{ $wardNo }}
                    },
                    success: function(response) {
                        if(response.success) {
                            showMessage('success', 'Shop data updated successfully');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            showMessage('error', response.error || 'Error updating shop data');
                        }
                    },
                    error: function(xhr) {
                        showMessage('error', 'Error updating shop data');
                    }
                });
            });

            // Add Shop
            $(document).on("click", ".add-shop-btn", function() {
                var pointId = $(this).data("point-id");

                var newShopData = {
                    point_data_id: pointId,
                    shop_floor: '',
                    shop_name: 'New Shop',
                    shop_owner_name: '',
                    shop_category: '',
                    shop_mobile: '',
                    license: '',
                    number_of_employee: 0
                };

                $.ajax({
                    url: "{{ route('surveyor.addShopRecord') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        point_id: pointId,
                        shop_data: newShopData,
                        corp: {{ $corp }},
                        zone: '{{ $zone }}',
                        ward_no: {{ $wardNo }}
                    },
                    success: function(response) {
                        if(response.success) {
                            showMessage('success', 'Shop added successfully');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            showMessage('error', response.error || 'Error adding shop');
                        }
                    },
                    error: function(xhr) {
                        showMessage('error', 'Error adding shop');
                    }
                });
            });

            // Delete Shop
            $(document).on("click", ".delete-shop-btn", function() {
                if (!confirm('Are you sure you want to delete this shop?')) return;

                var shopId = $(this).data("shop-id");
                var pointId = $(this).data("point-id");

                $.ajax({
                    url: "{{ route('surveyor.deleteShopRecord') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        shop_id: shopId,
                        point_id: pointId,
                        corp: {{ $corp }},
                        zone: '{{ $zone }}',
                        ward_no: {{ $wardNo }}
                    },
                    success: function(response) {
                        if(response.success) {
                            showMessage('success', 'Shop deleted successfully');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            showMessage('error', response.error || 'Error deleting shop');
                        }
                    },
                    error: function(xhr) {
                        showMessage('error', 'Error deleting shop');
                    }
                });
            });
        });
    </script>
@endsection
