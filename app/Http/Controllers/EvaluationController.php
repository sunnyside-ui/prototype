<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->user()->role;
        $base = Evaluation::query()->with(['student', 'instructor']);
        match ($role) {
            'student' => $base->where('student_id', $request->user()->id),
            'instructor' => $base->where('instructor_id', $request->user()->id),
            'coordinator' => $base,
        };
        $actionOnly = $request->query('status') === 'action' && $role !== 'student';
        if ($actionOnly) {
            $base->whereIn('status', $role === 'coordinator' ? ['submitted'] : ['draft', 'returned']);
        }
        $query = trim($request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? '');
        $records = $base->when($query !== '', function (Builder $records) use ($query) {
            $search = '%'.$query.'%';
            $records->where(function (Builder $matches) use ($search) {
                $matches->where('subject', 'like', $search)
                    ->orWhere('subject_code', 'like', $search)
                    ->orWhereHas('student', fn (Builder $student) => $student->where('name', 'like', $search));
            });
        })->orderByRaw("CASE status WHEN 'submitted' THEN 0 WHEN 'returned' THEN 1 WHEN 'draft' THEN 2 ELSE 3 END")
            ->orderByDesc('updated_at')->orderBy('id')->paginate(10)->withQueryString();

        return view('evaluations.index', compact('role', 'records', 'query', 'actionOnly'));
    }

    public function show(Request $request, Evaluation $evaluation): View
    {
        $role = $request->user()->role;
        abort_unless(match ($role) {
            'student' => $evaluation->student_id === $request->user()->id,
            'instructor' => $evaluation->instructor_id === $request->user()->id,
            'coordinator' => true,
        }, 404);

        return view('evaluations.show', compact('evaluation', 'role'));
    }

    public function submit(Request $request, Evaluation $evaluation): RedirectResponse
    {
        abort_unless($evaluation->instructor_id === $request->user()->id, 404);
        $data = $request->validate([
            'score' => ['required', 'integer', 'between:0,100'],
            'feedback' => ['required', 'string', 'max:2000'],
        ], ['score.between' => 'Enter a score from 0 to 100.', 'score.integer' => 'Enter a whole number.']);

        $changed = Evaluation::whereKey($evaluation->id)->whereIn('status', ['draft', 'returned'])
            ->update($data + ['status' => 'submitted', 'review_note' => null]);

        return back()->with('notice', $changed ? 'Evaluation sent to the coordinator.' : 'This evaluation has already moved to review or publication.');
    }

    public function review(Request $request, Evaluation $evaluation): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:published,returned'],
            'review_note' => ['nullable', 'required_if:decision,returned', 'string', 'max:1000'],
        ], ['review_note.required_if' => 'Tell the instructor what needs to change.']);

        $changed = Evaluation::whereKey($evaluation->id)->where('status', 'submitted')->update([
            'status' => $data['decision'],
            'review_note' => $data['decision'] === 'returned' ? $data['review_note'] : null,
        ]);

        $message = $data['decision'] === 'published'
            ? 'Result published. The student can now see it.'
            : 'Evaluation returned to the instructor.';

        return back()->with('notice', $changed ? $message : 'This evaluation has already been reviewed.');
    }
}


