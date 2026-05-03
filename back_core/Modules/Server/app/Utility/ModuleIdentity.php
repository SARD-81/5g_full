<?php

namespace Modules\Server\Utility;

use Illuminate\Validation\ValidationException;
use Modules\Server\Models\Module;

class ModuleIdentity
{
    public const SERVICE_PREFIX = 'bbdh-';
    public const SERVICE_SUFFIX = 'd';

    public const UPF_TYPE = 'upf';
    public const UPF_JSON_CONFIG_FILE = 'DPW.JSON';

    public static function normalizeKey(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = preg_replace('/[^a-z0-9]+/', '-', $normalized);
        $normalized = trim($normalized, '-');

        if ($normalized === '' || $normalized === null) {
            throw ValidationException::withMessages([
                'name' => 'The provided module name cannot produce a valid technical identity.',
            ]);
        }

        return substr($normalized, 0, 60);
    }

    public static function configFileName(Module $module, ?string $storedConfig = null): string
    {
        $extension = self::resolveConfigExtension($storedConfig);

        if (self::isUpfJsonConfig($module, $extension)) {
            return self::UPF_JSON_CONFIG_FILE;
        }

        return $module->service_key . '.' . $extension;
    }

    private static function isUpfJsonConfig(Module $module, string $extension): bool
    {
        $types = array_map(
            fn ($type) => strtolower(trim($type)),
            explode(',', (string) $module->type)
        );

        return in_array(self::UPF_TYPE, $types, true)
            && strtolower($extension) === 'json';
    }

    private static function resolveConfigExtension(?string $storedConfig): string
    {
        if (!is_string($storedConfig) || trim($storedConfig) === '') {
            return 'yaml';
        }

        $decoded = json_decode($storedConfig, true);
        if (!is_array($decoded)) {
            return 'yaml';
        }

        $extension = strtolower((string) ($decoded['extension'] ?? ''));
        if ($extension !== '') {
            return $extension;
        }

        $format = strtolower((string) ($decoded['format'] ?? ''));
        return match ($format) {
            'conf' => 'conf',
            'json' => 'json',
            default => 'yaml',
        };
    }

    public static function serviceUnitName(Module $module): string
    {
        return self::SERVICE_PREFIX . $module->service_key . self::SERVICE_SUFFIX;
    }
}