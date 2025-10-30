@props(['images', 'type', 'entityId', 'canEdit' => false, 'title' => 'Gallery', 'showSourceTags' => false])

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-images me-2 text-orange"></i>{{ $title }}
            <span class="badge bg-orange ms-2">{{ count($images) }}</span>
        </h5>
        @if ($canEdit)
            <button type="button" class="btn btn-orange btn-sm" data-bs-toggle="modal"
                data-bs-target="#uploadImagesModal-{{ $type }}-{{ $entityId }}">
                <i class="bi bi-cloud-upload me-1"></i> Upload Images
            </button>
        @endif
    </div>
    <div class="card-body">
        @if (empty($images))
            <div class="text-center py-5 text-muted">
                <i class="bi bi-image" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-3 mb-0">No images uploaded yet.</p>
                @if ($canEdit)
                    <button type="button" class="btn btn-orange btn-sm mt-2" data-bs-toggle="modal"
                        data-bs-target="#uploadImagesModal-{{ $type }}-{{ $entityId }}">
                        <i class="bi bi-cloud-upload me-1"></i> Upload Your First Image
                    </button>
                @endif
            </div>
        @else
            <div class="image-gallery-grid" id="gallery-{{ $type }}-{{ $entityId }}">
                @foreach ($images as $imageData)
                    @php
                        $image = is_array($imageData) ? $imageData['image'] : $imageData;
                        $source = is_array($imageData) ? $imageData['source'] : null;
                        $sourceLabel = is_array($imageData) ? $imageData['source_label'] : null;
                        $isEditable = is_array($imageData) ? $imageData['editable'] : $canEdit;
                    @endphp
                    <div class="gallery-item" data-image-id="{{ $image->id }}">
                        <div class="gallery-image-wrapper">
                            <img src="{{ $image->url }}" alt="{{ $image->title ?? 'Image' }}"
                                class="gallery-thumbnail" loading="lazy">

                            @if ($showSourceTags && $source)
                                <span class="source-badge source-badge-{{ $source }}">
                                    @if ($source === 'site')
                                        <i class="bi bi-building"></i> Site
                                    @elseif($source === 'building')
                                        <i class="bi bi-buildings"></i> Building
                                    @elseif($source === 'land')
                                        <i class="bi bi-map"></i> Land
                                    @endif
                                    @if ($sourceLabel)
                                        <br><small>{{ $sourceLabel }}</small>
                                    @endif
                                </span>
                            @endif

                            @if ($image->is_primary)
                                <span class="primary-badge">
                                    <i class="bi bi-star-fill"></i> Primary
                                </span>
                            @endif

                            <div class="gallery-overlay">
                                <button type="button" class="btn btn-light btn-sm"
                                    onclick="viewImage('{{ $image->url }}', '{{ $image->title ?? '' }}', '{{ $image->description ?? '' }}')">
                                    <i class="bi bi-zoom-in"></i> View
                                </button>
                                @if ($isEditable)
                                    <button type="button" class="btn btn-light btn-sm"
                                        onclick="editImage({{ $image->id }}, '{{ $image->title ?? '' }}', '{{ $image->description ?? '' }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if (!$image->is_primary)
                                        <button type="button" class="btn btn-warning btn-sm"
                                            onclick="setPrimary({{ $image->id }})">
                                            <i class="bi bi-star"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="deleteImage({{ $image->id }}, '{{ $image->imageable_type }}', {{ $image->imageable_id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        @if ($image->title || $image->description)
                            <div class="gallery-info">
                                @if ($image->title)
                                    <h6 class="mb-1">{{ $image->title }}</h6>
                                @endif
                                @if ($image->description)
                                    <p class="text-muted small mb-0">{{ Str::limit($image->description, 60) }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($canEdit && count($images) > 1)
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-orange btn-sm"
                        onclick="enableReorder('{{ $type }}', {{ $entityId }})">
                        <i class="bi bi-arrows-move me-1"></i> Reorder Images
                    </button>
                    <button type="button" class="btn btn-success btn-sm d-none"
                        id="saveOrderBtn-{{ $type }}-{{ $entityId }}"
                        onclick="saveOrder('{{ $type }}', {{ $entityId }})">
                        <i class="bi bi-check-lg me-1"></i> Save Order
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm d-none"
                        id="cancelOrderBtn-{{ $type }}-{{ $entityId }}"
                        onclick="cancelReorder('{{ $type }}', {{ $entityId }})">
                        <i class="bi bi-x-lg me-1"></i> Cancel
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>

@if ($canEdit)
    <!-- Upload Images Modal -->
    <div class="modal fade" id="uploadImagesModal-{{ $type }}-{{ $entityId }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Upload Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('images.upload', ['type' => $type, 'id' => $entityId]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Images</label>
                            <input type="file" name="images[]" class="form-control" accept="image/*" multiple
                                required>
                            <small class="text-muted">Maximum 10MB per file. Accepted: JPEG, PNG, JPG, GIF, WebP</small>
                        </div>
                        <div id="imagePreviewContainer-{{ $type }}-{{ $entityId }}"
                            class="image-preview-grid mt-3"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-orange">
                            <i class="bi bi-upload me-1"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Image Modal -->
    <div class="modal fade" id="editImageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Image Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editImageForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="editImageTitle" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editImageDescription" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-orange">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Image Viewer Modal (PhotoSwipe-like) -->
<div class="modal fade" id="imageViewerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0 bg-transparent text-white">
                <h5 class="modal-title" id="imageViewerTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <div class="image-viewer-container">
                    <img id="imageViewerImg" src="" alt="" class="img-fluid"
                        style="max-height: 70vh; width: auto;">
                </div>
            </div>
            <div class="modal-footer border-0 bg-transparent text-white justify-content-center">
                <p id="imageViewerDescription" class="mb-0"></p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Image Confirmation Modal -->
<div class="modal fade" id="deleteImageModal" tabindex="-1" aria-labelledby="deleteImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteImageModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Image
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this image? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteImageButton">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .image-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .gallery-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        background: #f8f9fa;
        transition: transform 0.2s;
    }

    .gallery-item:hover {
        transform: translateY(-2px);
    }

    .gallery-image-wrapper {
        position: relative;
        padding-top: 100%;
        /* 1:1 Aspect Ratio */
        overflow: hidden;
        background: #e9ecef;
    }

    .gallery-thumbnail {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .gallery-item:hover .gallery-thumbnail {
        transform: scale(1.05);
    }

    .primary-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #ff7900;
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .source-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        text-align: center;
        line-height: 1.2;
    }

    .source-badge small {
        font-size: 0.65rem;
        opacity: 0.9;
    }

    .source-badge-site {
        background: #0d6efd;
    }

    .source-badge-building {
        background: #198754;
    }

    .source-badge-land {
        background: #0dcaf0;
        color: #000;
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        gap: 0.5rem;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-info {
        padding: 0.75rem;
        background: white;
    }

    .gallery-info h6 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #212529;
    }

    .image-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
    }

    .image-viewer-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        background: #000;
    }

    .btn-outline-orange {
        color: #ff7900;
        border-color: #ff7900;
    }

    .btn-outline-orange:hover {
        background: #ff7900;
        color: white;
    }

    /* Sortable styling */
    .gallery-item.sortable-ghost {
        opacity: 0.4;
    }

    .gallery-item.sortable-drag {
        cursor: move;
    }
