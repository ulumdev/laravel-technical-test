<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    protected $fillable = ['user_id', 'entity', 'file_name', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
