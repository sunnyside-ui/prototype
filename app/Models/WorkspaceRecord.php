<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceRecord extends Model
{
    protected $fillable = ['module', 'evaluation_id', 'student_id', 'title', 'details', 'status', 'scheduled_on', 'task_id', 'attachment_path', 'attachment_name'];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


