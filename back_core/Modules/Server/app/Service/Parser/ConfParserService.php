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

        for ($index = 0; $index < count($lines); $index++) {
            $line = $lines[$index];
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

            if (preg_match('/^(\s*)([A-Za-z0-9_.-]+)(\s*)(?:=(\s*)(?:"([^"]*)"(\s*))?)?\{\s*(.*?)\s*$/', $line, $blockMatches)) {
                $blockKey = trim($blockMatches[2]);
                $blockEntry = [];
                if (isset($blockMatches[5]) && $blockMatches[5] !== '') {
                    $blockEntry['value'] = $blockMatches[5];
                }
                $blockMetaEntries = [];
                $baseIndent = ($blockMatches[1] ?? '') . '    ';
                $inlineContent = trim((string) ($blockMatches[7] ?? ''));
                $openedInlineAndClosed = false;

                if ($inlineContent !== '') {
                    if (preg_match('/^(.*?)\}\s*;\s*$/', $inlineContent, $inlineCloseMatch)) {
                        $inlineContent = trim($inlineCloseMatch[1]);
                        $openedInlineAndClosed = true;
                    }
                    if ($inlineContent !== '') {
                        foreach (preg_split('/;/', $inlineContent) as $inlinePart) {
                            $inlinePart = trim((string) $inlinePart);
                            if ($inlinePart === '') {
                                continue;
                            }
                            if (preg_match('/^([^=:]+?)(\s*)([=:])(\s*)(.*)$/', $inlinePart, $nestedKvMatches)) {
                                $nestedKey = trim($nestedKvMatches[1]);
                                $nestedRawValue = trim((string) $nestedKvMatches[5]);
                                $nestedValue = trim($nestedRawValue, " \t\"'");
                                self::addValue($blockEntry, $nestedKey, $nestedValue);
                                $blockMetaEntries[] = [
                                    'type' => 'kv',
                                    'key' => $nestedKey,
                                    'rawFormat' => [
                                        'prefix' => $baseIndent,
                                        'suffixKey' => $nestedKvMatches[2],
                                        'separator' => $nestedKvMatches[3],
                                        'valuePrefix' => $nestedKvMatches[4],
                                        'terminator' => ';',
                                    ],
                                ];
                            } else {
                                self::addValue($blockEntry, '_directives', self::normalizeDirectiveValue($inlinePart));
                                $blockMetaEntries[] = [
                                    'type' => 'directive',
                                    'key' => self::normalizeDirectiveValue($inlinePart),
                                    'rawFormat' => ['prefix' => $baseIndent, 'terminator' => ';'],
                                ];
                            }
                        }
                    }
                }

                if (! $openedInlineAndClosed) {
                    for ($index++; $index < count($lines); $index++) {
                        $blockLine = $lines[$index];
                        if (preg_match('/^\s*}\s*;\s*$/', $blockLine)) {
                            break;
                        }

                        if (trim($blockLine) === '') {
                            $blockMetaEntries[] = ['type' => 'empty', 'raw' => $blockLine];
                            continue;
                        }

                    if (preg_match('/^\s*[#;]/', $blockLine)) {
                        $blockMetaEntries[] = ['type' => 'comment', 'raw' => $blockLine];
                        continue;
                    }

                    if (preg_match('/^(\s*)([^=:]+?)(\s*)([=:])(\s*)(.*?)(;?)\s*$/', $blockLine, $nestedKvMatches)) {
                        $nestedKey = trim($nestedKvMatches[2]);
                        $nestedRawValue = trim((string) $nestedKvMatches[6]);
                        $nestedValue = trim($nestedRawValue, " \t\"'");
                        self::addValue($blockEntry, $nestedKey, $nestedValue);
                        $blockMetaEntries[] = [
                            'type' => 'kv',
                            'key' => $nestedKey,
                            'rawFormat' => [
                                'prefix' => $nestedKvMatches[1],
                                'suffixKey' => $nestedKvMatches[3],
                                'separator' => $nestedKvMatches[4],
                                'valuePrefix' => $nestedKvMatches[5],
                                'terminator' => $nestedKvMatches[7] === ';' ? ';' : '',
                            ],
                        ];
                        continue;
                    }

                    $nestedDirective = self::parseDirectiveLine($blockLine);
                    if ($nestedDirective !== null) {
                        self::addValue($blockEntry, '_directives', $nestedDirective['key']);
                        $blockMetaEntries[] = [
                            'type' => 'directive',
                            'key' => $nestedDirective['key'],
                            'rawFormat' => [
                                'prefix' => $nestedDirective['prefix'],
                                'terminator' => $nestedDirective['terminator'],
                            ],
                        ];
                        continue;
                    }

                        $blockMetaEntries[] = ['type' => 'raw', 'raw' => $blockLine];
                    }
                }

                if ($currentSection) {
                    self::addValue($data[$currentSection], $blockKey, $blockEntry);
                } else {
                    self::addValue($data, $blockKey, $blockEntry);
                }

                $meta[] = [
                    'type' => 'block',
                    'key' => $blockKey,
                    'section' => $currentSection,
                    'rawFormat' => [
                        'prefix' => $blockMatches[1],
                        'suffixKey' => $blockMatches[3],
                        'separator' => '=',
                        'valuePrefix' => $blockMatches[4] . '"',
                        'valueSuffix' => '"',
                        'childIndent' => $baseIndent,
                    ],
                    'entries' => $blockMetaEntries,
                ];
                continue;
            }

            if (preg_match('/^(\s*)([^=:]+?)(\s*)([=:])(\s*)(.*)$/', $line, $matches)) {
                $key = trim($matches[2]);
                $separator = $matches[4];
                $valueRaw = $matches[6];
                $value = trim($valueRaw, " \t\"'");
                if ($separator === '=' && $key === 'LoadExtension') {
                    $value = self::parseLoadExtensionValue($valueRaw);
                }

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

            if ($type === 'block') {
                $section = $item['section'] ?? null;
                $key = $item['key'] ?? '';
                $format = $item['rawFormat'] ?? [];
                $jsonRef = $section ? ($jsonData[$section] ?? null) : $jsonData;
                $counterKey = ($section ?? 'root') . '.' . $key . '.__block';
                $index = $keyCounters[$counterKey] ?? 0;
                $keyCounters[$counterKey] = $index + 1;
                $candidate = is_array($jsonRef) ? ($jsonRef[$key] ?? null) : null;
                $entry = is_array($candidate) && array_is_list($candidate)
                    ? ($candidate[$index] ?? null)
                    : ($index === 0 ? $candidate : null);

                if (!is_array($entry)) {
                    continue;
                }

                $hasBlockValue = array_key_exists('value', $entry) && trim((string) $entry['value']) !== '';
                if ($hasBlockValue) {
                    $blockValue = trim((string) $entry['value']);
                    $lines[] = sprintf(
                        '%s%s%s%s%s%s {',
                        $format['prefix'] ?? '',
                        $key,
                        $format['suffixKey'] ?? ' ',
                        $format['separator'] ?? '=',
                        $format['valuePrefix'] ?? ' "',
                        $blockValue . ($format['valueSuffix'] ?? '"'),
                    );
                } else {
                    $lines[] = sprintf('%s%s {', $format['prefix'] ?? '', $key);
                }

                $blockMetaEntries = $item['entries'] ?? [];
                $blockKeyCounters = [];
                $usedDirectives = [];
                $defaultIndent = $format['childIndent'] ?? '    ';

                foreach ($blockMetaEntries as $blockMeta) {
                    $blockType = $blockMeta['type'] ?? 'raw';
                    if (in_array($blockType, ['comment', 'empty', 'raw'], true)) {
                        $lines[] = $blockMeta['raw'] ?? '';
                        continue;
                    }

                    if ($blockType === 'kv') {
                        $nestedKey = $blockMeta['key'] ?? null;
                        if (!$nestedKey) {
                            continue;
                        }
                        $nestedFormat = $blockMeta['rawFormat'] ?? [];
                        $counter = $blockKeyCounters[$nestedKey] ?? 0;
                        $blockKeyCounters[$nestedKey] = $counter + 1;
                        $nestedCandidate = $entry[$nestedKey] ?? '';
                        $nestedValue = is_array($nestedCandidate)
                            ? ($nestedCandidate[$counter] ?? '')
                            : ($counter === 0 ? $nestedCandidate : '');

                        $lines[] = sprintf(
                            '%s%s%s%s%s%s%s',
                            $nestedFormat['prefix'] ?? $defaultIndent,
                            $nestedKey,
                            $nestedFormat['suffixKey'] ?? ' ',
                            $nestedFormat['separator'] ?? '=',
                            $nestedFormat['valuePrefix'] ?? ' ',
                            (string) $nestedValue,
                            $nestedFormat['terminator'] ?? ';',
                        );
                        continue;
                    }

                    if ($blockType === 'directive') {
                        $nestedFormat = $blockMeta['rawFormat'] ?? [];
                        $candidateDirectives = $entry['_directives'] ?? [];
                        $directivesList = is_array($candidateDirectives) ? $candidateDirectives : [$candidateDirectives];
                        $directiveIndex = count($usedDirectives);
                        $directiveValue = $directivesList[$directiveIndex] ?? null;
                        if (!is_string($directiveValue) || trim($directiveValue) === '') {
                            continue;
                        }

                        $normalizedDirective = self::normalizeDirectiveValue($directiveValue);
                        $usedDirectives[] = $normalizedDirective;
                        $lines[] = sprintf(
                            '%s%s%s',
                            $nestedFormat['prefix'] ?? $defaultIndent,
                            $normalizedDirective,
                            $nestedFormat['terminator'] ?? ';',
                        );
                    }
                }

                $extraDirectives = self::remainingDirectives($entry['_directives'] ?? [], $usedDirectives);
                foreach ($extraDirectives as $directive) {
                    $lines[] = $defaultIndent . self::normalizeDirectiveValue($directive) . ';';
                }
                $lines[] = ($format['prefix'] ?? '') . '};';
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

                $lines[] = sprintf('%s%s%s', $format['prefix'] ?? '', self::normalizeDirectiveValue($directiveValue), $format['terminator'] ?? '');
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

            if ($key === 'LoadExtension') {
                if (is_array($value)) {
                    $path = (string) ($value['value'] ?? '');
                    $argument = isset($value['argument']) ? (string) $value['argument'] : '';
                    $value = $argument !== ''
                        ? '"' . $path . '" : "' . $argument . '";'
                        : '"' . $path . '";';
                } else {
                    $text = trim((string) $value);
                    if (!str_ends_with($text, ';')) {
                        $text .= ';';
                    }
                    $value = $text;
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

        foreach (self::remainingDirectives($jsonData['_directives'] ?? [], []) as $directive) {
            $lines[] = self::normalizeDirectiveValue($directive) . ';';
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

    private static function parseLoadExtensionValue(string $rawValue): mixed
    {
        $trimmed = trim($rawValue);
        $trimmed = preg_replace('/;\s*$/', '', $trimmed) ?? $trimmed;

        if (preg_match('/^"([^"]+)"\s*:\s*"([^"]+)"$/', $trimmed, $matches)) {
            return [
                'value' => $matches[1],
                'argument' => $matches[2],
            ];
        }

        if (preg_match('/^"([^"]+)"$/', $trimmed, $matches)) {
            return ['value' => $matches[1]];
        }

        return trim($trimmed, " \t\"'");
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

    private static function normalizeDirectiveValue(string $directive): string
    {
        $normalized = trim($directive);
        $normalized = ltrim($normalized, ';');
        $normalized = rtrim($normalized, ';');
        return trim($normalized);
    }

    private static function remainingDirectives(mixed $candidate, array $used): array
    {
        $directives = is_array($candidate) ? $candidate : [$candidate];
        $usedMap = [];
        foreach ($used as $item) {
            $usedMap[self::normalizeDirectiveValue((string) $item)] = true;
        }

        $remaining = [];
        foreach ($directives as $directive) {
            if (!is_string($directive) || trim($directive) === '') {
                continue;
            }
            $normalized = self::normalizeDirectiveValue($directive);
            if ($normalized === '' || isset($usedMap[$normalized])) {
                continue;
            }
            $remaining[] = $normalized;
        }

        return $remaining;
    }
}
