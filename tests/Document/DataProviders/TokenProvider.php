<?php

namespace Test\Document\DataProviders;

use Novelist\Document\Parser\TokenType;

class TokenProvider
{
    /**
     * Retrieves test data for tokenization tests.
     *
     * @return array An array containing test data for tokenization. Each element in the array represents
     *               a single test case and follows this structure:
     *               - The #1 element (string): The input string to be tokenized.
     *               - The #2 element (int): The expected token type after tokenization.
     *               - The #3 element (mixed): The expected value associated with the token.
     */
    public static function getTokens(): array
    {
        return [
            [' [ ', TokenType::SQUARE_BRACKET_OPEN , null],
            [' ] ', TokenType::SQUARE_BRACKET_CLOSE, null],
            [' { ', TokenType::CURLY_BRACKET_OPEN, null],
            [' } ', TokenType::CURLY_BRACKET_CLOSE, null ],
            [' ( ', TokenType::ROUND_BRACKET_OPEN, null ],
            [' ) ', TokenType::ROUND_BRACKET_CLOSE, null ],
            [' = ', TokenType::EQUAL, null ],
            [' , ', TokenType::COMMA, null ],
            [' "test string" ', TokenType::STRING_VALUE, 'test string'],
            [' 123 ', TokenType::NUMBER_VALUE, 123 ],
            [' true ', TokenType::BOOLEAN_VALUE, true],
            [' false ', TokenType::BOOLEAN_VALUE, false],
            [' null ', TokenType::NULL_VALUE, null],
            ['namespace', TokenType::IDENTIFIER, 'namespace'],
            ['child-1', TokenType::IDENTIFIER, 'child-1'],
            ['# test comment', TokenType::COMMENT, 'test comment'],
            ['""" text content """', TokenType::NOWDOC_STRING_VALUE, 'text content'],
            ['``` source code ```', TokenType::EMBEDDED_SOURCE_CODE, 'source code'],
            ['', TokenType::END_OF_TOKEN, null]
        ];
    }
}