<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportJob extends Model
{
    protected $fillable = [
        'user_id',
        'entity',
        'file_name',
        'status',
        'message',
    ];
}
