<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    protected $fillable = ['score', 'feedback', 'status', 'review_note'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Not started',
            'submitted' => 'Awaiting review',
            'returned' => 'Changes requested',
            'published' => 'Published',
        };
    }
}

