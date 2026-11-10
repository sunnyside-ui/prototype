<?php

use App\Models\AcademicTracker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores independent trackers and exports a saved summer term for the coordinator', function () {
    $student = User::factory()->create(['role'=>'student']);
    $coordinator = User::factory()->create(['role'=>'coordinator']);
    $this->actingAs($student)->get(route('coordinator.trackers.index'))->assertForbidden();
    $this->actingAs($coordinator)->get(route('coordinator.trackers.index'))->assertOk();
    $form = ['student_id'=>$student->id, 'name'=>'Enrollment plan'];
    $this->post(route('coordinator.trackers.store'), $form)->assertRedirect();
    $this->post(route('coordinator.trackers.store'), $form)->assertRedirect();
    $tracker = AcademicTracker::firstOrFail();
    $data = ['academic_year'=>'2026-2027', 'rows'=>[['code'=>'TEST 101','title'=>'Sample course','work'=>'1'], ['code'=>null,'title'=>null]]];
    $this->post(route('coordinator.trackers.update',[$tracker,'3-summer']), $data)->assertNotFound();
    $this->post(route('coordinator.trackers.update',[$tracker,'2-summer']), $data)->assertRedirect();
    expect($tracker->fresh()->periods['2-summer']['rows'])->toHaveCount(1);
    expect(AcademicTracker::latest('id')->first()->periods['2-summer']['rows'][0]['code'])->toBe('NST 102');
    $this->get(route('coordinator.trackers.show',[$tracker,'term'=>'2-summer']))->assertOk()->assertSee('Sample course')
        ->assertSee('<th scope="col">Work<br>experience</th>', false)
        ->assertSee('<th scope="col">Course<br>related</th>', false)
        ->assertSee('<th scope="col">Seminars/<br>Trainings</th>', false)
        ->assertSee('<th scope="col">To be<br>enrolled</th>', false)
        ->assertDontSee('@else');
    $download = $this->get(route('coordinator.trackers.download',$tracker))->assertDownload();
    expect($download->streamedContent())->toContain('TEST 101','2026-2027','Yes');
});


