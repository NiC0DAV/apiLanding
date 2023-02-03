<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;
    protected $table = 'prospects';

    protected $fillable = [
        'name',
        'email',
        'phoneNumber',
        'message'
    ];
}
