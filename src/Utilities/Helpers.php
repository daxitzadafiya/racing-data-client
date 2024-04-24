<?php

namespace RacingDataClient\Utilities;

use Carbon\Carbon;
use Illuminate\Support\Str;

class Helpers
{
    /**
     * Check whether a string exists within an array value.
     *
     * @param  string  $string
     * @param  array  $array
     * @return bool
     */
    public function contains(string $string, array $array): bool
    {
        foreach ($array as $key => $value) {
            $position = stripos($string, $value);

            if ($position !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Modify string casing.
     *
     * @param  string  $value
     * @return string
     */
    public function modifyCasing(string $value): string
    {
        $position = $this->contains($value, ['id', 'api']);

        if ($position) {
            $string = substr($value, $position);
            $value = str_replace($string, Str::upper($string), $value);
        }

        return $value == 'id' || $value == 'api' ? Str::upper($value) : Str::studly($value);
    }

    /**
     * Modify the property casing on a response object.
     *
     * @param  object  $object
     * @return array
     */
    public function toResponse(object $object): array
    {
        $properties = [];

        foreach ($object as $property => $value) {
            if (is_object($value)) {
                $value = $this->toResponse($value);
            }

            $properties[$this->toSnakeCase($property)] = $value;
        }

        return $properties;
    }

    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @return string
     */
    public function toSnakeCase(string $value): string
    {
        $position = $this->contains($value, ['id', 'api']);

        if ($position) {
            $string = substr($value, $position);
            $value = str_replace($string, '_'.Str::lower($string), $value);
        }

        return $this->contains($value, ['id', 'api']) ? Str::lower($value) : Str::snake($value);
    }

    /**
     * Convert an array's keys to snake case.
     *
     * @param  array|object  $items
     * @return array
     */
    public function toSnakeCaseKeys(array|object $items): array
    {
        $result = [];

        foreach ($items as $key => $value) {
            $snakeKey = $this->toSnakeCaseKey($key);

            if (is_array($value) || is_object($value) && ! $value instanceof Carbon) {
                // If $value is an array, recursively transform its keys
                $result[$snakeKey] = $this->toSnakeCaseKeys($value);
            } else {
                // If $value is not an array, just assign the transformed key and original value
                $result[$snakeKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Convert a string to snake case.
     *
     * @param  string  $string
     * @return string
     */
    public function toSnakeCaseKey(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    /**
     * Convert string/array values to title case.
     *
     * @param  string|array  $properties
     * @return array|string
     */
    public function toTitleCase(string|array $properties): array|string
    {
        if (is_array($properties)) {
            array_walk($properties, function (&$property, $key) {
                if (is_array($property) || is_string($property)) {
                    $value = $this->contains($key, ['id', 'api']) ? $property : $this->toTitleCase($property);

                    $property = (is_array($value) || is_int($value) ? $value : $this->contains($key, ['id', 'api'])) ? $value : str_replace('_', ' ', $value);
                }
            });

            return $properties;
        }

        return ucwords($properties);
    }

    /**
     * Convert the passed value to a Carbon instance.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function convertDatesToCarbon(mixed $value): mixed
    {
        // If the value is an array, we will recurse back into the
        // function and convert each of the values in the array.
        if (is_array($value)) {
            return array_map([$this, 'convertDatesToCarbon'], $value);
        }

        // If the value is a string that looks like it contains a date, we will
        // create a Carbon instance from the value.
        if (is_string($value) && preg_match('(\d{1,4}([.\-/])\d{1,2}([.\-/])\d{1,4})', $value)) {
            return Carbon::parse(date('d-m-Y H:i:s', strtotime($value)));
        }

        return $value;
    }

    public function arraySearch($array, string $key, string|int|bool $value): bool
    {
        foreach ($array as $subArray) {
            if (isset($subArray[$key]) && str_contains($subArray[$key], $value)) {
                return true;
            }
        }

        return false;
    }
}
