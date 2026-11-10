<?php

use App\Models\Evaluation;
use App\Models\User;
use App\Models\WorkspaceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('renders the dashboard sections for each role', function () {
    $this->seed();
    foreach (['student', 'instructor', 'coordinator'] as $role) {
        $this->actingAs(User::whereRole($role)->firstOrFail());
        $this->get(route($role.'.index'))->assertOk()->assertSee('Next on the schedule');
        foreach (array_keys(config('navigation.'.$role)) as $section) {
            $this->get(route($role.'.section', $section))->assertOk();
        }
        $this->get(route($role.'.grades'))->assertOk();
        $this->get(route($role.'.profile'))->assertOk()->assertSee('Personal details');
        $modules = ['subjects', 'lessons', 'schedule', 'attendance', 'assignments', 'projects', 'submissions', 'competencies', 'announcements', 'notifications'];
        $modules = array_merge($modules, match ($role) {
            'student' => ['applications', 'requirements', 'accreditation'],
            'instructor' => ['people'],
            'coordinator' => ['people', 'reports', 'applications', 'requirements', 'accreditation'],
        });
        foreach ($modules as $module) {
            if ($role === 'coordinator' && $module === 'subjects') {
                $this->get(route($role.'.module', $module))->assertRedirect(route('coordinator.module', 'people'));
                continue;
            }
            $this->get(route($role.'.module', $module))->assertOk();
        }
    }
});

it('validates scoped attendance and passes coursework to the assigned teacher', function () {
    $this->seed();
    $student = User::whereRole('student')->firstOrFail();
    $teacher = User::whereRole('instructor')->firstOrFail();
    $evaluation = Evaluation::whereSubjectCode('ITE 116')->firstOrFail();
    $task = WorkspaceRecord::whereModule('assignments')->firstOrFail();
    $attendance = route('instructor.record.save', 'attendance');
    $this->actingAs($teacher)->post($attendance, [
        'title' => 'Practice class', 'evaluation_id' => $evaluation->id, 'status' => 'Unknown',
    ])->assertSessionHasErrors(['status', 'scheduled_on']);
    $this->post($attendance, [
        'title' => 'Practice class', 'evaluation_id' => $evaluation->id, 'status' => 'Late', 'scheduled_on' => '2026-11-09',
    ])->assertRedirect();
    $this->actingAs($student)->get(route('student.module','attendance'))->assertOk()->assertSee('Practice class')->assertSee('Late');
    $this->post(route('student.work.submit'), ['task' => $task->id, 'details' => 'My prototype outline'])->assertRedirect();
    $this->actingAs($teacher)->get(route('instructor.module','submissions'))->assertOk()->assertSee('My prototype outline');
    $other = User::factory()->create(['role' => 'student']);
    $this->actingAs($other)->post(route('student.work.submit'), ['task' => $task->id, 'details' => 'Unauthorized'])->assertNotFound();
    $this->get(route('student.module','attendance'))->assertOk()->assertDontSee('Practice class');
});

it('passes a new student application to the coordinator and shows the decision', function () {
    $this->seed();
    $student = User::factory()->create(['role' => 'student']);
    $coordinator = User::whereRole('coordinator')->firstOrFail();
    $this->actingAs($student)->get(route('student.module', 'applications'))->assertOk()->assertSee('Send application');
    $this->post(route('student.application.submit'), ['program' => 'BS Information Technology', 'details' => 'Five years of relevant work.'])->assertRedirect();
    $application = WorkspaceRecord::whereModule('applications')->where('student_id', $student->id)->firstOrFail();
    expect($application->status)->toBe('Pending');
    $this->actingAs($coordinator)->get(route('coordinator.module', 'courses'))->assertRedirect(route('coordinator.module', 'people'));
    $this->get(route('coordinator.module', 'applications'))->assertOk()->assertSee('Five years of relevant work.');
    $this->post(route('coordinator.record.save', ['applications', $application->id]), [
        'title' => $application->title, 'student_id' => $student->id, 'details' => 'Reviewed by the coordinator.', 'status' => 'Approved',
    ])->assertRedirect();
    $this->actingAs($student)->get(route('student.module', 'applications'))->assertOk()->assertSee('Reviewed by the coordinator.')->assertSee('Approved');
    $this->get(route('student.module', 'courses'))->assertForbidden();
});


it('shows only actionable evaluations from staff dashboard shortcuts', function () {
    $this->seed();
    $this->actingAs(User::whereRole('coordinator')->firstOrFail())
        ->get(route('coordinator.grades', ['status' => 'action']))
        ->assertOk()->assertSee('ITE 112')->assertDontSee('ITE 105')->assertDontSee('ITE 116');
    $this->actingAs(User::whereRole('instructor')->firstOrFail())
        ->get(route('instructor.grades', ['status' => 'action']))
        ->assertOk()->assertSee('ITE 116')->assertDontSee('ITE 105')->assertDontSee('ITE 112');
    $this->actingAs(User::whereRole('student')->firstOrFail())
        ->get(route('student.section', 'admissions'))->assertNotFound();
});

