<?php

namespace Modules\Server\Service\Parser;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModuleConfigParserService
{
    public static function parseUploadedFile(UploadedFile $file): array
    {
        $extension = ConfParserService::detectExtensionFromName($file->getClientOriginalName());
        $rawContent = file_get_contents($file->getRealPath()) ?: '';

        if (ConfParserService::supportsExtension($extension)) {
            $parsed = ConfParserService::parseUploadedFile($file);
            return [
                'format' => 'conf',
                'extension' => $parsed['extension'] ?? $extension,
                'stored_json' => json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                'remote_content' => ConfParserService::serialize($parsed),
                'editor_data' => $parsed['data'],
            ];
        }

        $jsonContent = YamlParserService::uploadModuleFile($file);
$normalizedExtension = strtolower($extension);

if (str_ends_with(strtolower($file->getClientOriginalName()), '.yaml.in')) {
    $normalizedExtension = 'yaml.in';
}

$decodedJsonContent = json_decode($jsonContent, true);
$format = $normalizedExtension === 'json' ? 'json' : 'yaml';

if ($format === 'json') {
    if (!is_array($decodedJsonContent)) {
        throw ValidationException::withMessages([
            'config_file' => 'The uploaded JSON file is invalid.',
        ]);
    }

    return [
        'format' => 'json',
        'extension' => 'json',
        'stored_json' => self::buildStoredJsonConfig($decodedJsonContent),
        'remote_content' => json_encode(
            $decodedJsonContent,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ),
        'editor_data' => $decodedJsonContent,
    ];
}

return [
    'format' => 'yaml',
    'extension' => in_array($normalizedExtension, ['yaml', 'yml', 'yaml.in'], true)
        ? $normalizedExtension
        : 'yaml',
    'stored_json' => $jsonContent,
    'remote_content' => $rawContent,
    'editor_data' => $decodedJsonContent,
];
    }

