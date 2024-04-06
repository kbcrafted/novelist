<?php

namespace Test\Document\DataProviders;

use Novelist\Document\Exceptions\InvalidTokenException;

class ElementProvider
{
    /**
     * Provides a set of test cases for valid element parsing.
     *
     * Each test case is an array with the following structure:
     * 1. The element string as it would appear in a document.
     * 2. The expected identifier of the element after parsing.
     * 3. The expected number of attributes the element has.
     * 4. The expected number of child elements the element contains.
     * 5. An array of expected text content or embedded the element has.
     *
     * These test cases cover a variety of scenarios, including elements with no attributes or children, elements with only text content,
     * elements with attributes but no children, and elements with both children and text content.
     *
     * @return array An array of test cases for validating the parsing of elements.
     */
    public static function getElements(): array
    {
        return [
            ['name () {} ', 'name', 0, 0, []],
            ['another-name () { """ text content """ } ', 'another-name', 0, 0, ['text content']],
            ['name-1 () { ``` source code ```} ', 'name-1', 0, 0, ['source code']],
            ['size (size = 12, length = 23) {} ', 'size', 2, 0, []],
            ['element-with-children () { """ text content """, child-one {}, child-two {} } ', 'element-with-children', 0, 2, ['text content']],
        ];
    }

    /**
     * Provides a set of test cases for elements that are expected to fail parsing.
     *
     * Each test case is an array where the first element is a string representing an invalid element
     * and the second element is the exception class expected to be thrown due to the parsing error.
     *
     * This method aims to test the robustness of the element parsing mechanism by including scenarios
     * with various syntax errors or invalid formats, such as missing delimiters, incorrect nesting,
     * or invalid content within an element definition.
     *
     * @return array An array of test cases containing invalid elements and the expected exceptions.
     */
    public static function getInvalidElements(): array
    {
        return [
            ['name another-name', InvalidTokenException::class],
            ['name []', InvalidTokenException::class],
            ['name """ content """', InvalidTokenException::class],
            ['name ``` source code ```', InvalidTokenException::class]
        ];
    }
}