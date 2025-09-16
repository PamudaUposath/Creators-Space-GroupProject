// My Courses JavaScript
document.addEventListener("DOMContentLoaded", function() {
    console.log("My courses page loaded");
    loadEnrolledCourses();
});

function loadEnrolledCourses() {
    const coursesGrid = document.getElementById("coursesGrid");
    const enrolledCourses = JSON.parse(localStorage.getItem("enrolledCourses")) || [];

    if (enrolledCourses.length === 0) {
        showEmptyState();
        return;
    }

    coursesGrid.innerHTML = '';
    
    enrolledCourses.forEach((course, index) => {
        const courseCard = createCourseCard(course, index);
        coursesGrid.appendChild(courseCard);
    });
}

function createCourseCard(course, index) {
    const card = document.createElement("div");
    card.className = "course-card";

    card.innerHTML = `
        <div class="course-image">
            <img src="${course.image || './assets/images/webdev.png'}" alt="${course.title}" />
        </div>
        <div class="course-content">
            <h3 class="course-title">${course.title}</h3>
            <p class="course-description">${course.description || 'Continue your learning journey with this course.'}</p>
            <div class="course-actions">
                <button class="btn continue-btn" onclick="continueCourse('${course.id || index}')">
                    Continue Learning
                </button>
                <button class="btn remove-btn" onclick="removeCourse(${index})">
                    Remove
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

function continueCourse(courseId) {
    console.log('Continuing course:', courseId);
    alert('Redirecting to course content...');
}

function removeCourse(index) {
    if (confirm('Are you sure you want to remove this course from your enrolled courses?')) {
        let enrolledCourses = JSON.parse(localStorage.getItem("enrolledCourses")) || [];
        enrolledCourses.splice(index, 1);
        localStorage.setItem("enrolledCourses", JSON.stringify(enrolledCourses));
        
        showToast('Course removed successfully!');
        loadEnrolledCourses();
    }
}


// Simple toast notification
function showToast(message) {
    let toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');
    toast.innerText = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('fade-out');
        toast.addEventListener('transitionend', () => {
            toast.remove();
        });
    }, 2000);
}
