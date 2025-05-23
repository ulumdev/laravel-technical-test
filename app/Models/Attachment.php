<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'task_id',
        'file_path',
        'info',
        'is_public',
        'uploaded_at',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
