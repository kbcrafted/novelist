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

use Novelist\Document\Exceptions\InvalidTokenException;
use Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use Novelist\Document\Parser\Lexer;
use Novelist\Document\Parser\ParserHelper;
use Novelist\Document\Parser\ParserInterface;
use Novelist\Document\Parser\Token;
use Novelist\Document\Parser\TokenType;

/**
 *
 */
class ElementAttribute implements ParserInterface
{
    use ParserHelper;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var mixed
     */
    protected mixed $value = true;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool|mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @throws InvalidTokenException|InvalidElementAttributeValueException
     */
    public function parse(Lexer $lexer, ?Token $token = null): Token
    {
        if(is_null($token)) {
            $token = $this->getNextToken($lexer);
        }

        $this->expectOneOf($token, [
                TokenType::IDENTIFIER
                ]);

        $this->name = $token->getValue();
        $token = $this->getNextToken($lexer);

        /*
         * After an attribute name, the following tokens are valid:
         * - An EQUAL sign, indicating that a value will be assigned to this attribute.
         * - An IDENTIFIER, which implies that the attribute is assigned a default value of TRUE,
         *   and the current token marks the beginning of the next attribute.
         * - A COMMA, separating this attribute from the next one, if multiple attributes are present.
         * - A ROUND_BRACKET_CLOSE, signaling the end of the attribute list.
         */
        $this->expectOneOf($token, [
            TokenType::EQUAL,
            TokenType::IDENTIFIER,
            TokenType::COMMA,
            TokenType::ROUND_BRACKET_CLOSE
        ]);

        /*
         * If no EQUAL sign follows, defer handling of subsequent tokens to the parent parser.
         */
        if ($token->getType() != TokenType::EQUAL) {
            return $token;
        }

        /*
         * Parse the attribute value
         */
        $value = new ElementAttributeValue();
        $value->parse($lexer);
        $this->value = $value->getValue();

        return $this->getNextToken($lexer);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->name => $this->value
        ];
    }
}