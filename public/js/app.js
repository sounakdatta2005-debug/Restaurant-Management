/**
 * Restaurant Management System - Main SPA Application
 * Vanilla JavaScript with Fetch API
 * 
 * This file handles SPA routing, dynamic page loading, and navigation
 */

class RestaurantSPA {
    constructor() {
        this.currentPage = null;
        this.baseUrl = '/restaurant-management/public';
        this.apiBaseUrl = this.baseUrl + '/api';
        this.init();
    }

    /**
     * Initialize the SPA
     */
    init() {
        this.setupEventListeners();

        const content = document.getElementById('app-content');
        if (content && content.children.length === 0) {
            this.loadPage('dashboard');
        }
    }

    /**
     * Setup event listeners for navigation
     */
    setupEventListeners() {
        // Handle internal links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[data-link]');
            if (link) {
                e.preventDefault();
                const page = link.dataset.link;
                this.loadPage(page);
                this.updateActiveNav(link);
            }
        });

        // Handle form submissions (API calls)
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.dataset.api) {
                e.preventDefault();
                this.handleFormSubmit(form);
            }
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.page) {
                this.loadPage(e.state.page);
            }
        });
    }

    /**
     * Load a page dynamically
     */
    async loadPage(page, options = {}) {
        try {
            // Show loading state
            this.showLoading();

            const url = `${this.baseUrl}/${page}`;
            const response = await fetch(url);

            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = `${this.baseUrl}/login`;
                    return;
                }
                throw new Error(`Page load failed with status ${response.status}`);
            }

            const html = await response.text();
            const container = document.getElementById('app-content');

            if (!container) {
                console.error('App content container not found');
                return;
            }

            // Fade out effect
            container.style.opacity = '0';
            setTimeout(() => {
                container.innerHTML = html;
                // Fade in effect
                container.style.opacity = '1';
                this.currentPage = page;

                // Push to browser history
                window.history.pushState({ page: page }, page, `${this.baseUrl}/${page}`);

                // Reinitialize components on new page
                this.initializePageComponents();
            }, 300);

        } catch (error) {
            console.error('Error loading page:', error);
            this.showError('Failed to load page. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Initialize page-specific components and scripts
     */
    initializePageComponents() {
        // Re-bind event listeners if needed
        this.setupEventListeners();
        
        // Initialize any data-tables, charts, etc.
        if (typeof initPageScripts === 'function') {
            initPageScripts();
        }
    }

    /**
     * Handle form submission via API
     */
    async handleFormSubmit(form) {
        try {
            const apiEndpoint = form.dataset.api;
            const method = form.method.toUpperCase() || 'POST';
            const formData = new FormData(form);
            
            // Show loading state
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Loading...';
            }

            const response = await this.apiCall(apiEndpoint, method, formData);

            if (response.success) {
                this.showSuccess(response.message || 'Operation successful!');
                
                // Redirect if specified
                if (response.redirect) {
                    setTimeout(() => this.loadPage(response.redirect), 1000);
                }
                
                // Clear form if needed
                if (form.dataset.clearOnSuccess !== 'false') {
                    form.reset();
                }
                
                // Trigger custom callback if specified
                if (form.dataset.onSuccess && typeof window[form.dataset.onSuccess] === 'function') {
                    window[form.dataset.onSuccess](response);
                }
            } else {
                this.showError(response.message || 'Operation failed. Please try again.');
            }

        } catch (error) {
            console.error('Form submission error:', error);
            this.showError('An error occurred. Please try again.');
        } finally {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.dataset.originalText || 'Submit';
            }
        }
    }

    /**
     * Make API call using Fetch
     */
    async apiCall(endpoint, method = 'GET', data = null) {
        const url = `${this.apiBaseUrl}/${endpoint}`;
        const options = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        };

        // Handle request body
        if (data) {
            if (data instanceof FormData) {
                options.body = data;
            } else {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(data);
            }
        }

        const response = await fetch(url, options);
        const result = await response.json();

        if (!response.ok && result.code === 401) {
            // Token expired or unauthorized
            window.location.href = `${this.baseUrl}/login`;
            return;
        }

        return result;
    }

    /**
     * Update active navigation link
     */
    updateActiveNav(link) {
        document.querySelectorAll('a[data-link]').forEach(el => {
            el.classList.remove('active');
        });
        link.classList.add('active');
    }

    /**
     * Show loading indicator
     */
    showLoading() {
        const loader = document.getElementById('loader');
        if (loader) {
            loader.style.display = 'flex';
        }
    }

    /**
     * Hide loading indicator
     */
    hideLoading() {
        const loader = document.getElementById('loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4CAF50' : '#f44336'};
            color: white;
            border-radius: 4px;
            z-index: 9999;
            animation: slideIn 0.3s ease-in-out;
        `;
        
        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Initialize SPA when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.app = new RestaurantSPA();
});

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    #app-content {
        transition: opacity 0.3s ease-in-out;
    }

    [type="submit"]:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
`;
document.head.appendChild(style);
