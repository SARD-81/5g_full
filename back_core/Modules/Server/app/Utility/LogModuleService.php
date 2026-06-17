<?php

namespace Modules\Server\Utility;

use Illuminate\Support\Facades\Auth;
use Modules\Server\Models\Server;

class LogModuleService
{
    public static function getArrayChanges(array $array1, array $array2) {
        $changes = [];

        foreach ($array2 as $key => $value) {
            if (!array_key_exists($key, $array1))
                $changes[$key] = $value;

            elseif (is_array($value) && is_array($array1[$key])) {
                $subChanges = self::getArrayChanges($array1[$key], $value);

                if (!empty($subChanges))
                    $changes[$key] = $subChanges;

                elseif ($array1[$key] !== $value)
                    $changes[$key] = [
                        'old' => $array1[$key],
                        'new' => $value
                    ];
            }
        }

        return $changes;
    }
    public static function logModuleUpdate ($module, Server $server, array $array2)
    {
        $array1 = json_decode($module->pivot->current_config, true);
        $change = json_encode(self::getArrayChanges($array1, $array2));

        activity('update-module-config')
            ->causedBy(Auth::user())
            ->event('update-config-module')
            ->withProperties([
                'type-log' => 'server',
                'route' => request()->fullUrl(),
                'method' => 'updateConfigModule',
                'user' =>  Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                'user_role' =>Auth::user()->roles()->pluck('name')->first(),
                'changes' => $change,
                'server' => $server,
                'server_id' => $server->id,
                'module_id' => $module['id'],
                'module_name' => $module['name'],
                'module_type' => $module['type'],
            ])
            ->log('The configuration values have been changed');
    }
}
