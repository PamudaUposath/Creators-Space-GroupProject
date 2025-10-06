# Contributing to Creators-Space E-Learning Platform

Welcome to the Creators-Space project! We're excited to have you contribute to our comprehensive e-le---

## 🧪 Testing Guidelines

### Testing Requirements
All contributions must include appropriate testing:

#### Backend Testing
```bash
# Run PHP unit tests
cd backend/test
php -f run_tests.php

# Database tests
php -f test_database_connection.php
php -f test_api_endpoints.php
```

#### Frontend Testing
```bash
# JavaScript testing
npm test

# Browser compatibility testing
# Test on Chrome, Firefox, Safari, Edge
# Mobile testing on iOS Safari, Chrome Mobile
```

#### Security Testing
- Input validation testing
- SQL injection prevention testing  
- XSS protection verification
- Authentication and authorization testing

### Code Quality Standards

#### PHP Code Standards
- Follow PSR-12 coding standards
- Use type hints and return types
- Implement proper error handling
- Document all public methods
- Use prepared statements for database queries

#### JavaScript Standards  
- Use ES6+ features
- Follow consistent naming conventions
- Implement proper error handling
- Comment complex logic
- Optimize for performance

#### Database Standards
- Use indexes for frequently queried columns
- Follow naming conventions (snake_case)
- Include proper foreign key constraints
- Document complex queries

---

## 🔒 Security & Production Considerations

### Security Requirements
- **Input Validation**: All user inputs must be validated and sanitized
- **Authentication**: Implement proper session management and JWT handling
- **Authorization**: Role-based access control for all endpoints
- **Encryption**: Sensitive data must be encrypted at rest and in transit
- **Logging**: Security events must be logged for audit purposes

### Production Deployment
- **Environment Variables**: Use secure configuration management
- **Database Migration**: Follow proper migration procedures
- **Cloud Services**: AWS S3 integration must follow security best practices
- **Payment Processing**: PayHere integration must be PCI compliant
- **Email Security**: PHPMailer must use secure SMTP with TLS

### Performance Guidelines
- **Database**: Optimize queries, use appropriate indexes
- **Caching**: Implement caching where appropriate
- **Media Delivery**: Optimize images and videos for web delivery
- **CDN**: Properly configure AWS S3 for optimal content delivery

---

## 📋 Pull Request Process

### Before Submitting
1. **Code Review Checklist**:
   - [ ] Code follows project standards
   - [ ] All tests pass
   - [ ] Security requirements met
   - [ ] Documentation updated
   - [ ] Performance impact considered

2. **Testing Evidence**:
   - [ ] Unit tests added/updated
   - [ ] Manual testing completed
   - [ ] Cross-browser testing (if frontend)
   - [ ] Mobile responsiveness verified
   - [ ] Security testing completed

### PR Description Template
```markdown
## 📝 Description
Brief description of changes

## 🎯 Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update
- [ ] Performance improvement
- [ ] Security enhancement

## 🧪 Testing
- [ ] Unit tests pass
- [ ] Integration tests pass
- [ ] Manual testing completed
- [ ] Security testing completed

## 📸 Screenshots/Videos
(If applicable, add screenshots or videos)

## 🔗 Related Issues
Closes #(issue_number)

## 🚀 Deployment Notes
(Any special deployment considerations)
```

### Code Review Process
1. **Automated Checks**: All automated tests must pass
2. **Peer Review**: At least one team member must review
3. **Security Review**: Security-sensitive changes require security review
4. **Performance Review**: Performance-critical changes require performance review
5. **Documentation Review**: Documentation changes require technical writing review

---

## 🛡 Security Guidelines

### Secure Coding Practices
- **Input Validation**: Validate all inputs on both client and server side
- **Output Encoding**: Properly encode outputs to prevent XSS
- **Authentication**: Use secure password hashing (bcrypt)
- **Session Management**: Implement secure session handling
- **Database Security**: Use parameterized queries exclusively
- **File Upload Security**: Validate file types and scan for malware
- **API Security**: Implement rate limiting and authentication for all endpoints

