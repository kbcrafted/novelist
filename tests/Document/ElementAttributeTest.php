<?php

namespace Test\Document;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Novelist\Document\ElementAttribute;
use Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use Novelist\Document\Exceptions\InvalidTokenException;
use Novelist\Document\Parser\Lexer;
use Novelist\Document\Stream\StringInputStream;
use Test\Document\DataProviders\ElementAttributeProvider;

class ElementAttributeTest extends TestCase
{
    /**
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in attribute: $expectedAttributeName = $expectedAttributeValue')]
    #[DataProviderExternal(ElementAttributeProvider::class, 'getAttributePairs')]
    public function parseAttributePairs($input, $expectedAttributeName, $expectedAttributeValue)
    {
        $lexer = new Lexer(new StringInputStream($input));

        $attribute = new ElementAttribute();
        $attribute->parse($lexer);

        // Check that the attribute name and value are correctly set
        $this->assertEquals($expectedAttributeName, $attribute->getName());
        $this->assertEquals($expectedAttributeValue, $attribute->getValue());
    }

    /**
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in an exception $expectedException')]
    #[DataProviderExternal(ElementAttributeProvider::class, 'getInvalidAttributeNames')]
    public function parseBadAttributeNames($input, $expectedException)
    {
        $lexer = new Lexer(new StringInputStream($input));
        $attribute = new ElementAttribute();

        $this->expectException($expectedException);
        $attribute->parse($lexer);
    }

     /**
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in an exception $expectedException')]
    #[DataProviderExternal(ElementAttributeProvider::class, 'getInvalidAttributeValues')]
    public function parseBadAttributeValues($input, $expectedException)
    {
        $lexer = new Lexer(new StringInputStream($input));
        $attribute = new ElementAttribute();

        $this->expectException($expectedException);
        $attribute->parse($lexer);
    }
}