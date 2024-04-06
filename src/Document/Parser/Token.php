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

/**
 * Represents a token generated by the lexer during the parsing process.
 */
class Token {

    /**
     * @var TokenType The type of the token.
     */
    protected TokenType $type;

    /**
     * @var mixed|null The value associated with the token.
     */
    protected mixed $value;

    /**
     * Token constructor.
     *
     * @param TokenType $type The type of the token.
     * @param mixed|null $value The value associated with the token.
     */
    public function __construct(TokenType $type, mixed $value = null)
    {
        $this->type  = $type;
        $this->value = $value;
    }

    /**
     * Get the type of the token.
     *
     * @return TokenType The type of the token.
     */
    public function getType(): TokenType
    {
        return $this->type;
    }

    /**
     * Get the value associated with the token.
     *
     * @return mixed|null The value associated with the token.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Set the value associated with the token.
     *
     * @param mixed $value The value to set.
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

}