<?php

namespace Modules\SystemSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monitoring extends Model
{
    use HasFactory;

    protected $table = 'monitoring';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'address',
    ];

}
