<?php

namespace Modules\Server\Service\Parser;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModuleConfigParserService
{
    public static function parseUploadedFile(UploadedFile $file): array
    {
        $extension = ConfParserService::detectExtensionFromName($file->getClientOriginalName());

        if (ConfParserService::supportsExtension($extension)) {
            $parsed = ConfParserService::parseUploadedFile($file);
            return [
                'format' => 'conf',
                'stored_json' => json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                'remote_content' => ConfParserService::serialize($parsed),
                'editor_data' => $parsed['data'],
            ];
        }

        $jsonContent = YamlParserService::uploadModuleFile($file);
        return [
            'format' => 'yaml-json',
            'stored_json' => $jsonContent,
            'remote_content' => YamlParserService::convertJsonToYaml($jsonContent),
            'editor_data' => json_decode($jsonContent, true),
        ];
    }

    public static function serializeForRemote(string $storedConfig): string
    {
        $decoded = json_decode($storedConfig, true);
        if (!is_array($decoded)) {
            return YamlParserService::convertJsonToYaml($storedConfig);
        }

        if (($decoded['format'] ?? null) === 'conf') {
            $decoded['data'] = self::normalizeConfDuplicateKeys($decoded['data'] ?? []);
            return ConfParserService::serialize($decoded);
        }

        return YamlParserService::convertJsonToYaml($storedConfig);
    }

    public static function normalizeUpdatePayload(mixed $payload, ?string $existingStoredConfig = null): array
    {
        if (is_array($payload) && (($payload['format'] ?? null) === 'conf' || ($payload['_format'] ?? null) === 'conf')) {
            $confPayload = [
                'format' => 'conf',
                'extension' => $payload['extension'] ?? 'conf',
                'data' => self::normalizeConfDuplicateKeys($payload['data'] ?? []),
                'meta' => $payload['meta'] ?? [],
            ];

            $storedJson = json_encode($confPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return [
                'format' => 'conf',
                'stored_json' => $storedJson,
                'remote_content' => ConfParserService::serialize($confPayload),
                'editor_data' => $confPayload['data'],
            ];
        }

        if (is_array($payload) && $existingStoredConfig) {
            $existingDecoded = json_decode($existingStoredConfig, true);
            if (is_array($existingDecoded) && ($existingDecoded['format'] ?? null) === 'conf') {
                $confPayload = [
                    'format' => 'conf',
                    'extension' => $existingDecoded['extension'] ?? 'conf',
                    'data' => self::normalizeConfDuplicateKeys($payload),
                    'meta' => $existingDecoded['meta'] ?? [],
                ];

                $storedJson = json_encode($confPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return [
                    'format' => 'conf',
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

        $jsonContent = json_encode($payload, JSON_PRETTY_PRINT);

        return [
            'format' => 'yaml-json',
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
