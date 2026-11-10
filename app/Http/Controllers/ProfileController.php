<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $records = match ($user->role) {
            'student' => Evaluation::where('student_id', $user->id),
            'instructor' => Evaluation::where('instructor_id', $user->id),
            default => Evaluation::query(),
        };
        $total = (clone $records)->count();
        $published = (clone $records)->where('status', 'published')->count();
        $percent = $total ? round(100 * $published / $total) : 0;
        $role = $user->role;

        return view('workspace.profile', compact('user', 'role', 'total', 'published', 'percent'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->forceFill($data)->save();

        return redirect()->route($user->role.'.profile')->with('notice', 'Your details were updated.');
    }

    public function password(Request $request): RedirectResponse
    {
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:10', 'max:72', 'confirmed'],
        ]);
        $request->user()->forceFill(['password' => Hash::make($request->input('password'))])->save();

        return redirect()->route($request->user()->role.'.profile')->with('notice', 'Your password was changed.');
    }
}
