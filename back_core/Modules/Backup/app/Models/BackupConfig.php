<?php

namespace Modules\Backup\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

class BackupConfig extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'destination_path',
        'run_backup_at',
        'last_run_backup_at',
        'user_id',
    ];

    protected $casts = [
        'last_run_backup_at' => 'datetime'
    ];

    protected $appends = [
        'next_backups'
    ];



    public function getNextBackupsAttribute(): array
    {
        if (!preg_match('/^\s*(\d{1,2})\s+(\d{1,2}):(\d{2})\s*$/', $this->run_backup_at, $m))
            return [];

        $intervalDays = (int)$m[1];
        $hour         = (int)$m[2];
        $minute       = (int)$m[3];

        $base = $this->last_run_backup_at
            ? Carbon::parse($this->last_run_backup_at)
            : Carbon::parse($this->created_at);

        $next = $base->copy()->addDays($intervalDays)->setTime($hour, $minute, 0);

        return [
            $next->copy()->format('Y-m-d H:i'),
            $next->copy()->addDays($intervalDays)->format('Y-m-d H:i'),
            $next->copy()->addDays($intervalDays * 2)->format('Y-m-d H:i'),
        ];
    }


    public function history (): HasOne
    {
        return $this->hasOne(BackupHistory::class);
    }

    public function user (): belongsTo
    {
        return $this->belongsTo(User::class);
    }
}
