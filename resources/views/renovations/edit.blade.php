@extends('layouts.app')

@section('title', 'Edit Renovation')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('renovations.index') }}">Renovations</a></li>
    <li class="breadcrumb-item"><a href="{{ route('renovations.show', $renovation) }}">{{ $renovation->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@php
    // Extract short class name from full class name (e.g., "App\Models\Building" -> "Building")
    $shortClassName = class_basename($renovation->innovatable_type);
@endphp

@section('styles')
    <style>
        .hierarchy-tree {
            font-size: 0.95rem;
        }

        .child-node {
            transition: all 0.2s ease;
            background-color: #fff;
        }

        .child-node:not(.border-2):hover {
            background-color: #f8f9fa;
            border-color: #FF7900 !important;
            cursor: pointer;
        }

        .entity-item {
            transition: all 0.2s ease;
        }

        .entity-item:hover {
            background-color: #fff3e6 !important;
            border-left: 3px solid #FF7900;
        }

        #entityTree {
            background-color: #fafafa;
        }

        .site-node {
            transition: all 0.2s ease;
        }

        .site-node:not(.border-2):hover {
            background-color: #e9ecef;
            border-color: #FF7900 !important;
            cursor: pointer;
        }

        /* Land display text wrapping */
        .child-node .flex-grow-1 {
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.4;
        }

        #selectedEntityText {
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.4;
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-fill me-2 text-orange"></i>
                        Edit Renovation
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('renovations.update', $renovation) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-link-45deg me-2"></i>Related Entity
                        </h5>

                        <!-- Hidden inputs for actual form submission -->
                        <input type="hidden" name="innovatable_type" id="innovatable_type"
                            value="{{ old('innovatable_type', $shortClassName) }}">
                        <input type="hidden" name="innovatable_id" id="innovatable_id"
                            value="{{ old('innovatable_id', $renovation->innovatable_id) }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Search & Select Entity <span class="text-danger">*</span>
                            </label>

                            <!-- Search Box -->
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="entitySearch" class="form-control"
                                    placeholder="Search for site, building, or land by code or name...">
                            </div>

                            <!-- Selected Entity Display -->
                            <div id="selectedEntityDisplay" class="alert alert-info d-none mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1 me-3">
                                        <strong>Selected:</strong> <span id="selectedEntityText"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light border" id="clearSelection">
                                        <i class="bi bi-x-circle me-1"></i> Change
                                    </button>
                                </div>
                            </div> <!-- Entity Selection Tree -->
                            <div id="entityTree" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-search fs-3 d-block mb-2"></i>
                                    <p class="mb-0">Loading current selection...</p>
                                </div>
                            </div>

                            @error('innovatable_type')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                            @error('innovatable_id')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-info-circle me-2"></i>Renovation Details
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">
                                    Renovation Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $renovation->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="cost" class="form-label fw-bold">
                                    Cost (JOD) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="cost" id="cost"
                                    class="form-control @error('cost') is-invalid @enderror"
                                    value="{{ old('cost', $renovation->cost) }}" required>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="date" class="form-label fw-bold">
                                    Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="date" id="date"
                                    class="form-control @error('date') is-invalid @enderror"
                                    value="{{ old('date', $renovation->date ? $renovation->date->format('Y-m-d') : '') }}"
                                    required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description', $renovation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Describe the renovation work performed</small>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('renovations.show', $renovation) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Update Renovation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Changing Entity -->
    <div class="modal fade" id="changeEntityModal" tabindex="-1" aria-labelledby="changeEntityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="changeEntityModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Entity Change
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><strong>Current:</strong> <span id="currentEntityText"></span></p>
                    <p class="mb-2"><strong>New:</strong> <span id="newEntityText"></span></p>
                    <hr>
                    <p class="text-muted mb-0"><small>Are you sure you want to change the associated entity for this
                            renovation?</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmEntityChange">
                        <i class="bi bi-check-circle me-1"></i> Confirm Change
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const entitySearch = document.getElementById('entitySearch');
            const entityTree = document.getElementById('entityTree');
            const selectedDisplay = document.getElementById('selectedEntityDisplay');
            const selectedText = document.getElementById('selectedEntityText');
            const clearBtn = document.getElementById('clearSelection');
            const typeInput = document.getElementById('innovatable_type');
            const idInput = document.getElementById('innovatable_id');
            const currentEntityTextEl = document.getElementById('currentEntityText');
            const newEntityTextEl = document.getElementById('newEntityText');

            const originalType = '{{ old('innovatable_type', $shortClassName) }}';
            const originalId = {{ old('innovatable_id', $renovation->innovatable_id) }};

            let allSites = [];
            let allBuildings = [];
            let allLands = [];
            let selectedEntity = null;
            let pendingSelection = null;

            // Load all data
            Promise.all([
                fetch('/api/sites-list').then(r => r.json()),
                fetch('/api/buildings-list').then(r => r.json()),
                fetch('/api/lands-list').then(r => r.json())
            ]).then(([sites, buildings, lands]) => {
                allSites = sites;
                allBuildings = buildings;
                allLands = lands;

                // Load current selection
                let entity = null;
                if (originalType === 'Site') {
                    entity = allSites.find(s => s.id == originalId);
                    if (entity) selectEntity('Site', entity, null, true);
                } else if (originalType === 'Building') {
                    entity = allBuildings.find(b => b.id == originalId);
                    if (entity) selectEntity('Building', entity, null, true);
                } else if (originalType === 'Land') {
                    entity = allLands.find(l => l.id == originalId);
                    if (entity) selectEntity('Land', entity, null, true);
                }
            }).catch(error => {
                console.error('Error loading entities:', error);
                entityTree.innerHTML = '<div class="text-danger text-center py-3">Error loading data</div>';
            });

            // Search functionality
            entitySearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                if (!searchTerm) {
                    // Show current selection hierarchy
                    if (selectedEntity) {
                        const {
                            type,
                            entity
                        } = selectedEntity;
                        if (type === 'Building' || type === 'Land') {
                            showHierarchy(type, entity.id, entity.site_id);
                        } else {
                            showHierarchy('Site', entity.id);
                        }
                    }
                    return;
                }

                // Search across all entities
                const results = [];

                allSites.forEach(site => {
                    const searchableText = `${site.code || ''} ${site.name || ''}`.toLowerCase();
                    if (searchableText.includes(searchTerm)) {
                        results.push({
                            type: 'Site',
                            data: site,
                            match: true
                        });
                    }
                });

                allBuildings.forEach(building => {
                    const searchableText = `${building.code || ''} ${building.name || ''}`
                        .toLowerCase();
                    if (searchableText.includes(searchTerm)) {
                        results.push({
                            type: 'Building',
                            data: building,
                            match: true
                        });
                    }
                });

                allLands.forEach(land => {
                    // Search by plot_key for lands
                    const searchableText =
                        `${land.plot_key || ''} ${land.directorate || ''} ${land.village || ''} ${land.basin || ''} ${land.neighborhood || ''}`
                        .toLowerCase();
                    if (searchableText.includes(searchTerm)) {
                        results.push({
                            type: 'Land',
                            data: land,
                            match: true
                        });
                    }
                });

                displayResults(results);
            });

            function displayResults(results) {
                if (results.length === 0) {
                    entityTree.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            <p class="mb-0">No results found</p>
                        </div>
                    `;
                    return;
                }

                let html = '<div class="list-group list-group-flush">';

                results.forEach(result => {
                    const {
                        type,
                        data
                    } = result;
                    let displayName = '';

                    if (type === 'Land') {
                        // Format land display with conditional formatting to avoid empty values
                        const parts = [];

                        if (data.plot_key) parts.push(data.plot_key);

                        if (data.directorate) {
                            parts.push(data.directorate_number ?
                                `${data.directorate}(${data.directorate_number})` :
                                data.directorate);
                        }

                        if (data.village) {
                            parts.push(data.village_number ?
                                `${data.village}(${data.village_number})` :
                                data.village);
                        }

                        if (data.basin) {
                            parts.push(data.basin_number ?
                                `${data.basin}(${data.basin_number})` :
                                data.basin);
                        }

                        if (data.neighborhood) parts.push(data.neighborhood);

                        if (data.plot_number) parts.push(`Plot ${data.plot_number}`);

                        displayName = parts.length > 0 ? parts.join(' - ') : 'Land (No data)';
                    } else {
                        displayName = `${data.code || ''} - ${data.name || ''}`.trim();
                    }

                    const icon = type === 'Site' ? 'building' : (type === 'Building' ? 'house-door' :
                    'map');
                    const badgeColor = type === 'Site' ? 'primary' : (type === 'Building' ? 'success' :
                        'warning');
                    const siteName = data.site_name ?
                        `<small class="text-muted ms-2">(Site: ${data.site_name})</small>` : '';

                    html += `
                        <a href="#" class="list-group-item list-group-item-action entity-item"
                           data-type="${type}" data-id="${data.id}" data-site-id="${data.site_id || data.id}">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-${icon} me-2 text-${badgeColor}"></i>
                                <div class="flex-grow-1">
                                    <strong>${displayName}</strong>
                                    ${siteName}
                                </div>
                                <span class="badge bg-${badgeColor}">${type}</span>
                            </div>
                        </a>
                    `;
                });

                html += '</div>';
                entityTree.innerHTML = html;

                // Add click handlers
                document.querySelectorAll('.entity-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const type = this.dataset.type;
                        const id = parseInt(this.dataset.id);
                        const siteId = parseInt(this.dataset.siteId);

                        let entity = null;
                        if (type === 'Site') {
                            entity = allSites.find(s => s.id === id);
                        } else if (type === 'Building') {
                            entity = allBuildings.find(b => b.id === id);
                        } else if (type === 'Land') {
                            entity = allLands.find(l => l.id === id);
                        }

                        if (entity) {
                            // Check if different from original
                            if (type !== originalType || id !== originalId) {
                                showChangeConfirmation(type, entity, siteId);
                            }
                        }
                    });
                });
            }

            function showChangeConfirmation(type, entity, siteId = null) {
                // Store pending selection
                pendingSelection = {
                    type,
                    entity,
                    siteId
                };

                // Show what's changing
                const currentDisplayName = selectedEntity ?
                    `${selectedEntity.entity.code} - ${selectedEntity.entity.name} (${selectedEntity.type})` :
                    'None';
                const newDisplayName = `${entity.code} - ${entity.name} (${type})`;

                currentEntityTextEl.textContent = currentDisplayName;
                newEntityTextEl.textContent = newDisplayName;

                // Show modal
                const modal = new boosted.Modal(document.getElementById('changeEntityModal'));
                modal.show();
            }

            function selectEntity(type, entity, siteId = null, isInitial = false) {
                selectedEntity = {
                    type,
                    entity
                };

                // Update hidden inputs
                typeInput.value = type;
                idInput.value = entity.id;

                // Display selection
                let displayName = '';
                if (type === 'Land') {
                    const parts = [];

                    if (entity.plot_key) parts.push(entity.plot_key);

                    if (entity.directorate) {
                        parts.push(entity.directorate_number ?
                            `${entity.directorate}(${entity.directorate_number})` :
                            entity.directorate);
                    }

                    if (entity.village) {
                        parts.push(entity.village_number ?
                            `${entity.village}(${entity.village_number})` :
                            entity.village);
                    }

                    if (entity.basin) {
                        parts.push(entity.basin_number ?
                            `${entity.basin}(${entity.basin_number})` :
                            entity.basin);
                    }

                    if (entity.neighborhood) parts.push(entity.neighborhood);

                    if (entity.plot_number) parts.push(`Plot ${entity.plot_number}`);

                    displayName = parts.length > 0 ? parts.join(' - ') : 'Land (No data)';
                } else {
                    displayName = `${entity.code || ''} - ${entity.name || ''}`.trim();
                }

                const icon = type === 'Site' ? 'building' : (type === 'Building' ? 'house-door' : 'map');
                const badgeColor = type === 'Site' ? 'primary' : (type === 'Building' ? 'success' : 'warning');

                selectedText.innerHTML = `
                    <i class="bi bi-${icon} me-2"></i>
                    <span class="badge bg-${badgeColor} me-2">${type}</span>
                    ${displayName}
                `;
                selectedDisplay.classList.remove('d-none');

                // Show hierarchy
                if (type === 'Building' || type === 'Land') {
                    const actualSiteId = siteId || entity.site_id;
                    showHierarchy(type, entity.id, actualSiteId);
                } else if (type === 'Site') {
                    showHierarchy('Site', entity.id);
                }
            }

            function showHierarchy(selectedType, selectedId, siteId = null) {
                const targetSiteId = selectedType === 'Site' ? selectedId : siteId;
                const site = allSites.find(s => s.id === targetSiteId);

                if (!site) return;

                const siteBuildings = allBuildings.filter(b => b.site_id === targetSiteId);
                const siteLands = allLands.filter(l => l.site_id === targetSiteId);

                let html = '<div class="hierarchy-tree">';

                // Site header (now clickable in edit mode too, but shows confirmation)
                const siteSelected = selectedType === 'Site' && selectedId === site.id;
                html += `
                    <div class="site-node p-3 mb-2 bg-light border rounded ${siteSelected ? 'border-primary border-2' : ''}"
                         style="cursor: ${siteSelected ? 'default' : 'pointer'};"
                         ${!siteSelected ? `onclick="selectFromTree('Site', ${site.id})"` : ''}>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-building fs-4 me-3 text-primary"></i>
                            <div class="flex-grow-1">
                                <strong>${site.code} - ${site.name}</strong>
                                <br><small class="text-muted">Site</small>
                            </div>
                            ${siteSelected ? '<i class="bi bi-check-circle-fill text-primary fs-4"></i>' : ''}
                        </div>
                    </div>
                `;

                // Buildings
                if (siteBuildings.length > 0) {
                    html += '<div class="ms-4 mb-3">';
                    html +=
                        '<div class="text-muted mb-2"><small><i class="bi bi-house-door me-1"></i> Buildings</small></div>';
                    siteBuildings.forEach(building => {
                        const isSelected = selectedType === 'Building' && selectedId === building.id;
                        html += `
                            <div class="child-node p-2 mb-1 border rounded ${isSelected ? 'border-success border-2 bg-light' : ''}"
                                 style="cursor: ${isSelected ? 'default' : 'pointer'};"
                                 ${!isSelected ? `onclick="selectFromTree('Building', ${building.id})"` : ''}>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-house-door me-2 text-success"></i>
                                    <div class="flex-grow-1">
                                        ${building.code} - ${building.name}
                                    </div>
                                    ${isSelected ? '<i class="bi bi-check-circle-fill text-success"></i>' : ''}
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }

                // Lands
                if (siteLands.length > 0) {
                    html += '<div class="ms-4">';
                    html +=
                    '<div class="text-muted mb-2"><small><i class="bi bi-map me-1"></i> Lands</small></div>';
                    siteLands.forEach(land => {
                        const isSelected = selectedType === 'Land' && selectedId === land.id;

                        const parts = [];

                        if (land.plot_key) parts.push(land.plot_key);

                        if (land.directorate) {
                            parts.push(land.directorate_number ?
                                `${land.directorate}(${land.directorate_number})` :
                                land.directorate);
                        }

                        if (land.village) {
                            parts.push(land.village_number ?
                                `${land.village}(${land.village_number})` :
                                land.village);
                        }

                        if (land.basin) {
                            parts.push(land.basin_number ?
                                `${land.basin}(${land.basin_number})` :
                                land.basin);
                        }

                        if (land.neighborhood) parts.push(land.neighborhood);

                        if (land.plot_number) parts.push(`Plot ${land.plot_number}`);

                        const landDisplay = parts.length > 0 ? parts.join(' - ') : 'Land (No data)';

                        html += `
                            <div class="child-node p-2 mb-1 border rounded ${isSelected ? 'border-warning border-2 bg-light' : ''}"
                                 style="cursor: ${isSelected ? 'default' : 'pointer'};"
                                 ${!isSelected ? `onclick="selectFromTree('Land', ${land.id})"` : ''}>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-map me-2 text-warning"></i>
                                    <div class="flex-grow-1">
                                        ${landDisplay}
                                    </div>
                                    ${isSelected ? '<i class="bi bi-check-circle-fill text-warning"></i>' : ''}
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }

                html += '</div>';
                entityTree.innerHTML = html;
            }

            // Global function for tree selection
            window.selectFromTree = function(type, id) {
                let entity = null;
                if (type === 'Site') {
                    entity = allSites.find(s => s.id === id);
                    if (entity && (type !== originalType || id !== originalId)) {
                        showChangeConfirmation('Site', entity);
                    }
                } else if (type === 'Building') {
                    entity = allBuildings.find(b => b.id === id);
                    if (entity && (type !== originalType || id !== originalId)) {
                        showChangeConfirmation('Building', entity, entity.site_id);
                    }
                } else if (type === 'Land') {
                    entity = allLands.find(l => l.id === id);
                    if (entity && (type !== originalType || id !== originalId)) {
                        showChangeConfirmation('Land', entity, entity.site_id);
                    }
                }
            };

            // Confirm entity change
            document.getElementById('confirmEntityChange').addEventListener('click', function() {
                if (pendingSelection) {
                    const {
                        type,
                        entity,
                        siteId
                    } = pendingSelection;
                    selectEntity(type, entity, siteId);
                    pendingSelection = null;

                    // Close modal
                    const modalElement = document.getElementById('changeEntityModal');
                    const modal = boosted.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });

            // Cancel entity change - revert to original on modal close
            document.getElementById('changeEntityModal').addEventListener('hidden.bs.modal', function() {
                if (pendingSelection) {
                    // User canceled - clear search and show current selection
                    entitySearch.value = '';
                    if (selectedEntity) {
                        const {
                            type,
                            entity
                        } = selectedEntity;
                        if (type === 'Building' || type === 'Land') {
                            showHierarchy(type, entity.id, entity.site_id);
                        } else {
                            showHierarchy('Site', entity.id);
                        }
                    }
                    pendingSelection = null;
                }
            });

            // Clear selection button
            clearBtn.addEventListener('click', function() {
                entitySearch.value = '';
                entitySearch.focus();
                entityTree.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-search fs-3 d-block mb-2"></i>
                        <p class="mb-0">Start typing to search for sites, buildings, or lands</p>
                    </div>
                `;
            });
        });
    </script>
@endpush
