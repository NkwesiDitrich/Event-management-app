class EventRegistration {
    constructor() {
        this.initializeRegistrationButtons();
        this.setupCSRFToken();
    }

    setupCSRFToken() {
        // Ensure all AJAX requests include the CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Override fetch to include CSRF token by default
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            const options = args[1] || {};
            options.headers = {
                ...options.headers,
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            args[1] = options;
            return originalFetch.apply(this, args);
        };
    }

    initializeRegistrationButtons() {
        const registrationButtons = document.querySelectorAll('.registration-btn');
        
        registrationButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleRegistration(button);
            });
        });
    }

    async handleRegistration(button) {
        const eventId = button.dataset.eventId;
        const action = button.dataset.action; // 'register' or 'unregister'

        // Store original state for restoration
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = 'Processing...';

        try {
            const response = await fetch(`/events/${eventId}/${action}`, {
                method: 'POST'
            });

            const data = await response.json();

            if (response.ok) {
                this.updateRegistrationUI(button, data);
                this.showMessage(data.message, 'success');
            } else {
                this.showMessage(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            this.showMessage('Network error occurred', 'error');
        } finally {
            button.disabled = false;
            if (button.textContent === 'Processing...') {
                button.textContent = originalText;
            }
        }
    }

    updateRegistrationUI(button, data) {
        const eventCard = button.closest('.event-card');
        if (!eventCard) return;

        // Update capacity display
        const capacityDisplay = eventCard.querySelector('.capacity-display');
        if (capacityDisplay) {
            capacityDisplay.textContent = 
                `${data.currentRegistrations}${data.hasCapacityLimit ? '/' + data.capacityLimit : ''} registered`;
        }

        // Update status badge
        const statusBadge = eventCard.querySelector('.status-badge');
        if (statusBadge) {
            if (data.hasAvailableCapacity) {
                statusBadge.textContent = 'Available';
                statusBadge.className = 'status-badge badge bg-success';
            } else {
                statusBadge.textContent = 'Full';
                statusBadge.className = 'status-badge badge bg-warning';
            }
        }

        // Update button state
        if (data.isRegistered) {
            button.textContent = 'Unregister';
            button.dataset.action = 'unregister';
            button.classList.remove('btn-primary');
            button.classList.add('btn-secondary');
        } else {
            button.textContent = 'Register';
            button.dataset.action = 'register';
            button.classList.remove('btn-secondary');
            button.classList.add('btn-primary');
        }

        // Disable button if event is full and user is not registered
        if (!data.hasAvailableCapacity && !data.isRegistered) {
            button.disabled = true;
            button.textContent = 'Event Full';
        }
    }

    showMessage(message, type) {
        // Remove any existing messages
        const existingAlerts = document.querySelectorAll('.alert-dismissible');
        existingAlerts.forEach(alert => alert.remove());

        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type} alert-dismissible fade show`;
        messageDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        `;

        // Insert at the top of main content
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.insertBefore(messageDiv, mainContent.firstChild);
        }

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new EventRegistration();
});