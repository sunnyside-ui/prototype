<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicTracker extends Model
{
    protected $fillable = ['student_id', 'name', 'periods'];

    protected function casts(): array
    {
        return ['periods' => 'array'];
    }

    public function student()
    {
        return $this->belongsTo(User::class);
    }
}
