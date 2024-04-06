<?php
/*
 * This file is part of Stravigor Novelist, a PHP library for generating Laravel applications based on specifications.
 *
 * @package     Novelist
 * @author      Liva Ramarolahy <lr@stravigor.com>
 * @link        https://github.com/stravigor/novelist
 * @license     MIT License (https://opensource.org/licenses/MIT)
 */

namespace Novelist\Document;

use Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use Novelist\Document\Exceptions\InvalidTokenException;
use Novelist\Document\Parser\Lexer;
use Novelist\Document\Parser\ParserHelper;
use Novelist\Document\Parser\ParserInterface;
use Novelist\Document\Parser\Token;
use Novelist\Document\Parser\TokenType;

class ElementAttributeValue implements ParserInterface
{
    use ParserHelper;

    /**
     * @var mixed
     */
    protected mixed $value = true;

    /**
     * @return bool|mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param Lexer $lexer
     * @param Token|null $token
     * @return Token
     * @throws Exceptions\InvalidElementAttributeValueException
     * @throws Exceptions\InvalidTokenException
     */
    function parse(Lexer $lexer, ?Token $token = null): Token
    {
        if(is_null($token)) {
            $token = $this->getNextToken($lexer);
        }

        /*
         * Ensure the current token matches one of the expected types for attribute values:
         * - IDENTIFIER          : User-defined constant name.
         * - STRING_VALUE        : String literal.
         * - BOOLEAN_VALUE       : Boolean value (TRUE or FALSE).
         * - NULL_VALUE          : Null.
         * - NUMBER_VALUE        : Integer or floating-point number.
         * - SQUARE_BRACKET_OPEN : Start of an array.
         */
        $this->expectOneOf($token, [
            TokenType::IDENTIFIER,
            TokenType::STRING_VALUE,
            TokenType::BOOLEAN_VALUE,
            TokenType::NULL_VALUE,
            TokenType::NUMBER_VALUE,
            TokenType::SQUARE_BRACKET_OPEN
        ], InvalidElementAttributeValueException::class);

        if ($token->getType() == TokenType::SQUARE_BRACKET_OPEN) {
            $this->value = $this->parseArrayValues($lexer);
        } else {
            $this->value = $token->getValue();
        }

        return $token;
    }

    /**
     * @param Lexer $lexer
     * @return array
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    protected function parseArrayValues(Lexer $lexer): array
    {
        $values = [];
        $token = $this->getNextToken($lexer);

        while (!in_array($token->getType(), [TokenType::SQUARE_BRACKET_CLOSE, TokenType::END_OF_TOKEN])) {

            $value = new ElementAttributeValue();
            $value->parse($lexer, $token);
            $values[] = $value->getValue();

            $token = $this->getNextToken($lexer);
            $this->expectOneOf($token, [
                TokenType::COMMA,
                TokenType::SQUARE_BRACKET_CLOSE
            ]);

            // Skip the comma
            $token = $this->skipComma($lexer, $token);

            if ($token->getType() == TokenType::SQUARE_BRACKET_CLOSE) {
                break;
            }
        }
        // Make sure the array was closed.
        $this->expectOneOf($token, [TokenType::SQUARE_BRACKET_CLOSE]);

        return $values;
    }

    function toArray(): array
    {
        return [];
    }
}