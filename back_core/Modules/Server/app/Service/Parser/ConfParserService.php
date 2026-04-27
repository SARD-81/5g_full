<?php

namespace Modules\Server\Service\Parser;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ConfParserService
{
    public static function supportsExtension(string $extension): bool
    {
        return in_array(strtolower($extension), ['conf', 'conf.in'], true);
    }

    public static function detectExtensionFromName(string $fileName): string
    {
        $normalized = strtolower(trim($fileName));

        if (str_ends_with($normalized, '.conf.in')) {
            return 'conf.in';
        }

        if (str_ends_with($normalized, '.conf')) {
            return 'conf';
        }

        return strtolower(pathinfo($normalized, PATHINFO_EXTENSION));
    }

    public static function parseUploadedFile(UploadedFile $file): array
    {
        $content = file_get_contents($file->getRealPath()) ?: '';
        $extension = self::detectExtensionFromName($file->getClientOriginalName());

        return self::parseString($content, $extension);
    }

    public static function parseString(string $content, string $extension = 'conf'): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $content);
        $data = [];
        $meta = [];
        $currentSection = null;

        foreach ($lines as $line) {
            if (trim($line) === '') {
                $meta[] = ['type' => 'empty', 'raw' => $line];
                continue;
            }

            if (preg_match('/^\s*[#;]/', $line)) {
                $meta[] = ['type' => 'comment', 'raw' => $line];
                continue;
            }

            if (preg_match('/^\s*\[([^\]]+)\]\s*$/', $line, $sectionMatch)) {
                $currentSection = trim($sectionMatch[1]);
                $data[$currentSection] ??= [];
                $meta[] = ['type' => 'section', 'name' => $currentSection, 'raw' => $line];
                continue;
            }

            if (preg_match('/^(\s*)([^=:]+?)(\s*)([=:])(\s*)(.*)$/', $line, $matches)) {
                $key = trim($matches[2]);
                $separator = $matches[4];
                $valueRaw = $matches[6];
                $value = trim($valueRaw, " \t\"'");

                if ($currentSection) {
                    self::addValue($data[$currentSection], $key, $value);
                } else {
                    self::addValue($data, $key, $value);
                }

                $meta[] = [
                    'type' => 'kv',
                    'key' => $key,
                    'section' => $currentSection,
                    'raw' => $line,
                    'rawFormat' => [
                        'prefix' => $matches[1],
                        'suffixKey' => $matches[3],
                        'separator' => $separator,
                        'valuePrefix' => $matches[5],
                    ],
                ];
                continue;
            }

            $meta[] = ['type' => 'raw', 'raw' => $line];
        }

        return [
            'format' => 'conf',
            'extension' => $extension,
            'data' => $data,
            'meta' => $meta,
        ];
    }

    public static function serialize(array|string $data): string
    {
        if (is_string($data)) {
            return $data;
        }

        $payload = $data;
        if (isset($payload['format']) && $payload['format'] === 'conf') {
            $meta = $payload['meta'] ?? [];
            $jsonData = $payload['data'] ?? [];
        } else {
            $meta = $payload['meta'] ?? [];
            $jsonData = $payload['data'] ?? $payload;
        }

        if (!is_array($meta) || $meta === []) {
            return '';
        }

        $lines = [];
        $keyCounters = [];

        foreach ($meta as $item) {
            $type = $item['type'] ?? 'raw';

            if (in_array($type, ['comment', 'empty', 'raw', 'section'], true)) {
                $lines[] = $item['raw'] ?? '';
                continue;
            }

            if ($type !== 'kv') {
                continue;
            }

            $section = $item['section'] ?? null;
            $key = $item['key'] ?? null;
            $format = $item['rawFormat'] ?? [];

            if (!$key) {
                $lines[] = $item['raw'] ?? '';
                continue;
            }

            $jsonRef = $section ? ($jsonData[$section] ?? null) : $jsonData;
            $value = '';
            if (is_array($jsonRef) && array_key_exists($key, $jsonRef)) {
                $candidate = $jsonRef[$key];
                if (is_array($candidate)) {
                    $counterKey = ($section ?? 'root') . '_' . $key;
                    $index = $keyCounters[$counterKey] ?? 0;
                    $value = $candidate[$index] ?? '';
                    $keyCounters[$counterKey] = $index + 1;
                } else {
                    $value = $candidate;
                }
            }

            $lines[] = sprintf(
                '%s%s%s%s%s%s',
                $format['prefix'] ?? '',
                $key,
                $format['suffixKey'] ?? '',
                $format['separator'] ?? '=',
                $format['valuePrefix'] ?? '',
                (string) $value,
            );
        }

        return implode("\n", $lines);
    }

    private static function addValue(array &$target, string $key, mixed $value): void
    {
        if (array_key_exists($key, $target)) {
            if (!is_array($target[$key])) {
                $target[$key] = [$target[$key]];
            }
            $target[$key][] = $value;
            return;
        }

        $target[$key] = $value;
    }
}
