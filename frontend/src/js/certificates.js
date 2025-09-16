// Certificate Verification JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const verificationForm = document.getElementById('verificationForm');
    const resultDiv = document.getElementById('verificationResult');

    if (verificationForm) {
        verificationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const certificateId = document.getElementById('certificateId').value.trim();
            
            if (!certificateId) {
                showResult('Please enter a certificate ID.', 'error');
                return;
            }

            // Show loading state
            const submitBtn = verificationForm.querySelector('.verify-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            submitBtn.disabled = true;

            // Simulate verification process
            setTimeout(() => {
                verifyyCertificate(certificateId);
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    }

    function verifyyCertificate(certificateId) {
        // Mock verification logic
        // In a real application, this would make an API call to verify the certificate
        
        const mockValidCertificates = [
            'CS2024-WD-001',
            'CS2024-DS-002', 
            'CS2024-ML-003',
            'CS2024-UI-004',
            'CS2024-FS-005'
        ];

        if (mockValidCertificates.includes(certificateId.toUpperCase())) {
            // Valid certificate
            const certificateData = getMockCertificateData(certificateId);
            showResult(generateValidCertificateHTML(certificateData), 'success');
        } else {
            // Invalid certificate
            showResult(generateInvalidCertificateHTML(), 'error');
        }
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
                        <span class="value">${data.id}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Student Name:</span>
                        <span class="value">${data.studentName}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Course:</span>
                        <span class="value">${data.courseName}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Completion Date:</span>
                        <span class="value">${formatDate(data.completionDate)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Issue Date:</span>
                        <span class="value">${formatDate(data.issueDate)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Instructor:</span>
                        <span class="value">${data.instructor}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Grade:</span>
                        <span class="value grade-${data.grade.replace('+', 'plus')}">${data.grade}</span>
                    </div>
                    <div class="skills-section">
                        <span class="label">Skills Covered:</span>
                        <div class="skills-tags">
                            ${data.skills.map(skill => `<span class="skill-tag">${skill}</span>`).join('')}
                        </div>
                    </div>
                </div>
                <div class="verification-footer">
                    <p><i class="fas fa-shield-alt"></i> This certificate is authentic and issued by Creators Space</p>
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
