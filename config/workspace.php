<?php

// Scope determines which records can be opened. Editors must also pass the role check.
return [
    'courses' => ['Subject and course catalog', 'global', ['coordinator'], []],
    'applications' => ['Applications', 'student', ['coordinator'], ['Pending', 'Approved', 'Rejected']],
    'requirements' => ['Requirements checklist', 'student', ['coordinator'], ['Submitted', 'Missing', 'Verified']],
    'accreditation' => ['Accreditation status', 'student', ['coordinator'], ['Not started', 'Under review', 'Accredited']],
    'lessons' => ['Lessons and materials', 'subject', ['instructor'], ['To do', 'Completed']],
    'schedule' => ['Class schedule', 'subject', ['coordinator'], []],
    'attendance' => ['Attendance', 'subject', ['instructor'], ['Present', 'Absent', 'Late']],
    'assignments' => ['Assignments and activities', 'subject', ['instructor'], []],
    'projects' => ['Projects', 'subject', ['instructor'], []],
    'submissions' => ['Submissions', 'subject', [], []],
    'competencies' => ['Competency assessment', 'subject', ['instructor'], ['Developing', 'Competent']],
    'announcements' => ['Announcements', 'global', ['coordinator'], []],
    'notifications' => ['Notifications', 'derived', [], []],
];


