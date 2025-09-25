// Profile Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    initializeProfileEdit();
    initializeImageUpload();
});

function initializeTabs() {
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.style.display = 'none');
            this.classList.add('active');
            document.getElementById(targetTab).style.display = 'block';
        });
    });
}

function initializeImageUpload() {
    const imageUpload = document.getElementById('imageUpload');
    if (imageUpload) {
        imageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                uploadProfileImage(file);
            }
        });
    }
}

function initializeProfileEdit() {
    const editBtn = document.getElementById('editProfileBtn');
    const saveBtn = document.getElementById('saveProfileBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            document.getElementById('viewMode').style.display = 'none';
            document.getElementById('editMode').style.display = 'grid';
            this.style.display = 'none';
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            document.getElementById('viewMode').style.display = 'grid';
            document.getElementById('editMode').style.display = 'none';
            editBtn.style.display = 'inline-flex';
        });
    }
    
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            saveProfile();
        });
    }
}

function saveProfile() {
    const saveBtn = document.getElementById('saveProfileBtn');
    const originalText = saveBtn.innerHTML;
    
    saveBtn.innerHTML = 'Saving...';
    saveBtn.disabled = true;
    
    const profileData = {
        first_name: document.getElementById('firstName').value.trim(),
        last_name: document.getElementById('lastName').value.trim(),
        email: document.getElementById('email').value.trim(),
        username: document.getElementById('username').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        date_of_birth: document.getElementById('dateOfBirth').value,
        bio: document.getElementById('bio').value.trim(),
        skills: document.getElementById('skills').value.trim()
    };
    
    fetch('../backend/api/profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(profileData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update profile'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

function openImageUpload() {
    document.getElementById('imageUpload').click();
}

function uploadProfileImage(file) {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Please upload a JPEG, PNG, or GIF image', 'error');
        return;
    }
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('Image size must be less than 5MB', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('profile_image', file);
    
    // Show loading state
    const overlay = document.querySelector('.avatar-overlay');
    const originalContent = overlay.innerHTML;
    overlay.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch('../backend/api/profile.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update profile page image
            const profileImage = document.getElementById('profileImage');
            const newImageUrl = data.image_url + '?t=' + Date.now(); // Prevent caching
            profileImage.src = newImageUrl;
            
            // Update navbar profile image
            updateNavbarProfileImage(newImageUrl);
            
            showNotification('Profile image updated successfully!', 'success');
        } else {
            showNotification(data.message || 'Failed to upload image', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error. Please try again.', 'error');
    })
    .finally(() => {
        overlay.innerHTML = originalContent;
    });
}

function updateNavbarProfileImage(imageUrl) {
    // Update navbar profile image if it exists
    const navbarProfileImg = document.getElementById('navbarProfileImg');
    if (navbarProfileImg) {
        navbarProfileImg.src = imageUrl;
    } else {
        // If navbar shows icon instead of image, replace it with image
        const profileBtn = document.querySelector('.navbar .profile-btn');
        if (profileBtn) {
            const icon = profileBtn.querySelector('i.fas.fa-user');
            if (icon) {
                icon.remove();
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = 'Profile';
                img.className = 'navbar-profile-img';
                img.id = 'navbarProfileImg';
                profileBtn.appendChild(img);
            }
        }
    }
}

function showNotification(message, type = 'info', duration = 4000) {
    // Try to use existing notification element first
    let notification = document.getElementById('notification');
    
    if (!notification) {
        // Create notification element if it doesn't exist
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.className = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            display: none;
            max-width: 350px;
        `;
        
        const content = document.createElement('div');
        content.className = 'notification-content';
        content.innerHTML = `
            <i class="fas fa-info-circle"></i>
            <span class="notification-text">${message}</span>
        `;
        notification.appendChild(content);
        document.body.appendChild(notification);
    }
    
    const notificationText = notification.querySelector('.notification-text');
    const icon = notification.querySelector('i');
    
    // Update content
    notificationText.textContent = message;
    
    // Update style and icon based on type
    notification.className = 'notification';
    if (type === 'success') {
        notification.classList.add('success');
        notification.style.background = '#4caf50';
        icon.className = 'fas fa-check-circle';
    } else if (type === 'error') {
        notification.classList.add('error');
        notification.style.background = '#dc3545';
        icon.className = 'fas fa-exclamation-circle';
    } else {
        notification.style.background = '#333';
        icon.className = 'fas fa-info-circle';
    }
    
    // Show notification
    notification.style.display = 'block';
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 100);
    
    // Hide after duration
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 300);
    }, duration);
}
