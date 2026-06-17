<?php

namespace Modules\Server\Utility;

class CommandOutputAnalyzerService
{
    public static function extractErrors(string $output): array
    {
        $errors = [];

        $patterns = [
            '/(permission\s+denied)/i'                     => 'Permission denied',
            '/(no\s+such\s+file\s+or\s+directory)/i'       => 'No such file or directory',
            '/(command\s+not\s+found)/i'                   => 'Command not found',
            '/(is\s+a\s+directory)/i'                      => 'Target is a directory',
            '/(not\s+a\s+directory)/i'                     => 'Target is not a directory',
            '/(cannot\s+.*?:\s+.*)/i'                      => 'An unknown error occurred',
            '/(failed\s+to\s+.*)/i'                        => 'Command execution failed',
            '/(file\s+exists)/i'                           => 'File already exists',
            '/unit\s+.*?\.service\s+could\s+not\s+be\s+found/i' => 'Service not found',
        ];

        foreach ($patterns as $pattern => $message) {
            if (preg_match($pattern, $output, $matches)) {
                $errors[] = [
                    'message' => $message,
                    'matched_text' => $matches[0],
                ];
            }
        }

        return $errors;
    }
}
