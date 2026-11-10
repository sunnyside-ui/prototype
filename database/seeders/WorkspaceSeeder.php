<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\User;
use App\Models\WorkspaceRecord;
use Illuminate\Database\Seeder;

class WorkspaceSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::whereEmail('student@demo.test')->firstOrFail();
        $evaluation = Evaluation::where('student_id', $student->id)->where('subject_code', 'ITE 116')->firstOrFail();

        foreach ([
            ['courses', 'ITE 116 · Integrative and Programming Technologies', null, 'BS Information Technology', null],
            ['applications', 'ETEEAP intake application', 'Approved', 'Program: BS Information Technology. Demo intake reviewed by the coordinator.', null],
            ['requirements', 'Employment certificate', 'Verified', 'The coordinator has reviewed the certificate.', null],
            ['requirements', 'Prior learning portfolio', 'Missing', 'Prepare evidence of relevant work and competencies.', null],
            ['accreditation', 'Prior learning review', 'Under review', 'The coordinator is reviewing supporting records.', null],
            ['lessons', 'Planning an accessible prototype', 'Completed', 'Identify user roles and map their main tasks.', null],
            ['lessons', 'Server validation and access checks', 'To do', 'Review Laravel validation rules and role restrictions.', null],
            ['schedule', 'Prototype consultation', null, '10:00 AM · SITE consultation room', '2026-11-10'],
            ['attendance', 'Requirements consultation', 'Present', null, '2026-11-07'],
            ['assignments', 'Role workflow outline', null, 'Describe each role and one handoff. Submit your response or a share link.', '2026-11-12'],
            ['projects', 'Capstone prototype', null, 'Prepare the working evaluation flow and a short demonstration.', '2026-11-16'],
            ['competencies', 'Define role-based access', 'Competent', 'Identifies each role and the records it can access.', null],
            ['announcements', 'Prototype consultation week', null, 'Check your schedule for the assigned consultation date.', null],
        ] as [$module, $title, $status, $details, $date]) {
            $scope = config('workspace')[$module][1];
            WorkspaceRecord::firstOrCreate([
                'module' => $module,
                'title' => $title,
                'student_id' => $scope === 'student' ? $student->id : null,
                'evaluation_id' => $scope === 'subject' ? $evaluation->id : null,
            ], ['status' => $status, 'details' => $details, 'scheduled_on' => $date]);
        }
    }
}


