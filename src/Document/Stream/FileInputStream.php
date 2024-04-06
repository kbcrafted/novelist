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

use Exception;

/**
 * Implementation of InputStreamInterface for reading from a file.
 */
final class FileInputStream implements InputStreamInterface
{
    /**
     * Specifies the size of each chunk to read from the file.
     */
    public const CHUNK_SIZE = 4096;

    /**
     * @var resource|false|null The file handle for reading from the file.
     */
    private $fileHandle;

     /**
     * @var bool Indicates whether the end of the stream has been reached.
     */
    private bool $isEndOfStream = false;

    /**
     * Constructs a FileInputStream object with the provided file path.
     *
     * @param string $filepath The path to the file to be read.
     * @throws Exception
     */
    public function __construct(string $filepath)
    {
        $this->fileHandle = @fopen($filepath, 'r');
        if($this->fileHandle === false) {
            $this->isEndOfStream = true;
            throw new Exception(sprintf("Failed to open file %s", $filepath));
        }
    }

    /**
     * Reads a chunk of data from the file.
     *
     * @return string The data read from the file.
     */
    public function read(): string
    {
        // Read a chunk of data from the file
        $buffer = fgets($this->fileHandle, self::CHUNK_SIZE);

        // If end of file is reached, set the end of stream flag
        if($buffer === false) {
            $this->isEndOfStream = true;
            $buffer = '';
        }
        return $buffer;
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