/**
 * Modern Modal Utility Library
 * Provides accessible, styled modal dialogs to replace browser alerts/confirms
 * 
 * Usage:
 * - Modal.confirm(title, message, onConfirm, onCancel)
 * - Modal.alert(title, message, type)
 * - Modal.toast(message, type)
 */

class ModalUtility {
    constructor() {
        this.activeModals = new Set();
        this.setupGlobalStyles();
    }

    /**
     * Setup global modal styles if not already present
     */
    setupGlobalStyles() {
        if (!document.querySelector('#modal-utility-styles')) {
            const linkElement = document.createElement('link');
            linkElement.id = 'modal-utility-styles';
            linkElement.rel = 'stylesheet';
            linkElement.href = './src/css/modal.css';
            document.head.appendChild(linkElement);
        }
    }

    /**
     * Show confirmation dialog
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {function} onConfirm - Callback for confirm action
     * @param {function} onCancel - Callback for cancel action
     * @param {object} options - Additional options
     */
    confirm(title, message, onConfirm, onCancel, options = {}) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'modal-title');
        modal.setAttribute('aria-describedby', 'modal-description');
        
        const confirmText = options.confirmText || 'Confirm';
        const cancelText = options.cancelText || 'Cancel';
        const confirmClass = options.confirmClass || 'btn-danger';
        
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modal-title" class="modal-title">${this.escapeHtml(title)}</h3>
                    <button class="modal-close" aria-label="Close modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p id="modal-description">${this.escapeHtml(message)}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary modal-cancel">${this.escapeHtml(cancelText)}</button>
                    <button class="btn ${confirmClass} modal-confirm">${this.escapeHtml(confirmText)}</button>
                </div>
            </div>
        `;
        
        this.showModal(modal, onConfirm, onCancel);
        return modal;
    }

    /**
     * Show alert/notification dialog
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {string} type - Type: 'info', 'success', 'warning', 'error'
     * @param {function} onClose - Callback when modal closes
     */
    alert(title, message, type = 'info', onClose = null) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay notification-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'notification-title');
        
        const iconMap = {
            'info': 'ℹ️',
            'success': '✅',
            'warning': '⚠️',
            'error': '❌'
        };
        
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="notification-title" class="modal-title">
                        <span class="modal-icon">${iconMap[type] || iconMap.info}</span>
                        ${this.escapeHtml(title)}
                    </h3>
                    <button class="modal-close" aria-label="Close notification">&times;</button>
                </div>
                <div class="modal-body">
                    <p>${this.escapeHtml(message)}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary modal-ok">OK</button>
                </div>
            </div>
        `;
        
        this.showNotificationModal(modal, onClose);
        return modal;
    }

    /**
     * Show toast notification
     * @param {string} message - Toast message
     * @param {string} type - Type: 'success', 'error', 'info', 'warning'
     * @param {number} duration - Auto-hide duration in ms (default: 3000)
     */
    toast(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        
        const iconMap = {
            'success': '✓',
            'error': '✗',
            'warning': '⚠',
            'info': 'ℹ'
        };
        
        toast.innerHTML = `
            <span class="toast-icon">${iconMap[type] || iconMap.info}</span>
            <span class="toast-message">${this.escapeHtml(message)}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Show animation
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });
        
        // Auto-hide
        setTimeout(() => {
            toast.classList.add('fade-out');
            toast.addEventListener('transitionend', () => {
                if (toast.parentNode) {
                    toast.remove();
                }
            });
        }, duration);

        return toast;
    }

    /**
     * Show modal with confirmation handling
     */
    showModal(modal, onConfirm, onCancel) {
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        this.activeModals.add(modal);
        
        const confirmBtn = modal.querySelector('.modal-confirm');
        const cancelBtn = modal.querySelector('.modal-cancel');
        const closeBtn = modal.querySelector('.modal-close');
        
        confirmBtn.focus();
        
        const handleConfirm = () => {
            this.closeModal(modal);
            if (onConfirm) onConfirm();
        };
        
        const handleCancel = () => {
            this.closeModal(modal);
            if (onCancel) onCancel();
        };
        
        // Event listeners
        confirmBtn.addEventListener('click', handleConfirm);
        cancelBtn.addEventListener('click', handleCancel);
        closeBtn.addEventListener('click', handleCancel);
        
        // Close on overlay click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) handleCancel();
        });
        
        // Keyboard navigation
        this.setupKeyboardNavigation(modal, handleConfirm, handleCancel);
        
        // Show animation
        requestAnimationFrame(() => {
            modal.classList.add('show');
        });
    }

    /**
     * Show notification modal
     */
    showNotificationModal(modal, onClose) {
        document.body.appendChild(modal);
        this.activeModals.add(modal);
        
        const okBtn = modal.querySelector('.modal-ok');
        const closeBtn = modal.querySelector('.modal-close');
        
        okBtn.focus();
        
        const handleClose = () => {
            this.closeModal(modal);
            if (onClose) onClose();
        };
        
        okBtn.addEventListener('click', handleClose);
        closeBtn.addEventListener('click', handleClose);
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) handleClose();
        });
        
        modal.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Enter') {
                handleClose();
            }
        });
        
        // Show animation
        requestAnimationFrame(() => {
            modal.classList.add('show');
        });
    }

    /**
     * Setup keyboard navigation for modal
     */
    setupKeyboardNavigation(modal, onConfirm, onCancel) {
        modal.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                onCancel();
            } else if (e.key === 'Tab') {
                // Tab trapping
                const focusableElements = modal.querySelectorAll('button');
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];
                
                if (e.shiftKey && document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                } else if (!e.shiftKey && document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
    }

    /**
     * Close modal and cleanup
     */
    closeModal(modal) {
        this.activeModals.delete(modal);
        
        // Restore scroll if no active modals
        if (this.activeModals.size === 0) {
            document.body.style.overflow = '';
        }
        
        modal.remove();
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Close all active modals
     */
    closeAll() {
        this.activeModals.forEach(modal => {
            modal.remove();
        });
        this.activeModals.clear();
        document.body.style.overflow = '';
    }
}

// Create global instance
const Modal = new ModalUtility();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModalUtility;
}

// Legacy browser compatibility
window.Modal = Modal;