<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use App\Models\WorkspaceRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WorkspaceController extends Controller
{
    public function home(Request $request)
    {
        $role = $request->user()->role;
        $subjects = $this->subjects($request);
        $total = (clone $subjects)->count();
        $published = (clone $subjects)->where('status', 'published')->count();
        $awaiting = (clone $subjects)->where('status', 'submitted')->count();
        $attention = match ($role) {
            'coordinator' => WorkspaceRecord::whereModule('applications')->where('status', 'Pending')->count(),
            'instructor' => (clone $subjects)->whereIn('status', ['draft', 'returned'])->count(),
            'student' => 0,
        };
        $percent = $total ? round(100 * $published / $total) : 0;
        $upcoming = WorkspaceRecord::whereModule('schedule')->whereIn('evaluation_id', $subjects->select('id'))
            ->whereDate('scheduled_on', '>=', now('Asia/Manila')->toDateString())->orderBy('scheduled_on')->first();

        $openTasks = 0;
        if ($role === 'student') {
            $tasks = WorkspaceRecord::whereIn('module', ['assignments', 'projects'])->whereIn('evaluation_id', $subjects->select('id'));
            $openTasks = (clone $tasks)->whereNotIn('id', WorkspaceRecord::whereNotNull('task_id')->select('task_id'))->count();
        }

        return view('workspace.home', compact('role', 'total', 'published', 'awaiting', 'attention', 'percent', 'upcoming', 'openTasks'));
    }

    public function section(Request $request, string $section)
    {
        $role = $request->user()->role;
        $definition = config('navigation.'.$role)[$section] ?? null;
        abort_unless($definition, 404);
        [$title, $description, $pages] = $definition;

        return view('workspace.section', compact('role', 'title', 'description', 'pages', 'section'));
    }

    public function show(Request $request, string $module)
    {
        $role = $request->user()->role;
        if ($role === 'coordinator' && in_array($module, ['courses', 'subjects'], true)) {
            return redirect()->route('coordinator.module', 'people');
        }
        if ($module === 'profile') {
            return redirect()->route($role.'.profile');
        }
        $subjects = $this->subjects($request)->with(['student', 'instructor'])->orderBy('subject_code')->get();
        $students = $role === 'coordinator'
            ? User::whereRole('student')->orderBy('name')->get()
            : $subjects->pluck('student')->unique('id');

        if (in_array($module, ['people', 'subjects', 'reports', 'profile'], true)) {
            abort_unless(match ($module) {
                'people' => $role !== 'student',
                'reports' => $role === 'coordinator',
                'profile' => $role === 'student',
                default => true,
            }, 403);

            $teachers = $role === 'coordinator' ? User::whereRole('instructor')->orderBy('name')->get() : collect();

            return view('workspace.directory', compact('module', 'role', 'subjects', 'students', 'teachers'));
        }

        [$title, $scope, $editors, $statuses] = $this->definition($request, $module);
        $canEdit = in_array($role, $editors, true);
        $records = $scope === 'derived' ? collect() : $this->records($request, $module, $scope)
            ->when($module === 'submissions' && $role === 'student', fn (Builder $records) => $records->whereNull('task_id'))
            ->with(['evaluation.student', 'evaluation.instructor', 'student'])
            ->when($module === 'schedule', fn (Builder $records) => $records->orderBy('scheduled_on'))
            ->latest('updated_at')->paginate(15);
        $tasks = $module === 'submissions' && $role === 'student'
            ? WorkspaceRecord::whereIn('module', ['assignments', 'projects'])->whereIn('evaluation_id', $this->subjects($request)->select('id'))->with('evaluation')->get()
            : collect();

        $announcements = $module === 'notifications'
            ? WorkspaceRecord::whereModule('announcements')->latest('updated_at')->limit(5)->get()
            : collect();

        if ($module === 'submissions') {
            $submitted = $role === 'student' ? $this->records($request, $module, $scope)
                ->whereNotNull('task_id')->get()->keyBy('task_id') : collect();

            return view('workspace.submissions', compact('role', 'module', 'tasks', 'records', 'submitted'));
        }

        return view('workspace.module', compact('module', 'role', 'title', 'scope', 'statuses', 'canEdit', 'records', 'subjects', 'students', 'tasks', 'announcements'));
    }

    public function save(Request $request, string $module, ?int $id = null)
    {
        [$title, $scope, $editors, $statuses] = $this->definition($request, $module);
        abort_unless(in_array($request->user()->role, $editors, true), 403);
        $record = $id ? $this->records($request, $module, $scope)->findOrFail($id) : new WorkspaceRecord(['module' => $module]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'details' => ['nullable', 'string', 'max:5000'],
            'status' => $statuses ? ['required', Rule::in($statuses)] : ['prohibited'],
            'scheduled_on' => ['nullable', 'date_format:Y-m-d', ...in_array($module, ['schedule', 'attendance'], true) ? ['required'] : []],
            'student_id' => $scope === 'student' ? ['required', Rule::exists('users', 'id')->where('role', 'student')] : ['prohibited'],
            'evaluation_id' => $scope === 'subject' ? ['required', 'integer'] : ['prohibited'],
        ]);
        if ($scope === 'subject') {
            $this->subjects($request)->findOrFail($data['evaluation_id']);
        }

        $record->fill($data)->save();

        return back()->with('notice', $title.' record saved.');
    }

    public function submit(Request $request)
    {
        abort_unless($request->user()->role === 'student', 403);
        $data = $request->validate([
            'task' => ['required', 'integer'],
            'details' => ['nullable', 'required_without:attachment', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,docx,pptx,xlsx,txt,png,jpg,jpeg', 'max:10240'],
        ]);
        $task = WorkspaceRecord::whereIn('module', ['assignments', 'projects'])
            ->whereIn('evaluation_id', $this->subjects($request)->select('id'))->findOrFail($data['task']);

        if (WorkspaceRecord::where('task_id', $task->id)->exists()) {
            return back()->with('notice', 'You already submitted this task.');
        }

        $attachment = $request->file('attachment');
        $path = $attachment?->store('coursework', 'local');
        if ($attachment && ! $path) {
            return back()->withErrors(['attachment' => 'The file could not be saved. Please try again.'])->withInput();
        }

        try {
            $submission = WorkspaceRecord::firstOrCreate(['task_id' => $task->id], [
                'module' => 'submissions', 'evaluation_id' => $task->evaluation_id,
                'title' => $task->title, 'details' => $data['details'] ?? null, 'status' => 'Submitted',
                'attachment_path' => $path, 'attachment_name' => $attachment?->getClientOriginalName(),
            ]);
        } catch (\Throwable $error) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            throw $error;
        }

        if (! $submission->wasRecentlyCreated && $path) {
            Storage::disk('local')->delete($path);
        }

        return back()->with('notice', $submission->wasRecentlyCreated
            ? 'Your work was submitted.' : 'You already submitted this task.');
    }

    public function attachment(Request $request, int $id)
    {
        $submission = $this->records($request, 'submissions', 'subject')->findOrFail($id);
        abort_unless($submission->attachment_path && Storage::disk('local')->exists($submission->attachment_path), 404);

        return Storage::disk('local')->download($submission->attachment_path, $submission->attachment_name);
    }

    public function apply(Request $request)
    {
        $student = $request->user();
        abort_unless($student->role === 'student', 403);
        $data = $request->validate([
            'program' => ['required', 'string', 'max:120'],
            'details' => ['required', 'string', 'max:5000'],
        ]);

        $application = WorkspaceRecord::firstOrCreate(
            ['module' => 'applications', 'student_id' => $student->id],
            ['title' => $data['program'], 'details' => $data['details'], 'status' => 'Pending'],
        );

        return back()->with('notice', $application->wasRecentlyCreated
            ? 'Application sent to the coordinator.'
            : 'An application is already on file. Contact the coordinator for changes.');
    }

    public function person(Request $request, ?int $id = null)
    {
        abort_unless($request->user()->role === 'coordinator', 403);
        $person = $id ? User::whereIn('role', ['student', 'instructor'])->findOrFail($id) : new User;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($person->id)],
            'role' => $id ? ['prohibited'] : ['required', Rule::in(['student', 'instructor'])],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:10', 'max:72'],
            'student_status' => ['nullable', Rule::in(['Active', 'Inactive', 'Completed'])],
        ]);
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $person->forceFill($data)->save();

        return back()->with('notice', 'Account saved.');
    }

    public function enroll(Request $request, ?int $id = null)
    {
        abort_unless($request->user()->role === 'coordinator', 403);
        $record = $id ? Evaluation::findOrFail($id) : new Evaluation(['status' => 'draft']);
        $data = $request->validate([
            'student_id' => $id ? ['prohibited'] : ['required', Rule::exists('users', 'id')->where('role', 'student')],
            'instructor_id' => ['required', Rule::exists('users', 'id')->where('role', 'instructor')],
            'subject_code' => $id ? ['prohibited'] : ['required', 'string', 'max:20', Rule::unique('evaluations')->where('student_id', $request->input('student_id'))],
            'subject' => ['required', 'string', 'max:160'],
        ]);
        $record->forceFill($data)->save();

        return back()->with('notice', 'Student and teacher assignment saved.');
    }

    private function definition(Request $request, string $module): array
    {
        $definition = config('workspace')[$module] ?? null;
        abort_unless($definition, 404);
        abort_if($module === 'courses' && $request->user()->role !== 'coordinator', 403);
        abort_if($request->user()->role === 'instructor' && $definition[1] === 'student', 403);

        return $definition;
    }

    private function subjects(Request $request): Builder
    {
        $user = $request->user();

        return match ($user->role) {
            'student' => Evaluation::where('student_id', $user->id),
            'instructor' => Evaluation::where('instructor_id', $user->id),
            'coordinator' => Evaluation::query(),
        };
    }

    private function records(Request $request, string $module, string $scope): Builder
    {
        $records = WorkspaceRecord::where('module', $module);
        if ($scope === 'subject') {
            $records->whereIn('evaluation_id', $this->subjects($request)->select('id'));
        }
        if ($scope === 'student' && $request->user()->role === 'student') {
            $records->where('student_id', $request->user()->id);
        }

        return $records;
    }
}







