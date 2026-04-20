@extends('layouts.surveyor-layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">Edit Point Data</h3>
                        <p class="text-muted mb-0">GIS ID: {{ $gisid }} | Found {{ count($points) }} record(s)</p>
                    </div>
                    <div>
                        <a href="{{ route('surveyor.mapview') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Back to Map
                        </a>
                    </div>
                </div>
            </div>

            <form id="editPointForm" method="POST">
                @csrf
                <input type="hidden" name="gisid" value="{{ $gisid }}">

                @foreach($points as $index => $point)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            Record #{{ $loop->iteration }}
                            <small>(ID: {{ $point->id }} | Type: {{ $point->assessment_type ?? 'N/A' }})</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="points[{{ $index }}][id]" value="{{ $point->id }}">

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                                <div class="mb-3">
                                    <label class="form-label">Assessment Number</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][assessment]" value="{{ $point->assessment }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Old Assessment</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][old_assessment]" value="{{ $point->old_assessment }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Owner Name</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][owner_name]" value="{{ $point->owner_name }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Present Owner Name</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][present_owner_name]" value="{{ $point->present_owner_name }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Floor</label>
                                    <input type="number" class="form-control" name="points[{{ $index }}][floor]" value="{{ $point->floor }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Old Door No</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][old_door_no]" value="{{ $point->old_door_no }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Door No</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][new_door_no]" value="{{ $point->new_door_no }}">
                                </div>
                            </div>

                            <!-- Tax & Financial Information -->
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2 mb-3">Tax & Financial Information</h6>
                                <div class="mb-3">
                                    <label class="form-label">Bill Usage</label>
                                    <select class="form-control" name="points[{{ $index }}][bill_usage]">
                                        <option value="">Select</option>
                                        <option value="Residential" {{ $point->bill_usage == 'Residential' ? 'selected' : '' }}>Residential</option>
                                        <option value="Commercial" {{ $point->bill_usage == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                        <option value="Mixed" {{ $point->bill_usage == 'Mixed' ? 'selected' : '' }}>Mixed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">EB Connection</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][eb]" value="{{ $point->eb }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Water Tax</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][water_tax]" value="{{ $point->water_tax }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Old Water Tax</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][old_water_tax]" value="{{ $point->old_water_tax }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Professional Tax</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][professional_tax]" value="{{ $point->professional_tax }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">GST</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][gst]" value="{{ $point->gst }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Trade Income</label>
                                    <input type="number" step="0.01" class="form-control" name="points[{{ $index }}][trade_income]" value="{{ $point->trade_income }}">
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
                                <div class="mb-3">
                                    <label class="form-label">Aadhar Number</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][aadhar_no]" value="{{ $point->aadhar_no }}" maxlength="12">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ration Number</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][ration_no]" value="{{ $point->ration_no }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][phone_number]" value="{{ $point->phone_number }}" maxlength="10">
                                </div>
                            </div>

                            <!-- QC Information -->
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2 mb-3">Quality Check Information</h6>
                                <div class="mb-3">
                                    <label class="form-label">QC Area</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][qc_area]" value="{{ $point->qc_area }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">QC Usage</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][qc_usage]" value="{{ $point->qc_usage }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">QC Name</label>
                                    <input type="text" class="form-control" name="points[{{ $index }}][qc_name]" value="{{ $point->qc_name }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">QC Remarks</label>
                                    <textarea class="form-control" rows="2" name="points[{{ $index }}][qc_remarks]">{{ $point->qc_remarks }}</textarea>
                                </div>
                            </div>

                            <!-- Additional Fields -->
                            <div class="col-md-12">
                                <h6 class="border-bottom pb-2 mb-3">Additional Information</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Plot Area</label>
                                            <input type="text" class="form-control" name="points[{{ $index }}][plot_area]" value="{{ $point->plot_area }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Half Year Tax</label>
                                            <input type="text" class="form-control" name="points[{{ $index }}][halfyeartax]" value="{{ $point->halfyeartax }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Balance</label>
                                            <input type="text" class="form-control" name="points[{{ $index }}][balance]" value="{{ $point->balance }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">OTS Area</label>
                                            <input type="text" class="form-control" name="points[{{ $index }}][otsarea]" value="{{ $point->otsarea }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Establishment Remarks</label>
                                    <textarea class="form-control" rows="2" name="points[{{ $index }}][establishment_remarks]">{{ $point->establishment_remarks }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea class="form-control" rows="2" name="points[{{ $index }}][remarks]">{{ $point->remarks }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Shops Section -->
                        <div class="mt-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                Shops ({{ $point->shops->count() }})
                                <button type="button" class="btn btn-sm btn-success add-shop" data-point-index="{{ $index }}">
                                    <i class="fas fa-plus"></i> Add Shop
                                </button>
                            </h6>
                            <div class="shops-container" data-point-index="{{ $index }}">
                                @foreach($point->shops as $shopIndex => $shop)
                                <div class="card mb-3 shop-item">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>Shop #{{ $loop->iteration }}</strong>
                                            <button type="button" class="btn btn-sm btn-danger remove-shop">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" name="points[{{ $index }}][shops][{{ $shopIndex }}][id]" value="{{ $shop->id }}">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Floor</label>
                                                <input type="text" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][shop_floor]" value="{{ $shop->shop_floor }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Shop Name</label>
                                                <input type="text" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][shop_name]" value="{{ $shop->shop_name }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Owner Name</label>
                                                <input type="text" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][shop_owner_name]" value="{{ $shop->shop_owner_name }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Category</label>
                                                <input type="text" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][shop_category]" value="{{ $shop->shop_category }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Mobile</label>
                                                <input type="text" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][shop_mobile]" value="{{ $shop->shop_mobile }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">License</label>
                                                <input type="text" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][license]" value="{{ $shop->license }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">No of Employees</label>
                                                <input type="number" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][number_of_employee]" value="{{ $shop->number_of_employee }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Type</label>
                                                <input type="text" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][type]" value="{{ $shop->type }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Coordinates</label>
                                                <input type="text" class="form-control" name="points[{{ $index }}][shops][{{ $shopIndex }}][coordinates]" value="{{ $shop->coordinates }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Save All Changes
                        </button>
                        <a href="{{ route('surveyor.mapview') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Add shop dynamically
    $('.add-shop').click(function() {
        let pointIndex = $(this).data('point-index');
        let container = $(`.shops-container[data-point-index="${pointIndex}"]`);
        let shopCount = container.find('.shop-item').length;
        let newIndex = Date.now(); // Unique index

        let shopHtml = `
            <div class="card mb-3 shop-item">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>New Shop</strong>
                        <button type="button" class="btn btn-sm btn-danger remove-shop">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Floor</label>
                            <input type="text" class="form-control" name="points[${pointIndex}][shops][${newIndex}][shop_floor]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Shop Name</label>
                            <input type="text" class="form-control" name="points[${pointIndex}][shops][${newIndex}][shop_name]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Owner Name</label>
                            <input type="text" class="form-control" name="points[${pointIndex}][shops][${newIndex}][shop_owner_name]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="points[${pointIndex}][shops][${newIndex}][shop_category]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" class="form-control" name="points[${pointIndex}][shops][${newIndex}][shop_mobile]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">License</label>
                            <input type="text" class="form-control" name="points[${pointIndex}][shops][${newIndex}][license]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">No of Employees</label>
                            <input type="number" class="form-control" name="points[${pointIndex}][shops][${newIndex}][number_of_employee]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <input type="text" class="form-control" name="points[${pointIndex}][shops][${newIndex}][type]">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Coordinates</label>
                            <input type="text" class="form-control" name="points[${pointIndex}][shops][${newIndex}][coordinates]">
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.append(shopHtml);
    });

    // Remove shop
    $(document).on('click', '.remove-shop', function() {
        $(this).closest('.shop-item').remove();
    });

    // Form submission
    $('#editPointForm').submit(function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: '{{ route("surveyor.update-point-data") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (response.redirect_url) {
                            window.location.href = response.redirect_url;
                        }
                    });
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                let message = xhr.responseJSON?.message || 'An error occurred';

                if (errors) {
                    let errorHtml = '<ul>';
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value + '</li>';
                    });
                    errorHtml += '</ul>';
                    message = errorHtml;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: message
                });
            }
        });
    });
});
</script>
@endpush

<style>
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
}
.dashboard-header h3 {
    color: white;
    font-weight: 700;
}
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
@endsection
