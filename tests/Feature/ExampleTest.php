<?php

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('signs in, protects role pages and signs out', function () {
    $this->seed();
    $this->get('/student')->assertRedirect('/login');
    $this->post('/login', ['email' => 'student@demo.test', 'password' => 'wrong'])->assertSessionHasErrors('email');
    $this->post('/login', ['email' => 'student@demo.test', 'password' => 'Progress2026!'])->assertRedirect('/');
    $this->get('/')->assertRedirect('/student');
    $this->get('/student')->assertOk()->assertSee('My academic progress');
    $this->get('/coordinator')->assertForbidden();
    $this->post('/logout')->assertRedirect('/login');
    $this->get('/student')->assertRedirect('/login');
});

it('validates, returns, resubmits and publishes an assigned evaluation', function () {
    $this->seed();
    $student = User::whereRole('student')->firstOrFail();
    $instructor = User::whereRole('instructor')->firstOrFail();
    $coordinator = User::whereRole('coordinator')->firstOrFail();
    $record = Evaluation::whereSubjectCode('ITE 116')->firstOrFail();
    $submit = route('instructor.submit', $record);
    $review = route('coordinator.review', $record);
    $studentResult = route('student.show', $record);
    $this->actingAs($instructor)->get(route('instructor.grades'))->assertOk()->assertSee('Course evaluations');
    $this->post($submit, ['score' => 101, 'feedback' => 'Example'])->assertSessionHasErrors('score');
    expect($record->fresh()->status)->toBe('draft');
    $this->post($submit, ['score' => 90, 'feedback' => 'Initial result'])->assertRedirect();
    expect($record->fresh()->status)->toBe('submitted');
    $this->actingAs($student)->get($studentResult)->assertOk()->assertDontSee('Initial result');
    $this->post($review, ['decision' => 'published'])->assertForbidden();
    $this->actingAs($coordinator)->get(route('coordinator.grades'))->assertOk()->assertSee('Evaluation review');
    $this->post($review, ['decision' => 'returned'])->assertSessionHasErrors('review_note');
    $this->post($review, ['decision' => 'returned', 'review_note' => 'Clarify the next step.'])->assertRedirect();
    $this->actingAs($instructor)->get(route('instructor.show', $record))->assertOk()->assertSee('Clarify the next step.');
    $this->post($submit, ['score' => 94, 'feedback' => 'Revise the database diagram.'])->assertRedirect();
    $this->actingAs($coordinator)->post($review, ['decision' => 'published'])->assertRedirect();
    $this->actingAs($student)->get($studentResult)->assertOk()->assertSee('Revise the database diagram.');
    $otherStudent = User::factory()->create(['role' => 'student']);
    $this->actingAs($otherStudent)->get($studentResult)->assertNotFound();
    $this->actingAs($instructor)->post($submit, ['score' => 1, 'feedback' => 'Stale update'])->assertRedirect();
    expect($record->fresh()->score)->toBe(94);
});


