<?php

namespace App\Http\Controllers;

use App\Models\AcademicTracker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicTrackerController extends Controller
{
    private const TERMS = [
        '1-first' => 'First year · First semester', '1-second' => 'First year · Second semester', '1-summer' => 'First year · Summer',
        '2-first' => 'Second year · First semester', '2-second' => 'Second year · Second semester', '2-summer' => 'Second year · Summer',
        '3-first' => 'Third year · First semester', '3-second' => 'Third year · Second semester',
        '4-first' => 'Fourth year · First semester', '4-second' => 'Fourth year · Second semester',
    ];

    private const CHECKS = [
        'work' => 'Work Experience', 'course' => 'Course Related',
        'training' => 'Seminars/Trainings', 'enroll' => 'To be Enrolled',
    ];

    public function index()
    {
        $students = User::whereRole('student')->orderBy('name')->get();
        $trackers = AcademicTracker::with('student')->latest('updated_at')->paginate(20);

        return view('trackers.index', compact('students', 'trackers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', Rule::exists('users', 'id')->where('role', 'student')],
            'name' => ['required', 'string', 'max:120'],
        ]);
        $tracker = AcademicTracker::create($data + ['periods' => config('tracker_curriculum')]);

        return redirect()->route('coordinator.trackers.show', $tracker);
    }

    public function show(AcademicTracker $tracker)
    {
        return view('trackers.show', ['tracker' => $tracker, 'terms' => self::TERMS, 'checks' => self::CHECKS, 'periods' => $tracker->periods + config('tracker_curriculum')]);
    }

    public function update(Request $request, AcademicTracker $tracker, string $term)
    {
        abort_unless(isset(self::TERMS[$term]), 404);
        $rules = [
            'academic_year' => ['nullable', 'regex:/^\d{4}-\d{4}$/'],
            'rows' => ['nullable', 'array', 'max:100'],
            'rows.*' => ['array:code,title,units,planned_term,work,course,training,enroll'],
            'rows.*.code' => ['nullable', 'required_with:rows.*.title,rows.*.work,rows.*.course,rows.*.training,rows.*.enroll', 'string', 'max:30'],
            'rows.*.units' => ['nullable', 'integer', 'min:0', 'max:99'],
            'rows.*.planned_term' => ['nullable', 'string', 'max:60'],
            'rows.*.title' => ['nullable', 'required_with:rows.*.code', 'string', 'max:240'],
        ];
        foreach (self::CHECKS as $key => $label) {
            $rules['rows.*.'.$key] = ['sometimes', 'boolean'];
        }
        $data = $request->validate($rules, ['academic_year.regex' => 'Enter the academic year as YYYY-YYYY.']);
        $rows = collect($data['rows'] ?? [])->filter(fn ($row) => filled($row['code'] ?? null))->values()->all();
        $original = $tracker->getRawOriginal('periods');
        $periods = $tracker->periods;
        $periods[$term] = ['academic_year' => $data['academic_year'] ?? null, 'rows' => $rows];
        $changed = AcademicTracker::whereKey($tracker->id)->where('periods', $original)
            ->update(['periods' => json_encode($periods, JSON_THROW_ON_ERROR)]);

        return redirect()->route('coordinator.trackers.show', [$tracker, 'term' => $term])
            ->with('notice', $changed ? 'Term saved.' : 'Another edit was saved first. Review the term and try again.');
    }

    public function download(AcademicTracker $tracker)
    {
        return response()->streamDownload(function () use ($tracker) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Student', 'Form', 'Term', 'Academic Year', 'Course Code', 'Descriptive Title', 'Total Units', ...array_values(self::CHECKS), 'Semester & Academic Year'], ',', '"', '');
            foreach (self::TERMS as $key => $term) {
                $period = $tracker->periods[$key] ?? config('tracker_curriculum.'.$key) ?? [];
                foreach ($period['rows'] ?? [] as $row) {
                    $cells = [$tracker->student->name, $tracker->name, $term, $period['academic_year'] ?? '', $row['code'], $row['title'], (string) ($row['units'] ?? '')];
                    foreach (self::CHECKS as $check => $label) {
                        $cells[] = empty($row[$check]) ? 'No' : 'Yes';
                    }
                    $cells[] = $row['planned_term'] ?? '';
                    fputcsv($out, array_map(fn ($cell) => preg_match('/^\s*[=+@-]|^[\t\r\n]/', $cell) ? "'".$cell : $cell, $cells), ',', '"', '');
                }
            }
            fclose($out);
        }, 'academic-tracker-'.$tracker->id.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

