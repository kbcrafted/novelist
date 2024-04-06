<?php

namespace Test\Document\DataProviders;

use Novelist\Document\Exceptions\InvalidElementAttributeValueException;


/**
 * Provides test data for validating the parsing of attribute values within a document.
 *
 * This class is designed to supply sets of attribute values, both valid and invalid,
 * to facilitate the testing of attribute value parsing functionality. It helps in
 * assessing the parser's ability to correctly interpret different data types and
 * structures, as well as its error handling capabilities when faced with syntactically
 * incorrect inputs.
 */
class ElementAttributeValueProvider
{
    /**
     * Retrieves valid attribute value declarations along with their expected parsed values for testing.
     *
     * Each test case array contains:
     *   - The first element (string): A raw input string representing an attribute value as it might appear in a document.
     *   - The second element (mixed): The expected parsed value, demonstrating how the raw input should be interpreted
     *     by the parser.
     *
     * @return array[] An array of arrays, each representing a test case with a raw input string and its expected parsed value.
     */
    public static function getValues(): array
    {
        return [
            [ '"string  "', 'string  ' ],
            [ 'STRING', 'STRING' ],
            [ 'true', true ],
            [ 'false', false ],
            [ '1345', 1345 ],
            [ '[1, 2, 3]', [1, 2, 3] ],
            [ '[1, 2, ["one", "two", "three"]]', [1, 2, ['one', 'two', 'three']] ]
        ];
    }

    /**
     * Retrieves examples of invalid attribute value declarations expected to trigger parsing exceptions.
     *
     * Each test case array includes:
     *   - The first element (string): An input string representing a malformed or syntactically incorrect attribute value.
     *   - The second element (string): The class name of the exception that is expected to be thrown when attempting
     *     to parse the given input.
     *
     * This method assists in verifying the parser's robustness and its ability to gracefully handle invalid input
     * by throwing the appropriate exceptions.
     *
     * @return array[] An array of arrays, each indicating a test case with an invalid input string and the expected exception.
     */
    public static function getInvalidValues(): array
    {
        return [
            [ '{ }' , InvalidElementAttributeValueException::class],
            [ '( )' , InvalidElementAttributeValueException::class],
            [ '""" now doc """' , InvalidElementAttributeValueException::class],
            [ '``` source code ```' , InvalidElementAttributeValueException::class],
            [ '# comment' , InvalidElementAttributeValueException::class],
        ];
    }
}