<?php

namespace Test\Document;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Novelist\Document\ElementAttributeValue;
use Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use Novelist\Document\Exceptions\InvalidTokenException;
use Novelist\Document\Parser\Lexer;
use Novelist\Document\Stream\StringInputStream;
use Test\Document\DataProviders\ElementAttributeValueProvider;

class ElementAttributeValueTest extends TestCase
{
    /**
     * Tests the parsing of attribute values from provided input strings to ensure correct
     * extraction and representation of values.
     *
     * By feeding various input strings representing different types of attribute values,
     * this method assesses the ability of `ElementAttributeValue` to correctly parse and
     * capture these values. The method checks if the parsed value matches the expected value,
     * thus verifying the correctness of the parsing logic.
     *
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in value $expectedValue')]
    #[DataProviderExternal(ElementAttributeValueProvider::class, 'getValues')]
    public function parseAttributeValues($input, $expectedValue)
    {
        $lexer = new Lexer(new StringInputStream($input));
        $value = new ElementAttributeValue();
        $value->parse($lexer);
        $this->assertEquals($expectedValue, $value->getValue());
    }

    /**
     * Verifies the exception handling in `ElementAttributeValue` by providing invalid inputs
     * and expecting specific exceptions to be thrown.
     *
     * This test ensures that `ElementAttributeValue` not only parses valid attribute values correctly
     * but also robustly handles errors by throwing specific exceptions when encountering invalid inputs.
     * Each test case provided by the data provider includes an input string known to be invalid in
     * some way and the type of exception that the parsing process is expected to throw as a result.
     *
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in $expectedException')]
    #[DataProviderExternal(ElementAttributeValueProvider::class, 'getInvalidValues')]
    public function parseInvalidAttributeValues($input, $expectedException)
    {
        $lexer = new Lexer(new StringInputStream($input));
        $value = new ElementAttributeValue();

        $this->expectException($expectedException);
        $value->parse($lexer);
    }
}