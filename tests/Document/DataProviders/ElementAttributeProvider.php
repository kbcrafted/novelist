<?php

namespace Test\Document\DataProviders;

use Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use Novelist\Document\Exceptions\InvalidTokenException;

final class ElementAttributeProvider
{
    /**
     * Provides a collection of test cases for valid attribute pairs.
     *
     * Each test case is an associative array where the value is an array containing:
     * 1. The attribute pair string as it would appear in a document.
     * 2. The expected attribute name after parsing.
     * 3. The expected attribute value after parsing.
     *
     * @return array An associative array of test cases for valid attribute pairs.
     */
    public static function getAttributePairs(): array
    {
        return [
            'string_value' => [
                'name = "Novelist"', 'name', 'Novelist'
            ],
            'number_value' => [
                'count = 123', 'count', 123
            ],
            'boolean_true_value' => [
                'is_true = true', 'is_true', true
            ],
            'boolean_default_value' => [
                'is_default_true,', 'is_default_true', true
            ],
            'boolean_false_value' => [
                'is_false = false', 'is_false', false
            ],
            'null_value' => [
                'sat = null', 'sat', null
            ],
            'array_value' => [
                'list = [1, 2, 3]', 'list', [1, 2, 3]
            ],
            'nested_array' => [
                'name-1 = [1, 2, [1, 2]]', 'name-1', [1, 2, [1, 2]]
            ],
            'constant_value' => [
                'default = DEFAULT', 'default', 'DEFAULT'
            ],
        ];
    }

    /**
     * Provides a collection of test cases for invalid attribute names.
     *
     * Each test case is an associative array where the value is an array containing:
     * 1. The attribute pair string with an invalid attribute name.
     * 2. The expected exception class that should be thrown due to the invalid attribute name.
     *
     * @return array An associative array of test cases for invalid attribute names.
     */
    public static function getInvalidAttributeNames(): array
    {
        return [
            'invalid_name_01' => [
                '[name] = "Novelist"', InvalidTokenException::class,
            ],
            'invalid_name_02' => [
                '"name" = "Novelist"', InvalidTokenException::class,
            ],
            'invalid_name_03' => [
                '{name} = "Novelist"', InvalidTokenException::class,
            ],
            'invalid_name_04' => [
                '(name) = "Novelist"', InvalidTokenException::class,
            ],

        ];
    }

    /**
     * Provides a collection of test cases for invalid attribute values.
     *
     * Each test case is an associative array where the value is an array containing:
     * 1. The attribute pair string with an invalid attribute value.
     * 2. The expected exception class that should be thrown due to the invalid attribute value.
     *
     * The test cases are designed to test the parser's handling of various invalid value formats,
     * including incorrectly formatted arrays and unsupported data structures, to ensure that the
     * parser can gracefully handle and report errors in attribute values.
     *
     * @return array An associative array of test cases for invalid attribute values.
     */
    public static function getInvalidAttributeValues(): array
    {
        return [
            'invalid_value_01' => [
                'name = {}', InvalidTokenException::class,
            ],
            'invalid_value_03' => [
                'name = [1, 2, {}]', InvalidElementAttributeValueException::class,
            ],
            'invalid_value_04' => [
                'name = [1, 2, ()]', InvalidElementAttributeValueException::class,
            ],
            'invalid_value_05' => [
                'name = [1, 2,,]', InvalidTokenException::class,
            ],
        ];
    }

}