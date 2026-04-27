<?php

namespace Modules\Server\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Modules\Server\Utility\ModuleIdentity;
use Spatie\Activitylog\LogOptions;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'service_key',
        'type',
        'server_id',
        'initial_config',
        'previous_config',
        'current_config'
    ];

    protected static function booted(): void
    {
        static::creating(function (Module $module) {
            if (empty($module->service_key) && !empty($module->name)) {
                $module->service_key = ModuleIdentity::normalizeKey($module->name);
            }
        });

        static::updating(function (Module $module) {
            if ($module->isDirty('service_key')) {
                throw ValidationException::withMessages([
                    'service_key' => 'Module technical identity is immutable after creation.',
                ]);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->useLogName('module')
            ->logOnly(['id', 'name', 'service_key', 'type', 'server_id', 'created_at', 'updated_at']);
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class)
            ->withPivot('initial_config', 'previous_config', 'current_config');
    }
}
