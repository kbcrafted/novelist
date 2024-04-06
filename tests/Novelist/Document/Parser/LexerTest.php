<?php

namespace Test\Novelist\Document\Parser;

use Exception;
use KBCrafted\Novelist\Document\Parser\Lexer;
use KBCrafted\Novelist\Document\Stream\StringInputStream;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Test\Novelist\Document\DataProviders\TokenProvider;

class LexerTest extends TestCase
{

    /**
     * Tests the tokenization process by providing input strings and verifying the produced tokens.
     *
     * @throws Exception If an error occurs during the test.
     */
    #[Test]
    #[TestDox('Parsing $input to results in $expectedType ($expectedValue)')]
    #[DataProviderExternal(TokenProvider::class, 'getTokens')]
    public function getNextToken($input, $expectedType, $expectedValue)
    {
        $lexer = new Lexer(new StringInputStream($input));
        $token = $lexer->getNextToken();
        $this->assertEquals($expectedType, $token->getType());
        $this->assertEquals($expectedValue, $token->getValue());
    }
}