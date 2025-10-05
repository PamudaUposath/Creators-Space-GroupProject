<?php
// Include database connection
require_once __DIR__ . '/../backend/config/db_connect.php';

// Set page-specific variables
$pageTitle = "My Profile";
$pageDescription = "View and edit your profile information.";
$additionalCSS = ['./src/css/profile.css?v=' . time()];
$additionalJS = ['./src/js/profile.js'];

// Check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Function to fetch user profile
function getUserProfile($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name, email, username, role, 
                   profile_image, skills, bio, phone, date_of_birth, created_at
            FROM users 
            WHERE id = ? AND is_active = 1
        ");
        
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error fetching user profile: " . $e->getMessage());
        return null;
    }
}

// Function to get user statistics
function getUserStats($pdo, $user_id) {
    try {
        // Get enrolled courses count
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as enrolled_courses 
            FROM enrollments 
            WHERE user_id = ? AND status = 'active'
        ");
        $stmt->execute([$user_id]);
        $enrolled = $stmt->fetch(PDO::FETCH_ASSOC)['enrolled_courses'];
        
        // Get completed courses count
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as completed_courses 
            FROM enrollments 
            WHERE user_id = ? AND status = 'completed'
        ");
        $stmt->execute([$user_id]);
        $completed = $stmt->fetch(PDO::FETCH_ASSOC)['completed_courses'];
        
        // Get certificates count
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as certificates 
            FROM certificates 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $certificates = $stmt->fetch(PDO::FETCH_ASSOC)['certificates'];
        
        return [
            'enrolled_courses' => $enrolled,
            'completed_courses' => $completed,
            'certificates' => $certificates
        ];
        
    } catch (PDOException $e) {
        error_log("Error fetching user stats: " . $e->getMessage());
        return [
            'enrolled_courses' => 0,
            'completed_courses' => 0,
            'certificates' => 0
        ];
    }
}

// Fetch user profile and stats
$user = getUserProfile($pdo, $user_id);
$stats = getUserStats($pdo, $user_id);

// Debug: Check if user data was fetched
if (!$user) {
    die("Error: Could not fetch user profile. Please check database connection.");
}

if (!$user) {
    header('Location: login.php');
    exit;
}

// Include header
include './includes/header.php';
?>

