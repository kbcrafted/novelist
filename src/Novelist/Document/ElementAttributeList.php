<?php
/*
 * This file is part of Stravigor Novelist, a PHP library for generating Laravel applications based on specifications.
 *
 * @package     Novelist
 * @author      Liva Ramarolahy <lr@stravigor.com>
 * @link        https://github.com/stravigor/novelist
 * @license     MIT License (https://opensource.org/licenses/MIT)
 */

namespace KBCrafted\Novelist\Document;

use KBCrafted\Novelist\Document\Parser\Lexer;
use KBCrafted\Novelist\Document\Parser\ParserHelper;
use KBCrafted\Novelist\Document\Parser\ParserInterface;
use KBCrafted\Novelist\Document\Parser\Token;
use KBCrafted\Novelist\Document\Parser\TokenType;

/**
 * Represents a list of element attributes
 */
class ElementAttributeList implements ParserInterface
{
    use ParserHelper;

    /**
     * An array containing the parsed element attributes.
     * @var ElementAttribute[]
     */
    protected array $attributes = [];

    /**
     * @return ElementAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->attributes);
    }

    /**
     * Parses the list of element attributes.
     *
     * @param Lexer $lexer
     * @param ?Token $token
     * @return Token
     * @throws \KBCrafted\Novelist\Document\Exceptions\InvalidElementAttributeValueException
     * @throws \KBCrafted\Novelist\Document\Exceptions\InvalidTokenException
     */
    public function parse(Lexer $lexer, ?Token $token = null): Token
    {
        if(is_null($token)) {
            $token = $this->getNextToken($lexer);
        }

        $this->expectOneOf($token, [
                TokenType::ROUND_BRACKET_OPEN
                ]);

        $token = $this->getNextToken($lexer);

        /*
         * Check if it's an empty attribute list.
         */
        if($token->getType() == TokenType::ROUND_BRACKET_CLOSE) {
            return $this->getNextToken($lexer);
        }

        while (!in_array($token->getType(), [TokenType::ROUND_BRACKET_CLOSE, TokenType::END_OF_TOKEN])) {
            $attribute = new ElementAttribute();
            $token = $attribute->parse($lexer, $token);
            // Skip the comma
            $token = $this->skipComma($lexer, $token);
            $this->attributes[] = $attribute;
        }

        $this->expectOneOf($token, [TokenType::ROUND_BRACKET_CLOSE]);

        return $this->getNextToken($lexer);
    }

    /**
     * Converts the parsed element attributes into an associative array.
     *
     * @return array An associative array containing the attribute names and values.
     */
    public function toArray(): array
    {
        $attributes = [];
        foreach ($this->attributes as $attribute) {
            $attributes = array_merge($attributes, $attribute->toArray());
        }
        return $attributes;
    }
}