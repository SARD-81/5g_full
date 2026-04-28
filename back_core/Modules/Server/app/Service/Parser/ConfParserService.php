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

            $directive = self::parseDirectiveLine($line);
            if ($directive !== null) {
                if ($currentSection) {
                    self::addValue($data[$currentSection], '_directives', $directive['key']);
                } else {
                    self::addValue($data, '_directives', $directive['key']);
                }

                $meta[] = [
                    'type' => 'directive',
                    'key' => $directive['key'],
                    'section' => $currentSection,
                    'raw' => $line,
                    'rawFormat' => [
                        'prefix' => $directive['prefix'],
                        'terminator' => $directive['terminator'],
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
        $directiveCounters = [];

        foreach ($meta as $item) {
            $type = $item['type'] ?? 'raw';

            if (in_array($type, ['comment', 'empty', 'raw', 'section'], true)) {
                $lines[] = $item['raw'] ?? '';
                continue;
            }

            if ($type !== 'kv') {
                if ($type !== 'directive') {
                    continue;
                }

                $section = $item['section'] ?? null;
                $format = $item['rawFormat'] ?? [];
                $jsonRef = $section ? ($jsonData[$section] ?? null) : $jsonData;

                if (!is_array($jsonRef) || !array_key_exists('_directives', $jsonRef)) {
                    $lines[] = $item['raw'] ?? '';
                    continue;
                }

                $candidate = $jsonRef['_directives'];
                $counterKey = ($section ?? 'root') . '._directives';
                $index = $directiveCounters[$counterKey] ?? 0;
                $directiveCounters[$counterKey] = $index + 1;

                if (is_array($candidate)) {
                    $directiveValue = $candidate[$index] ?? null;
                } elseif ($index === 0) {
                    $directiveValue = $candidate;
                } else {
                    $directiveValue = null;
                }

                if (!is_string($directiveValue) || trim($directiveValue) === '') {
                    $lines[] = $item['raw'] ?? '';
                    continue;
                }

                $lines[] = sprintf(
                    '%s%s%s',
                    $format['prefix'] ?? '',
                    trim($directiveValue),
                    $format['terminator'] ?? '',
                );
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

    public static function normalizeLegacyDirectiveMeta(array $payload): array
    {
        $meta = $payload['meta'] ?? [];
        $data = $payload['data'] ?? [];

        if (!is_array($meta) || !is_array($data)) {
            return $payload;
        }

        foreach ($meta as $index => $item) {
            if (($item['type'] ?? null) !== 'raw') {
                continue;
            }

            $rawLine = $item['raw'] ?? '';
            if (!is_string($rawLine)) {
                continue;
            }

            $directive = self::parseDirectiveLine($rawLine);
            if ($directive === null) {
                continue;
            }

            $section = $item['section'] ?? null;
            if (is_string($section) && $section !== '') {
                $data[$section] ??= [];
                if (!is_array($data[$section])) {
                    continue;
                }
                self::addValue($data[$section], '_directives', $directive['key']);
            } else {
                self::addValue($data, '_directives', $directive['key']);
            }

            $meta[$index] = [
                'type' => 'directive',
                'key' => $directive['key'],
                'section' => $section,
                'raw' => $rawLine,
                'rawFormat' => [
                    'prefix' => $directive['prefix'],
                    'terminator' => $directive['terminator'],
                ],
            ];
        }

        $payload['data'] = $data;
        $payload['meta'] = $meta;

        return $payload;
    }

    private static function parseDirectiveLine(string $line): ?array
    {
        if (!preg_match('/^(\s*)([A-Za-z0-9_.-]+)(\s*)(;?)\s*$/', $line, $matches)) {
            return null;
        }

        if ($matches[2] === '{' || $matches[2] === '}') {
            return null;
        }

        return [
            'prefix' => $matches[1],
            'key' => $matches[2],
            'terminator' => $matches[4] === ';' ? ';' : '',
        ];
    }
}
