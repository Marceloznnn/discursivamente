/**
 * Discursivamente Admin Dashboard JavaScript
 * Version: 1.0.2
 * 
 * This file handles all the interactive functionality for the admin dashboard
 * including sidebar toggling, notifications, user menu, theme switching,
 * and responsive behavior.
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // Core elements
    const adminWrapper = document.getElementById('adminWrapper');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const themeToggle = document.getElementById('themeToggle');
    const userMenuToggle = document.getElementById('userMenuToggle');
    const notificationToggle = document.getElementById('notificationToggle');
    const globalSearch = document.getElementById('globalSearch');
    
    // Initialize the UI state
    initializeUI();
    
    /**
     * Initialize the UI state based on saved preferences
     */
    function initializeUI() {
        // Check for saved sidebar state
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed) {
            toggleSidebar();
        }
        
        // Check for saved theme preference
        const darkMode = localStorage.getItem('darkMode') === 'true';
        if (darkMode) {
            toggleTheme();
        }
        
        // Initialize event listeners
        setupEventListeners();
        
        // Fade in the admin wrapper
        if (adminWrapper) {
            adminWrapper.classList.add('animate-fadeIn');
        }
        
        // Set up submenu expansion for active items
        setupSubmenus();
    }
    
    /**
     * Set up all event listeners for the admin interface
     */
    function setupEventListeners() {
        // Sidebar toggle button
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                toggleSidebar();
            });
        }
        
        // Mobile sidebar toggle button
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('mobile-open');
                
                // Add overlay when sidebar is open on mobile
                if (adminSidebar.classList.contains('mobile-open')) {
                    createOverlay();
                } else {
                    removeOverlay();
                }
            });
        }
        
        // Theme toggle button
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                toggleTheme();
            });
        }
        
        // User menu toggle
        if (userMenuToggle) {
            const userDropdown = userMenuToggle.parentElement.querySelector('.user-dropdown');
            userMenuToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDropdown(userMenuToggle.parentElement, userDropdown);
            });
        }
        
        // Notification toggle
        if (notificationToggle) {
            const notificationsDropdown = notificationToggle.parentElement.querySelector('.notifications-dropdown');
            notificationToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDropdown(notificationToggle.parentElement, notificationsDropdown);
                
                // Pulse animation for unread notifications
                if (notificationToggle.querySelector('.notification-badge')) {
                    notificationToggle.querySelector('.notification-badge').classList.add('animate-pulse');
                }
            });
            
            // Handle mark all as read button
            const markReadBtn = document.querySelector('.mark-read');
            if (markReadBtn) {
                markReadBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    markAllAsRead();
                });
            }
        }
        
        // Global search keyboard shortcut ('/')
        document.addEventListener('keydown', (e) => {
            if (e.key === '/' && document.activeElement !== globalSearch) {
                e.preventDefault();
                if (globalSearch) {
                    globalSearch.focus();
                }
            }

            // Escape key to close dropdowns and modals
            if (e.key === 'Escape') {
                closeAllDropdowns();
                removeOverlay();
                if (adminSidebar) {
                    adminSidebar.classList.remove('mobile-open');
                }
            }
        });
        
        // Global search
        if (globalSearch) {
            globalSearch.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') {
                    performSearch(globalSearch.value);
                }
            });
        }
        
        // Click outside to close dropdowns
        document.addEventListener('click', () => {
            closeAllDropdowns();
        });
        
        // Prevent clicks inside dropdowns from closing them
        const dropdowns = document.querySelectorAll('.user-dropdown, .notifications-dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });
        
        // Handle window resize for mobile/desktop transitions
        window.addEventListener('resize', handleResize);
    }
    
    /**
     * Toggle sidebar collapsed state
     */
    function toggleSidebar() {
        if (!adminWrapper || !adminSidebar) return;
        
        adminWrapper.classList.toggle('sidebar-collapsed');
        const isCollapsed = adminWrapper.classList.contains('sidebar-collapsed');
        
        // Save preference to localStorage
        localStorage.setItem('sidebarCollapsed', isCollapsed);
        
        // Announce state change for screen readers
        const state = isCollapsed ? 'recolhida' : 'expandida';
        announceToScreenReader(`Barra lateral ${state}`);
    }
    
    /**
     * Toggle dark/light theme
     */
    function toggleTheme() {
        document.body.classList.toggle('dark-theme');
        const isDarkMode = document.body.classList.contains('dark-theme');
        
        // Update theme toggle icon
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
        
        // Save preference to localStorage
        localStorage.setItem('darkMode', isDarkMode);
        
        // Announce theme change for screen readers
        const theme = isDarkMode ? 'escuro' : 'claro';
        announceToScreenReader(`Tema ${theme} ativado`);
    }
    
    /**
     * Toggle dropdown visibility
     * @param {HTMLElement} parent - Parent element containing the dropdown
     * @param {HTMLElement} dropdown - The dropdown element to toggle
     */
    function toggleDropdown(parent, dropdown) {
        // Close all other dropdowns first
        closeAllDropdowns();
        
        // Toggle this dropdown
        if (parent && dropdown) {
            parent.classList.toggle('active');
            dropdown.classList.toggle('active');
        }
    }
    
    /**
     * Close all open dropdowns
     */
    function closeAllDropdowns() {
        const activeParents = document.querySelectorAll('.user-menu.active, .header-notifications.active');
        const activeDropdowns = document.querySelectorAll('.user-dropdown.active, .notifications-dropdown.active');
        
        activeParents.forEach(item => item.classList.remove('active'));
        activeDropdowns.forEach(dropdown => dropdown.classList.remove('active'));
    }
    
    /**
     * Mark all notifications as read
     */
    function markAllAsRead() {
        // Remove all notification badges
        const notificationBadges = document.querySelectorAll('.notification-badge');
        notificationBadges.forEach(badge => {
            badge.classList.add('animate-fadeIn');
            setTimeout(() => {
                badge.remove();
            }, 300);
        });
        
        // Update notification items to mark as read
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.style.opacity = '0.6';
        });
        
        // Show confirmation message in empty notification container
        const notificationsList = document.querySelector('.notifications-list');
        if (notificationsList) {
            setTimeout(() => {
                notificationsList.innerHTML = `
                    <div class="notification-empty">
                        <p>Todas as notificações foram marcadas como lidas</p>
                    </div>
                `;
            }, 500);
        }
        
        // Send read status to server (simulated)
        console.log('Notifications marked as read');
    }
    
    /**
     * Create overlay for mobile sidebar
     */
    function createOverlay() {
        // Remove any existing overlay first
        removeOverlay();
        
        // Create new overlay
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        overlay.style.zIndex = '98';
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.3s ease';
        
        // Add click event to close sidebar
        overlay.addEventListener('click', () => {
            if (adminSidebar) {
                adminSidebar.classList.remove('mobile-open');
            }
            removeOverlay();
        });
        
        // Add to document and animate in
        document.body.appendChild(overlay);
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
    }
    
    /**
     * Remove sidebar overlay
     */
    function removeOverlay() {
        const overlay = document.querySelector('.sidebar-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }
    }
    
    /**
     * Handle window resize events
     */
    function handleResize() {
        // Check window width for mobile/desktop views
        const isMobile = window.innerWidth < 768;
        
        // Handle sidebar state on resize
        if (!isMobile && adminSidebar) {
            adminSidebar.classList.remove('mobile-open');
            removeOverlay();
        }
        
        // Close dropdowns on resize
        closeAllDropdowns();
    }
    
    /**
     * Setup submenus for active nav items
     */
    function setupSubmenus() {
        const navItems = document.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            const subMenu = item.querySelector('.sub-menu');
            
            // Only add click handlers if there's a submenu
            if (link && subMenu && !item.classList.contains('active')) {
                link.addEventListener('click', (e) => {
                    // Only prevent default if it has a submenu and we're not on mobile
                    if (window.innerWidth >= 768 && adminWrapper && !adminWrapper.classList.contains('sidebar-collapsed')) {
                        e.preventDefault();
                        
                        // Toggle active state for this item
                        navItems.forEach(otherItem => {
                            if (otherItem !== item && !otherItem.classList.contains('active')) {
                                otherItem.classList.remove('submenu-open');
                                const otherSubMenu = otherItem.querySelector('.sub-menu');
                                if (otherSubMenu) {
                                    slideUp(otherSubMenu);
                                }
                            }
                        });
                        
                        item.classList.toggle('submenu-open');
                        
                        // Show/hide submenu with animation
                        if (item.classList.contains('submenu-open')) {
                            slideDown(subMenu);
                        } else {
                            slideUp(subMenu);
                        }
                    }
                });
            }
        });
    }
    
    /**
     * Slide down animation
     * @param {HTMLElement} element - Element to slide down
     */
    function slideDown(element) {
        element.style.display = 'block';
        element.style.height = '0';
        const height = element.scrollHeight;
        element.style.overflow = 'hidden';
        element.style.height = '0';
        element.style.transition = 'height 0.3s ease';
        
        setTimeout(() => {
            element.style.height = height + 'px';
        }, 10);
        
        setTimeout(() => {
            element.style.height = '';
            element.style.overflow = '';
        }, 300);
    }
    
    /**
     * Slide up animation
     * @param {HTMLElement} element - Element to slide up
     */
    function slideUp(element) {
        element.style.overflow = 'hidden';
        element.style.height = element.scrollHeight + 'px';
        element.style.transition = 'height 0.3s ease';
        
        setTimeout(() => {
            element.style.height = '0';
        }, 10);
        
        setTimeout(() => {
            element.style.display = 'none';
            element.style.height = '';
            element.style.overflow = '';
        }, 300);
    }
    
    /**
     * Perform global search
     * @param {string} query - Search query
     */
    function performSearch(query) {
        if (!query.trim()) return;
        
        console.log(`Searching for: ${query}`);
        
        // Show search loading state
        if (globalSearch) {
            const searchIcon = globalSearch.nextElementSibling;
            if (searchIcon) {
                searchIcon.className = 'fas fa-spinner fa-spin';
            }
            
            // Simulate search delay
            setTimeout(() => {
                // Restore search icon
                if (searchIcon) {
                    searchIcon.className = 'fas fa-search';
                }
                
                // Here you would normally navigate to search results page
                // For demo, we'll just log the results
                console.log('Search complete');
                
                // Example of how you might navigate to search results page:
                // window.location.href = `/admin/search?q=${encodeURIComponent(query)}`;
            }, 800);
        }
    }
    
    /**
     * Announce message to screen readers
     * @param {string} message - Message to announce
     */
    function announceToScreenReader(message) {
        let ariaLive = document.getElementById('aria-live');
        
        if (!ariaLive) {
            ariaLive = document.createElement('div');
            ariaLive.id = 'aria-live';
            ariaLive.setAttribute('aria-live', 'polite');
            ariaLive.classList.add('sr-only');
            document.body.appendChild(ariaLive);
            
            // Add SR-only style if not present in CSS
            const style = document.createElement('style');
            style.textContent = `
                .sr-only {
                    position: absolute;
                    width: 1px;
                    height: 1px;
                    padding: 0;
                    margin: -1px;
                    overflow: hidden;
                    clip: rect(0, 0, 0, 0);
                    white-space: nowrap;
                    border: 0;
                }
            `;
            document.head.appendChild(style);
        }
        
        ariaLive.textContent = message;
    }
    
    /**
     * Initialize dynamic loading of page content
     * Useful for SPA-like behavior (optional implementation)
     */
    function initDynamicPageLoading() {
        // Get all navigation links that should load content dynamically
        const navLinks = document.querySelectorAll('.nav-link:not([data-static])');
        
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                
                // Skip if it's just a hash or javascript: link
                if (href.startsWith('#') || href.startsWith('javascript:')) {
                    return;
                }
                
                e.preventDefault();
                
                // Set active state
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                link.closest('.nav-item').classList.add('active');
                
                // Update breadcrumbs (ideally would come from the loaded content)
                updateBreadcrumbs(link.textContent.trim(), href);
                
                // Load content
                loadContent(href);
                
                // Update URL without page reload
                history.pushState(null, null, href);
            });
        });
        
        // Handle browser back/forward navigation
        window.addEventListener('popstate', () => {
            loadContent(window.location.pathname);
        });
    }
    
    /**
     * Load content via AJAX (implementation depends on your backend)
     * @param {string} url - URL to load content from
     */
    function loadContent(url) {
        const contentContainer = document.querySelector('.admin-content');
        if (!contentContainer) return;
        
        // Show loading indicator
        contentContainer.innerHTML = '<div class="loading-spinner flex-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
        
        // Fetch content
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                // Extract just the content part (this depends on your HTML structure)
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.admin-content');
                
                if (newContent) {
                    contentContainer.innerHTML = newContent.innerHTML;
                } else {
                    contentContainer.innerHTML = html;
                }
                
                // Update page title if available
                const newTitle = doc.querySelector('title');
                if (newTitle) {
                    document.title = newTitle.textContent;
                }
                
                // Re-initialize any needed scripts for the new content
                initializeContentScripts();
            })
            .catch(error => {
                console.error('Error loading content:', error);
                contentContainer.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Erro ao carregar conteúdo</h3>
                        <p>Não foi possível carregar o conteúdo solicitado. Por favor, tente novamente.</p>
                        <button class="btn-primary mt-3" onclick="window.location.reload()">Recarregar</button>
                    </div>
                `;
            });
    }
    
    /**
     * Update breadcrumbs based on navigation
     * @param {string} title - Page title
     * @param {string} url - Page URL
     */
    function updateBreadcrumbs(title, url) {
        const breadcrumbs = document.querySelector('.breadcrumbs');
        if (!breadcrumbs) return;
        
        // Extract module and action from URL
        const parts = url.split('/').filter(Boolean);
        const isAdmin = parts[0] === 'admin';
        const module = isAdmin && parts.length > 1 ? parts[1] : null;
        const action = isAdmin && parts.length > 2 ? parts[2] : null;
        
        let breadcrumbsHTML = '<a href="/admin">Admin</a>';
        
        if (module) {
            breadcrumbsHTML += `<i class="fas fa-chevron-right"></i>
                               <a href="/admin/${module}">${capitalizeFirstLetter(module)}</a>`;
        }
        
        if (action) {
            breadcrumbsHTML += `<i class="fas fa-chevron-right"></i>
                               <span>${capitalizeFirstLetter(action)}</span>`;
        }
        
        breadcrumbs.innerHTML = breadcrumbsHTML;
    }
    
    /**
     * Capitalize first letter of a string
     * @param {string} string - String to capitalize
     * @returns {string} Capitalized string
     */
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    
    /**
     * Initialize scripts needed for dynamically loaded content
     */
    function initializeContentScripts() {
        // This function would initialize any scripts needed for newly loaded content
        // For example, datepickers, form validation, etc.
        console.log('Initializing content scripts');
        
        // Example: Initialize form validation
        const forms = document.querySelectorAll('form.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
        
        // Example: Initialize tooltips
        const tooltips = document.querySelectorAll('[data-toggle="tooltip"]');
        if (typeof bootstrap !== 'undefined' && tooltips.length) {
            tooltips.forEach(tooltip => {
                new bootstrap.Tooltip(tooltip);
            });
        }
    }
    
    // Optional: Initialize dynamic page loading
    // Uncomment this if you want SPA-like behavior
    // initDynamicPageLoading();
});