// My Courses JavaScript
document.addEventListener("DOMContentLoaded", function() {
    console.log("My courses page loaded");
    
    // Check if required elements exist
    const coursesGrid = document.getElementById("coursesGrid");
    if (!coursesGrid) {
        console.error("coursesGrid element not found!");
        return;
    }
    
    loadEnrolledCourses();
});

function loadEnrolledCourses() {
    const coursesGrid = document.getElementById("coursesGrid");
    
    // Show loading state
    coursesGrid.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Loading your courses...</p>
        </div>
    `;
    
    // Fetch enrolled courses from backend API
    fetch('../backend/api/my-courses.php', {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const enrolledCourses = data.data || [];
            
            if (enrolledCourses.length === 0) {
                showEmptyState();
                return;
            }

            coursesGrid.innerHTML = '';
            
            enrolledCourses.forEach((course, index) => {
                const courseCard = createCourseCard(course, index);
                coursesGrid.appendChild(courseCard);
            });
        } else {
            showErrorState(data.message || 'Failed to load courses');
        }
    })
    .catch(error => {
        console.error('Error loading enrolled courses:', error);
        showErrorState('Network error. Please check your connection and try again.');
    });
}

function createCourseCard(course, index) {
    const card = document.createElement("div");
    card.className = "course-card";

    // Calculate progress bar width
    const progress = course.overall_progress || 0;
    const progressWidth = Math.min(Math.max(progress, 0), 100);
    
    // Format enrollment date
    const enrolledDate = course.enrollment?.enrolled_at ? 
        new Date(course.enrollment.enrolled_at).toLocaleDateString() : 'N/A';
    
    // Determine status badge
    const status = course.enrollment?.status || 'active';
    const statusClass = status === 'completed' ? 'completed' : status === 'paused' ? 'paused' : 'active';

    card.innerHTML = `
        <div class="course-image">
            <img src="${course.image || './assets/images/webdev.png'}" alt="${course.title}" />
            <div class="status-badge status-${statusClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</div>
            <div class="progress-overlay">
                <div class="course-progress-indicator">
                    <div class="progress-circle" style="--progress: ${progress}">
                        <span>${Math.round(progress)}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="course-content">
            <h3 class="course-title">${course.title}</h3>
            <p class="course-description">${course.description || 'Continue your learning journey with this course.'}</p>
            <div class="course-meta">
                <span class="instructor">By: ${course.instructor_name || 'Unknown'}</span>
                <span class="enrolled-date">Enrolled: ${enrolledDate}</span>
            </div>
            <div class="progress-section">
                <div class="progress-info">
                    <span>Progress: ${Math.round(progress)}%</span>
                    ${course.last_accessed_lesson_id ? `<span class="last-lesson">Last watched lesson</span>` : ''}
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${progressWidth}%"></div>
                </div>
            </div>
            <div class="course-actions">
                <button class="btn continue-btn" onclick="continueVideoLearning('${course.id}')">
                    <i class="fas fa-play"></i>
                    ${progress > 0 ? 'Continue Learning' : 'Start Learning'}
                </button>
                <button class="btn view-btn" onclick="viewCourse('${course.id}')">
                    <i class="fas fa-eye"></i>
                    View Course
                </button>
                <button class="btn remove-btn" onclick="removeCourse('${course.enrollment_id}', '${course.title}')">
                    <i class="fas fa-times"></i>
                    Unenroll
                </button>
            </div>
        </div>
    `;

    return card;
}

function showEmptyState() {
    const coursesGrid = document.getElementById("coursesGrid");
    coursesGrid.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-graduation-cap fa-3x"></i>
            <h3>Courses are empty</h3>
            <p>You haven't enrolled in any courses yet. Start exploring our courses and enroll in your favorites.</p>
            <a href="courses.php" class="btn primary">Browse Courses</a>
        </div>
    `;
}

function showErrorState(message) {
    const coursesGrid = document.getElementById("coursesGrid");
    coursesGrid.innerHTML = `
        <div class="error-state">
            <i class="fas fa-exclamation-triangle fa-3x"></i>
            <h3>Error Loading Courses</h3>
            <p>${message}</p>
            <button class="btn primary" onclick="loadEnrolledCourses()">Try Again</button>
        </div>
    `;
}

// Continue learning with video player
function continueVideoLearning(courseId) {
    console.log('Continue video learning called for course:', courseId);
    
    // Check if video player is available
    if (typeof videoPlayer === 'undefined') {
        console.error('Video player not available, falling back to course detail page');
        viewCourse(courseId);
        return;
    }
    
    // Use the video player's continue learning function
    if (videoPlayer.continueLearning) {
        videoPlayer.continueLearning(courseId);
    } else {
        console.error('Video player continueLearning method not available');
        viewCourse(courseId);
    }
}

// View course detail page
function viewCourse(courseId) {
    window.location.href = `course-detail.php?id=${courseId}`;
}

// Legacy continue course function for backward compatibility
function continueCourse(courseId) {
    continueVideoLearning(courseId);
}

function removeCourse(enrollmentId, courseName) {
    Modal.confirm(
        'Unenroll from Course',
        `Are you sure you want to unenroll from "${courseName}"? You will lose your progress and may need to re-purchase if it's a paid course.`,
        () => {
            // Confirmed - remove the enrollment
            fetch('../backend/api/unenroll.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    enrollment_id: enrollmentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Modal.toast(`Successfully unenrolled from ${courseName}!`, 'success');
                    loadEnrolledCourses(); // Reload the courses
                } else {
                    Modal.toast(data.message || 'Failed to unenroll from course', 'error');
                }
            })
            .catch(error => {
                console.error('Error unenrolling from course:', error);
                Modal.toast('Network error. Please try again.', 'error');
            });
        },
        () => {
            // Cancelled - show info message
            Modal.toast('Unenrollment cancelled', 'info', 2000);
        },
        {
            confirmText: 'Unenroll',
            cancelText: 'Keep Course',
            confirmClass: 'btn-danger'
        }
    );
}


// Legacy wrapper for backwards compatibility
function showToast(message, type = 'success') {
    Modal.toast(message, type);
}
