@extends('layouts.app')

@section('title', 'Bulk Add Electricity Readings')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electric.index') }}">Electricity Services</a></li>
    <li class="breadcrumb-item active">Bulk Add Readings</li>
@endsection

@section('content')
    <style>
        #content {
            background-color: #f8f9fa !important;
            background-image: none !important;
            position: relative;
        }

        #content::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("{{ asset('assets/images/energie.png') }}") !important;
            background-repeat: repeat !important;
            background-size: 22px 22px !important;
            opacity: 0.18;
            pointer-events: none;
            z-index: 0;
        }

        #content>* {
            position: relative;
            z-index: 1;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                Bulk Add Electricity Readings
            </h2>
            <p class="text-muted mb-0">Add multiple meter readings at once by searching and entering data.</p>
        </div>
        <a href="{{ route('electric.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>Back to Services
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-search me-2"></i>Search & Add Reading</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="serviceSearch" class="form-label fw-bold">
                            Search Service <span class="text-muted">(البحث عن خدمة)</span>
                        </label>
                        <input type="text" class="form-control" id="serviceSearch"
                            placeholder="Type registration number, meter number, or subscriber name..." autocomplete="off">
                        <small class="text-muted">رقم التسجيل، رقم العداد، أو اسم المشترك</small>
                        <div id="searchSpinner" class="text-center mt-2 d-none">
                            <div class="spinner-border spinner-border-sm text-warning" role="status">
                                <span class="visually-hidden">Searching...</span>
                            </div>
                        </div>
                    </div>

                    <div id="searchResults" class="d-none mb-4">
                        <h6 class="mb-3">Search Results</h6>
                        <div id="searchResultsList"></div>
                    </div>

                    <div id="readingInputSection" class="d-none">
                        <div class="alert alert-info d-flex align-items-start gap-2 mb-3">
                            <i class="bi bi-info-circle fs-4"></i>
                            <div id="serviceInfo"></div>
                        </div>

                        <form id="readingForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="reading_date" class="form-label fw-bold">
                                        Reading Date <span class="text-muted">(تاريخ القراءة)</span> <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="reading_date" required>
                                </div>

                                <div class="col-md-6" id="importedCurrentGroup">
                                    <label for="imported_current" class="form-label fw-bold">
                                        Imported Current <span class="text-muted">(القراءة المستجرة الحالية)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="imported_current">
                                    <small class="text-muted" id="importedHint"></small>
                                </div>

                                <div class="col-md-6" id="importedCalculatedGroup">
                                    <label for="imported_calculated" class="form-label fw-bold">
                                        Imported Calculated <span class="text-muted">(المستجرة المحتسبة)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="imported_calculated">
                                </div>

                                <div class="col-md-6 d-none" id="producedCurrentGroup">
                                    <label for="produced_current" class="form-label fw-bold">
                                        Produced Current <span class="text-muted">(القراءة المصدّرة الحالية)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="produced_current">
                                    <small class="text-muted" id="producedHint"></small>
                                </div>

                                <div class="col-md-6 d-none" id="producedCalculatedGroup">
                                    <label for="produced_calculated" class="form-label fw-bold">
                                        Produced Calculated <span class="text-muted">(المصدّرة المحتسبة)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="produced_calculated">
                                </div>

                                <div class="col-md-6 d-none" id="savedEnergyGroup">
                                    <label for="saved_energy" class="form-label fw-bold">
                                        Saved Energy <span class="text-muted">(الطاقة الموفرة)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="saved_energy">
                                </div>

                                <div class="col-md-6">
                                    <label for="bill_amount" class="form-label fw-bold">
                                        Bill Amount <span class="text-muted">(مبلغ الفاتورة)</span> (JOD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="bill_amount">
                                </div>

                                <div class="col-md-6">
                                    <label for="is_paid" class="form-label fw-bold">
                                        Payment Status <span class="text-muted">(حالة الدفع)</span>
                                    </label>
                                    <select class="form-select" id="is_paid">
                                        <option value="0">Unpaid (غير مدفوع)</option>
                                        <option value="1">Paid (مدفوع)</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label fw-bold">
                                        Notes <span class="text-muted">(ملاحظات)</span>
                                    </label>
                                    <textarea class="form-control" id="notes" rows="2"></textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-plus-circle me-1"></i>Add to List
                                    </button>
                                    <button type="button" class="btn btn-light" id="cancelButton">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" id="readingsListCard" style="display: none;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2 text-warning"></i>Readings to Add
                    </h5>
                    <span class="badge bg-warning text-dark" id="readingsCount">0 readings</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Imported</th>
                                    <th class="solar-col d-none">Produced</th>
                                    <th class="solar-col d-none">Saved</th>
                                    <th>Bill (JOD)</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="readingsTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" id="clearAllButton">
                        <i class="bi bi-trash me-1"></i>Clear All
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmAllButton">
                        <i class="bi bi-check-circle me-1"></i>Confirm & Add All Readings
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-list-check me-2 text-warning"></i>Instructions</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0 ps-3 small">
                        <li class="mb-2">Start typing in the search box - results appear automatically</li>
                        <li class="mb-2">Select the service from search results</li>
                        <li class="mb-2">Fill in the reading details (date and values are required)</li>
                        <li class="mb-2">Click "Add to List" to add it to the batch</li>
                        <li class="mb-2">Repeat for other services</li>
                        <li>Click "Confirm & Add All Readings" when done</li>
                    </ol>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips</h6>
                </div>
                <div class="card-body small text-muted">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">For solar services, additional fields (produced, saved energy) will appear
                            automatically</li>
                        <li class="mb-2">Previous readings are shown as hints below each field</li>
                        <li>You can remove any reading from the list before confirming</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true"
        style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title" id="confirmModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Action
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                    <!-- Message will be inserted here -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmModalAction">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Alert Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true"
        style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title" id="errorModalLabel">
                        <i class="bi bi-x-circle-fill me-2"></i>Error
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorModalBody">
                    <!-- Error message will be inserted here -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const readingsList = [];
        let selectedService = null;
        let hasSolarInBatch = false;
        let searchTimeout = null;
        let confirmModal, errorModal;

        // Initialize modals when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const confirmModalEl = document.getElementById('confirmModal');
            const errorModalEl = document.getElementById('errorModal');

            if (typeof boosted !== 'undefined') {
                // Using Boosted
                confirmModal = new boosted.Modal(confirmModalEl);
                errorModal = new boosted.Modal(errorModalEl);
            } else {
                // Fallback to plain Bootstrap data-bs methods
                confirmModal = {
                    show: () => confirmModalEl.classList.add('show'),
                    hide: () => confirmModalEl.classList.remove('show')
                };
                errorModal = {
                    show: () => errorModalEl.classList.add('show'),
                    hide: () => errorModalEl.classList.remove('show')
                };
            }
        });

        // Show error modal
        function showError(message) {
            document.getElementById('errorModalBody').innerHTML = `<p class="mb-0">${message}</p>`;
            const errorModalEl = document.getElementById('errorModal');
            errorModalEl.classList.add('show');
            errorModalEl.style.display = 'block';
            document.body.classList.add('modal-open');

            // Create backdrop
            let backdrop = document.querySelector('.modal-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.style.zIndex = '9998';
                document.body.appendChild(backdrop);
            }
        }

        // Show confirmation modal
        function showConfirm(message, callback) {
            document.getElementById('confirmModalBody').innerHTML = `<p class="mb-0">${message}</p>`;
            const actionBtn = document.getElementById('confirmModalAction');
            const confirmModalEl = document.getElementById('confirmModal');

            // Remove old event listeners by cloning
            const newActionBtn = actionBtn.cloneNode(true);
            actionBtn.parentNode.replaceChild(newActionBtn, actionBtn);

            newActionBtn.addEventListener('click', function() {
                hideModal(confirmModalEl);
                callback();
            });

            // Show modal
            confirmModalEl.classList.add('show');
            confirmModalEl.style.display = 'block';
            document.body.classList.add('modal-open');

            // Create backdrop
            let backdrop = document.querySelector('.modal-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.style.zIndex = '9998';
                document.body.appendChild(backdrop);
            }
        }

        // Hide modal helper
        function hideModal(modalEl) {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');

            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }

        // Add close button handlers
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    hideModal(modal);
                });
            });
        });

        // Auto-search as user types
        document.getElementById('serviceSearch').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                document.getElementById('searchResults').classList.add('d-none');
                return;
            }

            document.getElementById('searchSpinner').classList.remove('d-none');

            searchTimeout = setTimeout(() => {
                searchServices();
            }, 500); // Wait 500ms after user stops typing
        });

        async function searchServices() {
            const query = document.getElementById('serviceSearch').value.trim();
            const resultsDiv = document.getElementById('searchResults');
            const resultsList = document.getElementById('searchResultsList');
            const spinner = document.getElementById('searchSpinner');

            if (query.length < 2) {
                resultsDiv.classList.add('d-none');
                spinner.classList.add('d-none');
                return;
            }

            try {
                const response = await fetch(`/api/electricity-services/search?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                spinner.classList.add('d-none');

                if (data.length === 0) {
                    resultsList.innerHTML =
                        '<div class="alert alert-warning mb-0">No services found. Try a different search term.</div>';
                    resultsDiv.classList.remove('d-none');
                    return;
                }

                displaySearchResults(data);
            } catch (error) {
                console.error('Search error:', error);
                spinner.classList.add('d-none');
                showError('Failed to search for services. Please try again.');
            }
        }

        function displaySearchResults(services) {
            const resultsDiv = document.getElementById('searchResults');
            const resultsList = document.getElementById('searchResultsList');

            if (services.length === 0) {
                resultsList.innerHTML = '<div class="alert alert-warning">No services found</div>';
                resultsDiv.classList.remove('d-none');
                return;
            }

            let html = '<div class="list-group">';
            services.forEach(service => {
                html += `
                    <button type="button" class="list-group-item list-group-item-action" onclick="selectService(${service.id})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${service.subscriber_name}</h6>
                                <p class="mb-1 small text-muted">
                                    Reg: ${service.registration_number} | Meter: ${service.meter_number}
                                </p>
                                <p class="mb-0 small">${service.building_name || 'No building'}</p>
                            </div>
                            <div class="text-end">
                                ${service.has_solar_power ? '<span class="badge bg-success">Solar</span>' : '<span class="badge bg-secondary">Standard</span>'}
                            </div>
                        </div>
                    </button>
                `;
            });
            html += '</div>';

            resultsList.innerHTML = html;
            resultsDiv.classList.remove('d-none');
        }

        async function selectService(serviceId) {
            try {
                const response = await fetch(`/api/electricity-services/${serviceId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                selectedService = await response.json();
                showReadingInput();
            } catch (error) {
                console.error('Error loading service:', error);
                showError('Failed to load service details. Please try again.');
            }
        }

        function showReadingInput() {
            const isSolar = selectedService.has_solar_power;

            // Show/hide solar fields
            document.getElementById('producedCurrentGroup').classList.toggle('d-none', !isSolar);
            document.getElementById('producedCalculatedGroup').classList.toggle('d-none', !isSolar);
            document.getElementById('savedEnergyGroup').classList.toggle('d-none', !isSolar);

            // Update service info
            document.getElementById('serviceInfo').innerHTML = `
                <div>
                    <strong>${selectedService.subscriber_name}</strong><br>
                    Reg: ${selectedService.registration_number} | Meter: ${selectedService.meter_number}<br>
                    ${selectedService.building_name || 'No building'} ${isSolar ? '<span class="badge bg-success ms-2">Solar Service</span>' : ''}
                </div>
            `;

            // Update hints with previous readings
            if (selectedService.latest_reading) {
                const latest = selectedService.latest_reading;
                document.getElementById('importedHint').textContent = `Previous: ${latest.imported_current || 0} kWh`;
                if (isSolar) {
                    document.getElementById('producedHint').textContent = `Previous: ${latest.produced_current || 0} kWh`;
                }
            } else {
                document.getElementById('importedHint').textContent = 'No previous readings';
                if (isSolar) {
                    document.getElementById('producedHint').textContent = 'No previous readings';
                }
            }

            // Set today's date
            document.getElementById('reading_date').valueAsDate = new Date();

            // Show input section
            document.getElementById('readingInputSection').classList.remove('d-none');
            document.getElementById('searchResults').classList.add('d-none');
        }

        // Handle form submission
        document.getElementById('readingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const reading = {
                service_id: selectedService.id,
                service_name: selectedService.subscriber_name,
                registration_number: selectedService.registration_number,
                meter_number: selectedService.meter_number,
                building_name: selectedService.building_name || 'No building',
                has_solar: selectedService.has_solar_power,
                reading_date: document.getElementById('reading_date').value,
                imported_current: document.getElementById('imported_current').value || null,
                imported_calculated: document.getElementById('imported_calculated').value || null,
                produced_current: selectedService.has_solar_power ? (document.getElementById('produced_current')
                    .value || null) : null,
                produced_calculated: selectedService.has_solar_power ? (document.getElementById(
                    'produced_calculated').value || null) : null,
                saved_energy: selectedService.has_solar_power ? (document.getElementById('saved_energy')
                    .value || null) : null,
                bill_amount: document.getElementById('bill_amount').value || null,
                is_paid: document.getElementById('is_paid').value,
                notes: document.getElementById('notes').value || null
            };

            // Validate required fields
            if (!reading.reading_date || !reading.imported_current) {
                showError('Reading date and Imported Current are required');
                return;
            }

            readingsList.push(reading);
            if (reading.has_solar) {
                hasSolarInBatch = true;
            }
            updateReadingsTable();
            resetForm();
        });

        function updateReadingsTable() {
            const tbody = document.getElementById('readingsTableBody');
            const count = document.getElementById('readingsCount');
            const card = document.getElementById('readingsListCard');

            // Show/hide solar columns
            document.querySelectorAll('.solar-col').forEach(col => {
                col.classList.toggle('d-none', !hasSolarInBatch);
            });

            if (readingsList.length === 0) {
                card.style.display = 'none';
                return;
            }

            card.style.display = 'block';
            count.textContent = `${readingsList.length} reading${readingsList.length !== 1 ? 's' : ''}`;

            let html = '';
            readingsList.forEach((reading, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <div class="fw-semibold">${reading.service_name}</div>
                            <small class="text-muted">Reg: ${reading.registration_number}</small>
                            ${reading.has_solar ? '<span class="badge bg-success ms-1">Solar</span>' : ''}
                        </td>
                        <td>${reading.reading_date}</td>
                        <td>${reading.imported_current || '—'} / ${reading.imported_calculated || '—'}</td>
                        ${hasSolarInBatch ? `<td class="solar-col">${reading.produced_current || '—'} / ${reading.produced_calculated || '—'}</td>` : ''}
                        ${hasSolarInBatch ? `<td class="solar-col">${reading.saved_energy || '—'}</td>` : ''}
                        <td>${reading.bill_amount || '—'}</td>
                        <td><span class="badge ${reading.is_paid == '1' ? 'bg-success' : 'bg-warning text-dark'}">${reading.is_paid == '1' ? 'Paid' : 'Unpaid'}</span></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeReading(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function removeReading(index) {
            readingsList.splice(index, 1);
            hasSolarInBatch = readingsList.some(r => r.has_solar);
            updateReadingsTable();
        }

        function resetForm() {
            document.getElementById('readingForm').reset();
            document.getElementById('readingInputSection').classList.add('d-none');
            document.getElementById('serviceSearch').value = '';
            selectedService = null;
        }

        document.getElementById('cancelButton').addEventListener('click', resetForm);

        document.getElementById('clearAllButton').addEventListener('click', function() {
            showConfirm('Remove all readings from the list?', function() {
                readingsList.length = 0;
                hasSolarInBatch = false;
                updateReadingsTable();
            });
        });

        document.getElementById('confirmAllButton').addEventListener('click', async function() {
            if (readingsList.length === 0) {
                showError('No readings to add');
                return;
            }

            showConfirm(`Add ${readingsList.length} reading(s) to the database?`, async function() {
                const button = document.getElementById('confirmAllButton');
                button.disabled = true;
                button.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                try {
                    const response = await fetch('/api/electricity-readings/bulk', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            readings: readingsList
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        window.location.href = '{{ route('electric.index') }}?success=' +
                            encodeURIComponent(data.message);
                    } else {
                        showError(data.message || 'Failed to add readings');
                        button.disabled = false;
                        button.innerHTML =
                            '<i class="bi bi-check-circle me-1"></i>Confirm & Add All Readings';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showError('An error occurred while saving readings');
                    button.disabled = false;
                    button.innerHTML =
                        '<i class="bi bi-check-circle me-1"></i>Confirm & Add All Readings';
                }
            });
        });
    </script>
@endsection
