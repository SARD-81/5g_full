<?php

namespace Modules\Server\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

// use Modules\Server\Database\Factories\ModuleScheduleFactory;

class ModuleSchedule extends Model
{
    use HasFactory;

    const WAITING = 0;
    const RUNNING = 1;
    const FAILED  = 2;
    const SUCCESS = 3;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'module_id',
        'server_id',
        'run_scheduled_at',
        'status',
        'config',
        'username_ssh',
        'password_ssh',
        'port_ssh',
    ];

    protected $casts = [
        'run_scheduled_at' => 'datetime',
    ];

    protected function usernameSsh(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? null : Crypt::decryptString($value),
            set: fn ($value) => $value === null ? null : Crypt::encryptString($value),
        );
    }

    protected function passwordSsh(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? null : Crypt::decryptString($value),
            set: fn ($value) => $value === null ? null : Crypt::encryptString($value),
        );
    }


    protected function getStatusName(int $status): ?string
    {
        $reflect = new \ReflectionClass($this);
        $constants = $reflect->getConstants();

        return array_search($status, $constants, true) ?: null;
    }
    public function toArray(): array
    {
        $array = parent::toArray();

        if (isset($array['status'])) $array['status'] = $this->getStatusName($array['status']);

        return $array;
    }




    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

}
