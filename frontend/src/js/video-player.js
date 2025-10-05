// Enhanced Video Player with Skip Prevention for Certificate Validation
class VideoPlayer {
    constructor() {
        this.currentVideo = null;
        this.progressInterval = null;
        this.saveProgressTimeout = null;
        this.resumeTime = 0;
        
        // Skip prevention tracking
        this.actualWatchTime = 0;
        this.lastPosition = 0;
        this.watchStartTime = null;
        this.seekViolations = 0;
        this.allowedSkipThreshold = 10; // Maximum seconds allowed to skip
        this.skipViolationLimit = 3; // Maximum number of skip violations allowed
        this.isValidWatching = true;
        this.watchSessions = 0;
        this.totalSkippedDuration = 0;
        
        // Tracking intervals
        this.watchTimeTracker = null;
        this.positionMonitor = null;
    }

    // Play lesson with resume functionality
    async playLesson(lessonId) {
        try {
            const response = await fetch(`../backend/api/get_lesson.php?lesson_id=${lessonId}`);
            const data = await response.json();
            
            if (data.success && data.lesson.video_url) {
                // Add course name to lesson data
                data.lesson.course_name = data.lesson.course_title;
                this.showVideoPlayer(data.lesson);
            } else {
                this.showError(data.message || 'Video not available');
            }
        } catch (error) {
            console.error('Error loading lesson:', error);
            this.showError('Error loading video');
        }
    }

    // Continue learning from last position
    async continueLearning(courseId) {
        console.log('Continue learning called for course:', courseId);
        
        try {
            const response = await fetch(`../backend/api/get_continue_data.php?course_id=${courseId}`);
            const data = await response.json();
            
            console.log('Continue learning response:', data);
            
            if (data.success && data.continue_data.lesson_id) {
                const lessonData = {
                    ...data.continue_data,
                    id: data.continue_data.lesson_id,
                    title: data.continue_data.lesson_title,
                    video_url: data.continue_data.video_url,
                    last_watched_time: data.continue_data.last_watched_time,
                    course_id: courseId
                };
                
                console.log('Playing lesson with data:', lessonData);
                
                if (!lessonData.video_url) {
                    this.showError('No video URL available for this lesson');
                    return;
                }
                
                this.showVideoPlayer(lessonData, true);
            } else {
                console.error('Continue learning failed:', data);
                this.showError(data.message || 'No video to continue');
            }
        } catch (error) {
            console.error('Error continuing lesson:', error);
            this.showError('Error loading video');
        }
    }

