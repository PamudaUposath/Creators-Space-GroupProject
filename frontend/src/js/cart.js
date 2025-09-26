/**
 * Shopping Cart JavaScript
 * Handles cart operations (add, remove, update) via API calls
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart functionality
    initializeCartPage();
});

/**
 * Initialize cart page functionality
 */
function initializeCartPage() {
    // Update cart display on load
    updateCartDisplay();
}

/**
 * Update quantity of a cart item
 */
async function updateQuantity(cartId, change, absolute = false) {
    const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
    const quantityInput = cartItem.querySelector('.quantity-input input');
    
    let newQuantity;
    if (absolute) {
        newQuantity = parseInt(change);
    } else {
        const currentQuantity = parseInt(quantityInput.value);
        newQuantity = currentQuantity + parseInt(change);
    }
    
    // Ensure minimum quantity of 1
    if (newQuantity < 1) {
        newQuantity = 1;
    }
    
    // Update input value immediately for better UX
    quantityInput.value = newQuantity;
    
    try {
        const response = await fetch('/Creators-Space-GroupProject/backend/api/cart.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                cart_id: parseInt(cartId),
                quantity: newQuantity
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            
            // Update cart counter in navbar
            if (typeof window.updateCartCounter === 'function') {
                window.updateCartCounter();
            }
            
            // Refresh the page to update totals
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            showNotification(data.message || 'Failed to update quantity', 'error');
            // Revert the input value
            quantityInput.value = quantityInput.value - change;
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        showNotification('Network error. Please try again.', 'error');
        // Revert the input value
        quantityInput.value = quantityInput.value - change;
    }
}

/**
 * Remove item from cart
 */
async function removeFromCart(cartId) {
    if (!confirm('Are you sure you want to remove this course from your cart?')) {
        return;
    }

    const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
    const removeBtn = cartItem.querySelector('.remove-btn');
    
    // Show loading state
    const originalText = removeBtn.innerHTML;
    removeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...';
    removeBtn.disabled = true;

    try {
        const response = await fetch('/Creators-Space-GroupProject/backend/api/cart.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                cart_id: parseInt(cartId)
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            
            // Update cart counter in navbar
            if (typeof window.updateCartCounter === 'function') {
                window.updateCartCounter();
            }
            
            // Animate item removal
            cartItem.style.opacity = '0';
            cartItem.style.transform = 'translateX(-100%)';
            
            setTimeout(() => {
                cartItem.remove();
                
                // Check if cart is now empty
                const remainingItems = document.querySelectorAll('.cart-item');
                if (remainingItems.length === 0) {
                    window.location.reload();
                } else {
                    // Refresh to update totals
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            }, 300);
        } else {
            showNotification(data.message || 'Failed to remove item', 'error');
            removeBtn.innerHTML = originalText;
            removeBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showNotification('Network error. Please try again.', 'error');
        removeBtn.innerHTML = originalText;
        removeBtn.disabled = false;
    }
}

/**
 * Proceed to checkout
 */
function proceedToCheckout() {
    // For now, show a message that checkout is not implemented
    showNotification('Checkout functionality will be implemented soon!', 'info');
    
    // In a real implementation, this would redirect to a payment processor
    // window.location.href = 'checkout.php';
}

/**
 * Update cart display (refresh totals, counters)
 */
async function updateCartDisplay() {
    try {
        const response = await fetch('/Creators-Space-GroupProject/backend/api/cart.php', {
            method: 'GET',
            credentials: 'same-origin'
        });

        const data = await response.json();
        if (data.success) {
            // Update cart counter in navigation if it exists
            const cartCounter = document.querySelector('.cart-counter');
            if (cartCounter) {
                cartCounter.textContent = data.count;
                cartCounter.style.display = data.count > 0 ? 'block' : 'none';
            }
        }
    } catch (error) {
        console.error('Error updating cart display:', error);
    }
}

/**
 * Show notification to user
 */
function showNotification(message, type = 'info', duration = 4000) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        notification.remove();
    });

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    // Set icon based on type
    let icon = 'fas fa-info-circle';
    if (type === 'success') icon = 'fas fa-check-circle';
    else if (type === 'error') icon = 'fas fa-exclamation-circle';
    else if (type === 'warning') icon = 'fas fa-exclamation-triangle';

    notification.innerHTML = `
        <i class="${icon}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add to document
    document.body.appendChild(notification);

    // Show with animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Auto remove after duration
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, duration);
}

/**
 * Add item to cart (for external use)
 */
async function addToCart(courseId) {
    try {
        const response = await fetch('/Creators-Space-GroupProject/backend/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                course_id: parseInt(courseId),
                quantity: 1
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            updateCartDisplay();
            return true;
        } else {
            showNotification(data.message || 'Failed to add course to cart', 'error');
            return false;
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Network error. Please try again.', 'error');
        return false;
    }
}