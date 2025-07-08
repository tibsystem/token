<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = ['investor_id', 'name', 'email', 'document', 'password'];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
