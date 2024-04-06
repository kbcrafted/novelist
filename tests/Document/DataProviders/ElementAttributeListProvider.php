<?php

namespace Test\Document\DataProviders;

use Novelist\Document\Exceptions\InvalidTokenException;

class ElementAttributeListProvider
{
    /**
     * Retrieves valid attribute lists along with their expected parsed attributes and values.
     *
     * Each test case array contains:
     *   - The first element (string): A raw input string representing an attribute list as it might appear in a document.
     *   - The second element (int): The expected number of attributes in the parsed list.
     *   - The third element (array): The expected names of the attributes in the parsed list.
     *   - The fourth element (array): The expected values for each attribute in the list, corresponding by index.
     *
     * @return array An array of arrays, each representing a test case with a raw input string and its expected parsed outcome.
     */
    public static function getAttributeLists(): array
    {
        return [
            [
                '( name = "Tsootr", version = -1.0 )',
                2,
                ['name', 'version'],
                ['Tsootr', -1.0]
            ],
            [
                '( version = 1.0 encoding = "UTF-8" default = DEPRECATED max-count = 10 laravel = null )',
                5,
                ['version', 'encoding', 'default', 'max-count', 'laravel'],
                [1.0, "UTF-8", 'DEPRECATED', 10, null]
            ],
            [
                '( name = "sizes" type = "enum" values = [1.0, 2.0, 3.0, 4.0] default = 2.0 )',
                4,
                ['name', 'type', 'values', 'default'],
                ["sizes", "enum", [1.0, 2.0, 3.0, 4.0], 2.0]
            ],
            [
                '( name = "role"  type = "enum" values = [SUBSCRIBER, PUBLISHER, ADMIN, TEST]  cast )',
                4,
                ['name', 'type', 'values', 'cast'],
                ["role", "enum",  ['SUBSCRIBER', 'PUBLISHER', 'ADMIN', 'TEST'], true]
            ],

        ];
    }

    /**
     * @return array
     */
    public static function getInvalidAttributeLists(): array
    {
        return [
            [
                '( name = "Tsootr", [] = -1.0 )',
                InvalidTokenException::class
            ],
            [
                '( "version" = 1.0 encoding = "UTF-8" default = DEPRECATED max-count = 10 laravel = null )',
                InvalidTokenException::class
            ],
            [
                '( name = "sizes" type = "enum" {} = [1.0, 2.0, 3.0, 4.0] default = 2.0 )',
                InvalidTokenException::class
            ],
            [
                '( name = "role"  type = "enum" values = [SUBSCRIBER, PUBLISHER, ADMIN, TEST]  () )',
                InvalidTokenException::class
            ]
        ];
    }
}