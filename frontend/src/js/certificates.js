// Certificate Verification JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const verifyBtn = document.getElementById('verifyBtn');
    const resultDiv = document.getElementById('verificationResult');
    const certificateInput = document.getElementById('certificateId');

    // Handle button click
    if (verifyBtn) {
        verifyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const certificateId = certificateInput.value.trim();
            
            if (!certificateId) {
                showResult('Please enter a certificate ID.', 'error');
                return;
            }

            // Show loading state
            const originalText = verifyBtn.innerHTML;
            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            verifyBtn.disabled = true;

            // Simulate verification process
            setTimeout(() => {
                verifyCertificate(certificateId);
                
                // Reset button
                verifyBtn.innerHTML = originalText;
                verifyBtn.disabled = false;
            }, 1500);
        });
    }

    // Handle Enter key press in input field
    if (certificateInput) {
        certificateInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifyBtn.click();
            }
        });
    }

    // Debug button handler
    const debugBtn = document.getElementById('debugBtn');
    if (debugBtn) {
        debugBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('=== DEBUG TEST START ===');
            
            // Test 1: Direct fetch to proxy
            console.log('Test 1: Testing proxy directly');
            fetch('verify_proxy.php?id=CERT-FSWD-2024-002')
                .then(response => {
                    console.log('Debug response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('Debug raw response:', text);
                    try {
                        const data = JSON.parse(text);
                        console.log('Debug parsed data:', data);
                        if (data.success && data.verified) {
                            showResult(`<div style="color: #4CAF50; padding: 20px; text-align: center;"><h3>✅ DEBUG SUCCESS!</h3><p>Certificate found: ${data.data.student_name}</p><pre>${JSON.stringify(data, null, 2)}</pre></div>`, 'success');
                        } else {
                            showResult(`<div style="color: #f44336; padding: 20px; text-align: center;"><h3>❌ DEBUG: Certificate Not Found</h3><pre>${JSON.stringify(data, null, 2)}</pre></div>`, 'error');
                        }
                    } catch (e) {
                        console.error('Debug parse error:', e);
                        showResult(`<div style="color: #f44336; padding: 20px; text-align: center;"><h3>❌ DEBUG: Parse Error</h3><p>${e.message}</p><pre>${text}</pre></div>`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Debug fetch error:', error);
                    showResult(`<div style="color: #f44336; padding: 20px; text-align: center;"><h3>❌ DEBUG: Fetch Error</h3><p>${error.message}</p></div>`, 'error');
                });
        });
    }

    function verifyCertificate(certificateId) {
        // Use the same domain with a simple path
        const apiUrl = `verify_proxy.php?id=${encodeURIComponent(certificateId)}`;
        console.log('Making API call to:', apiUrl);
        
        fetch(apiUrl)
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);
                    
                    if (data.success && data.verified) {
                        showResult(generateValidCertificateHTML(data.data), 'success');
                    } else {
                        showResult(generateInvalidCertificateHTML(), 'error');
                    }
                } catch (parseError) {
                    console.error('Parse error:', parseError);
                    showResult(generateErrorHTML(), 'error');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showResult(generateErrorHTML(), 'error');
            });
    }

    function getMockCertificateData(certificateId) {
        const certificates = {
            'CS2024-WD-001': {
                id: 'CS2024-WD-001',
                studentName: 'John Doe',
                courseName: 'Full Stack Web Development',
                issueDate: '2024-01-15',
                completionDate: '2024-01-10',
                instructor: 'Sarah Johnson',
                grade: 'A+',
                skills: ['HTML', 'CSS', 'JavaScript', 'React', 'Node.js']
            },
            'CS2024-DS-002': {
                id: 'CS2024-DS-002',
                studentName: 'Jane Smith',
                courseName: 'Data Science Fundamentals',
                issueDate: '2024-02-20',
                completionDate: '2024-02-15',
                instructor: 'Dr. Michael Chen',
                grade: 'A',
                skills: ['Python', 'Pandas', 'NumPy', 'Machine Learning', 'Data Visualization']
            },
            'CS2024-ML-003': {
                id: 'CS2024-ML-003',
                studentName: 'Alex Johnson',
                courseName: 'Machine Learning Specialist',
                issueDate: '2024-03-10',
                completionDate: '2024-03-05',
                instructor: 'Dr. Emily Watson',
                grade: 'A+',
                skills: ['TensorFlow', 'PyTorch', 'Deep Learning', 'Neural Networks', 'AI Ethics']
            }
        };

        return certificates[certificateId.toUpperCase()] || null;
    }

    function generateValidCertificateHTML(data) {
        return `
            <div class="verification-success">
                <div class="success-header">
                    <i class="fas fa-check-circle"></i>
                    <h4>Certificate Verified Successfully!</h4>
                </div>
                <div class="certificate-details">
                    <div class="detail-row">
                        <span class="label">Certificate ID:</span>
                        <span class="value">${data.certificate_id}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Student Name:</span>
                        <span class="value">${data.student_name}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Course:</span>
                        <span class="value">${data.course_name}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Level:</span>
                        <span class="value">${data.level}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Category:</span>
                        <span class="value">${data.category}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Duration:</span>
                        <span class="value">${data.duration || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Completion Date:</span>
                        <span class="value">${data.completion_date ? formatDate(data.completion_date) : 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Issue Date:</span>
                        <span class="value">${formatDate(data.issue_date)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Instructor:</span>
                        <span class="value">${data.instructor}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Progress:</span>
                        <span class="value progress-bar">
                            <div class="progress-container">
                                <div class="progress-fill" style="width: ${data.progress}%"></div>
                                <span class="progress-text">${data.progress}%</span>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="verification-footer">
                    <p><i class="fas fa-shield-alt"></i> This certificate is authentic and issued by Creators Space</p>
                    <p class="verified-time">Verified on ${formatDate(data.verified_at)}</p>
                </div>
            </div>
        `;
    }

    function generateErrorHTML() {
        return `
            <div class="verification-error">
                <div class="error-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h4>Verification Error</h4>
                </div>
                <div class="error-content">
                    <p>Unable to verify certificate at this time. This could be due to:</p>
                    <ul>
                        <li>Server connection issues</li>
                        <li>Database connectivity problems</li>
                        <li>Temporary service unavailability</li>
                    </ul>
                    <p>Please try again later or contact support if the issue persists.</p>
                </div>
                <div class="error-footer">
                    <a href="mailto:support@creatorsspace.com" class="contact-btn">
                        <i class="fas fa-envelope"></i> Contact Support
                    </a>
                </div>
            </div>
        `;
    }

    function generateInvalidCertificateHTML() {
        return `
            <div class="verification-error">
                <div class="error-header">
                    <i class="fas fa-times-circle"></i>
                    <h4>Certificate Not Found</h4>
                </div>
                <div class="error-content">
                    <p>The certificate ID you entered could not be verified. This could mean:</p>
                    <ul>
                        <li>The certificate ID is incorrect or contains typos</li>
                        <li>The certificate has not been issued yet</li>
                        <li>The certificate has been revoked or expired</li>
                    </ul>
                    <p>Please check the certificate ID and try again. If you believe this is an error, please contact our support team.</p>
                </div>
                <div class="error-footer">
                    <a href="mailto:support@creatorsspace.com" class="contact-btn">
                        <i class="fas fa-envelope"></i> Contact Support
                    </a>
                </div>
            </div>
        `;
    }

    function showResult(html, type) {
        if (resultDiv) {
            resultDiv.innerHTML = html;
            resultDiv.className = `verification-result ${type}`;
            resultDiv.style.display = 'block';
            
            // Smooth scroll to result
            resultDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    // Add CSS for verification results
    addVerificationStyles();

    function addVerificationStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .verification-result {
                margin-top: 2rem;
                border-radius: 12px;
                overflow: hidden;
                animation: slideIn 0.3s ease-out;
            }

            .verification-success {
                background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.05) 100%);
                border: 1px solid rgba(76, 175, 80, 0.3);
                color: #ffffff;
            }

            .verification-error {
                background: linear-gradient(135deg, rgba(244, 67, 54, 0.1) 0%, rgba(244, 67, 54, 0.05) 100%);
                border: 1px solid rgba(244, 67, 54, 0.3);
                color: #ffffff;
            }

            .success-header, .error-header {
                padding: 1.5rem;
                text-align: center;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .success-header i {
                font-size: 3rem;
                color: #4CAF50;
                margin-bottom: 1rem;
                display: block;
            }

            .error-header i {
                font-size: 3rem;
                color: #f44336;
                margin-bottom: 1rem;
                display: block;
            }

            .success-header h4, .error-header h4 {
                font-size: 1.5rem;
                font-weight: 600;
                margin: 0;
            }

            .certificate-details {
                padding: 1.5rem;
            }

            .detail-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .detail-row:last-child {
                border-bottom: none;
            }

            .detail-row .label {
                font-weight: 600;
                color: rgba(255, 255, 255, 0.8);
            }

            .detail-row .value {
                color: #ffffff;
                font-weight: 500;
            }

            .grade-A, .grade-Aplus {
                color: #4CAF50;
                font-weight: 700;
            }

            .skills-section {
                padding: 1rem 0 0;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                margin-top: 1rem;
            }

            .skills-section .label {
                display: block;
                font-weight: 600;
                color: rgba(255, 255, 255, 0.8);
                margin-bottom: 0.5rem;
            }

            .skills-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .skill-tag {
                background: rgba(102, 126, 234, 0.2);
                color: #667eea;
                padding: 0.3rem 0.8rem;
                border-radius: 20px;
                font-size: 0.85rem;
                font-weight: 500;
                border: 1px solid rgba(102, 126, 234, 0.3);
            }

            .verification-footer, .error-footer {
                padding: 1.5rem;
                text-align: center;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                background: rgba(255, 255, 255, 0.05);
            }

            .verification-footer i {
                color: #4CAF50;
                margin-right: 0.5rem;
            }

            .error-content {
                padding: 1.5rem;
            }

            .error-content ul {
                color: rgba(255, 255, 255, 0.8);
                margin: 1rem 0;
                padding-left: 1.5rem;
            }

            .error-content li {
                margin-bottom: 0.5rem;
                line-height: 1.5;
            }

            .contact-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.5rem;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #ffffff;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .contact-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
                color: #ffffff;
                text-decoration: none;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width: 768px) {
                .detail-row {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.25rem;
                }

                .skills-tags {
                    justify-content: center;
                }
            }
        `;
        document.head.appendChild(style);
    }
});
