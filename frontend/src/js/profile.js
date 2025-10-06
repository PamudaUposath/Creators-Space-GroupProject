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
    
    // Use XMLHttpRequest instead of fetch for better error handling
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../backend/api/profile.php', true);
    xhr.withCredentials = true;
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('Upload XHR Status:', xhr.status);
            console.log('Upload XHR Response Text:', xhr.responseText);
            
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    console.log('Upload Parsed JSON:', data);
                    
                    if (data.success) {
                        // Update profile page image
                        const profileImage = document.getElementById('profileImage');
                        const newImageUrl = data.image_url + '?t=' + Date.now(); // Prevent caching
                        profileImage.src = newImageUrl;
                        
                        // Show remove button since we now have a custom image
                        let removeBtn = document.getElementById('removeImageBtn');
                        if (removeBtn) {
                            removeBtn.style.display = 'flex';
                        } else {
                            // Create remove button if it doesn't exist
                            const avatar = document.querySelector('.profile-avatar');
                            const newRemoveBtn = document.createElement('div');
                            newRemoveBtn.className = 'avatar-remove';
                            newRemoveBtn.id = 'removeImageBtn';
                            newRemoveBtn.title = 'Remove profile picture';
                            newRemoveBtn.onclick = removeProfileImage;
                            newRemoveBtn.innerHTML = '<i class="fas fa-times"></i>';
                            avatar.appendChild(newRemoveBtn);
                        }
                        
                        // Update navbar profile image
                        updateNavbarProfileImage(newImageUrl);
                        
                        showNotification('Profile image updated successfully!', 'success');
                    } else {
                        showNotification(data.message || 'Failed to upload image', 'error');
                    }
                } catch (e) {
                    console.error('Upload JSON Parse Error:', e);
                    console.log('Upload Raw response:', xhr.responseText);
                    
                    // If JSON parsing fails, assume upload worked and refresh
                    alert('Upload completed. Refreshing page to verify...');
                    location.reload();
                }
            } else {
                console.error('Upload HTTP Error:', xhr.status, xhr.statusText);
                alert('Upload error: ' + xhr.status + '. The upload might have worked. Refreshing page...');
                location.reload();
            }
            
            // Restore overlay content
            overlay.innerHTML = originalContent;
        }
    };
    
    xhr.onerror = function() {
        console.error('Upload XHR Network Error');
        alert('Network error during upload. The upload might have worked. Refreshing page...');
        location.reload();
    };
    
    xhr.send(formData);
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

function removeProfileImage() {
    console.log('🔥 removeProfileImage function called!');
    
    if (!confirm('Are you sure you want to remove your profile picture?')) {
        console.log('❌ User cancelled removal');
        return;
    }
    
    console.log('✅ User confirmed removal');
    
    // Show loading state
    const removeBtn = document.getElementById('removeImageBtn');
    if (!removeBtn) {
        console.error('❌ Remove button not found');
        return;
    }
    
    console.log('✅ Remove button found:', removeBtn);
    
    const originalContent = removeBtn.innerHTML;
    removeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    console.log('🔄 Sending DELETE request to API...');
    
    fetch('../backend/api/profile.php', {
        method: 'DELETE',
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('📡 Response received:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 API response data:', data);
        if (data.success) {
            // Update profile image to default
            const profileImage = document.getElementById('profileImage');
            const defaultImage = './assets/images/userIcon_Square.png';
            profileImage.src = defaultImage;
            
            // Update navbar profile image
            updateNavbarProfileImage(defaultImage);
            
            // Hide the remove button since no custom image exists
            removeBtn.style.display = 'none';
            
            showNotification('Profile picture removed successfully!', 'success');
        } else {
            showNotification(data.message || 'Failed to remove profile picture', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error. Please try again.', 'error');
    })
    .finally(() => {
        if (removeBtn) {
            removeBtn.innerHTML = originalContent;
        }
    });
}

function initializeRemoveImageButton() {
    console.log('🚀 Initializing remove image button...');
    const removeBtn = document.getElementById('removeImageBtn');
    if (removeBtn) {
        console.log('✅ Remove button found, adding event listener');
        removeBtn.addEventListener('click', removeProfileImage);
        
        // Test if button is clickable
        removeBtn.addEventListener('click', function() {
            console.log('🖱️ Remove button clicked!');
        });
    } else {
        console.log('❌ Remove button not found during initialization');
    }
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
            
            // Show remove button since we now have a custom image
            let removeBtn = document.getElementById('removeImageBtn');
            if (removeBtn) {
                removeBtn.style.display = 'flex';
            } else {
                // Create remove button if it doesn't exist
                const avatar = document.querySelector('.profile-avatar');
                const newRemoveBtn = document.createElement('div');
                newRemoveBtn.className = 'avatar-remove';
                newRemoveBtn.id = 'removeImageBtn';
                newRemoveBtn.title = 'Remove profile picture';
                newRemoveBtn.innerHTML = '<i class="fas fa-times"></i>';
                newRemoveBtn.addEventListener('click', removeProfileImage);
                avatar.appendChild(newRemoveBtn);
            }
            
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
