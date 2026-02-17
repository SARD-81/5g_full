<?php

namespace Modules\Server\Models;

use Modules\User\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\Server\Database\Factories\ServerFactory;

class Server extends Model
{
    use HasFactory, HasRoles;


    const OFF = 1;
    const ON = 0;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'ip',
        'path_config',
        'path_run_config',
        'is_down',
    ];


    protected $guard_name = 'web';


    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logAll()
    //         ->logOnlyDirty()
    //         ->useLogName('server');
    // }


        // oen to many
    // public function modules()
    // {
    //     return $this->hasMany(Module::class);
    // }

    public function modules ()
    {
        return $this->belongsToMany(Module::class)
        ->withPivot('initial_config', 'previous_config', 'current_config');
    }

    public function users ()
    {
        return $this->hasMany(User::class);
    }
}