<div class="profile-container">
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-cover">
                <div class="profile-info">
                    <div class="profile-avatar">
                        <img src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : './assets/images/userIcon_Square.png'; ?>" 
                             alt="Profile Image" id="profileImage">
                        <div class="avatar-overlay" onclick="openImageUpload()">
                            <i class="fas fa-camera"></i>
                        </div>
                        <?php if (!empty($user['profile_image'])): ?>
                        <div class="avatar-remove" id="removeImageBtn" title="Remove profile picture" onclick="removeProfileImage();">
                            <i class="fas fa-times"></i>
                        </div>
                        <?php endif; ?>
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                    </div>
                    
                    <div class="profile-details">
                        <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                        <p class="profile-username">@<?php echo htmlspecialchars(isset($user['username']) && $user['username'] ? $user['username'] : 'user' . $user['id']); ?></p>
                        <p class="profile-role">
                            <i class="fas fa-user-tag"></i>
                            <?php echo ucfirst($user['role']); ?>
                        </p>
                        <p class="profile-join-date">
                            <i class="fas fa-calendar-alt"></i>
                            Member since <?php echo isset($user['created_at']) && $user['created_at'] ? date('F Y', strtotime($user['created_at'])) : 'Unknown'; ?>
                        </p>
                    </div>
                    
                    <div class="profile-actions">
                        <button class="btn btn-primary" id="editProfileBtn">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Stats -->
        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['enrolled_courses']; ?></div>
                <div class="stat-label">Enrolled Courses</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['completed_courses']; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['certificates']; ?></div>
                <div class="stat-label">Certificates</div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <div class="profile-tabs">
                <button class="tab-btn active" data-tab="about">About</button>
                <button class="tab-btn" data-tab="courses">My Courses</button>
                <button class="tab-btn" data-tab="certificates">Certificates</button>
                <button class="tab-btn" data-tab="settings">Settings</button>
            </div>

            <!-- About Tab -->
            <div class="tab-content" id="about">
                <div class="profile-section">
                    <h3><i class="fas fa-user"></i> Personal Information</h3>
                    <div class="info-grid" id="viewMode">
                        <div class="info-item">
                            <label>First Name</label>
                            <span><?php echo htmlspecialchars($user['first_name']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Last Name</label>
                            <span><?php echo htmlspecialchars($user['last_name'] ?: 'Not specified'); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email</label>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Username</label>
                            <span><?php echo htmlspecialchars(isset($user['username']) && $user['username'] ? $user['username'] : 'Not set'); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Phone</label>
                            <span><?php echo htmlspecialchars(isset($user['phone']) && $user['phone'] ? $user['phone'] : 'Not specified'); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Date of Birth</label>
                            <span><?php echo isset($user['date_of_birth']) && $user['date_of_birth'] ? date('F j, Y', strtotime($user['date_of_birth'])) : 'Not specified'; ?></span>
                        </div>
                        <div class="info-item full-width">
                            <label>Bio</label>
                            <span><?php echo nl2br(htmlspecialchars(isset($user['bio']) && $user['bio'] ? $user['bio'] : 'No bio available')); ?></span>
                        </div>
                        <div class="info-item full-width">
                            <label>Skills</label>
                            <span class="skills-display">
                                <?php 
                                if (isset($user['skills']) && $user['skills']) {
                                    $skills = explode(',', $user['skills']);
                                    foreach ($skills as $skill) {
                                        echo '<span class="skill-tag">' . htmlspecialchars(trim($skill)) . '</span>';
                                    }
                                } else {
                                    echo 'No skills listed';
                                }
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- Edit Mode Form -->
                    <form class="info-grid" id="editMode" style="display: none;">
                        <div class="info-item">
                            <label>First Name *</label>
                            <input type="text" id="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="info-item">
                            <label>Last Name</label>
                            <input type="text" id="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                        </div>
                        <div class="info-item">
                            <label>Email *</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="info-item">
                            <label>Username</label>
                            <input type="text" id="username" value="<?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : ''); ?>">
                        </div>
                        <div class="info-item">
                            <label>Phone</label>
                            <input type="tel" id="phone" value="<?php echo htmlspecialchars(isset($user['phone']) ? $user['phone'] : ''); ?>">
                        </div>
                        <div class="info-item">
                            <label>Date of Birth</label>
                            <input type="date" id="dateOfBirth" value="<?php echo isset($user['date_of_birth']) ? $user['date_of_birth'] : ''; ?>">
                        </div>
                        <div class="info-item full-width">
                            <label>Bio</label>
                            <textarea id="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars(isset($user['bio']) ? $user['bio'] : ''); ?></textarea>
                        </div>
                        <div class="info-item full-width">
                            <label>Skills (comma separated)</label>
                            <input type="text" id="skills" value="<?php echo htmlspecialchars(isset($user['skills']) ? $user['skills'] : ''); ?>" 
                                   placeholder="e.g., JavaScript, PHP, React, Node.js">
                        </div>
                        
                        <div class="info-item full-width">
                            <div class="form-actions">
                                <button type="button" class="btn btn-success" id="saveProfileBtn">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancelEditBtn">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- My Courses Tab -->
            <div class="tab-content" id="courses" style="display: none;">
                <div class="profile-section">
                    <h3><i class="fas fa-graduation-cap"></i> My Courses</h3>
                    <div id="userCourses">
                        <p>Loading courses...</p>
                    </div>
                </div>
            </div>

            <!-- Certificates Tab -->
            <div class="tab-content" id="certificates" style="display: none;">
                <div class="profile-section">
                    <h3><i class="fas fa-certificate"></i> My Certificates</h3>
                    <div id="userCertificates">
                        <p>Loading certificates...</p>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-content" id="settings" style="display: none;">
                <div class="profile-section">
                    <h3><i class="fas fa-cog"></i> Account Settings</h3>
                    
                    <!-- Password Change Section -->
                    <div class="settings-section">
                        <h4>Change Password</h4>
                        <form id="passwordChangeForm">
                            <div class="form-group">
                                <label for="currentPassword">Current Password</label>
                                <input type="password" id="currentPassword" required>
                            </div>
                            <div class="form-group">
                                <label for="newPassword">New Password</label>
                                <input type="password" id="newPassword" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirm New Password</label>
                                <input type="password" id="confirmPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </form>
                    </div>

                    <!-- Account Preferences -->
                    <div class="settings-section">
                        <h4>Preferences</h4>
                        <div class="preference-item">
                            <label>
                                <input type="checkbox" id="emailNotifications"> 
                                Receive email notifications
                            </label>
                        </div>
                        <div class="preference-item">
                            <label>
                                <input type="checkbox" id="courseReminders"> 
                                Course progress reminders
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary" id="savePreferencesBtn">
                            <i class="fas fa-save"></i> Save Preferences
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="notification" class="notification" style="display: none;">
    <div class="notification-content">
        <i class="fas fa-check-circle"></i>
        <span class="notification-text">Profile updated successfully!</span>
    </div>
</div>

<!-- Inline JavaScript for Profile Image Removal -->
<script>
function removeProfileImage() {
    if (!confirm('Are you sure you want to remove your profile picture?')) {
        return;
    }
    
    const removeBtn = document.getElementById('removeImageBtn');
    if (!removeBtn) {
        alert('Remove button not found');
        return;
    }
    
    const originalContent = removeBtn.innerHTML;
    removeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Use XMLHttpRequest instead of fetch for better debugging
    const xhr = new XMLHttpRequest();
    xhr.open('DELETE', '../backend/api/profile.php', true);
    xhr.withCredentials = true;
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('XHR Status:', xhr.status);
            console.log('XHR Response Text:', xhr.responseText);
            
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    console.log('Parsed JSON:', data);
                    
                    if (data.success) {
                        // Update profile image to default
                        const profileImage = document.getElementById('profileImage');
                        const defaultImage = './assets/images/userIcon_Square.png';
                        profileImage.src = defaultImage;
                        
                        // Hide the remove button
                        removeBtn.style.display = 'none';
                        
                        alert('Profile picture removed successfully!');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        alert('Error: ' + (data.message || 'Failed to remove profile picture'));
                    }
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    console.log('Raw response:', xhr.responseText);
                    
                    // If JSON parsing fails, just assume it worked and refresh
                    alert('Operation completed. Refreshing page...');
                    location.reload();
                }
            } else {
                console.error('HTTP Error:', xhr.status, xhr.statusText);
                alert('HTTP Error: ' + xhr.status + '. The operation might have worked. Refreshing page...');
                location.reload();
            }
            
            // Restore button content
            if (removeBtn) {
                removeBtn.innerHTML = originalContent;
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('XHR Network Error');
        alert('Network error occurred. The operation might have worked. Refreshing page...');
        location.reload();
    };
    
    xhr.send();
}
</script>

<?php include './includes/footer.php'; ?>