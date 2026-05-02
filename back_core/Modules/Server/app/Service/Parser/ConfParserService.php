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

            $inlineBlock = self::parseInlineBlock($line);
            if ($inlineBlock !== null) {
                self::addValueToScope($data, $currentSection, $inlineBlock['key'], $inlineBlock['value']);

                $meta[] = [
                    'type' => 'block',
                    'key' => $inlineBlock['key'],
                    'section' => $currentSection,
                    'inline' => true,
                    'raw' => $line,
                    'rawFormat' => $inlineBlock['rawFormat'],
                    'entries' => $inlineBlock['entries'],
                ];

                continue;
            }

            $blockStart = self::parseBlockStart($line);
            if ($blockStart !== null) {
                $blockEntry = $blockStart['value'];
                $blockMetaEntries = [];
                $defaultIndent = $blockStart['rawFormat']['childIndent'] ?? '    ';

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

                    $nestedKv = self::parseKvLine($blockLine);
                    if ($nestedKv !== null) {
                        self::addValue($blockEntry, $nestedKv['key'], $nestedKv['value']);
                        $blockMetaEntries[] = [
                            'type' => 'kv',
                            'key' => $nestedKv['key'],
                            'raw' => $blockLine,
                            'rawFormat' => $nestedKv['rawFormat'],
                        ];
                        continue;
                    }

                    $nestedDirective = self::parseDirectiveLine($blockLine);
                    if ($nestedDirective !== null) {
                        self::addDirective($blockEntry, $nestedDirective['key']);
                        $blockMetaEntries[] = [
                            'type' => 'directive',
                            'key' => $nestedDirective['key'],
                            'raw' => $blockLine,
                            'rawFormat' => [
                                'prefix' => $nestedDirective['prefix'] ?: $defaultIndent,
                                'terminator' => $nestedDirective['terminator'] ?: ';',
                            ],
                        ];
                        continue;
                    }

                    $blockMetaEntries[] = ['type' => 'raw', 'raw' => $blockLine];
                }

                self::addValueToScope($data, $currentSection, $blockStart['key'], $blockEntry);

                $meta[] = [
                    'type' => 'block',
                    'key' => $blockStart['key'],
                    'section' => $currentSection,
                    'inline' => false,
                    'raw' => $blockStart['raw'],
                    'rawFormat' => $blockStart['rawFormat'],
                    'entries' => $blockMetaEntries,
                ];

                continue;
            }

            $kv = self::parseKvLine($line);
            if ($kv !== null) {
                self::addValueToScope($data, $currentSection, $kv['key'], $kv['value']);

                $meta[] = [
                    'type' => 'kv',
                    'key' => $kv['key'],
                    'section' => $currentSection,
                    'raw' => $line,
                    'rawFormat' => $kv['rawFormat'],
                ];

                continue;
            }

            $directive = self::parseDirectiveLine($line);
            if ($directive !== null) {
                if ($currentSection) {
                    $data[$currentSection] ??= [];
                    self::addDirective($data[$currentSection], $directive['key']);
                } else {
                    self::addDirective($data, $directive['key']);
                }

                $meta[] = [
                    'type' => 'directive',
                    'key' => $directive['key'],
                    'section' => $currentSection,
                    'raw' => $line,
                    'rawFormat' => [
                        'prefix' => $directive['prefix'],
                        'terminator' => $directive['terminator'] ?: ';',
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

    public static function serialize(array|string $payload): string
    {
        if (is_string($payload)) {
            return $payload;
        }

        if (($payload['format'] ?? null) === 'conf') {
            $jsonData = $payload['data'] ?? [];
            $meta = $payload['meta'] ?? [];
        } else {
            $jsonData = $payload['data'] ?? $payload;
            $meta = $payload['meta'] ?? [];
        }

        if (!is_array($jsonData)) {
            $jsonData = [];
        }

        if (!is_array($meta) || $meta === []) {
            return '';
        }

        $lines = [];
        $counters = [
            'kv' => [],
            'block' => [],
            'directive' => [],
        ];

        foreach ($meta as $item) {
            $type = $item['type'] ?? 'raw';

            if (in_array($type, ['comment', 'empty', 'raw', 'section'], true)) {
                $lines[] = $item['raw'] ?? '';
                continue;
            }

            if ($type === 'kv') {
                $section = $item['section'] ?? null;
                $key = $item['key'] ?? null;

                if (!is_string($key) || $key === '') {
                    continue;
                }

                $scope = self::getScope($jsonData, $section);
                $value = self::consumeValue($scope, $key, $counters['kv'], self::counterKey($section, $key, 'kv'));

                if ($value === null || $value === '') {
                    continue;
                }

                if ($key === 'LoadExtension') {
                    $lines[] = self::serializeLoadExtensionLine($key, $value, $item['rawFormat'] ?? []);
                    continue;
                }

                $lines[] = self::serializeKvLine($key, $value, $item['rawFormat'] ?? []);
                continue;
            }

            if ($type === 'block') {
                $section = $item['section'] ?? null;
                $key = $item['key'] ?? null;

                if (!is_string($key) || $key === '') {
                    continue;
                }

                $scope = self::getScope($jsonData, $section);
                $entry = self::consumeValue($scope, $key, $counters['block'], self::counterKey($section, $key, 'block'));

                if (!is_array($entry)) {
                    continue;
                }

                $lines = array_merge(
                    $lines,
                    self::serializeBlock($key, $entry, $item['rawFormat'] ?? [], $item['entries'] ?? [], (bool) ($item['inline'] ?? false))
                );

                continue;
            }

            if ($type === 'directive') {
                $section = $item['section'] ?? null;
                $scope = self::getScope($jsonData, $section);
                $format = $item['rawFormat'] ?? [];
                $directive = self::consumeDirective($scope, $counters['directive'], self::counterKey($section, '_directives', 'directive'));

                if ($directive === null || trim((string) $directive) === '') {
                    continue;
                }

                $lines[] = self::serializeDirectiveLine((string) $directive, $format);
            }
        }

        self::appendExtraValues($lines, $jsonData, $meta, $counters);

        return implode("\n", $lines);
    }

    private static function parseKvLine(string $line): ?array
    {
        if (!preg_match('/^(\s*)([A-Za-z0-9_.-]+)(\s*)([=:])(\s*)(.*?)(\s*;\s*)?$/', $line, $matches)) {
            return null;
        }

        $key = trim($matches[2]);
        $rawValue = $matches[6];
        $terminator = isset($matches[7]) && trim($matches[7]) === ';' ? ';' : '';

        if ($key === 'LoadExtension' && $matches[4] === '=') {
            $parsedLoadExtension = self::parseLoadExtensionValue($rawValue);

            return [
                'key' => $key,
                'value' => $parsedLoadExtension['value'],
                'rawFormat' => [
                    'prefix' => $matches[1],
                    'suffixKey' => $matches[3],
                    'separator' => $matches[4],
                    'valuePrefix' => $matches[5],
                    'terminator' => $terminator,
                    'loadExtension' => $parsedLoadExtension['format'],
                ],
            ];
        }

        $parsed = self::parseScalarToken($rawValue);

        return [
            'key' => $key,
            'value' => $parsed['value'],
            'rawFormat' => [
                'prefix' => $matches[1],
                'suffixKey' => $matches[3],
                'separator' => $matches[4],
                'valuePrefix' => $matches[5],
                'terminator' => $terminator,
                'quoted' => $parsed['quoted'],
                'quote' => $parsed['quote'],
            ],
        ];
    }

    private static function parseInlineBlock(string $line): ?array
    {
        if (!preg_match('/^(\s*)([A-Za-z0-9_.-]+)(\s*)(?:=(\s*)(?:(["\'])(.*?)\5\s*)?)?\{\s*(.*?)\s*}\s*;\s*$/', $line, $matches)) {
            return null;
        }

        $key = trim($matches[2]);
        $entry = [];
        $entries = [];
        $childIndent = ($matches[1] ?? '') . '    ';

        if (($matches[6] ?? '') !== '') {
            $entry['value'] = $matches[6];
        }

        $inlineContent = trim($matches[7] ?? '');
        foreach (self::splitConfStatements($inlineContent) as $statement) {
            $statement = trim($statement);

            if ($statement === '') {
                continue;
            }

            $nestedKv = self::parseKvLine($childIndent . $statement . ';');
            if ($nestedKv !== null) {
                self::addValue($entry, $nestedKv['key'], $nestedKv['value']);
                $entries[] = [
                    'type' => 'kv',
                    'key' => $nestedKv['key'],
                    'rawFormat' => $nestedKv['rawFormat'],
                ];
                continue;
            }

            $directive = self::parseDirectiveLine($childIndent . $statement . ';');
            if ($directive !== null) {
                self::addDirective($entry, $directive['key']);
                $entries[] = [
                    'type' => 'directive',
                    'key' => $directive['key'],
                    'rawFormat' => [
                        'prefix' => $childIndent,
                        'terminator' => ';',
                    ],
                ];
            }
        }

        return [
            'key' => $key,
            'value' => $entry,
            'entries' => $entries,
            'rawFormat' => [
                'prefix' => $matches[1],
                'suffixKey' => $matches[3],
                'separator' => isset($matches[4]) && $matches[4] !== '' ? '=' : '',
                'valuePrefix' => $matches[4] ?? ' ',
                'quoted' => ($matches[5] ?? '') !== '',
                'quote' => $matches[5] ?: '"',
                'childIndent' => $childIndent,
            ],
        ];
    }

    private static function parseBlockStart(string $line): ?array
    {
        if (!preg_match('/^(\s*)([A-Za-z0-9_.-]+)(\s*)(?:=(\s*)(?:(["\'])(.*?)\5\s*)?)?\{\s*$/', $line, $matches)) {
            return null;
        }

        $entry = [];
        if (($matches[6] ?? '') !== '') {
            $entry['value'] = $matches[6];
        }

        return [
            'key' => trim($matches[2]),
            'raw' => $line,
            'value' => $entry,
            'rawFormat' => [
                'prefix' => $matches[1],
                'suffixKey' => $matches[3],
                'separator' => isset($matches[4]) && $matches[4] !== '' ? '=' : '',
                'valuePrefix' => $matches[4] ?? ' ',
                'quoted' => ($matches[5] ?? '') !== '',
                'quote' => $matches[5] ?: '"',
                'childIndent' => ($matches[1] ?? '') . '    ',
            ],
        ];
    }

    private static function parseLoadExtensionValue(string $rawValue): array
    {
        $trimmed = trim($rawValue);
        $trimmed = preg_replace('/\s*;\s*$/', '', $trimmed) ?? $trimmed;

        if (preg_match('/^(["\'])(.*?)\1\s*:\s*(["\'])(.*?)\3$/', $trimmed, $matches)) {
            return [
                'value' => [
                    'value' => $matches[2],
                    'argument' => $matches[4],
                ],
                'format' => [
                    'valueQuoted' => true,
                    'valueQuote' => $matches[1],
                    'argumentQuoted' => true,
                    'argumentQuote' => $matches[3],
                    'argumentSeparator' => ' : ',
                ],
            ];
        }

        if (preg_match('/^(["\'])(.*?)\1$/', $trimmed, $matches)) {
            return [
                'value' => [
                    'value' => $matches[2],
                ],
                'format' => [
                    'valueQuoted' => true,
                    'valueQuote' => $matches[1],
                    'argumentQuoted' => true,
                    'argumentQuote' => '"',
                    'argumentSeparator' => ' : ',
                ],
            ];
        }

        if (preg_match('/^(.+?)\s*:\s*(.+)$/', $trimmed, $matches)) {
            return [
                'value' => [
                    'value' => trim($matches[1], " \t\"'"),
                    'argument' => trim($matches[2], " \t\"'"),
                ],
                'format' => [
                    'valueQuoted' => str_starts_with(trim($matches[1]), '"') || str_starts_with(trim($matches[1]), "'"),
                    'valueQuote' => '"',
                    'argumentQuoted' => str_starts_with(trim($matches[2]), '"') || str_starts_with(trim($matches[2]), "'"),
                    'argumentQuote' => '"',
                    'argumentSeparator' => ' : ',
                ],
            ];
        }

        return [
            'value' => [
                'value' => trim($trimmed, " \t\"'"),
            ],
            'format' => [
                'valueQuoted' => str_starts_with($trimmed, '"') || str_starts_with($trimmed, "'"),
                'valueQuote' => '"',
                'argumentQuoted' => true,
                'argumentQuote' => '"',
                'argumentSeparator' => ' : ',
            ],
        ];
    }

    private static function serializeLoadExtensionLine(string $key, mixed $value, array $format): string
    {
        $loadFormat = $format['loadExtension'] ?? [];
        $prefix = $format['prefix'] ?? '';
        $suffixKey = $format['suffixKey'] ?? ' ';
        $separator = $format['separator'] ?? '=';
        $valuePrefix = $format['valuePrefix'] ?? ' ';
        $terminator = $format['terminator'] ?? ';';

        if (is_string($value)) {
            $value = [
                'value' => trim($value, " \t;\"'"),
            ];
        }

        if (!is_array($value)) {
            return '';
        }

        $path = trim((string) ($value['value'] ?? ''), " \t;\"'");
        if ($path === '') {
            return '';
        }

        $pathText = self::quoteIfNeeded($path, $loadFormat['valueQuoted'] ?? true, $loadFormat['valueQuote'] ?? '"');

        $argument = array_key_exists('argument', $value) ? trim((string) $value['argument'], " \t;\"'") : '';
        if ($argument !== '') {
            $argumentText = self::quoteIfNeeded($argument, $loadFormat['argumentQuoted'] ?? true, $loadFormat['argumentQuote'] ?? '"');
            $pathText .= ($loadFormat['argumentSeparator'] ?? ' : ') . $argumentText;
        }

        return $prefix . $key . $suffixKey . $separator . $valuePrefix . $pathText . $terminator;
    }

    private static function serializeKvLine(string $key, mixed $value, array $format): string
    {
        return sprintf(
            '%s%s%s%s%s%s%s',
            $format['prefix'] ?? '',
            $key,
            $format['suffixKey'] ?? '',
            $format['separator'] ?? '=',
            $format['valuePrefix'] ?? '',
            self::serializeScalarValue($value, $format),
            $format['terminator'] ?? ''
        );
    }

    private static function serializeBlock(string $key, array $entry, array $format, array $entries, bool $inline): array
    {
        $blockValue = array_key_exists('value', $entry) ? trim((string) $entry['value']) : '';
        $prefix = $format['prefix'] ?? '';
        $childIndent = $format['childIndent'] ?? '    ';

        $bodyParts = [];
        $bodyLines = [];
        $nestedCounters = [];
        $directiveCounter = 0;

        foreach ($entries as $entryMeta) {
            $type = $entryMeta['type'] ?? 'raw';

            if ($type === 'kv') {
                $nestedKey = $entryMeta['key'] ?? null;
                if (!$nestedKey) {
                    continue;
                }

                $value = self::consumeValue($entry, $nestedKey, $nestedCounters, $nestedKey);
                if ($value === null || $value === '') {
                    continue;
                }

                $line = self::serializeKvLine($nestedKey, $value, $entryMeta['rawFormat'] ?? ['prefix' => $childIndent, 'terminator' => ';']);
                $bodyLines[] = $line;
                $bodyParts[] = trim($line);
                continue;
            }

            if ($type === 'directive') {
                $directives = self::listify($entry['_directives'] ?? []);
                $directive = $directives[$directiveCounter] ?? null;
                $directiveCounter++;

                if (!is_string($directive) || trim($directive) === '') {
                    continue;
                }

                $line = self::serializeDirectiveLine($directive, $entryMeta['rawFormat'] ?? ['prefix' => $childIndent, 'terminator' => ';']);
                $bodyLines[] = $line;
                $bodyParts[] = trim($line);
                continue;
            }

            if (in_array($type, ['comment', 'empty', 'raw'], true)) {
                $bodyLines[] = $entryMeta['raw'] ?? '';
            }
        }

        $directives = self::listify($entry['_directives'] ?? []);
        for ($i = $directiveCounter; $i < count($directives); $i++) {
            $directive = $directives[$i];
            if (!is_string($directive) || trim($directive) === '') {
                continue;
            }
            $line = $childIndent . self::normalizeDirectiveValue($directive) . ';';
            $bodyLines[] = $line;
            $bodyParts[] = trim($line);
        }

        if ($inline) {
            $head = self::serializeBlockHeader($key, $blockValue, $format, true);
            return [$head . ' ' . implode(' ', array_filter($bodyParts)) . ' };'];
        }

        $lines = [self::serializeBlockHeader($key, $blockValue, $format, false)];
        $lines = array_merge($lines, $bodyLines);
        $lines[] = $prefix . '};';

        return $lines;
    }

    private static function serializeBlockHeader(string $key, string $value, array $format, bool $inline): string
    {
        $prefix = $format['prefix'] ?? '';
        $suffixKey = $format['suffixKey'] ?? ' ';
        $separator = $format['separator'] ?? '=';
        $valuePrefix = $format['valuePrefix'] ?? ' ';
        $hasValue = $value !== '';

        if (!$hasValue) {
            return $prefix . $key . ' {';
        }

        $quotedValue = self::quoteIfNeeded($value, $format['quoted'] ?? true, $format['quote'] ?? '"');

        return $prefix . $key . $suffixKey . $separator . $valuePrefix . $quotedValue . ' {';
    }

    private static function serializeDirectiveLine(string $directive, array $format): string
    {
        $normalized = self::normalizeDirectiveValue($directive);
        if ($normalized === '') {
            return '';
        }

        return ($format['prefix'] ?? '') . $normalized . ($format['terminator'] ?? ';');
    }

    private static function parseDirectiveLine(string $line): ?array
    {
        if (!preg_match('/^(\s*)([A-Za-z0-9_.-]+)(\s*)(;?)\s*$/', $line, $matches)) {
            return null;
        }

        if (in_array($matches[2], ['{', '}'], true)) {
            return null;
        }

        return [
            'prefix' => $matches[1],
            'key' => $matches[2],
            'terminator' => $matches[4] === ';' ? ';' : '',
        ];
    }

    private static function parseScalarToken(string $rawValue): array
    {
        $trimmed = trim($rawValue);
        $trimmed = preg_replace('/\s*;\s*$/', '', $trimmed) ?? $trimmed;

        if (preg_match('/^(["\'])([^"\']*)\1$/', $trimmed, $matches)) {
            return [
                'value' => $matches[2],
                'quoted' => true,
                'quote' => $matches[1],
            ];
        }

        return [
            'value' => $trimmed,
            'quoted' => false,
            'quote' => null,
        ];
    }

    private static function serializeScalarValue(mixed $value, array $format): string
    {
        $text = trim((string) $value);

        if (!($format['quoted'] ?? false)) {
            return $text;
        }

        return self::quoteIfNeeded($text, true, $format['quote'] ?? '"');
    }

    private static function quoteIfNeeded(string $value, bool $quoted, string $quote = '"'): string
    {
        $clean = trim($value);

        if (preg_match('/^(["\'])(.*)\1$/s', $clean, $matches)) {
            $clean = $matches[2];
        }

        if (!$quoted) {
            return $clean;
        }

        return $quote . $clean . $quote;
    }

    private static function splitConfStatements(string $content): array
    {
        $parts = [];
        $buffer = '';
        $quote = null;
        $length = strlen($content);

        for ($i = 0; $i < $length; $i++) {
            $char = $content[$i];

            if (($char === '"' || $char === "'") && ($i === 0 || $content[$i - 1] !== '\\')) {
                if ($quote === null) {
                    $quote = $char;
                } elseif ($quote === $char) {
                    $quote = null;
                }
            }

            if ($char === ';' && $quote === null) {
                $parts[] = $buffer;
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        if (trim($buffer) !== '') {
            $parts[] = $buffer;
        }

        return $parts;
    }

    private static function addValueToScope(array &$data, ?string $section, string $key, mixed $value): void
    {
        if ($section) {
            $data[$section] ??= [];
            self::addValue($data[$section], $key, $value);
            return;
        }

        self::addValue($data, $key, $value);
    }

    private static function addValue(array &$target, string $key, mixed $value): void
    {
        if (array_key_exists($key, $target)) {
            if (!is_array($target[$key]) || !array_is_list($target[$key])) {
                $target[$key] = [$target[$key]];
            }

            $target[$key][] = $value;
            return;
        }

        $target[$key] = $value;
    }

    private static function addDirective(array &$target, string $directive): void
    {
        $normalized = self::normalizeDirectiveValue($directive);
        if ($normalized === '') {
            return;
        }

        $target['_directives'] ??= [];
        if (!is_array($target['_directives'])) {
            $target['_directives'] = [$target['_directives']];
        }

        $target['_directives'][] = $normalized;
    }

    private static function normalizeDirectiveValue(string $directive): string
    {
        $normalized = trim($directive);
        $normalized = ltrim($normalized, ';');
        $normalized = rtrim($normalized, ';');

        return trim($normalized);
    }

    private static function getScope(array $jsonData, ?string $section): array
    {
        if ($section === null || $section === '') {
            return $jsonData;
        }

        $scope = $jsonData[$section] ?? [];

        return is_array($scope) ? $scope : [];
    }

    private static function consumeValue(array $scope, string $key, array &$counters, string $counterKey): mixed
    {
        if (!array_key_exists($key, $scope)) {
            return null;
        }

        $candidate = $scope[$key];

        if (is_array($candidate) && array_is_list($candidate)) {
            $index = $counters[$counterKey] ?? 0;
            $counters[$counterKey] = $index + 1;

            return $candidate[$index] ?? null;
        }

        $index = $counters[$counterKey] ?? 0;
        $counters[$counterKey] = $index + 1;

        return $index === 0 ? $candidate : null;
    }

    private static function consumeDirective(array $scope, array &$counters, string $counterKey): ?string
    {
        $directives = self::listify($scope['_directives'] ?? []);
        $index = $counters[$counterKey] ?? 0;
        $counters[$counterKey] = $index + 1;

        $value = $directives[$index] ?? null;

        return is_string($value) ? $value : null;
    }

    private static function appendExtraValues(array &$lines, array $jsonData, array $meta, array $counters): void
    {
        $metaKeys = [];
        foreach ($meta as $item) {
            if (($item['section'] ?? null) !== null) {
                continue;
            }
            if (in_array(($item['type'] ?? ''), ['kv', 'block'], true) && isset($item['key'])) {
                $metaKeys[$item['key']] = $item['type'];
            }
        }

        foreach ($jsonData as $key => $value) {
            if ($key === '_directives') {
                $used = $counters['directive'][self::counterKey(null, '_directives', 'directive')] ?? 0;
                $directives = self::listify($value);
                for ($i = $used; $i < count($directives); $i++) {
                    if (is_string($directives[$i]) && trim($directives[$i]) !== '') {
                        $lines[] = self::normalizeDirectiveValue($directives[$i]) . ';';
                    }
                }
                continue;
            }

            if (isset($metaKeys[$key])) {
                $type = $metaKeys[$key];
                $counterKey = self::counterKey(null, $key, $type);
                $used = $counters[$type][$counterKey] ?? 0;
                $items = (is_array($value) && array_is_list($value)) ? $value : [$value];

                for ($i = $used; $i < count($items); $i++) {
                    if ($key === 'LoadExtension') {
                        $line = self::serializeLoadExtensionLine($key, $items[$i], [
                            'prefix' => '',
                            'suffixKey' => ' ',
                            'separator' => '=',
                            'valuePrefix' => ' ',
                            'terminator' => ';',
                            'loadExtension' => [
                                'valueQuoted' => true,
                                'valueQuote' => '"',
                                'argumentQuoted' => true,
                                'argumentQuote' => '"',
                                'argumentSeparator' => ' : ',
                            ],
                        ]);

                        if ($line !== '') {
                            $lines[] = $line;
                        }
                    }
                }
            }
        }
    }

    private static function listify(mixed $value): array
    {
        if (is_array($value)) {
            return array_is_list($value) ? $value : [$value];
        }

        if ($value === null || $value === '') {
            return [];
        }

        return [$value];
    }

    private static function counterKey(?string $section, string $key, string $type): string
    {
        return ($section ?: 'root') . '.' . $key . '.' . $type;
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
                if (is_array($data[$section])) {
                    self::addDirective($data[$section], $directive['key']);
                }
            } else {
                self::addDirective($data, $directive['key']);
            }

            $meta[$index] = [
                'type' => 'directive',
                'key' => $directive['key'],
                'section' => $section,
                'raw' => $rawLine,
                'rawFormat' => [
                    'prefix' => $directive['prefix'],
                    'terminator' => $directive['terminator'] ?: ';',
                ],
            ];
        }

        $payload['data'] = $data;
        $payload['meta'] = $meta;

        return $payload;
    }
}