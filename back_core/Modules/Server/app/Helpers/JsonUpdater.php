<?php

namespace Modules\Server\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class JsonUpdater
{

    public static function updateJsonValue(array $json, string $path, $value)
    {
        $path = preg_replace('/\[(\d+)\]/', '.$1', $path);
        $keys = explode('.', $path);
        $currentNode = &$json;

        foreach ($keys as $index => $key) {

            if (!is_array($currentNode))
                $currentNode = [];

                // number
            if (is_numeric($key) && is_array($currentNode)) {
                if (isset($currentNode[(int)$key])) {
                    $currentNode = &$currentNode[(int)$key];
                } else {
                    $currentNode[$key] = [];
                    $currentNode = &$currentNode[$key]; // find or create
                }
            }
                // string
            elseif (isset($currentNode[$key])) {
                $currentNode = &$currentNode[$key];

            }elseif (!isset($currentNode[$key])) {
                $currentNode[$key] = [];
                $currentNode = &$currentNode[$key]; // find or create

            }else
                return $json;


                    // validation types and keys
            if ($index == count($keys) - 1) {
                if ($index < count($keys) - 1)
                    throw new \Exception("After this point, the key exists. Please check if you entered the path incorrectly.");
            }
        }


        $currentNode = $value;

        return $json;
    }

    public static function deleteConfigInModule (array $json, string $path)
    {
        $path = preg_replace('/\[(\d+)\]/', '.$1', $path);
        $keys = explode('.', $path);
        $currentNode = &$json;

        foreach ($keys as $index => $key) {
            if (is_numeric($key) && is_array($currentNode)) {
                if (isset($currentNode[(int)$key]))
                    $currentNode = &$currentNode[(int)$key];

                else
                    throw new \Exception("The entered path does not exist: {$path}");

            } elseif (isset($currentNode[$key])) {

                if ($index === count($keys) - 1) {
                    unset($currentNode[$key]);
                    return $json;
                }

                $currentNode = &$currentNode[$key];
            } else {
                throw new \Exception("The entered path does not exist: {$path}");
            }
        }

        return $json;
    }
}