it('stores attachments privately, prevents duplicate submissions and checks download access', function () {
    $this->seed();
    \Illuminate\Support\Facades\Storage::fake('local');
    $student = User::whereRole('student')->firstOrFail();
    $teacher = User::whereRole('instructor')->firstOrFail();
    $task = WorkspaceRecord::whereModule('assignments')->firstOrFail();
    $submit = route('student.work.submit');
    $this->actingAs($student)->post($submit, ['task' => $task->id])->assertSessionHasErrors('details');
    $this->post($submit, [
        'task' => $task->id,
        'attachment' => \Illuminate\Http\UploadedFile::fake()->createWithContent('response.txt', 'Coursework response'),
    ])->assertRedirect();
    $submission = WorkspaceRecord::where('task_id', $task->id)->firstOrFail();
    \Illuminate\Support\Facades\Storage::disk('local')->assertExists($submission->attachment_path);
    $this->post($submit, ['task' => $task->id, 'details' => 'Duplicate'])->assertSessionHas('notice', 'You already submitted this task.');
    expect(WorkspaceRecord::where('task_id', $task->id)->count())->toBe(1);
    $this->get(route('student.module', 'submissions'))->assertOk()->assertSee('response.txt');
    $this->actingAs($teacher)->get(route('instructor.work.attachment', $submission->id))->assertDownload('response.txt');
    $this->actingAs(User::factory()->create(['role' => 'student']))
        ->get(route('student.work.attachment', $submission->id))->assertNotFound();
});

it('opens the signed in account profile for every role', function (string $role) {
    $this->seed();
    $user = User::whereRole($role)->firstOrFail();
    $profile = route($role.'.profile');

    $this->actingAs($user)->get(route($role.'.index'))
        ->assertOk()->assertSee('href="'.$profile.'"', false)->assertSee('View profile');
    $this->get($profile)->assertOk()->assertSee($user->name)->assertSee($user->email);
    $this->get(route($role.'.module', 'profile'))->assertRedirect($profile);
})->with(['student', 'instructor', 'coordinator']);

it('lets a user update their details and change their password', function () {
    $this->seed();
    $user = User::whereRole('student')->firstOrFail();
    $this->actingAs($user);

    $this->post(route('student.profile.update'), ['name' => 'Updated Name', 'email' => $user->email])
        ->assertRedirect(route('student.profile'));
    expect($user->fresh()->name)->toBe('Updated Name');

    $this->post(route('student.profile.password'), ['current_password' => 'wrong', 'password' => 'NewPassword2026', 'password_confirmation' => 'NewPassword2026'])
        ->assertSessionHasErrorsIn('password', 'current_password');

    $this->post(route('student.profile.password'), ['current_password' => 'Progress2026!', 'password' => 'NewPassword2026', 'password_confirmation' => 'NewPassword2026'])
        ->assertRedirect(route('student.profile'));
    expect(Hash::check('NewPassword2026', $user->fresh()->password))->toBeTrue();
});

it('lets only coordinators assign teachers and schedule their students', function () {
    $this->seed();
    $coordinator = User::whereRole('coordinator')->firstOrFail();
    $teacher = User::factory()->create(['role' => 'instructor']);
    $student = User::factory()->create(['role' => 'student']);
    $this->actingAs($coordinator)->post(route('coordinator.enrollment.save'), [
        'student_id' => $student->id, 'instructor_id' => $teacher->id,
        'subject_code' => 'ITE 200', 'subject' => 'Systems Design',
    ])->assertRedirect()->assertSessionHasNoErrors();
    $assignment = Evaluation::where('student_id', $student->id)->firstOrFail();
    $this->get(route('coordinator.module', 'people'))->assertOk()
        ->assertSee('Courses / subjects taught')->assertSee($student->name)->assertSee($teacher->name)
        ->assertSeeInOrder([$teacher->name, 'Courses / subjects taught', 'ITE 200', 'Systems Design', $student->name])
        ->assertDontSee('Subject and course catalog')->assertDontSee('Student and teacher assignments')
        ->assertDontSee('Manage assignments');
    $payload = ['evaluation_id' => $assignment->id, 'title' => 'Design consultation', 'scheduled_on' => '2026-11-15', 'details' => '10 AM, Room 2'];
    $this->post(route('coordinator.record.save', 'schedule'), $payload)->assertRedirect()->assertSessionHasNoErrors();
    $this->actingAs($teacher)->get(route('instructor.module', 'schedule'))
        ->assertOk()->assertSee('Design consultation')->assertDontSee('Schedule class');
    $this->post(route('instructor.record.save', 'schedule'), $payload)->assertForbidden();
    $this->actingAs($student)->get(route('student.module', 'schedule'))->assertOk()->assertSee('Design consultation')->assertSee($teacher->name);
    $otherStudent = User::factory()->create(['role' => 'student']);
    $this->actingAs($otherStudent)->get(route('student.module', 'schedule'))->assertOk()->assertDontSee('Design consultation');
});
