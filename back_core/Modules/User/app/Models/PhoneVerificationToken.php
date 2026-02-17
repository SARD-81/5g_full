<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PhoneVerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'expired_at',
        'token',
        'phone',
    ];
}
