<?php

namespace Test\Document;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Novelist\Document\ElementAttributeList;
use Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use Novelist\Document\Exceptions\InvalidTokenException;
use Novelist\Document\Parser\Lexer;
use Novelist\Document\Stream\StringInputStream;
use Test\Document\DataProviders\ElementAttributeListProvider;

class ElementAttributeListTest extends TestCase
{

    /**
     * Validates the parsing of attribute lists from given input strings.
     *
     * This test method asserts that, for a provided input string representing an element's attributes,
     * the parsing process correctly identifies and constructs a list of attribute objects with the
     * expected names and values. It checks the total count of parsed attributes, and individually
     * verifies each attribute's name and value against expected outcomes.
     *
     * @return void
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in $expectedCount parsed attributes')]
    #[DataProviderExternal(ElementAttributeListProvider::class, 'getAttributeLists')]
    public function parseAttributeList($input, $expectedCount, $expectedNames, $expectedValues)
    {
        $input = new StringInputStream($input);
        $lexer = new Lexer($input);

        $attributeList = new ElementAttributeList();
        $attributeList->parse($lexer);

        $attributes = $attributeList->getAttributes();

        $this->assertCount($expectedCount, $attributes);

        $names = array_map(function($attribute) {
            return $attribute->getName();
        }, $attributes);

        $this->assertEquals($expectedNames, $names);

        $values = array_map(function($attribute) {
            return $attribute->getValue();
        }, $attributes);

        $this->assertEquals($expectedValues, $values);
    }

    /**
     * Tests the error handling capabilities when parsing invalid attribute lists.
     *
     * This method is designed to feed invalid attribute list strings into the parsing mechanism
     * and expect specific exceptions to be thrown. It validates the robustness and correctness of
     * error handling within the `ElementAttributeList` parsing logic, ensuring that malformed inputs
     * are appropriately flagged with relevant exceptions.
     *
     * @param $input
     * @param $expectedException
     * @return void
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in $expectedException')]
    #[DataProviderExternal(ElementAttributeListProvider::class, 'getInvalidAttributeLists')]
    public function parseInvalidAttributeList($input, $expectedException)
    {
        $input = new StringInputStream($input);
        $lexer = new Lexer($input);

        $attributeList = new ElementAttributeList();

        $this->expectException($expectedException);
        $attributeList->parse($lexer);
    }
}