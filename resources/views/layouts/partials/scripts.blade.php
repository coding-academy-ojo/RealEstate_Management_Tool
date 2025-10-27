<!-- Boosted JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/boosted@5.3.7/dist/js/boosted.bundle.min.js"
    integrity="sha384-+p7ZVjaaUbkeiut4l53P6U00H3omwqzP9hjYmTXVZOEuLczbmRIDAwEc2uQUbDIV" crossorigin="anonymous">
</script>

<!-- Custom JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle functionality
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');

        // Load sidebar state from localStorage
        function loadSidebarState() {
            const savedState = localStorage.getItem('sidebarState');

            if (window.innerWidth >= 992) {
                if (savedState === 'minimized') {
                    sidebar.classList.add('minimized');
                    content.classList.add('sidebar-minimized');
                }
            }
        }

        // Save sidebar state to localStorage
        function saveSidebarState() {
            if (window.innerWidth >= 992) {
                if (sidebar.classList.contains('minimized')) {
                    localStorage.setItem('sidebarState', 'minimized');
                } else {
                    localStorage.setItem('sidebarState', 'expanded');
                }
            }
        }

        // Toggle sidebar with two states: expanded -> minimized -> expanded
        function toggleSidebar() {
            if (window.innerWidth < 992) {
                // Mobile behavior - simple toggle
                sidebar.classList.toggle('active');
                if (sidebar.classList.contains('active')) {
                    sidebarOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            } else {
                // Desktop behavior - two states
                if (sidebar.classList.contains('minimized')) {
                    sidebar.classList.remove('minimized');
                    content.classList.remove('sidebar-minimized');
                } else {
                    sidebar.classList.add('minimized');
                    content.classList.add('sidebar-minimized');
                }
                saveSidebarState();
            }
        }

        // Close sidebar on small screens when navigating
        function closeSidebarOnMobile() {
            if (window.innerWidth < 992 && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Initialize sidebar state
        loadSidebarState();

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                if (sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            });
        }

        // Close sidebar on navigation for mobile
        document.addEventListener('click', function(e) {
            const link = e.target.closest('.sidebar-link');
            if (link && link.getAttribute('href') && link.getAttribute('href') !== '#') {
                closeSidebarOnMobile();
            }
        });

        // Handle responsive behavior
        function handleResize() {
            if (window.innerWidth >= 992) {
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
                // Restore saved state on desktop
                loadSidebarState();
            } else {
                // Reset to mobile state
                sidebar.classList.remove('minimized');
                content.classList.remove('sidebar-minimized');
                if (sidebar.classList.contains('active')) {
                    sidebarOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }
        }

        window.addEventListener('resize', handleResize);

        // Sidebar Tooltip System
        const tooltipContainer = document.getElementById('sidebar-tooltip');
        let currentTooltip = null;
        let tooltipTimeout = null;
        let hideTimeout = null;
        let currentTriggerElement = null;
        let mouseOverSidebar = false;

        function showTooltip(element) {
            // Only show tooltips when sidebar is minimized and on desktop
            if (window.innerWidth < 992 || !sidebar.classList.contains('minimized')) {
                return;
            }

            const tooltipText = element.getAttribute('data-tooltip');
            if (!tooltipText || !tooltipContainer) return;

            // Clear any existing timeouts
            if (tooltipTimeout) {
                clearTimeout(tooltipTimeout);
                tooltipTimeout = null;
            }
            if (hideTimeout) {
                clearTimeout(hideTimeout);
                hideTimeout = null;
            }

            // Store current trigger element
            currentTriggerElement = element;

            // Remove existing tooltip if different
            if (currentTooltip && currentTooltip.textContent !== tooltipText) {
                if (currentTooltip.parentNode) {
                    currentTooltip.parentNode.removeChild(currentTooltip);
                }
                currentTooltip = null;
            }

            // Don't create if same tooltip already exists and is visible
            if (currentTooltip && currentTooltip.textContent === tooltipText && currentTooltip.classList
                .contains('show')) {
                return;
            }

            // Create tooltip element
            const tooltip = document.createElement('div');
            tooltip.className = 'sidebar-tooltip';
            tooltip.textContent = tooltipText;

            // Add to container first to measure
            tooltipContainer.appendChild(tooltip);
            currentTooltip = tooltip;

            // Position tooltip
            const rect = element.getBoundingClientRect();

            // Position to the right of the sidebar
            const left = rect.right + 10;
            const top = rect.top + (rect.height / 2);

            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';

            // Add hover events to tooltip itself to keep it visible
            tooltip.addEventListener('mouseenter', cancelHide);
            tooltip.addEventListener('mouseleave', scheduleHide);

            // Show tooltip with animation
            requestAnimationFrame(() => {
                tooltip.classList.add('show');
            });
        }

        function scheduleHide() {
            // Don't hide if mouse is still over sidebar area
            if (mouseOverSidebar) {
                return;
            }

            // Clear any existing hide timeout
            if (hideTimeout) {
                clearTimeout(hideTimeout);
            }

            // Schedule hide with a small delay
            hideTimeout = setTimeout(() => {
                // Double check mouse isn't over sidebar when timeout executes
                if (!mouseOverSidebar) {
                    hideTooltip();
                }
            }, 150);
        }

        function cancelHide() {
            if (hideTimeout) {
                clearTimeout(hideTimeout);
                hideTimeout = null;
            }
        }

        function hideTooltip() {
            if (currentTooltip) {
                currentTooltip.classList.remove('show');
                setTimeout(() => {
                    if (currentTooltip && currentTooltip.parentNode) {
                        currentTooltip.parentNode.removeChild(currentTooltip);
                    }
                    currentTooltip = null;
                    currentTriggerElement = null;
                }, 200);
            }

            // Clear hide timeout
            if (hideTimeout) {
                clearTimeout(hideTimeout);
                hideTimeout = null;
            }
        }

        // Initialize tooltip system only if container exists
        if (tooltipContainer) {
            // Track mouse over sidebar area
            sidebar.addEventListener('mouseenter', () => {
                mouseOverSidebar = true;
            });

            sidebar.addEventListener('mouseleave', () => {
                mouseOverSidebar = false;
                scheduleHide();
            });

            // Add event listeners to sidebar links
            const sidebarLinks = sidebar.querySelectorAll('.sidebar-link[data-tooltip]');
            sidebarLinks.forEach(link => {
                link.addEventListener('mouseenter', () => {
                    showTooltip(link);
                });
                // Remove individual mouseleave handlers as we now handle it at sidebar level
            });

            // Hide tooltip when sidebar is toggled
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', hideTooltip);
            }

            // Hide tooltip on window resize
            window.addEventListener('resize', hideTooltip);
        }

        // Initialize tooltips for non-sidebar elements
        const tooltipTriggerList = document.querySelectorAll(
            '[data-bs-toggle="tooltip"]:not(#sidebar [data-bs-toggle="tooltip"])');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
            tooltipTriggerEl));
    });

    // Notification functions
    function markAsReadAndRedirect(notificationId, redirectUrl) {
        // Prevent the default link behavior
        event.preventDefault();

        fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to the page
                    window.location.href = redirectUrl;
                } else {
                    console.error('Failed to mark notification as read');
                    // Still redirect even if marking as read fails
                    window.location.href = redirectUrl;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Still redirect even if there's an error
                window.location.href = redirectUrl;
            });
    }

    function markAsRead(notificationId, redirectUrl = null) {
        fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && redirectUrl && redirectUrl !== '#') {
                    window.location.href = redirectUrl;
                } else {
                    // Refresh the page to update notification count
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function markAllAsRead() {
        fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Close dropdown when clicking on notification
    document.addEventListener('DOMContentLoaded', function() {
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function() {
                // Close the dropdown
            });
        });
    });
</script>

<!-- Additional scripts from sections -->
@yield('scripts')

<!-- Page-specific scripts pushed with @push('scripts') -->
    @stack('scripts')