### Vulnerability Reporting
If you discover a security vulnerability:
1. **Do NOT create a public issue**
2. Email security concerns to: security@creators-space.com
3. Include detailed description and reproduction steps
4. Allow reasonable time for fix before disclosure

---

## 🌐 Internationalization (i18n)

### Multi-language Support
- Use language files for all user-facing text
- Support RTL languages
- Consider cultural differences in UI/UX
- Test with different character sets

### Current Language Support
- English (primary)
- Sinhala (planned)
- Tamil (planned)

---

## 📊 Performance & Monitoring

### Performance Standards
- **Page Load**: < 3 seconds on 3G connection
- **API Response**: < 500ms for standard requests  
- **Database Queries**: < 100ms for simple queries
- **Image Optimization**: WebP format with fallbacks
- **JavaScript Bundle**: < 250KB compressed

### Monitoring & Analytics
- Application performance monitoring
- Error tracking and logging
- User behavior analytics
- Security event monitoring
- Database performance monitoring

---

## 🚀 Release Process

### Version Numbering
We follow [Semantic Versioning](https://semver.org/):
- **MAJOR**: Breaking changes
- **MINOR**: New features (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

### Release Checklist
- [ ] All tests pass
- [ ] Security review completed
- [ ] Performance benchmarks met
- [ ] Documentation updated
- [ ] Database migrations tested
- [ ] Backup procedures verified
- [ ] Rollback plan documented

---

## 🤝 Community Guidelines

### Code of Conduct
- Be respectful and inclusive
- Provide constructive feedback
- Help newcomers learn
- Maintain professional communication
- Focus on technical merit

### Communication Channels
- **Issues**: Bug reports and feature requests
- **Pull Requests**: Code contributions and reviews  
- **Discussions**: General questions and suggestions
- **Email**: Security concerns and private matters

---

## 📚 Additional Resources

### Documentation
- [API Documentation](backend/api/README.md)
- [Database Schema](backend/ER_ASCII.txt)
- [Deployment Guide](docs/INSTALL.md)
- [Testing Guide](docs/SYSTEM_TEST.md)

### External Resources
- [PHP Best Practices](https://phptherightway.com/)
- [JavaScript Best Practices](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide)
- [AWS S3 Documentation](https://docs.aws.amazon.com/s3/)
- [PayHere API Documentation](https://www.payhere.lk/developers)

---

## 📞 Getting Help

### For Contributors
- Check existing issues and documentation first
- Ask questions in issue comments
- Join our community discussions
- Review similar projects for inspiration

### For Maintainers
- Provide timely feedback on contributions
- Maintain clear documentation
- Communicate changes effectively
- Support contributor growth

---

**Thank you for contributing to Creators-Space! Your efforts help create a better learning platform for everyone.** 🎉nagement system. This guide will help you understand our production-ready codebase and contribute effectively.

## 🚀 Quick Start for Contributors

1. **Review the architecture**: Check our [README.md](README.md) and [backend documentation](backend/README.md)
2. **Set up your development environment** (see Development Setup below)
3. **Find an issue** or propose a new feature → **assign yourself**
4. **Create a feature branch** from `main` → `git checkout -b feat/your-feature-name`
5. **Make changes** following our coding standards and security practices
6. **Test thoroughly** including unit tests and integration tests
7. **Open a Pull Request** with detailed description and testing evidence
8. **Collaborate on code review** → implement feedback → **merge when approved**ting Guide

## 🧭 TL;DR (Quick Start)

1. **Find an issue** in the table below (look for `good first issue`) → **assign yourself**.
2. **Create a branch** from `development` → `git checkout -b fix/issue-36-bookmarks`.
3. **Make changes** → **commit** using Conventional Commits (e.g., `fix: add bookmark button`).
4. **Open a Pull Request (PR)** into `development`, include `Fixes #36` in the description.
5. **Ask for a review** → make requested changes → **merge** when approved.
6. **Add your PR link** to the team’s Google Sheet.

## 🎯 Areas Where You Can Contribute

### 🔧 Backend Development (PHP 8.2+)
- **API Development**: REST endpoints, authentication, data validation
- **Database Optimization**: Query performance, indexing, schema improvements
- **Security Enhancements**: Input sanitization, authentication, authorization
- **AWS S3 Integration**: Media management, CDN optimization
- **Payment Gateway**: PayHere integration improvements
- **AI System**: Chatbot enhancements, knowledge base expansion

### 🎨 Frontend Development (Modern Web)
- **UI/UX Improvements**: Responsive design, accessibility, user experience
- **JavaScript Enhancements**: ES6+ features, performance optimization
- **Mobile Responsiveness**: Cross-device compatibility
- **Progressive Web App**: Offline functionality, push notifications
- **Performance**: Page load optimization, image compression

### 🤖 AI & Machine Learning
- **Chatbot Intelligence**: Conversation flow improvements
- **Recommendation System**: Course suggestion algorithms
- **Analytics**: User behavior analysis, learning pattern recognition
- **Natural Language Processing**: Multi-language support

### 📊 Testing & Quality Assurance
- **Unit Testing**: PHP unit tests, JavaScript testing
- **Integration Testing**: API testing, database testing
- **Security Testing**: Vulnerability assessment, penetration testing
- **Performance Testing**: Load testing, stress testing

### 📚 Documentation & DevOps
- **Technical Documentation**: API docs, deployment guides
- **User Documentation**: Help guides, video tutorials
- **DevOps**: CI/CD pipelines, Docker containerization
- **Monitoring**: Application performance monitoring, error tracking

---

## 🏗 Development Environment Setup

### Prerequisites
- **PHP**: 8.2 or higher
- **MySQL**: 8.0 or higher
- **Node.js**: 18+ (for frontend build tools)
- **Composer**: PHP dependency manager
- **Git**: Version control

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/Creators-Space-GroupProject.git
   cd Creators-Space-GroupProject
   ```

2. **Install PHP dependencies**
   ```bash
   cd backend
   composer install
   ```

3. **Database setup**
   ```bash
   # Import the database schema
   mysql -u root -p < backend/sql/creators_space(#final3-Pamuda).sql
   
   # Configure database connection
   cp backend/config/database.example.php backend/config/database.php
   # Edit database.php with your credentials
   ```

4. **Configure environment variables**
   ```bash
   # Backend configuration
   cp backend/config/.env.example backend/config/.env
   
   # Update with your settings:
   # - Database credentials
   # - AWS S3 credentials
   # - PayHere merchant details
   # - SMTP settings for PHPMailer
   ```

5. **Frontend setup**
   ```bash
   cd frontend
   npm install
   npm run build
   ```

6. **Start development servers**
   ```bash
   # PHP development server (backend)
   php -S localhost:8000 -t backend/public
   
   # Frontend development (if using build tools)
   npm run dev
   ```

---

## 🔄 Git Workflow & Branching Strategy

### Branch Structure
- **`main`**: Production-ready code, stable releases
- **`develop`**: Integration branch for features
- **`feature/*`**: New feature development
- **`hotfix/*`**: Critical production fixes
- **`release/*`**: Release preparation

### Branch Naming Convention
- **Features**: `feature/ai-chatbot-improvements`
- **Bug fixes**: `bugfix/payment-gateway-validation`
- **Hotfixes**: `hotfix/security-patch-v1.2.1`
- **Releases**: `release/v1.3.0`

### Commit Message Standards
We follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

**Examples:**
- `feat(ai): add course recommendation algorithm`
- `fix(auth): resolve JWT token expiration issue`
- `docs(api): update payment endpoint documentation`
- `perf(db): optimize course query performance`
- `security(auth): implement rate limiting for login`

   * `good first issue` (easiest starting point)
   * `bug`, `enhancement`, `UI/UX`
2. Open the issue → **read the description & comments**.
3. If you want to take it:

   * Click **“Assign yourself”** (or comment “I’d like to work on this.”).
   * Ask questions if anything is unclear.

> **Tip:** Start with **UI/UX** or **docs** if you’re new. They’re quick wins!

---

## 🐞 How to Report a Bug (Step-by-Step)

1. Go to **Issues** → **New issue** → choose **Bug report**.
2. Fill this format:

**Title (example):**
`[Bug]: Enroll button does nothing on enroll.html`

**Body checklist:**

* **Page/Feature:** `enroll.html`
* **Steps to Reproduce:**

  1. Open `enroll.html`
  2. Fill required fields
  3. Click **Enroll**
* **Expected:** User should be redirected or see success state
* **Actual:** Only a browser alert appears; no action taken
* **Screenshots/GIF:** *(attach if possible)*
* **Environment:** Chrome 139 / Windows 10
* **Related issues/PRs:** `#25` (logo not loading might affect button)
* **Labels:** `bug`, `UI/UX` *(if visual)*

> **Example of a great bug:** includes steps, expected vs actual, screenshot, and browser details.

---

## ✨ How to Suggest an Enhancement

1. **Issues** → **New issue** → choose **Feature request** or **Enhancement**.
2. Use this structure:

**Title (example):**
`[Enhancement]: Add "Change Password" option in profile`

**Body:**

* **What & Why:** Users can’t change password; add secure update flow
* **Proposed Solution:** Add “Change Password” modal in profile settings
* **Acceptance Criteria:**

  * Users can open modal from profile
  * Validation for current + new + confirm password
  * Clear success/error messaging (no default `alert()`)
* **Screens/Links:** (wireframes or references if any)
* **Labels:** `enhancement`, `UI/UX`

---

## 🌱 Create a Branch & Make Changes

### Option A — GitHub Web (no terminal)

1. Open the issue → **Create a branch** (from `development`) or click **“Edit this file”** for docs/UI tweaks.
2. GitHub will ask to create a new branch → name it:

   * `fix/issue-27-enroll-button`
   * `feat/change-password-ui`
3. Save changes → **Commit** with a clear message.
4. Click **Compare & pull request**.

### Option B — Git on your computer

```bash
# 1) Get latest code
git checkout development
git pull origin development

# 2) Create a new branch
git checkout -b fix/issue-27-enroll-button

# 3) Make changes, then:
git add .
git commit -m "fix: make Enroll button submit and redirect (Fixes #27)"
git push origin fix/issue-27-enroll-button
```

---

## 🧾 Commit Message Rules (Conventional Commits)

Use one of: `feat:`, `fix:`, `docs:`, `style:`, `refactor:`, `chore:`.

**Examples**

* `fix: enable remove from bookmarks (Fixes #34)`
* `feat: add change password flow with validation (Closes #37)`
* `docs: add screenshots to README`
* `refactor: organize project folders per module`

> Include `Fixes #<issue-number>` or `Closes #<issue-number>` to auto-close the issue when the PR merges.

---

## 🔀 Open a Pull Request (PR)

1. Base: **`development`** ← Compare: **your-branch**
2. PR **title**: short & clear
   Example: `fix: enroll button submits and redirects`
3. PR **description** (copy & fill):

```md
## What changed?
- Replaced alert() with form submit + redirect to confirmation page
- Added validation for required fields

## Why?
- Aligns with expected flow in Issue #27

## How to test
1. Open enroll.html
2. Fill fields and click Enroll
3. Expect redirect to enrollment-confirmation.html

## Screenshots
[attach before/after]

Fixes #27
```

4. **Request reviewers** (e.g., `@PamudaUposath`).
5. Pass checks (if any), respond to review comments, **update PR** as needed.

---

## ✅ Definition of Done

* [ ] All acceptance criteria met
* [ ] No console errors
* [ ] Mobile view checked (basic responsiveness)
* [ ] No raw `alert()` popups for UX flows
* [ ] Screenshots/GIF for UI changes
* [ ] PR references the issue (e.g., `Fixes #36`)
* [ ] Peer review approved
* [ ] Merged into `development`
* [ ] Added PR link to Google Sheet tracker

---

## 🧪 How to Test Locally (HTML/CSS/JS)

* Open the `.html` page in your browser.
* Use **DevTools** (Right-click → Inspect):

  * Console for errors
  * Responsive mode to check mobile
* For broken images/logos:

  * Check file paths are correct (relative to HTML)
  * Confirm the file actually exists in the repo
* For buttons/links:

  * Verify they navigate or trigger handlers
  * Avoid default alerts; use styled modals/toasts

---

## 🏷 Labels & When to Use Them

* `bug` — something broken (e.g., non-working button, broken link)
* `enhancement` — new feature or improvement
* `UI/UX` — visual, layout, spacing, typography issues
* `good first issue` — beginner-friendly tasks
* `invalid` — mistaken or not actionable

> If you’re not sure, just leave a comment—maintainers will label it.

---

## 🙋 Asking for Help

* Comment in the issue with your question.
* Share a short screen recording or screenshot if it’s a UI problem.
* Tag a maintainer/reviewer if you’re blocked: `@PamudaUposath`.

---

## 📚 Examples (Copy-Paste Friendly)

**Issue Titles**

* `[Bug]: Logo not displaying on homepage`
* `[Enhancement]: Add “Back to Home” button on tandc.html`
* `[UI/UX]: Improve navbar spacing on mobile`

**Bug Reproduction Example**

```
Steps:
1) Go to /courses.html
2) Click ⭐ on any course
3) Refresh page

Expected: Course remains bookmarked
Actual: Bookmark not saved; state resets
Env: Chrome 139 / Windows 10
```

**Commit Examples**

* `fix: persist course bookmarks in localStorage (Fixes #36)`
* `feat: add remove profile image option (Closes #38)`
* `docs: add beginner contribution guide`

---

## 🔒 Do & Don’t

**Do**

* Work on a fresh branch from `development`
* Keep PRs small and focused
* Add screenshots for UI changes
* Reference the issue number in your PR

**Don’t**

* Commit directly to `main`
* Combine unrelated fixes/features in one PR
* Use generic messages like “update file” or “fix things”

---

## 📎 Appendix: Useful Links

* 🔗 **Issue list (categorized)** — see the table below in this file
* 🔗 **How to write Markdown:** [https://www.markdownguide.org/basic-syntax/](https://www.markdownguide.org/basic-syntax/)
* 🔗 **Conventional Commits:** [https://www.conventionalcommits.org/](https://www.conventionalcommits.org/)

---

## 📋 Open Issues (For Contribution)

Here’s a categorized list of current issues. Pick one, create a branch, and submit a PR to `development`.

### 🐞 Bugs (Something isn't working)

| #  | Title                                                      | Status | Link                                                                           |
| -- | ---------------------------------------------------------- | ------ | ------------------------------------------------------------------------------ |
| 36 | No Option to Add Bookmarks in Courses Section              | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/36) |
| 35 | Bookmarks page accessible without login                    | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/35) |
| 34 | No option to remove items from Bookmarked page             | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/34) |
| 30 | Logo Not Displaying                                        | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/30) |
| 27 | Enroll button doesn't give any action                      | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/27) |
| 25 | Logo is not appearing (broken image icon)                  | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/25) |
| 23 | Course cards triggers not working/not added                | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/23) |
| 18 | Alignment & Design Issues in “Our Services” Section        | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/18) |
| 14 | Privacy Policy Link in Terms and Conditions page is broken | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/14) |
| 10 | Missing bullet point in tandc.html                         | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/10) |
| 5  | Forgot Password Option Not Working                         | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/5)  |
| 3  | Google Sign-In Fails (Error Shown as Alert)                | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/3)  |
| 2  | Improve Navbar Layout and Visibility Rules                 | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/2)  |

### ✨ Enhancements (New feature or improvement)

| #  | Title                                                  | Status | Link                                                                           |
| -- | ------------------------------------------------------ | ------ | ------------------------------------------------------------------------------ |
| 41 | Refactor: Organize Project File Structure              | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/41) |
| 39 | Profile/Account Info Editing Usability Improvements    | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/39) |
| 38 | No Option to Remove Profile Image                      | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/38) |
| 37 | No Option to Change Password                           | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/37) |
| 33 | Improve Navigation Bar Styling                         | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/33) |
| 31 | Improve Navigation Typography & Spacing                | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/31) |
| 22 | UI Issue in course.html                                | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/22) |
| 21 | Tech stack container smaller in mobile view            | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/21) |
| 20 | Search bar size in projects page                       | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/20) |
| 19 | UI issue in main section of projects page              | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/19) |
| 17 | Service Cards Layout and Styling Issues                | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/17) |
| 16 | UI/UX Issues in “Technologies Used” & “Stay Connected” | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/16) |
| 15 | Mission & Vision section layout problems               | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/15) |
| 13 | Add a "Back to Home" Button                            | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/13) |
| 12 | Improve design of tandc.html page                      | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/12) |
| 11 | Improve readability of tandc.html page                 | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/11) |
| 9  | Add Google Sign-Up Option                              | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/9)  |
| 8  | Password Eye Icon Not Updating on Signup Page          | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/8)  |
| 7  | Replace Alerts with Styled Popups                      | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/7)  |
| 6  | Remove Social Media Login Cards from Login Page        | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/6)  |
| 4  | Add Show/Hide Password Option on Login Page            | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/4)  |

### 🎨 UI/UX Issues

| #  | Title                                    | Status | Link                                                                           |
| -- | ---------------------------------------- | ------ | ------------------------------------------------------------------------------ |
| 29 | Bad search bar placement in blog page    | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/29) |
| 28 | Blog card size is wide in search results | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/28) |
| 24 | Different UI usage in enroll.html        | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/24) |

### 🧾 Invalid Issues

| #  | Title                                           | Status | Link                                                                           |
| -- | ----------------------------------------------- | ------ | ------------------------------------------------------------------------------ |
| 26 | Phone number format restricted for 69 (Invalid) | Open   | [View](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues/26) |

---

## 👏 Thanks!

Your contributions make this project better—for everyone. Whether it’s a tiny typo fix or a new feature, **we appreciate you!** 💙


## 📌 Steps to Contribute

1. **Fork** the repository.
2. **Clone** your fork:
   ```bash
   git clone https://github.com/your-username/Creators-Space.git
   cd Creators-Space
   ```
   > Replace your-username with your GitHub username.
   
3. **Create a New Branch**
    Always create a new branch before making changes:
    ```bash
    git checkout -b your-feature-branch
    ```
    > Use a descriptive name like feature/toggle animation or fix/header-alignment.

4. **Make Your Changes**  
  
   Work on the feature or bug assigned to you.

   Make sure your code is clean, well-commented, and follows the project’s coding standards.

   If necessary, update documentation or add helpful comments.

5. **Stage and Commit Changes**
    ```bash
    git add .
    git commit -m "Your meaningful commit message"
    ```

6. **Push the branch to your GitHub fork**
    ```bash
    git push origin feature/your-branch-name
    ```

7. **Create a Pull Request(PR)**

    Open your forked repository on GitHub.

    Click the "Compare & pull request" button.

    Write a clear and concise title and description for your PR.

    Submit the PR.


---
### ⏳ 8. Wait for Review

A project maintainer will review your pull request.

You may be asked to make changes — don’t worry, that’s part of the collaborative process.

Once approved, your code will be merged into the main branch.

## To run locally
Clone the repository:

```bash
git clone https://github.com/your-username/Creators-Space.git
cd Creators-Space

# Open in browser
Open index.html in your preferred web browser
```

## How to Contribute Without Coding
You can support the project in many non-coding ways:
- **Testing:** Run the project and report any bugs or unexpected behavior.
- **Issue Creation:** Open GitHub Issues to document problems, errors, or suggestions.
- **Documentation:** Fix typos, improve clarity, or add missing instructions.
- **Screenshots / Visuals:** Add or update screenshots, diagrams, or illustrations.
- **Ideas & Feedback:** Suggest improvements for features, design, or usability.
- **Reviewing PRs:** Read other people’s contributions and give constructive feedback.

## Workflow
1. Fork this repository.
2. Create a new branch for your changes.
   ```bash
   git checkout -b docs/non-coder-contribution
