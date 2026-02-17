<?php

namespace Modules\Backup\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetBackupConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'destination_path'    => $this->destination_path,
            'run_backup_at'       => $this->run_backup_at,
            'last_run_backup_at'  => $this->last_run_backup_at,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,

            'next_backups'    => $this->next_backups,
            'user' => [
                'id'          => $this->user->id,
                'first_name'  => $this->user->first_name,
                'last_name'   => $this->user->last_name,
                'auth_name'   => $this->user->auth_name,
                'role'        => $this->user->roles->pluck('name'),
                'permissions' => $this->user->permissions->pluck('name'),
            ]
        ];
    }
}
