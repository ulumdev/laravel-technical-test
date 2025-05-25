<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Attachment extends Model implements Auditable
{

    use SoftDeletes, HasFactory, \OwenIt\Auditing\Auditable;

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

    // Audit Note
    public $auditCustomNote = null;
    public function transformAudit(array $data): array
    {
        $data['custom_note'] = $this->auditCustomNote ?? null;
        return $data;
    }
}
