<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Project extends Model implements Auditable
{
    use SoftDeletes, HasFactory, \OwenIt\Auditing\Auditable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'details',
        'is_active',
        'start_date',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Audit Note
    public $auditCustomNote = null;
    public function transformAudit(array $data): array
    {
        $data['custom_note'] = $this->auditCustomNote ?? null;
        return $data;
    }
}
