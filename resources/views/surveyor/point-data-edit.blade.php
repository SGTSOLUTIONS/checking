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
                        <button type="button" class="btn btn-success mb-3" id="saveAllBtn">
                            <i class="fas fa-save"></i> Save All Changes
                        </button>
                        <a href="{{ route('surveyor.mapview') }}" class="btn btn-secondary mb-3">
                            <i class="fas fa-arrow-left"></i> Back to Map
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Point Data Table -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Point Data Records ({{ count($pointData) }} records found)</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover table-bordered" id="pointDataTable">
                    <thead class="table-light">
                        <tr id="pointHeaders"></tr>
                    </thead>
                    <tbody id="pointBody"></tbody>
                </table>
            </div>
        </div>

        <!-- Shops Data Section -->
        <div id="shopsSection"></div>
    </div>

    <script>
        $(document).ready(function() {
            var pointData = @json($pointData);
            var surveyor = @json($surveyor);
            console.log('Point Data:', pointData);
            console.log('Surveyor:', surveyor);

            // Define fields to display (exclude sensitive or unnecessary fields)
            var excludeFields = ['created_at', 'updated_at', 'deleted_at', 'building_data_id', 'shops'];
            var pointFields = [];

            if (pointData.length > 0) {
                // Get all unique headers from point data
                var allHeaders = Object.keys(pointData[0]);
                pointFields = allHeaders.filter(header => !excludeFields.includes(header));

                // Render Point Data Table
                renderPointTable(pointData, pointFields, surveyor);

                // Render Shops for each point
                renderShopsTables(pointData, surveyor);
            } else {
                $("#pointBody").html('<tr><td colspan="5" class="text-center">No data found</td></tr>');
            }

            function renderPointTable(data, fields, surveyor) {
                $("#pointHeaders").empty();
                $("#pointBody").empty();

                // Create headers
                fields.forEach(function(header) {
                    $("<th>").text(formatHeader(header)).appendTo("#pointHeaders");
                });
                $("<th>").text("Action").appendTo("#pointHeaders");

                // Create rows
                data.forEach(function(item, index) {
                    var row = $("<tr id='point-row-" + item.id + "' data-point-id='" + item.id + "' data-point-index='" + index + "'>");

                    fields.forEach(function(header) {
                        var readOnly = (header === 'id' || header === 'point_gisid' || header === 'assessment_type') ? 'readonly' : '';
                        var value = item[header] !== null && item[header] !== undefined ? item[header] : '';

                        // Special handling for certain fields
                        if (header === 'bill_usage') {
                            var selectHtml = '<select name="' + header + '" class="form-control form-control-sm" ' + (readOnly ? 'disabled' : '') + '>';
                            selectHtml += '<option value="">Select</option>';
                            selectHtml += '<option value="Residential" ' + (value === 'Residential' ? 'selected' : '') + '>Residential</option>';
                            selectHtml += '<option value="Commercial" ' + (value === 'Commercial' ? 'selected' : '') + '>Commercial</option>';
                            selectHtml += '<option value="Mixed" ' + (value === 'Mixed' ? 'selected' : '') + '>Mixed</option>';
                            selectHtml += '</select>';
                            $("<td>").html(selectHtml).appendTo(row);
                        } else {
                            $("<td>").html("<input type='text' class='form-control form-control-sm' value='" + escapeHtml(String(value)) + "' name='" + header + "' " + readOnly + ">").appendTo(row);
                        }
                    });

                    // Check if user can edit this record
                    var canEditRecord = canEdit(surveyor.name, item.worker_name);

                    // Action buttons
                    if (canEditRecord) {
                        var actionHtml = '<button type="button" class="btn btn-sm btn-primary updatePointBtn" data-point-id="' + item.id + '">Update</button>';
                        $("<td>").html(actionHtml).appendTo(row);
                    } else {
                        $("<td>").html('<span class="badge bg-secondary">Read Only (Created by: ' + (item.worker_name || 'Unknown') + ')</span>').appendTo(row);
                    }

                    $("#pointBody").append(row);
                });
            }

            function renderShopsTables(data, surveyor) {
                $("#shopsSection").empty();

                data.forEach(function(point, pointIndex) {
                    // Check if user can edit shops for this point
                    var canEditShops = canEdit(surveyor.name, point.worker_name);

                    if (point.shops && point.shops.length > 0) {
                        var addShopButton = canEditShops ?
                            '<button type="button" class="btn btn-sm btn-success float-end addShopBtn" data-point-id="' + point.id + '" data-point-index="' + pointIndex + '">' +
                                '<i class="fas fa-plus"></i> Add Shop' +
                            '</button>' : '';

                        var shopsCard = `
                            <div class="card mb-4" id="shops-card-${point.id}">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0">
                                        Shops for Point ID: ${point.id} (Assessment: ${point.assessment || 'N/A'})
                                        ${addShopButton}
                                    </h6>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table table-sm table-striped table-bordered" id="shops-table-${point.id}">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Shop Floor</th>
                                                <th>Shop Name</th>
                                                <th>Owner Name</th>
                                                <th>Category</th>
                                                <th>Mobile</th>
                                                <th>License</th>
                                                <th>No of Employees</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="shops-body-${point.id}">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                        $("#shopsSection").append(shopsCard);

                        // Render shops for this point
                        renderShopsTable(point.id, point.shops, surveyor, pointIndex, canEditShops);
                    } else if (canEditShops) {
                        // Show card with add button even if no shops exist
                        var shopsCard = `
                            <div class="card mb-4" id="shops-card-${point.id}">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0">
                                        Shops for Point ID: ${point.id} (Assessment: ${point.assessment || 'N/A'})
                                        <button type="button" class="btn btn-sm btn-success float-end addShopBtn" data-point-id="${point.id}" data-point-index="${pointIndex}">
                                            <i class="fas fa-plus"></i> Add Shop
                                        </button>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info text-center">No shops added yet. Click "Add Shop" to add.</div>
                                </div>
                            </div>
                        `;
                        $("#shopsSection").append(shopsCard);
                    }
                });
            }

            function renderShopsTable(pointId, shops, surveyor, pointIndex, canEditShops) {
                var tbody = $("#shops-body-" + pointId);
                tbody.empty();

                shops.forEach(function(shop, shopIndex) {
                    var row = $("<tr id='shop-row-" + shop.id + "'>");
                    row.append("<td><input type='text' class='form-control form-control-sm' value='" + (shop.id || '') + "' readonly style='width:60px'></td>");
                    row.append("<td><input type='text' class='form-control form-control-sm shop-floor' value='" + escapeHtml(shop.shop_floor || '') + "' data-shop-id='" + shop.id + "' data-field='shop_floor' " + (canEditShops ? '' : 'readonly') + "></td>");
                    row.append("<td><input type='text' class='form-control form-control-sm shop-name' value='" + escapeHtml(shop.shop_name || '') + "' data-shop-id='" + shop.id + "' data-field='shop_name' " + (canEditShops ? '' : 'readonly') + "></td>");
                    row.append("<td><input type='text' class='form-control form-control-sm shop-owner' value='" + escapeHtml(shop.shop_owner_name || '') + "' data-shop-id='" + shop.id + "' data-field='shop_owner_name' " + (canEditShops ? '' : 'readonly') + "></td>");
                    row.append("<td><input type='text' class='form-control form-control-sm shop-category' value='" + escapeHtml(shop.shop_category || '') + "' data-shop-id='" + shop.id + "' data-field='shop_category' " + (canEditShops ? '' : 'readonly') + "></td>");
                    row.append("<td><input type='text' class='form-control form-control-sm shop-mobile' value='" + escapeHtml(shop.shop_mobile || '') + "' data-shop-id='" + shop.id + "' data-field='shop_mobile' " + (canEditShops ? '' : 'readonly') + "></td>");
                    row.append("<td><input type='text' class='form-control form-control-sm shop-license' value='" + escapeHtml(shop.license || '') + "' data-shop-id='" + shop.id + "' data-field='license' " + (canEditShops ? '' : 'readonly') + "></td>");
                    row.append("<td><input type='number' class='form-control form-control-sm shop-employees' value='" + (shop.number_of_employee || '0') + "' data-shop-id='" + shop.id + "' data-field='number_of_employee' " + (canEditShops ? '' : 'readonly') + "></td>");

                    if (canEditShops) {
                        var actionHtml = '<button type="button" class="btn btn-sm btn-primary updateShopBtn" data-shop-id="' + shop.id + '">Update</button>';
                        actionHtml += ' <button type="button" class="btn btn-sm btn-danger deleteShopBtn" data-shop-id="' + shop.id + '" data-point-id="' + pointId + '">Delete</button>';
                        row.append($("<td>").html(actionHtml));
                    } else {
                        row.append($("<td>").html('<span class="badge bg-secondary">Read Only</span>'));
                    }

                    tbody.append(row);
                });
            }

            // Update individual Point
            $(document).on("click", ".updatePointBtn", function() {
                var row = $(this).closest("tr");
                var pointId = $(this).data("point-id");
                var rowData = {};

                row.find("input, select").each(function() {
                    var name = $(this).attr("name");
                    if (name) {
                        rowData[name] = $(this).val();
                    }
                });

                console.log("Updating Point ID " + pointId + ":", rowData);

                $.ajax({
                    url: "{{ route('surveyor.updatePointRecord') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: pointId,
                        type: 'point',
                        data: rowData,
                        corp: {{ $corp }},
                        zone: '{{ $zone }}',
                        ward_no: {{ $wardNo }}
                    },
                    success: function(response) {
                        if(response.success) {
                            showMessage('success', 'Point data updated successfully');
                        } else {
                            showMessage('error', response.error || 'Error updating point data');
                        }
                    },
                    error: function(xhr) {
                        showMessage('error', 'Error updating point data');
                    }
                });
            });

            // Update individual Shop
            $(document).on("click", ".updateShopBtn", function() {
                var shopId = $(this).data("shop-id");
                var row = $(this).closest("tr");
                var shopData = {};

                row.find("input").each(function() {
                    var field = $(this).data("field");
                    if (field) {
                        shopData[field] = $(this).val();
                    }
                });

                console.log("Updating Shop ID " + shopId + ":", shopData);

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
                        } else {
                            showMessage('error', response.error || 'Error updating shop data');
                        }
                    },
                    error: function(xhr) {
                        showMessage('error', 'Error updating shop data');
                    }
                });
            });

            // Add new Shop
            $(document).on("click", ".addShopBtn", function() {
                var pointId = $(this).data("point-id");
                var pointIndex = $(this).data("point-index");

                // Find the point to check permissions
                var point = pointData.find(p => p.id == pointId);
                if (!canEdit(surveyor.name, point.worker_name)) {
                    showMessage('error', 'You do not have permission to add shops to this record');
                    return;
                }

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
            $(document).on("click", ".deleteShopBtn", function() {
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

            // Save All Changes (Bulk Update) - Only show for editable records
            $("#saveAllBtn").click(function() {
                var allPointData = [];
                var hasEditableRecords = false;

                $("#pointBody tr").each(function() {
                    var pointId = $(this).data("point-id");
                    var updateBtn = $(this).find('.updatePointBtn');

                    // Only include if there's an update button (meaning user can edit)
                    if (updateBtn.length > 0) {
                        hasEditableRecords = true;
                        var pointDataObj = {};

                        $(this).find("input, select").each(function() {
                            var name = $(this).attr("name");
                            if (name && !$(this).prop('readonly')) {
                                pointDataObj[name] = $(this).val();
                            }
                        });

                        allPointData.push({
                            id: pointId,
                            data: pointDataObj
                        });
                    }
                });

                if (allPointData.length === 0) {
                    showMessage('warning', 'No editable records to save');
                    return;
                }

                $.ajax({
                    url: "{{ route('surveyor.bulkUpdatePoints') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        points: allPointData,
                        corp: {{ $corp }},
                        zone: '{{ $zone }}',
                        ward_no: {{ $wardNo }}
                    },
                    success: function(response) {
                        if(response.success) {
                            showMessage('success', 'All data saved successfully');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showMessage('error', response.error || 'Error saving data');
                        }
                    },
                    error: function(xhr) {
                        showMessage('error', 'Error saving data');
                    }
                });
            });

            // Helper Functions
            function formatHeader(header) {
                return header.replace(/_/g, ' ').toUpperCase();
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            function canEdit(surveyorName, workerName) {
                // Admin/Supervisor users who can edit everything
                var adminUsers = ['sgt', 'malaqc', 'mala57sgtqc', 'MALA QC SGT', 'malasgt', 'mala51qc', 'sir', 'Anand', 'anandnew', 'malanew', 'anandnew91', 'Anandnew55', 'SGT', 'ward90', 'anandnew89', 'malanew51', 'officeqc52', 'Anandnew51'];

                // Check if surveyor is admin OR worker_name matches surveyor name
                if (adminUsers.includes(surveyorName)) {
                    return true;
                }

                // Check if worker_name matches surveyor name (case insensitive)
                if (workerName && surveyorName && workerName.toLowerCase() === surveyorName.toLowerCase()) {
                    return true;
                }

                // Check if worker_name contains surveyor name (for format like "123-SurveyorName")
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
                }, 3000);
            }
        });
    </script>
@endsection