</style>

<script>
    // View Image in Modal
    function viewImage(url, title, description) {
        document.getElementById('imageViewerImg').src = url;
        document.getElementById('imageViewerTitle').textContent = title || 'Image';
        document.getElementById('imageViewerDescription').textContent = description || '';
        const modal = new boosted.Modal(document.getElementById('imageViewerModal'));
        modal.show();
    }

    // Edit Image
    function editImage(imageId, title, description) {
        document.getElementById('editImageTitle').value = title;
        document.getElementById('editImageDescription').value = description;
        document.getElementById('editImageForm').action = `/images/${imageId}`;
        const modal = new boosted.Modal(document.getElementById('editImageModal'));
        modal.show();
    }

    // Set Primary Image
    function setPrimary(imageId) {
        if (confirm('Set this image as primary?')) {
            fetch(`/images/${imageId}/set-primary`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }
    }

    // Delete Image
    let pendingDeleteImageId = null;

    function deleteImage(imageId, type, entityId) {
        pendingDeleteImageId = imageId;
        const deleteModal = document.getElementById('deleteImageModal');

        if (deleteModal && typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(deleteModal);
            modal.show();
        } else if (deleteModal && typeof boosted !== 'undefined') {
            const modal = new boosted.Modal(deleteModal);
            modal.show();
        } else {
            // Fallback to confirm if modal not available
            if (confirm('Are you sure you want to delete this image?')) {
                performImageDelete(imageId);
            }
        }
    }

    function performImageDelete(imageId) {
        fetch(`/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
    }

        // Image Preview on Upload
    document.addEventListener('DOMContentLoaded', function() {
        const confirmDeleteBtn = document.getElementById('confirmDeleteImageButton');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (pendingDeleteImageId !== null) {
                    performImageDelete(pendingDeleteImageId);

                    // Hide the modal
                    const deleteModal = document.getElementById('deleteImageModal');
                    if (deleteModal) {
                        const modalInstance = bootstrap.Modal.getInstance(deleteModal) ||
                                            boosted.Modal.getInstance(deleteModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }

                    pendingDeleteImageId = null;
                }
            });
        }

        // Reset pending ID when modal is hidden
        const deleteModal = document.getElementById('deleteImageModal');
        if (deleteModal) {
            deleteModal.addEventListener('hidden.bs.modal', function() {
                pendingDeleteImageId = null;
            });
        }

        document.querySelectorAll('input[type="file"][name="images[]"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const container = this.closest('.modal-body').querySelector(
                    '[id^="imagePreviewContainer"]');
                container.innerHTML = '';

                Array.from(this.files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'position-relative';
                            div.innerHTML =
                                `<img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 120px; object-fit: cover;">`;
                            container.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        });
    });

    // Enable Reorder Mode
    let sortableInstance = null;

    function enableReorder(type, entityId) {
        const gallery = document.getElementById(`gallery-${type}-${entityId}`);
        gallery.classList.add('reorder-mode');

        // Show save/cancel buttons
        document.getElementById(`saveOrderBtn-${type}-${entityId}`).classList.remove('d-none');
        document.getElementById(`cancelOrderBtn-${type}-${entityId}`).classList.remove('d-none');

        // Initialize Sortable
        if (typeof Sortable !== 'undefined') {
            sortableInstance = Sortable.create(gallery, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag'
            });
        }
    }

    // Save Order
    function saveOrder(type, entityId) {
        const gallery = document.getElementById(`gallery-${type}-${entityId}`);
        const imageIds = Array.from(gallery.querySelectorAll('.gallery-item')).map(item => item.dataset.imageId);

        fetch(`/images/${type}/${entityId}/reorder`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    order: imageIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
    }

    // Cancel Reorder
    function cancelReorder(type, entityId) {
        location.reload();
    }

    // Image Preview on Upload
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="file"][name="images[]"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const container = this.closest('.modal-body').querySelector(
                    '[id^="imagePreviewContainer"]');
                container.innerHTML = '';

                Array.from(this.files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'position-relative';
                            div.innerHTML =
                                `<img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 120px; object-fit: cover;">`;
                            container.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        });
    });
</script>
