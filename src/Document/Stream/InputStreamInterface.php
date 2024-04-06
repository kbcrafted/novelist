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

interface InputStreamInterface
{
    /**
     * Reads a chunk of bytes from the stream.
     *
     * @return string A chunk of bytes, or an empty string when there's nothing left to read.
     */
    function read(): string;

    /**
     * Checks if the end of the stream has been reached.
     * @return bool True if the end of the stream has been reached, false otherwise.
     */
    function isEndOfStream(): bool;
}