    public static function serializeForRemote(string $storedConfig): string
    {
        $decoded = json_decode($storedConfig, true);
        if (!is_array($decoded)) {
            return YamlParserService::convertJsonToYaml($storedConfig);
        }

        if (($decoded['format'] ?? null) === 'conf') {
            $decoded = ConfParserService::normalizeLegacyDirectiveMeta($decoded);
            $decoded['data'] = self::normalizeConfDuplicateKeys($decoded['data'] ?? []);
            return ConfParserService::serialize($decoded);
        }

        if (($decoded['format'] ?? null) === 'json' || ($decoded['extension'] ?? null) === 'json') {
            $content = $decoded['data'] ?? $decoded;
            return json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return YamlParserService::convertJsonToYaml($storedConfig);
    }

    public static function normalizeUpdatePayload(mixed $payload, ?string $existingStoredConfig = null): array
    {
        if (is_array($payload) && (($payload['format'] ?? null) === 'conf' || ($payload['_format'] ?? null) === 'conf')) {
            $confPayload = ConfParserService::normalizeLegacyDirectiveMeta([
                'format' => 'conf',
                'extension' => $payload['extension'] ?? 'conf',
                'data' => self::normalizeConfDuplicateKeys($payload['data'] ?? []),
                'meta' => $payload['meta'] ?? [],
            ]);

            $storedJson = json_encode($confPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return [
                'format' => 'conf',
                'extension' => $confPayload['extension'] ?? 'conf',
                'stored_json' => $storedJson,
                'remote_content' => ConfParserService::serialize($confPayload),
                'editor_data' => $confPayload['data'],
            ];
        }

        if (is_array($payload) && $existingStoredConfig) {
            $existingDecoded = json_decode($existingStoredConfig, true);
            if (is_array($existingDecoded) && ($existingDecoded['format'] ?? null) === 'conf') {
                $confPayload = ConfParserService::normalizeLegacyDirectiveMeta([
                    'format' => 'conf',
                    'extension' => $existingDecoded['extension'] ?? 'conf',
                    'data' => self::normalizeConfDuplicateKeys($payload),
                    'meta' => $existingDecoded['meta'] ?? [],
                ]);

                $storedJson = json_encode($confPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return [
                    'format' => 'conf',
                    'extension' => $confPayload['extension'] ?? 'conf',
                    'stored_json' => $storedJson,
                    'remote_content' => ConfParserService::serialize($confPayload),
                    'editor_data' => $confPayload['data'],
                ];
            }
        }

        if (!is_array($payload)) {
            throw ValidationException::withMessages([
                'data' => 'Invalid configuration payload.',
            ]);
        }

        array_walk_recursive($payload, function (&$value) {
            if (is_null($value)) {
                $value = '';
            }
        });

        $existingDecoded = is_string($existingStoredConfig) ? json_decode($existingStoredConfig, true) : null;
$existingFormat = is_array($existingDecoded) ? strtolower((string) ($existingDecoded['format'] ?? '')) : '';
$existingExtension = is_array($existingDecoded) ? strtolower((string) ($existingDecoded['extension'] ?? '')) : '';
$isJson = $existingFormat === 'json' || $existingExtension === 'json';

if ($isJson) {
    return [
        'format' => 'json',
        'extension' => 'json',
        'stored_json' => self::buildStoredJsonConfig($payload),
        'remote_content' => json_encode(
            $payload,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ),
        'editor_data' => $payload,
    ];
}

$jsonContent = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

return [
    'format' => 'yaml',
    'extension' => $existingExtension ?: 'yaml',
    'stored_json' => $jsonContent,
    'remote_content' => YamlParserService::convertJsonToYaml($jsonContent),
    'editor_data' => $payload,
];
    }

    public static function parseRawContent(string $content, string $fileName): array|string
    {
        $extension = ConfParserService::detectExtensionFromName($fileName);

        if (ConfParserService::supportsExtension($extension)) {
            return ConfParserService::parseString($content, $extension);
        }

        return YamlParserService::parseRawConfigContent($content, $extension);
    }

    public static function resolveEffectiveFormatFromStoredConfig(?string $storedConfig): string
    {
        if (!is_string($storedConfig) || trim($storedConfig) === '') {
            return 'yaml';
        }

        $decoded = json_decode($storedConfig, true);
        if (!is_array($decoded)) {
            return 'yaml';
        }

        $format = strtolower((string) ($decoded['format'] ?? ''));
        if (in_array($format, ['conf', 'yaml', 'json'], true)) {
            return $format;
        }

        $extension = strtolower((string) ($decoded['extension'] ?? ''));
        if ($extension === 'json') {
            return 'json';
        }
        if (in_array($extension, ['conf', 'conf.in'], true)) {
            return 'conf';
        }

        return 'yaml';
    }

    public static function resolveEffectiveExtensionFromStoredConfig(?string $storedConfig): string
    {
        if (!is_string($storedConfig) || trim($storedConfig) === '') {
            return 'yaml';
        }

        $decoded = json_decode($storedConfig, true);
        if (!is_array($decoded)) {
            return 'yaml';
        }

        $extension = strtolower((string) ($decoded['extension'] ?? ''));
        if (in_array($extension, ['conf', 'conf.in', 'yaml', 'yml', 'yaml.in', 'json'], true)) {
            return $extension;
        }

        $format = strtolower((string) ($decoded['format'] ?? 'yaml'));
        return $format === 'conf' ? 'conf' : ($format === 'json' ? 'json' : 'yaml');
    }

    private static function buildStoredJsonConfig(mixed $data): string
{
    return json_encode([
        'format' => 'json',
        'extension' => 'json',
        'data' => $data,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

public static function editorDataFromStoredConfig(?string $storedConfig): mixed
{
    if (!is_string($storedConfig) || trim($storedConfig) === '') {
        return [];
    }

    $decoded = json_decode($storedConfig, true);

    if (!is_array($decoded)) {
        return [];
    }

    if (
        in_array(strtolower((string) ($decoded['format'] ?? '')), ['json', 'conf'], true)
        && array_key_exists('data', $decoded)
    ) {
        return $decoded['data'];
    }

    return $decoded;
}

    private static function normalizeConfDuplicateKeys(mixed $data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        $normalized = [];
        $pendingCopyValues = [];

        foreach ($data as $key => $value) {
            $normalizedValue = self::normalizeConfDuplicateKeys($value);

            if (!is_string($key)) {
                $normalized[$key] = $normalizedValue;
                continue;
            }

            if (preg_match('/^(.*)\s+\(copy(?:\s+\d+)?\)$/i', $key, $matches)) {
                $baseKey = rtrim($matches[1]);
                $pendingCopyValues[$baseKey] ??= [];
                $pendingCopyValues[$baseKey][] = $normalizedValue;
                continue;
            }

            $normalized[$key] = $normalizedValue;
        }

        foreach ($pendingCopyValues as $baseKey => $copies) {
            if (!array_key_exists($baseKey, $normalized)) {
                if (count($copies) === 1) {
                    $normalized[$baseKey] = $copies[0];
                } else {
                    $normalized[$baseKey] = $copies;
                }
                continue;
            }

            if (!is_array($normalized[$baseKey]) || array_is_list($normalized[$baseKey])) {
                $baseValues = is_array($normalized[$baseKey]) && array_is_list($normalized[$baseKey])
                    ? $normalized[$baseKey]
                    : [$normalized[$baseKey]];
                $normalized[$baseKey] = array_merge($baseValues, $copies);
                continue;
            }

            // Preserve object-like arrays by appending copies as list entries.
            $normalized[$baseKey] = array_merge([$normalized[$baseKey]], $copies);
        }

        return $normalized;
    }
}
