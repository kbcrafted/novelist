<?php

namespace Test\Document;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Novelist\Document\Element;
use Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use Novelist\Document\Exceptions\InvalidTokenException;
use Novelist\Document\Parser\Lexer;
use Novelist\Document\Stream\StringInputStream;
use Test\Document\DataProviders\ElementProvider;

/**
 * Tests for the `Element` parsing functionality of the document processing library.
 *
 * This class focuses on verifying the correct parsing of valid elements and the proper handling
 * of errors during the parsing of invalid elements. It utilizes data providers to supply test cases,
 * allowing for a comprehensive and efficient way to test a wide range of scenarios.
 */
class ElementTest extends TestCase
{
    /**
     * Tests the parsing of valid element strings into `Element` objects.
     *
     * This method takes a string representing a document element (`$input`) and parses it
     * to verify that the resulting `Element` object matches the expected outcomes in terms of
     * its identifier, attribute count, children count, and contained text contents.
     *
     * @return void
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in identifier: $expectedIdentifier with: ' .
        '$expectedAttributeCount attributes, ' .
        '$expectedChildrenCount children, ' .
        '$expectedTextContents as text content, '
    )]
    #[DataProviderExternal(ElementProvider::class, 'getElements')]
    public function parseElements($input, $expectedIdentifier, $expectedAttributeCount, $expectedChildrenCount, $expectedTextContents)
    {
        $lexer = new Lexer(new StringInputStream($input));
        $element = new Element();
        $element->parse($lexer);

        $this->assertEquals($expectedIdentifier, $element->getIdentifier());
        $this->assertEquals($expectedAttributeCount, $element->getAttributes()->count());
        $this->assertEquals($expectedChildrenCount, $element->countChildren());
        $this->assertEquals($expectedTextContents, $element->getTextContents());
    }

    /**
     * Tests the parsing of invalid element strings to ensure they correctly throw exceptions.
     *
     * This method expects to encounter specific exceptions when attempting to parse invalid element strings,
     * effectively testing the error handling and validation mechanisms of the element parsing process.
     *
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    #[Test]
    #[TestDox('Parsing $input should result in an exception $expectedException')]
    #[DataProviderExternal(ElementProvider::class, 'getInvalidElements')]
    public function parseInvalidAttributeNames($input, $expectedException)
    {
        $lexer = new Lexer(new StringInputStream($input));
        $attribute = new Element();

        $this->expectException($expectedException);
        $attribute->parse($lexer);
    }
}