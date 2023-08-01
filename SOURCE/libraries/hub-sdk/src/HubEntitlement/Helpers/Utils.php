<?php

/**
 * Utility functions
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Helpers;


class Utils
{
    /**
     * Date RFC3339 Format
     */
    const RFC3339_EXTENDED = 'Y-m-d\TH:i:s.uP';

    /**
     * Transforms string to camelcase
     * snake_case to camelCase.
     *
     * @param $string
     * @return string
     */
    public static function snakeToCamelCase($string)
    {
        return implode(
            '',
            array_map(
                function ($item) {
                    return ucwords($item);
                },
                explode('_', $string)
            )
        );
    }

    /**
     * Transforms string snake case.
     *
     * @param $string
     * @return string
     */
    public static function camelCaseToSnake($string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    /**
     * Creates camel cased getter name.
     *
     * @param $name
     * @return string
     */
    public static function makeGetterName($name)
    {
        $camelCaseName = static::snakeToCamelCase($name);
        return "get$camelCaseName";
    }

    /**
     * Creates camel cased setter name.
     *
     * @param $name
     * @return string
     */
    public static function makeSetterName($name)
    {
        $camelCaseName = static::snakeToCamelCase($name);
        return "set$camelCaseName";
    }

    /**
     * Creates camel cased computed name.
     *
     * @param $name
     * @return string
     */
    public static function makeComputedName($name)
    {
        $camelCaseName = static::snakeToCamelCase($name);
        return "computed$camelCaseName";
    }

    /**
     * Creates camel cased foreign field.
     *
     * @param $name
     * @return string
     */
    public static function makeForeignFieldName($name)
    {
        $snakeCaseName = static::camelCaseToSnake($name);
        return "${snakeCaseName}_id";
    }

    /**
     * Converts string to datetime.
     *
     * @param $date
     * @return \DateTime
     */
    public static function toDateTime($date)
    {
        return new \DateTime($date);
    }

    /**
     * Sorts array of array.
     *
     * @param $multiArray
     * @param $key
     * @return mixed
     */
    public static function sortMultidimensionalArray(&$multiArray, $key)
    {
        usort($multiArray, function ($a, $b) use ($key) {
            return strcmp($a[$key], $b[$key]);
        });

        return $multiArray;
    }

    /**
     * Sorts array of objects.
     *
     * @param $objects
     * @param $key
     * @return mixed
     *
     * SB-800 modified by mtanada 20210316
     */
    public static function sortListOfObjects(&$objects, $key)
    {
        usort($objects, function ($a, $b) use ($key) {
            return ($a->$key < $b->$key) ? -1 : (($a->$key > $b->$key) ? 1 : 0);
        });

        return $objects;
    }

    /**
     * Pluralize a word.
     *
     * @param $singular
     * @return string
     */
    public static function pluralize($singular)
    {
        if (empty($singular)) {
            return $singular;
        }

        $lastLetter = strtolower($singular[strlen($singular) - 1]);
        $vowels = ['a', 'e', 'i', 'o', 'u'];

        switch ($lastLetter) {
            case 'y' && !in_array(substr($singular, 0, -2), $vowels):
                $pluralize = substr($singular, 0, -1) . 'ies';
                break;
            case 's':
                $pluralize = $singular . 'es';
                break;
            default:
                $pluralize = $singular . 's';
                break;
        }

        return $pluralize;
    }

    /**
     * Coverts objects like DateTime to string
     * so it's easier to convert them to JSON.
     *
     * @param $coreObject
     * @return string
     */
    public static function convertCoreObjectsToString($coreObject)
    {
        if ($coreObject instanceof \DateTime) {
            return $coreObject->format(static::RFC3339_EXTENDED);
        }

        return $coreObject;
    }
}
