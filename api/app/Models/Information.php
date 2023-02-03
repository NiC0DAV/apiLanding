<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    use HasFactory;

    protected $table = 'information';

    protected $fillable = [
        'name',
        'status',
        'welcomeSection',
        'servicesSection',
        'CharacteristicsSection',
        'footerSection',
        'facebookUrl',
        'instagramUrl',
        'whatsappNumber'
    ];
    
    protected $casts = [
        'welcomeSection' => 'array',
        'servicesSection' => 'array',
        'CharacteristicsSection' => 'array',
        'footerSection' => 'array'
    ];
}
