/**
 * Course Detail Page JavaScript
 * Handles tab switching, cart functionality, and course interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeTabs();
    initializeCartFunctionality();
    initializeEnrollmentFunctionality();
});

/**
 * Tab switching functionality
 */
function initializeTabs() {
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-pane');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

/**
 * Cart functionality with real API calls
 */
function initializeCartFunctionality() {
    console.log('Initializing cart functionality...');
    const addToCartBtn = document.getElementById('addToCartBtn');
    console.log('Add to cart button:', addToCartBtn);
    if (addToCartBtn) {
        console.log('Adding click listener to add to cart button');
        addToCartBtn.addEventListener('click', function() {
            console.log('Add to cart button clicked');
            const courseId = this.getAttribute('data-course-id');
            console.log('Course ID:', courseId);
            addToCart(courseId);
        });
    } else {
        console.log('Add to cart button not found!');
    }
}

/**
 * Add course to cart via API
 */
async function addToCart(courseId) {
    console.log('addToCart function called with courseId:', courseId);
    
    if (!courseId) {
        console.error('No course ID provided');
        showNotification('Error: Course ID not found', 'error');
        return;
    }

    // Show loading state
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (!addToCartBtn) {
        console.error('Add to cart button not found');
        return;
    }
    
    const originalText = addToCartBtn.innerHTML;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    addToCartBtn.disabled = true;

    try {
        console.log('Making API request to add course to cart...');
        const response = await fetch('../backend/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin', // Include session cookies
            body: JSON.stringify({
                course_id: parseInt(courseId),
                quantity: 1
            })
        });

        console.log('Response received:', response.status, response.statusText);

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        console.log('Response content-type:', contentType);
        
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            console.error('Non-JSON response received:', textResponse);
            throw new Error('Invalid response from server');
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (response.status === 401) {
            // User not authenticated - redirect to login
            showNotification('Please log in to add courses to your cart', 'warning');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
            return;
        }

        if (data.success) {
            showNotification(data.message, 'success');
            updateCartCounter();
            
            // Optionally update button state
            if (data.action === 'added') {
                addToCartBtn.innerHTML = '<i class="fas fa-check"></i> Added to Cart';
                addToCartBtn.classList.add('added');
            }
        } else {
            showNotification(data.message || 'Failed to add course to cart', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Network error. Please try again.', 'error');
    } finally {
        // Reset button state after delay
        setTimeout(() => {
            addToCartBtn.innerHTML = originalText;
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('added');
        }, 2000);
    }
}

/**
 * Update cart counter in navigation
 */
async function updateCartCounter() {
    try {
        const response = await fetch('../backend/api/cart.php', {
            method: 'GET',
            credentials: 'same-origin'
        });

        const data = await response.json();
        if (data.success) {
            const cartCounter = document.querySelector('.cart-counter');
            if (cartCounter) {
                cartCounter.textContent = data.count;
                cartCounter.style.display = data.count > 0 ? 'block' : 'none';
            }
        }
    } catch (error) {
        console.error('Error updating cart counter:', error);
    }
}

/**
 * Enrollment functionality
 */
function initializeEnrollmentFunctionality() {
    const enrollBtn = document.getElementById('enrollBtn');
    if (enrollBtn) {
        enrollBtn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            enrollInCourse(courseId);
        });
    }
}

/**
 * Enroll in course
 */
async function enrollInCourse(courseId) {
    if (!courseId) {
        showNotification('Error: Course ID not found', 'error');
        return;
    }

    // Show loading state
    const enrollBtn = document.getElementById('enrollBtn');
    const originalText = enrollBtn.innerHTML;
    enrollBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';
    enrollBtn.disabled = true;

    try {
        const response = await fetch('/Creators-Space-GroupProject/backend/api/enroll.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                course_id: parseInt(courseId)
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Successfully enrolled in course!', 'success');
            // Redirect to course or update UI
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to enroll in course', 'error');
        }
    } catch (error) {
        console.error('Error enrolling in course:', error);
        showNotification('Network error. Please try again.', 'error');
    } finally {
        enrollBtn.innerHTML = originalText;
        enrollBtn.disabled = false;
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
 * Quick add to course functionality (for course cards)
 */
function quickAddToCourse(courseId) {
    addToCart(courseId);
}

// Initialize cart counter on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCounter();
});
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    initializeCartFunctionality();
    initializeEnrollFunctionality();
});

// Tab switching functionality
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked button and corresponding pane
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

