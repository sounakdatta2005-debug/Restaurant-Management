/**
 * Utility Functions for Restaurant Management System
 */

/**
 * Format currency
 */
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(amount);
}

/**
 * Format date and time
 */
function formatDateTime(dateString, format = 'short') {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;

    const options = {
        short: { year: 'numeric', month: 'short', day: 'numeric' },
        long: { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' },
        time: { hour: '2-digit', minute: '2-digit' },
    };

    return date.toLocaleDateString('en-US', options[format] || options.short);
}

/**
 * Capitalize first letter
 */
function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Deep clone object
 */
function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

/**
 * Debounce function
 */
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Get URL parameter
 */
function getUrlParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

/**
 * Create table from array of objects
 */
function createTableFromData(data, containerId) {
    const container = document.getElementById(containerId);
    if (!container || !data || data.length === 0) return;

    const table = document.createElement('table');
    table.className = 'data-table';

    // Create header
    const thead = table.createTHead();
    const headerRow = thead.insertRow();
    const keys = Object.keys(data[0]);
    
    keys.forEach(key => {
        const th = document.createElement('th');
        th.textContent = capitalize(key.replace(/_/g, ' '));
        headerRow.appendChild(th);
    });

    // Create body
    const tbody = table.createTBody();
    data.forEach(item => {
        const row = tbody.insertRow();
        keys.forEach(key => {
            const cell = row.insertCell();
            cell.textContent = item[key] || '-';
        });
    });

    container.innerHTML = '';
    container.appendChild(table);
}

/**
 * Validate email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate password strength
 */
function validatePasswordStrength(password) {
    const strength = {
        score: 0,
        feedback: []
    };

    if (password.length >= 8) strength.score++;
    else strength.feedback.push('Password should be at least 8 characters');

    if (/[a-z]/.test(password)) strength.score++;
    else strength.feedback.push('Add lowercase letters');

    if (/[A-Z]/.test(password)) strength.score++;
    else strength.feedback.push('Add uppercase letters');

    if (/[0-9]/.test(password)) strength.score++;
    else strength.feedback.push('Add numbers');

    if (/[^a-zA-Z0-9]/.test(password)) strength.score++;
    else strength.feedback.push('Add special characters');

    return strength;
}

/**
 * Get status badge HTML
 */
function getStatusBadge(status) {
    const statusColors = {
        'available': 'green',
        'occupied': 'red',
        'reserved': 'yellow',
        'pending': 'orange',
        'preparing': 'blue',
        'served': 'purple',
        'paid': 'green',
        'cancelled': 'red',
        'active': 'green',
        'inactive': 'gray',
    };

    const color = statusColors[status] || 'gray';
    return `<span class="badge badge-${color}">${capitalize(status)}</span>`;
}

/**
 * Poll API endpoint for real-time updates
 */
function pollAPI(endpoint, callback, interval = 5000) {
    let pollInterval;
    
    const startPolling = async () => {
        try {
            const response = await fetch(endpoint);
            const data = await response.json();
            callback(data);
        } catch (error) {
            console.error('Polling error:', error);
        }
    };

    // Initial call
    startPolling();

    // Set up interval
    pollInterval = setInterval(startPolling, interval);

    // Return function to stop polling
    return () => clearInterval(pollInterval);
}

/**
 * Export data to CSV
 */
function exportToCSV(data, filename = 'export.csv') {
    if (!data || data.length === 0) return;

    const headers = Object.keys(data[0]);
    const csv = [
        headers.join(','),
        ...data.map(row => 
            headers.map(header => {
                const value = row[header];
                // Escape quotes and wrap in quotes if contains comma
                if (typeof value === 'string' && (value.includes(',') || value.includes('"'))) {
                    return `"${value.replace(/"/g, '""')}"`;
                }
                return value;
            }).join(',')
        )
    ].join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
    window.URL.revokeObjectURL(url);
}

/**
 * Simple chart library (using canvas)
 */
class SimpleChart {
    constructor(canvasId, type = 'bar') {
        this.canvas = document.getElementById(canvasId);
        this.type = type;
        this.ctx = this.canvas?.getContext('2d');
    }

    drawBarChart(data, labels) {
        // This is a simplified version. For production, use Chart.js library
        console.log('Drawing bar chart:', data, labels);
    }
}
