# Student Monitoring and Evaluation System

SPUP SITE · Expanded Tertiary Education Equivalency and Accreditation Program (ETEEAP)

**Group members** · Add the approved group roster before submission.

This Laravel prototype connects course evaluation and academic progress tracking for ETEEAP. Instructors submit scores and feedback, coordinators review them, and students see their published results.

| Role | Prototype actions | Email |
|---|---|---|
| Student | Find assigned subjects and read personal published results | student@demo.test |
| Instructor | Open assigned records, submit evaluations, revise returned evaluations | instructor@demo.test |
| Coordinator | Review submitted evaluations, return for changes, publish results | coordinator@demo.test |

All demo accounts use `Progress2026!`. Use synthetic data only. These accounts are for local demonstrations.

## Setup from a fresh clone

Requires PHP compatible with Composer’s lockfile (PHP 8.5 verified where available), Composer, and the PDO SQLite extension. CSS is served directly, so the prototype UI can run without a frontend build.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File database/database.sqlite -Force
php artisan migrate --seed
php artisan serve
```

Visit http://127.0.0.1:8000. For an existing install, keep the existing .env and database and run `php artisan migrate --seed`.

## Demonstration

1. Sign in as instructor. Open ITE 116, enter a score of 0–100 and feedback, and send for review. For server validation, submit an invalid score from a crafted request or temporarily remove the browser’s input constraint during the demo.
2. Sign in as coordinator. Open the pending result. Return it with a reason, then have the instructor revise and resubmit. Publish it.
3. Sign in as student. Open ITE 116 and read the score and feedback. Attempt to visit /coordinator to show the access restriction.
4. Sign out and visit /student to show the login redirect.
5. Walk through routes/web.php, RequireRole, and EvaluationController.

The prototype demonstrates the evaluation lifecycle. Attendance, schedules, accreditation, and program reporting remain research scope for later implementation. See AGENT_LOG.md for design sources and decisions.


## Expanded workspaces

- Teachers manage lessons, attendance, schedules, assignments, projects, and competency records for assigned learners. They can review submissions and publish grades through the existing coordinator review workflow.
- Coordinators manage accounts, application decisions, requirements, enrollment, teacher assignments, the course catalog, schedules, accreditation, and announcements. They can open the printable progress report.
- Students can see their profile, application and requirements, assigned subjects and teachers, class records, competency results, coursework, and published grades. Coursework submissions accept text or a share link.

The progress percentage is published evaluations divided by assigned subject records. Notifications are an in-app list of evaluation and announcement updates. Subject enrollments remain per learner. The course catalog is maintained separately from enrollment records.
