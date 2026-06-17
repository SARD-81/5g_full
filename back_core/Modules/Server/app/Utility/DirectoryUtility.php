<?php

namespace Modules\Server\Utility;

use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class DirectoryUtility {

    /**
     * validate is real path
     *          is readable
     *          is wirtable
     * @ValidationException::withMessages
     * @throws ValidationException
     */
    public static function basicValidator (string $path) : void
    {
        $realPath = realpath($path);
        if (!$realPath) throw ValidationException::withMessages(['path' => 'is not exists directory to path :' . $path]);

        if (! File::exists($realPath))
            throw ValidationException::withMessages(['path' => 'is not exists directory to path :' . $path]);

        if (! File::isDirectory($realPath))
            throw ValidationException::withMessages(['path' => 'is not a directory to path :' . $path]);

        if (! File::isWritable($realPath))
            throw ValidationException::withMessages(['path' => 'is not writable to path :' . $path]);

    }

    /**
     *  validate is by this user (apache user => www-data)
     * @throws ValidationException
     */
    public static function OwnerValidator (string $path) : void
    {
        $realPath = realpath($path);
        if (!$realPath) throw ValidationException::withMessages(['path' => 'is not exists directory to path :' . $path]);


        $ownerId     = fileowner($realPath);
        $currentId   = posix_geteuid();

        $ownerName   = posix_getpwuid($ownerId)['name']   ?? (string) $ownerId;
        $currentName = posix_getpwuid($currentId)['name'] ?? (string) $currentId;

        if ($ownerId !== $currentId)
            throw ValidationException::withMessages(['path' => sprintf("Directory owner mismatch: expected '%s', got '%s'", $currentName, $ownerName)]);
    }

    /***
     * validate is permission by this user (755, 775)
     * @throws ValidationException
     */
    public static function PermissionValidator (string $path, array $allowedModes = [0777, 0755, 0770]): void
    {
        $realPath = realpath($path);
        if (!$realPath) throw ValidationException::withMessages(['path' => 'is not exists directory to path :' . $path]);

        $permissions = fileperms($realPath) & 0777;
        $permOct     = decoct($permissions);
        $allowedList = implode(', ', array_map('decoct', $allowedModes));

        if (! in_array($permissions, $allowedModes, true))
            throw ValidationException::withMessages (['path' => sprintf("Invalid directory permissions: expected one of [%s], but got %s", $allowedList, $permOct)]);

    }
};
