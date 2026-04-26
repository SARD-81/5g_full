<?php

namespace Modules\Server\Service\Parser;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlParserService
{
    public function __construct() {}

    /**
     * Parse YAML or YAML-like file to array safely
     */
    public static function parseYamlToArray(UploadedFile $file)
    {
        try {
            $yamlContent = file_get_contents($file->getRealPath());

            if (!$yamlContent || trim($yamlContent) === '') {
                throw new \Exception('YAML file is empty or unreadable.');
            }

            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['yaml', 'yml', 'yaml.in'])) {
                $parsedArray = Yaml::parse($yamlContent);
                if (!is_array($parsedArray)) {
                    throw new \Exception('Invalid YAML structure.');
                }
                return $parsedArray;

            } elseif ($extension === 'json') {
                return json_decode($yamlContent, true, 512, JSON_THROW_ON_ERROR);

            } else {
                // conf / conf.in → raw text
                return ['raw' => $yamlContent];
            }

        } catch (ParseException $e) {
            throw ValidationException::withMessages([
                'config_file' => 'YAML Parse Error: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'config_file' => 'General Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Convert JSON string to valid YAML
     * Null / empty values removed (no '# key': null)
     */
    public static function convertJsonToYaml($jsonContent)
    {
        try {
            $arrayContent = json_decode(
                $jsonContent,
                true,
                512,
                JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR
            );

            $arrayContent = self::removeNullValues($arrayContent);

            $yamlContent = Yaml::dump($arrayContent, 10, 2);

            // اصلاح فرمت لیست‌ها
            $yamlContent = preg_replace('/^(\s*)-\s*/m', '$1- ', $yamlContent);

            return $yamlContent;

        } catch (\Exception $e) {
            throw new HttpResponseException(response()->json([
                'msg' => 'Error in convert json to yaml',
                'error' => $e->getMessage()
            ], 422));
        }
    }

    /**
     * Remove null / empty values recursively
     */
    private static function removeNullValues(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::removeNullValues($value);
                if ($array[$key] === []) unset($array[$key]);
            } elseif ($value === null || $value === '') {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Upload module file and convert to JSON
     */
    public static function uploadModuleFile(UploadedFile $file)
    {
        $arrayContent = self::parseYamlToArray($file);

        if (isset($arrayContent['raw'])) {
            return json_encode(['raw' => $arrayContent['raw']], JSON_PRETTY_PRINT);
        }

        return json_encode($arrayContent, JSON_PRETTY_PRINT);
    }

    public static function parseRawConfigContent(string $content, string $extension = 'yaml'): array
    {
        $normalizedExtension = strtolower($extension);
        try {
            if (in_array($normalizedExtension, ['yaml', 'yml', 'yaml.in'])) {
                $parsed = Yaml::parse($content);
                if (!is_array($parsed)) {
                    throw new \RuntimeException('Invalid YAML structure.');
                }

                return $parsed;
            }

            if ($normalizedExtension === 'json') {
                $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($decoded)) {
                    throw new \RuntimeException('Invalid JSON structure.');
                }

                return $decoded;
            }

            return ['raw' => $content];
        } catch (ParseException $e) {
            throw ValidationException::withMessages([
                'config_file' => 'YAML Parse Error: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'config_file' => 'Parse Error: ' . $e->getMessage(),
            ]);
        }
    }
}
