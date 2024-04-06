<?php
/*
 * This file is part of Stravigor Novelist, a PHP library for generating Laravel applications based on specifications.
 *
 * @package     Novelist
 * @author      Liva Ramarolahy <lr@stravigor.com>
 * @link        https://github.com/stravigor/novelist
 * @license     MIT License (https://opensource.org/licenses/MIT)
 */

namespace Novelist\Document\Stream;

/**
 * Implementation of InputStreamInterface for reading from a string.
 */
final class StringInputStream implements InputStreamInterface
{
    /**
     * @var string The string buffer to read from.
     */
    private string $buffer = '';

    /**
     * @var bool Indicates whether the end of the stream has been reached.
     */
    private bool $isEndOfStream = false;

    /**
     * Constructs a StringInputStream object with the provided string buffer.
     *
     * @param string $buffer The string buffer to read from.
     */
    public function __construct(string $buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * Reads the entire string buffer.
     *
     * @return string The entire string buffer.
     */
    public function read(): string
    {
        // Mark the end of the stream after reading the buffer
        $this->isEndOfStream = true;
        return $this->buffer;
    }

    /**
     * Checks if the end of the stream has been reached.
     *
     * @return bool True if the end of the stream has been reached, false otherwise.
     */
    public function isEndOfStream(): bool
    {
        return $this->isEndOfStream;
    }
}