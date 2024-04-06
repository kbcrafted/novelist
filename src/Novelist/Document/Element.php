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

use KBCrafted\Novelist\Document\Exceptions\InvalidElementAttributeValueException;
use KBCrafted\Novelist\Document\Exceptions\InvalidTokenException;
use KBCrafted\Novelist\Document\Parser\Lexer;
use KBCrafted\Novelist\Document\Parser\ParserHelper;
use KBCrafted\Novelist\Document\Parser\ParserInterface;
use KBCrafted\Novelist\Document\Parser\Token;
use KBCrafted\Novelist\Document\Parser\TokenType;

class Element implements ParserInterface
{

    use ParserHelper;

     /**
     * @var string
     */
    protected string $identifier = '';

    /**
     * @var ElementAttributeList
     */
    protected ElementAttributeList $attributes;

    /**
     * @var string
     */
    protected string $textContent = '';

    /**
     * @var Element[]
     */
    protected array $children = [];

    /**
     *
     */
    public function __construct()
    {
        $this->attributes = new ElementAttributeList();
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return ElementAttributeList
     */
    public function getAttributes(): ElementAttributeList
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function countChildren(): int
    {
        return count($this->children);
    }

    /**
     * @return string
     */
    public function getTextContent(): string
    {
        return $this->textContent;
    }

    /**
     * @param Lexer $lexer
     * @param Token|null $token
     * @return Token
     * @throws InvalidTokenException
     */
    public function parse(Lexer $lexer, ?Token $token = null): Token
    {
        if(is_null($token)) {
            $token = $this->getNextToken($lexer);
        }

        $this->expectOneOf($token, [
            TokenType::IDENTIFIER,
            TokenType::ROUND_BRACKET_OPEN,
            TokenType::CURLY_BRACKET_OPEN
        ]);
        // Expect an identifier
        $token = $this->parseIdentifier($lexer, $token);

        $this->expectOneOf($token, [
            TokenType::ROUND_BRACKET_OPEN,
            TokenType::CURLY_BRACKET_OPEN,
            TokenType::NOWDOC_STRING_VALUE,
            TokenType::EMBEDDED_SOURCE_CODE
        ]);
        // Expect an attribute list after the identifier
        $token = $this->parseAttributeList($lexer, $token);

        if(in_array($token->getType(), [TokenType::NOWDOC_STRING_VALUE, TokenType::EMBEDDED_SOURCE_CODE])) {
            $token = $this->parseTextContent($lexer, $token);
        }

        /*
         * After an attribute list, we expect a comma, which indicate a child-less element
         */
        $this->expectOneOf($token, [
            TokenType::COMMA,
            TokenType::IDENTIFIER,
            TokenType::CURLY_BRACKET_OPEN,
            TokenType::ROUND_BRACKET_OPEN,
            TokenType::CURLY_BRACKET_CLOSE
        ]);

        if($token->getType() == TokenType::CURLY_BRACKET_OPEN) {
            return $this->parseElementChildren($lexer, $token);
        }

        return $this->skipComma($lexer, $token);
    }

    /**
     * @param Lexer $lexer
     * @param Token $token
     * @return Token
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    protected function parseIdentifier(Lexer $lexer, Token $token): Token
    {
        if($token->getType() != TokenType::IDENTIFIER) {
            return $token;
        }

        $this->identifier = $token->getValue();
        return $this->getNextToken($lexer);
    }

    /**
     * @param Lexer $lexer
     * @param Token $token
     * @return Token
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    protected function parseAttributeList(Lexer $lexer, Token $token): Token
    {
        if($token->getType() != TokenType::ROUND_BRACKET_OPEN) {
            return $token;
        }

        $token = $this->attributes->parse($lexer, $token);

        return $this->skipComma($lexer, $token);
    }

    /**
     * @param Lexer $lexer
     * @param Token $token
     * @return Token
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    protected function parseElementChildren(Lexer $lexer, Token $token): Token
    {
        if($token->getType() != TokenType::CURLY_BRACKET_OPEN) {
            return $token;
        }

        $token = $this->getNextToken($lexer);
        while (!in_array($token->getType(), [
              TokenType::END_OF_TOKEN
            , TokenType::CURLY_BRACKET_CLOSE
            ])) {

            $element = new Element();
            $token = $element->parse($lexer, $token);
            $token = $this->skipComma($lexer, $token);
            $this->children[] = $element;
        }

        // Make sure the element was closed.
        $this->expectOneOf($token, [TokenType::CURLY_BRACKET_CLOSE]);

        return $this->getNextToken($lexer);
    }

    /**
     * @param Lexer $lexer
     * @param Token $token
     * @return Token
     * @throws InvalidElementAttributeValueException
     * @throws InvalidTokenException
     */
    protected function parseTextContent(Lexer $lexer, Token $token): Token
    {
        $this->textContent = $token->getValue();
        $token = $this->getNextToken($lexer);
        return $this->skipComma($lexer, $token);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $children = [];

        foreach ($this->children as $child) {
            $children[] = $child->toArray();
        }

        return [
            'identifier' => $this->identifier,
            'attributes' => $this->attributes->toArray(),
            'content'    => $this->textContent,
            'children'   => $children
        ];
    }
}