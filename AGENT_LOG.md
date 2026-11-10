# Agent log

## 2026-11-09 — Remove duplicate sidebar profile entry

Removed My profile from the student navigation group. The persistent account card remains the single profile entry in the sidebar for every role.

## 2026-11-09 — Stabilize workspace navigation and profile access

Moved desktop scrolling into the menu so the brand, account profile link and Sign out stay visible. Restored scroll and mobile disclosure state immediately after the sidebar markup rather than after deferred page loading. Kept active link font weight consistent to avoid wrapping shifts and retained mobile state when crossing the desktop breakpoint. The account card opens the existing profile page for students, instructors and coordinators, with details and password forms. Verified all three profile routes and legacy redirects with feature coverage. All 12 Laravel tests (180 assertions), three JavaScript navigation tests, production build and Blade compilation pass. Updated an outdated dashboard label assertion. Browser visual verification remains outstanding.

## 2026-11-09 — Center the right login panel
Centered the login content horizontally and vertically inside its sticky desktop viewport panel using flex layout and auto margins. Short screens retain reachable overflow without visible scrollbars.

## 2026-11-09 — Remove left-panel scrolling
Made the desktop introduction panel a non-scrolling sticky viewport panel. Simplified decorative content on short screens so the panel fits without an internal scrollbar. Login remains on the right.

## 2026-11-09 — Correct panel placement
Restored the original arrangement after clarification: brand and introductory panel on the left, login form on the right, and brand above the form on mobile. Retained viewport anchoring and hidden entry scrollbars.

## 2026-11-09 — Anchor login on the left
Moved the desktop login form into the left column and kept both panels anchored to the viewport. Removed visible scrollbars only on the entry screen and tightened short-screen spacing, including expanded demo credentials. Overflow remains reachable by touch, wheel and keyboard on small or zoomed screens. Mobile shows the login first. Workspace scrolling is unchanged.

## 2026-11-08 — Stabilize navigation and sign-in
Preserved the sidebar scroll position across page visits with per-user session storage, retained the mobile menu state, and added slim lilac scrollbars. Anchored the desktop sign-in rail to the viewport and top-aligned the form so expanding demo credentials does not recenter the left copy or form. Mobile keeps its stacked layout. JavaScript syntax and mocked desktop/mobile restoration checks pass. Laravel view compilation is blocked by the installed PHP 8.2 runtime; dependencies require PHP 8.4.1 or newer.

## 2026-11-08 — Define the prototype
Read Judea.docx and both Capstone Format PDFs from Downloads. The system serves SPUP SITE’s ETEEAP program. The prototype centers on course-based evaluation, a primary feature in Chapter 1. Chapter 2 identifies the coordinator, instructor, and student as users.

- Instructor: Find an assigned learner and subject, enter a score and feedback, then submit. The form should make the learner, scoring scale, and next step clear.
- Coordinator: Find pending results, read the evidence, publish or return with a reason. The queue should make unfinished work easy to pick up.
- Student: Find an assigned subject and read their own published score and feedback. The screen should explain what is available and avoid exposing work under review.

Coordinator review and the 0–100 scale are prototype decisions. Confirm both with the program before the real build.

