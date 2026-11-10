<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [];
        foreach (['student' => 'Mara Reyes', 'instructor' => 'Elena Cruz', 'coordinator' => 'Gabriel Santos'] as $role => $name) {
            $accounts[$role] = User::firstOrCreate(
                ['email' => $role.'@demo.test'],
                ['name' => $name, 'password' => Hash::make('Progress2026!')],
            );
            $accounts[$role]->forceFill(['role' => $role])->save();
        }

        foreach ([
            ['ITE 116', 'Integrative and Programming Technologies', 'draft', null, null],
            ['ITE 112', 'Database Management Systems', 'submitted', 88, 'The schema is clear. Add a short explanation of the foreign key choices.'],
            ['ITE 105', 'Human–Computer Interaction', 'published', 93, 'Your usability test identified clear improvements for the sign-in flow.'],
        ] as [$code, $subject, $status, $score, $feedback]) {
            $evaluation = new Evaluation(['status' => $status, 'score' => $score, 'feedback' => $feedback]);
            $evaluation->forceFill([
                'student_id' => $accounts['student']->id,
                'instructor_id' => $accounts['instructor']->id,
                'subject_code' => $code,
                'subject' => $subject,
            ]);
            if (! Evaluation::where($evaluation->only(['student_id', 'subject_code']))->exists()) {
                $evaluation->save();
            }
        }
        $this->call(WorkspaceSeeder::class);
    }
}


