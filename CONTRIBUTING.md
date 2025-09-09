# Contributing Guide

## 🧭 TL;DR (Quick Start)

1. **Find an issue** in the table below (look for `good first issue`) → **assign yourself**.
2. **Create a branch** from `development` → `git checkout -b fix/issue-36-bookmarks`.
3. **Make changes** → **commit** using Conventional Commits (e.g., `fix: add bookmark button`).
4. **Open a Pull Request (PR)** into `development`, include `Fixes #36` in the description.
5. **Ask for a review** → make requested changes → **merge** when approved.
6. **Add your PR link** to the team’s Google Sheet.

---

## 💡 Ways You Can Contribute

* **Report bugs** (something broken or confusing).
* **Suggest enhancements** (new features or UX improvements).
* **Improve UI/UX** (spacing, colors, typography, layout).
* **Write documentation** (README/FAQs/screenshots).
* **Refactor** (clean up code or reorganize files).

*No code experience? You can still help by testing, writing issues, and improving docs.*

---

## 🗂 Project Workflow at a Glance

**Default branches:**

* `main` → stable, presentation-ready
* `development` → active work

**Your branches:**

* Features: `feat/<short-name>`
* Fixes: `fix/<short-name>`
* Docs: `docs/<short-name>`
* Refactors: `refactor/<short-name>`

**Examples**

* `feat/change-password-ui`
* `fix/bookmarks-not-saving`
* `docs/add-contribution-guide`

---

## 🔎 How to Find Something to Work On

1. Go to **Issues** → filter by:

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
