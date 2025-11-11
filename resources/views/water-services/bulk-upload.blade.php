@extends('layouts.app')

@section('title', 'Bulk Upload Water Readings')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('water.services.index') }}">Water Services</a></li>
    <li class="breadcrumb-item active">Bulk Upload Readings</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Bulk Upload Water Readings</h2>
            <p class="text-muted mb-0">Import multiple readings at once using the Excel template.</p>
        </div>
        <a href="{{ route('water.services.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>Back to Services
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-orange text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Step 1: Download Template</h5>
                    </div>
                    <a href="{{ route('water-services.readings.bulk.template') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-download me-1"></i>Download Template
                    </a>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted">
                        Download the active-services template, fill in the latest readings, then upload it here. Each row
                        must include a
                        <strong>Reading Date</strong>, <strong>Current Reading</strong>, and <strong>Bill Amount</strong>.
                        The template now lists the
                        <strong>Previous Reading</strong> for reference and the <strong>Paid</strong> column defaults to
                        <em>No</em>.
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload me-2 text-orange"></i>Step 2: Upload Filled Template
                    </h5>
                </div>
                <div class="card-body">
                    <form id="bulkUploadForm" class="mb-4" enctype="multipart/form-data">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-9">
                                <label for="bulkUploadFile" class="form-label fw-bold">Filled Template File</label>
                                <input type="file" class="form-control" id="bulkUploadFile" name="file"
                                    accept=".xlsx,.xls" required>
                                <small class="text-muted">Accepted formats: .xlsx, .xls · Only active services are included
                                    in the template.</small>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-orange w-100" id="bulkUploadSubmit">
                                    <i class="bi bi-eye me-1"></i>Upload & Preview
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="bulkUploadErrors" class="d-none"></div>
                    <div id="bulkUploadWarnings" class="d-none"></div>

                    <div id="bulkPreviewSection" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Preview Imported Readings</h5>
                            <span class="badge bg-light text-dark" id="bulkPreviewCount"></span>
                        </div>
                        <div class="border rounded" style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-striped table-hover table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">Row</th>
                                        <th>Registration #</th>
                                        <th>Meter Owner</th>
                                        <th>Water Company</th>
                                        <th>Building</th>
                                        <th class="text-end">Previous Reading (m3)</th>
                                        <th class="text-end">Current Reading (m3)</th>
                                        <th class="text-end">Bill Amount (JOD)</th>
                                        <th>Reading Date</th>
                                        <th>Paid</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="bulkPreviewTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('water.services.index') }}" class="btn btn-light">Cancel</a>
                    <button type="button" class="btn btn-orange" id="bulkConfirmButton" disabled>
                        <i class="bi bi-check-circle me-1"></i>Confirm Upload
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-list-check me-2 text-orange"></i>Checklist</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3 small">
                        <li>Only rows with the required values are imported; others are skipped.</li>
                        <li>Skipped rows are listed so you can update the template and retry.</li>
                        <li>If a row is intentionally left blank, confirm after reviewing the preview.</li>
                        <li>The Paid column is a Yes/No dropdown and defaults to “No”.</li>
                        <li>Consumption is recalculated for each affected service after import.</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-question-circle me-2 text-orange"></i>Need help?</h6>
                </div>
                <div class="card-body small text-muted">
                    The template lists only active water services with today’s date pre-filled.
                    Update the necessary readings, save the file, and upload it back here to preview before committing.
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const indexUrl = "{{ route('water.services.index') }}";

        const bulkUploadState = {
            uploadKey: null
        };

        document.getElementById('bulkUploadForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const submitButton = document.getElementById('bulkUploadSubmit');
            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing';

            const formData = new FormData(event.target);
            formData.append('_token', csrfToken);

            try {
                const response = await fetch("{{ route('water-services.readings.bulk.preview') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const payload = await response.json();

                if (!response.ok || !payload.success) {
                    handleBulkUploadError(payload.message ?? 'Failed to process the uploaded file.', payload
                        .errors ?? []);
                    return;
                }

                bulkUploadState.uploadKey = payload.upload_key;
                renderBulkPreview(payload.rows);
                renderBulkWarnings(payload.errors ?? []);
                document.getElementById('bulkConfirmButton').disabled = false;
            } catch (error) {
                handleBulkUploadError('An unexpected error occurred while processing the file.');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });

        document.getElementById('bulkConfirmButton').addEventListener('click', async function() {
            if (!bulkUploadState.uploadKey) {
                return;
            }

            const confirmButton = this;
            confirmButton.disabled = true;
            const originalText = confirmButton.innerHTML;
            confirmButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving';

            try {
                const response = await fetch("{{ route('water-services.readings.bulk.confirm') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        upload_key: bulkUploadState.uploadKey
                    })
                });

                const payload = await response.json();

                if (!response.ok || !payload.success) {
                    handleBulkUploadError(payload.message ?? 'Unable to save the readings. Please try again.');
                    confirmButton.disabled = false;
                    confirmButton.innerHTML = originalText;
                    return;
                }

                window.location.href = indexUrl;
            } catch (error) {
                handleBulkUploadError('An unexpected error occurred while saving the readings.');
                confirmButton.disabled = false;
                confirmButton.innerHTML = originalText;
            }
        });

        function renderBulkPreview(rows) {
            const tableBody = document.getElementById('bulkPreviewTableBody');
            tableBody.innerHTML = '';

            rows.forEach(function(row) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center">${row.row_number}</td>
                    <td>
                        <div class="fw-semibold">${row.registration_number}</div>
                        <div class="text-muted small">Iron: ${row.iron_number}</div>
                    </td>
                    <td>${row.meter_owner_name}</td>
                    <td>${row.company_name}</td>
                    <td>${row.building_name}</td>
                    <td class="text-end">${row.previous_reading}</td>
                    <td class="text-end">${row.current_reading}</td>
                    <td class="text-end">${row.bill_amount}</td>
                    <td>${row.reading_date}</td>
                    <td>
                        <span class="badge rounded-pill ${row.is_paid === 'Yes' ? 'bg-success text-white' : 'bg-warning text-dark'}">${row.is_paid}</span>
                    </td>
                    <td>${row.notes}</td>
                `;
                tableBody.appendChild(tr);
            });

            document.getElementById('bulkPreviewCount').textContent = rows.length + ' row(s) ready';
            document.getElementById('bulkPreviewSection').classList.remove('d-none');
            document.getElementById('bulkUploadErrors').classList.add('d-none');
            document.getElementById('bulkUploadErrors').innerHTML = '';
        }

        function renderBulkWarnings(messages) {
            const warningContainer = document.getElementById('bulkUploadWarnings');

            if (!messages.length) {
                warningContainer.classList.add('d-none');
                warningContainer.innerHTML = '';
                return;
            }

            warningContainer.classList.remove('d-none');
            warningContainer.innerHTML = `
                <div class="alert alert-warning border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-2">Some rows were skipped:</h6>
                            <ul class="mb-2 ps-3">
                                ${messages.map(message => `<li>${message}</li>`).join('')}
                            </ul>
                            <hr class="my-2">
                            <p class="mb-0 small">
                                <strong>What to do:</strong> Update the template with the missing values and upload again.
                                If you intentionally left these rows blank, review the preview and confirm to proceed.
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }

        function handleBulkUploadError(message, details = []) {
            const errorContainer = document.getElementById('bulkUploadErrors');
            const warningContainer = document.getElementById('bulkUploadWarnings');

            errorContainer.classList.remove('d-none');
            errorContainer.innerHTML = `
                <div class="alert alert-danger border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-x-circle-fill fs-3 text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-2">Upload Failed</h6>
                            <p class="mb-0">${message}</p>
                            ${details.length ? `<ul class="mb-0 mt-2 ps-3">${details.map(item => `<li>${item}</li>`).join('')}</ul>` : ''}
                        </div>
                    </div>
                </div>
            `;

            warningContainer.classList.add('d-none');
            warningContainer.innerHTML = '';
            document.getElementById('bulkPreviewSection').classList.add('d-none');
            document.getElementById('bulkPreviewTableBody').innerHTML = '';
            document.getElementById('bulkPreviewCount').textContent = '';
            document.getElementById('bulkConfirmButton').disabled = true;
        }
    </script>
@endsection
