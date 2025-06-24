<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartContractModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'solidity_code',
        'version',
    ];
}
