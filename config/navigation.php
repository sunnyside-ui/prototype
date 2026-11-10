<?php

return [
    'student' => [
        'study' => ['My classes', 'Subjects, materials and class dates.', ['subjects'=>'Subjects and teachers','lessons'=>'Lessons and materials','schedule'=>'Class schedule','attendance'=>'My attendance']],
        'work' => ['Coursework', 'Find assignments and turn in your work.', ['assignments'=>'Assignments and activities','projects'=>'Projects','submissions'=>'Submit and review work']],
        'results' => ['Results and progress', 'See feedback and competency results.', ['grades'=>'Grades and feedback','competencies'=>'Competency results']],
        'program' => ['My ETEEAP records', 'Check your application and remaining requirements.', ['applications'=>'Application status','requirements'=>'Requirements checklist','accreditation'=>'Accreditation status']],
    ],
    'instructor' => [
        'classes' => ['Classes and students', 'Find your learners and manage class records.', ['people'=>'Student list','subjects'=>'Assigned subjects','lessons'=>'Lessons and topics','schedule'=>'Class schedule','attendance'=>'Attendance']],
        'work' => ['Coursework', 'Set tasks and review submitted work.', ['assignments'=>'Assignments and activities','projects'=>'Projects','submissions'=>'Student submissions']],
        'results' => ['Assessments', 'Record scores and evaluate competencies.', ['grades'=>'Course evaluations','competencies'=>'Competency evaluations']],
    ],
    'coordinator' => [
        'admissions' => ['Admissions', 'Review applications and track program requirements.', ['applications'=>'Application forms and decisions','requirements'=>'Student requirements','accreditation'=>'Accreditation status']],
        'classes' => ['People and classes', 'Review teachers, their subjects and students.', ['people'=>'Teachers and students','schedule'=>'Class schedules']],
        'results' => ['Academic monitoring', 'Review evaluations and see how students are progressing.', ['grades'=>'Evaluation review','attendance'=>'Attendance overview','competencies'=>'Competency results','reports'=>'Student progress reports']],
    ],
];