    async showVideoPlayer(lesson, isContinuing = false) {
        this.resumeTime = lesson.last_watched_time || 0;
        
        // Log lesson data for debugging
        console.log('Video player opening with lesson data:', {
            lesson_id: lesson.id,
            course_id: lesson.course_id || lesson.courseId,
            overall_progress: lesson.overall_progress,
            lesson_completion: lesson.lesson_completion,
            last_watched_time: lesson.last_watched_time
        });
        
        // Use course name from lesson data, with fallback
        let courseName = lesson.course_name || lesson.course_title || lesson.title;
        
        // Create modal HTML
        const modal = document.createElement('div');
        modal.className = 'video-modal';
        modal.innerHTML = `
            <div class="video-container">
                <div class="video-header">
                    <h3>${this.escapeHtml(courseName)}</h3>
                    <button onclick="videoPlayer.closeVideoPlayer()" class="close-btn">&times;</button>
                </div>
                <div class="video-wrapper">
                    <video 
                        id="lessonVideo" 
                        class="video-player" 
                        controls 
                        preload="metadata"
                        data-lesson-id="${lesson.id}"
                        data-course-id="${lesson.course_id || lesson.courseId}"
                        controlslist="nodownload noremoteplayback"
                        disablepictureinpicture
                        oncontextmenu="return false;"
                    >
                        <source src="${lesson.video_url}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    ${this.resumeTime > 0 ? `
                        <div class="resume-notification" id="resumeNotification">
                            Resuming from ${this.formatTime(this.resumeTime)}
                        </div>
                    ` : ''}
                    <div class="watch-warning" id="watchWarning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>You must watch the full video to be eligible for a certificate</span>
                    </div>
                    <div class="skip-violation-warning" id="skipWarning" style="display: none;">
                        <i class="fas fa-ban"></i>
                        <span>Skipping detected! Watch violations: <span id="violationCount">0</span>/${this.skipViolationLimit}</span>
                    </div>
                </div>
                <div class="video-controls">
                    <div class="progress-info">
                        <span style="color: #ffffffff;">Lesson Progress</span>
                        <span style="color: #ffffffff;" id="progressPercentage">${Math.round(lesson.lesson_completion || lesson.completion_percentage || 0)}%</span>
                    </div>
                    <div class="lesson-progress">
                        <div class="lesson-progress-bar" id="progressBar"  style="width: ${lesson.lesson_completion || lesson.completion_percentage || 0}%"></div>
                    </div>
                    <div class="video-actions">
                        <select class="speed-control" id="playbackSpeed">
                            <option value="0.5">0.5x</option>
                            <option value="0.75">0.75x</option>
                            <option value="1" selected>1x</option>
                            <option value="1.25">1.25x</option>
                            <option value="1.5">1.5x</option>
                            <option value="2">2x</option>
                        </select>
                        <div class="course-progress-indicator">
                            <div class="progress-circle" id="courseProgressCircle" style="--progress: ${lesson.overall_progress || 0}"></div>
                            <span id="courseProgressText">Course: ${Math.round(lesson.overall_progress || 0)}%</span>
                        </div>
                        <div class="watch-integrity-indicator">
                            <span id="watchTimeDisplay">Valid Watch Time: 0%</span>
                        </div>
                        <div class="certificate-status" id="certificateStatus">
                            <i class="fas fa-certificate"></i>
                            <span>Certificate Eligibility: Watching...</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        this.currentVideo = document.getElementById('lessonVideo');
        
        this.setupVideoEvents();
        this.setupSpeedControl();
        
        // Auto-resume if there's a saved position
        if (this.resumeTime > 0) {
            this.currentVideo.addEventListener('loadedmetadata', () => {
                this.currentVideo.currentTime = this.resumeTime;
            }, { once: true });
        }
        
        // Initialize progress displays with correct values
        this.initializeProgressDisplays(lesson);
        
        // Fetch and update latest course progress to ensure sync
        this.refreshCourseProgress(lesson.course_id || lesson.courseId);
    }

    setupVideoEvents() {
        if (!this.currentVideo) return;

        // Initialize tracking
        this.initializeWatchTracking();

        // Track progress and save periodically
        this.currentVideo.addEventListener('timeupdate', () => {
            this.updateProgress();
            this.trackWatchTime();
            this.scheduleProgressSave();
        });

        // Monitor for seeking/skipping
        this.currentVideo.addEventListener('seeking', () => {
            this.handleSeeking();
        });

        this.currentVideo.addEventListener('seeked', () => {
            this.handleSeeked();
        });

        // Track play/pause for watch validation
        this.currentVideo.addEventListener('play', () => {
            this.startWatchSession();
        });

        this.currentVideo.addEventListener('pause', () => {
            this.pauseWatchSession();
            this.saveProgress();
        });

        // Save progress when video ends
        this.currentVideo.addEventListener('ended', () => {
            this.handleVideoEnd();
        });

        // Handle video load errors
        this.currentVideo.addEventListener('error', () => {
            this.showError('Video failed to load. Please check your connection.');
        });

        // Prevent right-click and keyboard shortcuts
        this.currentVideo.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            return false;
        });

        // Prevent common skip shortcuts
        document.addEventListener('keydown', this.handleKeyboardShortcuts.bind(this));
    }

    initializeProgressDisplays(lesson) {
        // Ensure course progress displays correct initial value
        const courseProgressCircle = document.getElementById('courseProgressCircle');
        const courseProgressText = document.getElementById('courseProgressText');
        
        if (courseProgressCircle && courseProgressText) {
            const courseProgress = Math.round(lesson.overall_progress || 0);
            courseProgressCircle.style.setProperty('--progress', courseProgress);
            courseProgressText.textContent = `Course: ${courseProgress}%`;
            
            console.log(`Course progress initialized: ${courseProgress}%`);
        }
        
        // Initialize valid watch time display
        const watchTimeDisplay = document.getElementById('watchTimeDisplay');
        if (watchTimeDisplay) {
            watchTimeDisplay.textContent = 'Valid Watch Time: 0%';
        }
        
        // Initialize certificate status
        const certificateStatus = document.getElementById('certificateStatus');
        if (certificateStatus) {
            certificateStatus.innerHTML = `
                <i class="fas fa-certificate"></i>
                <span>Certificate Eligibility: Watching...</span>
            `;
            certificateStatus.className = 'certificate-status not-eligible';
        }
    }

    async refreshCourseProgress(courseId) {
        if (!courseId) return;
        
        try {
            const response = await fetch(`../backend/api/get_continue_data.php?course_id=${courseId}`);
            const data = await response.json();
            
            if (data.success && data.continue_data) {
                const courseProgressCircle = document.getElementById('courseProgressCircle');
                const courseProgressText = document.getElementById('courseProgressText');
                
                if (courseProgressCircle && courseProgressText) {
                    const courseProgress = Math.round(data.continue_data.overall_progress || 0);
                    courseProgressCircle.style.setProperty('--progress', courseProgress);
                    courseProgressText.textContent = `Course: ${courseProgress}%`;
                    
                    console.log(`Course progress refreshed: ${courseProgress}%`);
                }
            }
        } catch (error) {
            console.error('Error refreshing course progress:', error);
        }
    }

    initializeWatchTracking() {
        this.actualWatchTime = 0;
        this.lastPosition = this.currentVideo.currentTime;
        this.watchStartTime = null;
        this.seekViolations = 0;
        this.isValidWatching = true;
        this.watchSessions = 0;
        this.totalSkippedDuration = 0;
        
        // Show certificate requirement warning
        this.showWatchWarning();
    }

    trackWatchTime() {
        if (!this.currentVideo || this.currentVideo.paused) return;

        const currentTime = this.currentVideo.currentTime;
        const timeDiff = Math.abs(currentTime - this.lastPosition);
        
        // If time difference is normal (not a seek), count as valid watch time
        if (timeDiff <= 1.5 && timeDiff > 0) { // Allow for normal playback variations
            this.actualWatchTime += timeDiff;
        }
        
        this.lastPosition = currentTime;
        this.updateWatchDisplay();
    }

    handleSeeking() {
        const currentTime = this.currentVideo.currentTime;
        const seekDistance = Math.abs(currentTime - this.lastPosition);
        
        // Allow small seeks (might be user clicking slightly ahead)
        if (seekDistance > this.allowedSkipThreshold) {
            this.seekViolations++;
            this.totalSkippedDuration += seekDistance;
            
            // Show violation warning
            this.showSkipViolation();
            
            // If too many violations, invalidate watching
            if (this.seekViolations >= this.skipViolationLimit) {
                this.isValidWatching = false;
                this.showSkipViolationLimit();
            }
        }
    }

    handleSeeked() {
        this.lastPosition = this.currentVideo.currentTime;
    }

    startWatchSession() {
        this.watchStartTime = Date.now();
        this.watchSessions++;
    }

    pauseWatchSession() {
        if (this.watchStartTime) {
            const sessionTime = (Date.now() - this.watchStartTime) / 1000;
            // Session time is already tracked in trackWatchTime via timeupdate
            this.watchStartTime = null;
        }
    }

    handleVideoEnd() {
        this.pauseWatchSession();
        const duration = this.currentVideo.duration;
        const watchPercentage = (this.actualWatchTime / duration) * 100;
        
        // Video is considered properly watched if:
        // 1. At least 90% of actual watch time
        // 2. No more than allowed violations
        // 3. Not too much skipped content
        const isProperlyWatched = watchPercentage >= 90 && 
                                this.seekViolations < this.skipViolationLimit &&
                                this.isValidWatching;
        
        this.saveProgress(true, isProperlyWatched);
        
        if (isProperlyWatched) {
            this.showCertificateEligible();
        } else {
            this.showCertificateIneligible();
        }
    }

    handleKeyboardShortcuts(e) {
        // Prevent common video skip shortcuts
        if (this.currentVideo && document.querySelector('.video-modal')) {
            const preventKeys = [
                'ArrowRight', 'ArrowLeft', // Arrow key seeking
                'KeyJ', 'KeyL', 'KeyK', // YouTube-style shortcuts
                'Digit1', 'Digit2', 'Digit3', 'Digit4', 'Digit5', // Number key seeking
                'Digit6', 'Digit7', 'Digit8', 'Digit9', 'Digit0'
            ];
            
            if (preventKeys.includes(e.code)) {
                e.preventDefault();
                this.showSkipPreventionMessage();
            }
        }
    }

    updateWatchDisplay() {
        const duration = this.currentVideo ? this.currentVideo.duration : 0;
        if (duration > 0) {
            const watchPercentage = Math.min((this.actualWatchTime / duration) * 100, 100);
            
            // Update valid watch time display
            const display = document.getElementById('watchTimeDisplay');
            if (display) {
                display.textContent = `Valid Watch Time: ${Math.round(watchPercentage)}%`;
                
                // Color code based on validity
                if (watchPercentage >= 90) {
                    display.style.color = '#4CAF50'; // Green
                } else if (watchPercentage >= 70) {
                    display.style.color = '#FF9800'; // Orange
                } else {
                    display.style.color = '#F44336'; // Red
                }
            }
            
            // Update certificate status in real-time
            const certStatus = document.getElementById('certificateStatus');
            if (certStatus) {
                const isEligible = watchPercentage >= 90 && this.seekViolations <= 3 && this.isValidWatching;
                const statusText = isEligible ? 'Eligible ✓' : 
                                 watchPercentage >= 90 ? 'Reviewing...' : 
                                 'Watching...';
                
                certStatus.innerHTML = `
                    <i class="fas fa-certificate"></i>
                    <span>Certificate Eligibility: ${statusText}</span>
                `;
                certStatus.className = `certificate-status ${isEligible ? 'eligible' : 'not-eligible'}`;
            }
        }
    }

    showWatchWarning() {
        const warning = document.getElementById('watchWarning');
        if (warning) {
            warning.style.display = 'flex';
            setTimeout(() => {
                warning.style.display = 'none';
            }, 5000);
        }
    }

    showSkipViolation() {
        const warning = document.getElementById('skipWarning');
        const violationCount = document.getElementById('violationCount');
        if (warning && violationCount) {
            violationCount.textContent = this.seekViolations;
            warning.style.display = 'flex';
            setTimeout(() => {
                warning.style.display = 'none';
            }, 3000);
        }
    }

    showSkipViolationLimit() {
        const notification = document.createElement('div');
        notification.className = 'violation-limit-notification';
        notification.innerHTML = `
            <i class="fas fa-ban"></i>
            <strong>Certificate Eligibility Lost!</strong><br>
            Too many skip violations detected. You must restart the video to become eligible for a certificate.
        `;
        
        const videoWrapper = document.querySelector('.video-wrapper');
        if (videoWrapper) {
            videoWrapper.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 8000);
        }
    }

    showSkipPreventionMessage() {
        const notification = document.createElement('div');
        notification.className = 'skip-prevention-notification';
        notification.innerHTML = `
            <i class="fas fa-lock"></i>
            Skipping disabled for certificate eligibility
        `;
        
        const videoWrapper = document.querySelector('.video-wrapper');
        if (videoWrapper) {
            videoWrapper.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 2000);
        }
    }

    showCertificateEligible() {
        const notification = document.createElement('div');
        notification.className = 'certificate-eligible-notification';
        notification.innerHTML = `
            <i class="fas fa-certificate"></i>
            <strong>Congratulations!</strong><br>
            You've watched this lesson properly and are eligible for course completion credit.
        `;
        
        const videoWrapper = document.querySelector('.video-wrapper');
        if (videoWrapper) {
            videoWrapper.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    }

    showCertificateIneligible() {
        const notification = document.createElement('div');
        notification.className = 'certificate-ineligible-notification';
        notification.innerHTML = `
            <i class="fas fa-times-circle"></i>
            <strong>Certificate Not Earned</strong><br>
            Please restart and watch the full video without skipping to earn course completion credit.
        `;
        
        const videoWrapper = document.querySelector('.video-wrapper');
        if (videoWrapper) {
            videoWrapper.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 6000);
        }
    }

    setupSpeedControl() {
        const speedControl = document.getElementById('playbackSpeed');
        if (speedControl && this.currentVideo) {
            speedControl.addEventListener('change', (e) => {
                this.currentVideo.playbackRate = parseFloat(e.target.value);
            });
        }
    }

    updateProgress() {
        if (!this.currentVideo) return;

        const currentTime = this.currentVideo.currentTime;
        const duration = this.currentVideo.duration;
        
        if (duration > 0) {
            const percentage = (currentTime / duration) * 100;
            
            // Update progress bar
            const progressBar = document.getElementById('progressBar');
            const progressPercentage = document.getElementById('progressPercentage');
            
            if (progressBar) progressBar.style.width = percentage + '%';
            if (progressPercentage) progressPercentage.textContent = Math.round(percentage) + '%';
        }
    }

    scheduleProgressSave() {
        // Clear existing timeout
        if (this.saveProgressTimeout) {
            clearTimeout(this.saveProgressTimeout);
        }
        
        // Schedule save for 2 seconds from now
        this.saveProgressTimeout = setTimeout(() => {
            this.saveProgress();
        }, 2000);
    }

    async saveProgress(isCompleted = false, isProperlyWatched = null) {
        if (!this.currentVideo) return;

        const lessonId = this.currentVideo.dataset.lessonId;
        const courseId = this.currentVideo.dataset.courseId;
        const currentTime = this.currentVideo.currentTime;
        const totalDuration = this.currentVideo.duration;

        if (!lessonId || !courseId || !totalDuration) return;

        // Determine if properly watched
        if (isProperlyWatched === null) {
            const watchPercentage = (this.actualWatchTime / totalDuration) * 100;
            isProperlyWatched = watchPercentage >= 90 && 
                              this.seekViolations < this.skipViolationLimit &&
                              this.isValidWatching;
        }

        try {
            const response = await fetch('../backend/api/save_progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    lesson_id: parseInt(lessonId),
                    course_id: parseInt(courseId),
                    current_time: currentTime,
                    total_duration: totalDuration,
                    actual_watch_time: this.actualWatchTime,
                    watch_sessions: this.watchSessions,
                    skipped_duration: this.totalSkippedDuration,
                    seek_violations: this.seekViolations,
                    is_properly_watched: isProperlyWatched,
                    is_completed: isCompleted
                })
            });

            const data = await response.json();
            
            if (data.success && data.progress) {
                // Update course progress indicator
                const courseProgressCircle = document.getElementById('courseProgressCircle');
                const courseProgressText = document.getElementById('courseProgressText');
                
                if (courseProgressCircle && courseProgressText) {
                    const courseProgress = Math.round(data.progress.course_progress);
                    courseProgressCircle.style.setProperty('--progress', courseProgress);
                    courseProgressText.textContent = `Course: ${courseProgress}%`;
                }
                
                // Update valid watch time display
                const watchTimeDisplay = document.getElementById('watchTimeDisplay');
                if (watchTimeDisplay && data.progress.actual_watch_percentage !== undefined) {
                    const watchPercentage = Math.round(Math.min(data.progress.actual_watch_percentage, 100));
                    watchTimeDisplay.textContent = `Valid Watch Time: ${watchPercentage}%`;
                }
                
                // Show completion message if lesson completed properly
                if (data.progress.is_lesson_completed && isProperlyWatched && !isCompleted) {
                    this.showCompletionMessage(data.progress);
                }
                
                // Update certificate eligibility display
                if (data.progress.is_certificate_eligible !== undefined) {
                    this.updateCertificateEligibility(data.progress.is_certificate_eligible);
                }
                
                console.log('Progress updated:', {
                    lessonCompletion: Math.round(data.progress.lesson_completion),
                    courseProgress: Math.round(data.progress.course_progress),
                    validWatchTime: Math.round(data.progress.actual_watch_percentage || 0),
                    certificateEligible: data.progress.is_certificate_eligible
                });
            }
        } catch (error) {
            console.error('Error saving progress:', error);
        }
    }

    updateCertificateEligibility(isEligible) {
        const certStatus = document.getElementById('certificateStatus');
        if (certStatus) {
            const statusText = isEligible ? 'Eligible ✓' : 'Not Eligible ✗';
            certStatus.innerHTML = `
                <i class="fas fa-certificate"></i>
                <span>Certificate Eligibility: ${statusText}</span>
            `;
            certStatus.className = `certificate-status ${isEligible ? 'eligible' : 'not-eligible'}`;
        }
    }

    showCompletionMessage(progress) {
        const notification = document.createElement('div');
        notification.className = 'resume-notification';
        notification.innerHTML = `
            🎉 Lesson completed! Course progress: ${Math.round(progress.course_progress)}%
        `;
        
        const videoWrapper = document.querySelector('.video-wrapper');
        if (videoWrapper) {
            videoWrapper.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }

    closeVideoPlayer() {
        // Save progress before closing
        this.saveProgress();
        
        // Clear intervals and timeouts
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
            this.progressInterval = null;
        }
        
        if (this.saveProgressTimeout) {
            clearTimeout(this.saveProgressTimeout);
            this.saveProgressTimeout = null;
        }
        
        // Remove modal
        const modal = document.querySelector('.video-modal');
        if (modal) {
            modal.remove();
        }
        
        this.currentVideo = null;
        this.resumeTime = 0;
    }

    showError(message) {
        // Create error modal
        const modal = document.createElement('div');
        modal.className = 'video-modal';
        modal.innerHTML = `
            <div class="video-container" style="max-width: 400px;">
                <div class="video-header">
                    <h3>Error</h3>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="close-btn">&times;</button>
                </div>
                <div style="padding: 30px; text-align: center; color: white;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ff6b6b; margin-bottom: 15px;"></i>
                    <p style="margin: 0; font-size: 1.1rem;">${this.escapeHtml(message)}</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Auto-close after 3 seconds
        setTimeout(() => {
            modal.remove();
        }, 3000);
    }

    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Create global video player instance
const videoPlayer = new VideoPlayer();

// Update existing playLesson function to use new video player
function playLesson(lessonId) {
    videoPlayer.playLesson(lessonId);
}

// Add continue learning function
function continueLearning(courseId) {
    videoPlayer.continueLearning(courseId);
}

// Close video player when clicking outside
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('video-modal')) {
        videoPlayer.closeVideoPlayer();
    }
});

// Close video player with escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.querySelector('.video-modal')) {
        videoPlayer.closeVideoPlayer();
    }
});