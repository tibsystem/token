<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'path',
        'type_file',
        'size',
    ];

    public function fileable()
    {
        return $this->morphTo();
    }
}