// Add to cart functionality
function initializeCartFunctionality() {
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const courseName = this.getAttribute('data-course-name');
            const coursePrice = parseFloat(this.getAttribute('data-course-price'));
            const courseImage = this.getAttribute('data-course-image');
            
            // Show loading state
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding to Cart...';
            
            // Simulate adding to cart (replace with actual cart functionality)
            setTimeout(() => {
                // Show success state
                this.innerHTML = '<i class="fas fa-check"></i> Added to Cart!';
                this.style.background = 'linear-gradient(135deg, #28a745 0%, #1e7e34 100%)';
                
                // Show notification
                showNotification('Course added to cart successfully!', 'success');
                
                // Update cart counter if it exists
                updateCartCounter();
                
                // Reset button after 3 seconds
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.background = '';
                    this.disabled = false;
                }, 3000);
            }, 1000);
        });
    }
}

// Enroll functionality
function initializeEnrollFunctionality() {
    const enrollBtn = document.querySelector('.enroll-btn');
    
    if (enrollBtn) {
        enrollBtn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            
            // Show loading state
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';
            
            // Simulate enrollment process
            setTimeout(() => {
                // Show success and redirect
                this.innerHTML = '<i class="fas fa-check"></i> Enrolled!';
                showNotification('Successfully enrolled! Redirecting to your courses...', 'success');
                
                setTimeout(() => {
                    window.location.href = 'mycourses.php';
                }, 2000);
            }, 1500);
        });
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const notificationText = notification.querySelector('.notification-text');
    
    // Update notification content
    notificationText.textContent = message;
    
    // Update notification style based on type
    if (type === 'success') {
        notification.style.background = '#28a745';
    } else if (type === 'error') {
        notification.style.background = '#dc3545';
    }
    
    // Show notification
    notification.style.display = 'block';
    
    // Hide after 4 seconds
    setTimeout(() => {
        notification.style.display = 'none';
    }, 4000);
}

// Update cart counter (placeholder function)
function updateCartCounter() {
    // This would typically fetch the cart count from the server
    const cartCounter = document.querySelector('#cart-count');
    if (cartCounter) {
        let currentCount = parseInt(cartCounter.textContent) || 0;
        cartCounter.textContent = currentCount + 1;
        cartCounter.style.display = 'block';
    }
}

// Play lesson function
function playLesson(lessonId) {
    // This would typically open a video player or lesson content
    alert(`Playing lesson ${lessonId}. In a real application, this would open the lesson content.`);
}

// Quick add to course function for courses page
function quickAddToCourse(courseId) {
    const button = event.target.closest('.btn-quick-add');
    if (!button) return;
    
    // Show loading state
    button.disabled = true;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Simulate adding to cart
    setTimeout(() => {
        // Show success state
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.style.background = '#28a745';
        
        // Show mini notification
        showMiniNotification('Added to cart!');
        
        // Reset after 2 seconds
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.style.background = '#ff6b6b';
            button.disabled = false;
        }, 2000);
    }, 800);
}

// Show mini notification for quick actions
function showMiniNotification(message) {
    // Create mini notification
    const miniNotif = document.createElement('div');
    miniNotif.className = 'mini-notification';
    miniNotif.textContent = message;
    miniNotif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        z-index: 1000;
        font-size: 0.9rem;
        font-weight: 500;
        animation: slideInRight 0.3s ease;
    `;
    
    // Add animation style if not exists
    if (!document.querySelector('#mini-notif-style')) {
        const style = document.createElement('style');
        style.id = 'mini-notif-style';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(miniNotif);
    
    // Remove after 2 seconds
    setTimeout(() => {
        miniNotif.remove();
    }, 2000);
}

// Bookmark toggle function
function toggleBookmark(courseId) {
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    
    if (icon.classList.contains('far')) {
        // Add bookmark
        icon.classList.remove('far');
        icon.classList.add('fas');
        button.style.background = '#ffd700';
        showMiniNotification('Course bookmarked!');
    } else {
        // Remove bookmark
        icon.classList.remove('fas');
        icon.classList.add('far');
        button.style.background = 'rgba(255,255,255,0.9)';
        showMiniNotification('Bookmark removed');
    }
}

// Course card hover effects
document.addEventListener('DOMContentLoaded', function() {
    const courseCards = document.querySelectorAll('.course-card');
    
    courseCards.forEach(card => {
        const img = card.querySelector('img');
        const title = card.querySelector('h3');
        
        card.addEventListener('mouseenter', function() {
            if (img) {
                img.style.transform = 'scale(1.05)';
            }
            if (title) {
                title.style.color = '#007bff';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            if (img) {
                img.style.transform = 'scale(1)';
            }
            if (title) {
                title.style.color = '#2c3e50';
            }
        });
    });
});