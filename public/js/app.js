/**
 * School Register — Admin Dashboard JS Utilities
 */

// ---- Toast / Flash Messages ----
function showAlert(message, type = 'success', duration = 4000) {
    const icons = { success: '✓', danger: '✕', warning: '⚠', info: 'ℹ' };
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `<span>${icons[type] || ''}</span> ${message}`;
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.minWidth = '280px';
    document.body.appendChild(alert);
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => alert.remove(), 300);
    }, duration);
}

// ---- Modal Control ----
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('show');
        document.body.style.overflow = '';
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.show').forEach(m => {
            m.classList.remove('show');
        });
        document.body.style.overflow = '';
    }
});

// ---- Form Helpers ----
function resetForm(formId) {
    const form = document.getElementById(formId);
    if (form) form.reset();
}

function getFormData(formId) {
    const form = document.getElementById(formId);
    if (!form) return {};
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        if (value !== '') data[key] = value;
    });
    return data;
}

// ---- CSRF Token ----
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// ---- API Helper ----
async function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
    };
    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }
    try {
        const response = await fetch(url, options);
        const json = await response.json();
        if (!response.ok) {
            const message = json.message || `Error ${response.status}`;
            throw { status: response.status, message, errors: json.errors || {} };
        }
        return json;
    } catch (err) {
        if (err.status) throw err;
        throw { status: 0, message: 'Network error. Please try again.' };
    }
}

// ---- Delete Confirmation ----
function confirmDelete(url, itemName = 'item') {
    if (confirm(`Are you sure you want to delete this ${itemName}? This action cannot be undone.`)) {
        apiRequest(url, 'DELETE')
            .then(() => {
                showAlert(`${itemName} deleted successfully.`, 'success');
                setTimeout(() => location.reload(), 800);
            })
            .catch(err => showAlert(err.message, 'danger'));
    }
}

// ---- Form Validation Display ----
function showValidationErrors(errors) {
    // Clear previous errors
    document.querySelectorAll('.form-error').forEach(el => el.remove());
    document.querySelectorAll('.form-control.error').forEach(el => el.classList.remove('error'));

    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('error');
            input.style.borderColor = 'var(--danger)';
            const errorDiv = document.createElement('div');
            errorDiv.className = 'form-error';
            errorDiv.textContent = Array.isArray(messages) ? messages[0] : messages;
            input.parentNode.appendChild(errorDiv);
        }
    }
}

// ---- Sidebar Active State ----
document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(item => {
        const href = item.getAttribute('href');
        if (href && path.startsWith(href) && href !== '/') {
            item.classList.add('active');
        } else if (href === '/' && path === '/') {
            item.classList.add('active');
        }
    });

    // Mobile sidebar toggle
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
});
