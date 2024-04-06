<?php
/*
 * This file is part of Stravigor Novelist, a PHP library for generating Laravel applications based on specifications.
 *
 * @package     Novelist
 * @author      Liva Ramarolahy <lr@stravigor.com>
 * @link        https://github.com/stravigor/novelist
 * @license     MIT License (https://opensource.org/licenses/MIT)
 */

namespace Novelist\Document\Parser;

use Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use Novelist\Document\Exceptions\InvalidTokenException;

/**
 * The ParserHelper trait provides helper methods for token parsing.
 */
trait ParserHelper
{
    /**
     * Retrieves the next non-comment token from the lexer.
     *
     * @param Lexer $lexer The lexer instance.
     * @return Token The next non-comment token.
     * @throws InvalidTokenException If an invalid token is encountered.
     */
    protected function getNextToken(Lexer $lexer): Token
    {
         do {
            $token = $lexer->getNextToken();
         } while($token->getType() == TokenType::COMMENT);
         return $token;
    }

    /**
     * Skips a comma token if present and retrieves the next token.
     *
     * @param Lexer $lexer The lexer instance.
     * @param Token $token The current token.
     * @return mixed|Token The next token after skipping the comma, or the current token if no comma is present.
     * @throws InvalidTokenException If an invalid token is encountered.
     */
    protected function skipComma($lexer, $token): mixed
    {
        if($token->getType() == TokenType::COMMA) {
            $token = $this->getNextToken($lexer);
        }
        return $token;
    }

    /**
     * Ensures that the token type or value is one of the expected types or values.
     *
     * @param Token $token The token to check.
     * @param array $expectedTypesOrValues The array of expected token types or values.
     * @param string $exceptionClass The exception class to throw if the token does not match the expected types or values.
     * @throws InvalidTokenException|InvalidElementAttributeValueException If the token type or value does not match the expected types or values.
     * @return void
     */
    protected function expectOneOf(Token $token, array $expectedTypesOrValues, string $exceptionClass = InvalidTokenException::class): void
    {
        if (!in_array($token->getType(), $expectedTypesOrValues)) {

            $expectedTypesOrValuesString = array_map(function($token) {
                return $token->value;
            }, $expectedTypesOrValues);

            throw new $exceptionClass(sprintf(
                "Unexpected token %s [%s]. Expecting one of (%s)",
                $token->getType()->value,
                $token->getValue(),
                implode(', ', $expectedTypesOrValuesString)
            ));
        }
    }
}