## 2026-11-08 — Research and UX walkthrough
- [Campusly](https://mmrahmanbappi.github.io/100-free-admin-dashboards/campusly/index.html) places recent modules beside an academic overview. Adapt its clear navigation and scannable summary, with real evaluation counts and original lilac and cream styling.
- [NHS service search](https://www.nhs.uk/service-search/pharmacy/find-a-pharmacy) gives concrete examples of what to enter. Search here accepts student names or subject codes.
- [USWDS Search](https://designsystem.digital.gov/components/search/) uses a named button and accessible label. Keep both visible on phones.
- [GOV.UK task list](https://design-system.service.gov.uk/patterns/complete-multiple-tasks/) places status alongside the task. Apply this to each assigned subject.
- [GOV.UK check answers](https://design-system.service.gov.uk/patterns/check-answers/) groups facts before confirmation. Show learner, score, and feedback before publication.
- [Carbon data tables](https://www.carbondesignsystem.com/building-blocks/core/components/data-table/guidelines) keeps comparable fields together. Use compact desktop rows that stack on phones.
- [Slack threads](https://slack.com/help/articles/115000769927-Use-threads-to-organize-discussions) keep replies in context. Keep return reasons attached to the evaluation.

Sign-in explains the expected credentials. Workspaces show role and next task. Records use text status as well as color. Validation retains input and explains how to fix it. Published results are read-only. The student view hides scores and feedback until publication.

## Instructions to preserve
Edit the local Desktop Peta Prototype Laravel project. Follow the linked ITE 116 brief. Keep the code simple, use lilac and cream, use SVG icons rather than emoji, and build for mobile, keyboard, dyslexia, older adults, and beginners. Maintain this log and a visual walkthrough for each role and its handoffs. Use synthetic demo records only.


## 2026-11-08 — Implement and verify
Built role-based authentication, searchable evaluation workspaces, and an SQLite-backed workflow from instructor submission through coordinator publication and student feedback. Replaced the starter HTTP test with coverage for role and record boundaries, invalid input, return and resubmission, publication, and stale updates. Added a six-slide PowerPoint with editable flow diagrams and screen mockups.

Remaining for the capstone team: confirm the scoring and review rules, add the group roster to README, and publish the Git repository with instructor access. Attendance, schedules, accreditation records, and program reports are outside the midterm workflow. Validate the interface with ETEEAP participants before real use.

## 2026-11-08 — Expand all three dashboards
Added role-specific navigation and dashboard links. Reused evaluation enrollments to scope academic records and kept the existing grade publication rules. Added course catalog, account management, applications and decisions, requirements, enrollment, accreditation, report view, lesson checkmarks, schedules, attendance, coursework, submissions, competencies, announcements, and an in-app update list. Added synthetic examples without resetting the existing evaluation database.

Progress means the percentage of assigned subject evaluations published. Coursework accepts text or a share link. Reports use browser printing. The catalog and enrollment records are maintained separately. File upload, notification delivery and read receipts, and cohort-wide scheduling remain future work. The original presentation documents the evaluation workflow.

## 2026-11-08 — Simplify navigation and role dashboards
Replaced the long feature grid and nested menus with role-specific sections, a dedicated grades page, and direct dashboard links for coursework, evaluations and applications. Added current work counts, progress and upcoming classes. Preserved existing module URLs, records and role checks. Verified the student dashboard and coursework form in the browser and tested all role sections and actionable evaluation filters.

## 2026-11-08 — Refine the portal design
Gave dashboards and section pages a common visual hierarchy. Added consistent SVG navigation icons, a stronger current-section indicator, and clearer links on section landing pages. Applied the shared lilac-and-cream palette across sign-in, dashboards, and records.

## 2026-11-08 — Rework coursework submission
Replaced the task dropdown with one entry per assignment or project. Show instructions, due date, status, and a submission receipt together. Added optional private attachments, one submission per task, and scoped downloads for the student, assigned teacher, and coordinator. Earlier submissions remain visible as a separate history.

## 2026-11-08 — Flatten navigation and unify buttons
Sidebar now lists every page directly under its group heading, so no extra landing-page click is needed. Section pages and routes still exist. Replaced "Back to" links with a breadcrumb, collapsed the menu on phones, and gave buttons one primary and one secondary style. Disclosure toggles such as Add record and Edit now look like buttons. Button labels say what they do. Verified that all Blade views compile; the full test suite was not run because the vendor folder needs PHP 8.4.

## 2026-11-08 — Dashboard, navigation and profile redesign
Dashboard now has a greeting banner, four icon summary cards that link to the matching page, one clear next step, six icon shortcuts and side cards for progress and schedule. Sidebar items have icons and the account block opens a new profile page for all roles. The profile page shows details and progress, edits name and email, changes the password (current password required) and links to related pages. /workspace/profile now redirects to it. Added a test for profile updates. Reference page could not be opened; the layout follows the pattern in the Campusly note above. Views compile; tests not run because vendor needs PHP 8.4.

## 2026-11-10 — Add academic trackers to the Downloads project
Coordinators can create multiple independent student trackers, edit ten curriculum terms, print or save a PDF through the browser, and download CSV. Summer terms exist for years one and two only. Course rows start blank pending the official paper reference for exact codes and titles.

## 2026-11-10 � Coordinator assignments and simpler class records
Coordinators assign students and subjects to teachers through the renamed Student and teacher assignments page. The people directory shows teachers first, with their student/subject rosters, then each student's teachers. Assignment forms retain invalid input and reopen for corrections. The catalog uses name and optional program fields; class schedules use assignment, meeting name, date and meeting details, show the teacher and sort by date. Only coordinators can create or change schedules; students and teachers retain scoped viewing. No existing records were removed.
Role walkthrough: coordinator opens Teachers and students, reviews a roster, opens Manage assignments and assigns a student/subject/teacher; the teacher sees that assigned student and schedule, and the student sees their subject, teacher and schedule. Added feature coverage for coordinator assignment, schedule permission and student isolation. Browser visual verification remains outstanding.
Validation: all 14 Laravel tests passed (216 assertions); all 3 JavaScript navigation tests passed; Blade view compilation and modified PHP syntax checks passed.
