<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Task extends Model implements Auditable
{
    use SoftDeletes, HasFactory, \OwenIt\Auditing\Auditable;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'project_id',
        'title',
        'metadata',
        'is_done',
        'due_date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // Audit Note
    public $auditCustomNote = null;
    public function transformAudit(array $data): array
    {
        $data['custom_note'] = $this->auditCustomNote ?? null;
        return $data;
    }
}
