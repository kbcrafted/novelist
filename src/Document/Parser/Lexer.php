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

use Novelist\Document\Exceptions\InvalidTokenException;
use Novelist\Document\Stream\InputStreamInterface;

/**
 * The Lexer class tokenizes input strings into tokens.
 */
final class Lexer
{
    /**
     * @var int The length of the current buffer.
     */
    private int $length;

    /**
     * @var int The current cursor position.
     */
    private int $cursorPosition;

    /**
     * @var InputStreamInterface The input stream to tokenize.
     */
    private InputStreamInterface $inputStream;

    /**
     * @var string The current buffer being tokenized.
     */
    private string $currentBuffer;

    /**
     * Constructs a new Lexer instance.
     *
     * @param InputStreamInterface $inputStream The input stream to tokenize.
     */
    public function __construct(InputStreamInterface $inputStream)
    {
        $this->inputStream = $inputStream;
        $this->currentBuffer = $this->inputStream->read();
        $this->length = mb_strlen($this->currentBuffer);
        $this->cursorPosition = 0;
    }

    /**
     * Retrieves the current character being processed.
     *
     * @return string The current character.
     */
    public function current(): string
    {
        return mb_substr($this->currentBuffer, $this->cursorPosition, 1);
    }

    /**
     * Retrieves the next token from the input stream.
     *
     * @throws InvalidTokenException If an invalid token is encountered.
     * @return Token The next token.
     */
    public function getNextToken(): Token
    {
        do {
            $ch = $this->consume();
            if ($ch === null) {
                return new Token(TokenType::END_OF_TOKEN);
            }
        } while ($this->isSkipCharacter($ch));

        return match ($ch) {
            '[' => new Token(TokenType::SQUARE_BRACKET_OPEN),
            ']' => new Token(TokenType::SQUARE_BRACKET_CLOSE),
            '{' => new Token(TokenType::CURLY_BRACKET_OPEN),
            '}' => new Token(TokenType::CURLY_BRACKET_CLOSE),
            '(' => new Token(TokenType::ROUND_BRACKET_OPEN),
            ')' => new Token(TokenType::ROUND_BRACKET_CLOSE),
            '=' => new Token(TokenType::EQUAL),
            ',' => new Token(TokenType::COMMA),
            '#' => $this->getCommentToken(),
            '"' => $this->getStringToken(),
            '`' => $this->getEmbeddedSourceToken(),
            '+', '-', '.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' => $this->getNumberToken($ch),
            default => $this->getIdentifierOrLiteralToken($ch),
        };
    }

    /**
     * Consumes the next character from the input stream.
     *
     * @return string|null The consumed character, or null if the end of the stream is reached.
     */
    private function consume(): ?string
    {
        if ($this->length <= $this->cursorPosition) {
            if ($this->inputStream->isEndOfStream()) {
                return null;
            }
            $this->currentBuffer = $this->inputStream->read();
            $this->length = mb_strlen($this->currentBuffer);
            $this->cursorPosition = 0;
        }

        $ch = $this->current();
        $this->cursorPosition++;

        return $ch;
    }

    /**
     * Determines if the given character should be skipped during tokenization.
     *
     * @param string|null $ch The character to check.
     * @return bool True if the character should be skipped, false otherwise.
     */
    private function isSkipCharacter(?string $ch): bool
    {
        return $ch === ' ' || $ch === "\t" || $ch === "\n" || $ch === "\r";
    }

    /**
     * Retrieves the next string token from the input stream.
     *
     * @throws InvalidTokenException If an invalid token is encountered.
     * @return Token The string token.
     */
    private function getStringToken(): Token
    {
        $str = '';

        // Check if it's an nowdoc
        $next = mb_substr($this->currentBuffer, $this->cursorPosition, 2);
        $isNowDoc = ($next == '""');
        if($isNowDoc) {
            $this->cursorPosition += 2;
        }

        while (true) {
            $ch = $this->consume();

            if($isNowDoc) {
                $next = mb_substr($this->currentBuffer, $this->cursorPosition, 2);
                $isEndOfString = ($next == '""');
                if($isEndOfString) {
                    $this->cursorPosition += 3;
                }
            } else {
                $isEndOfString = ($ch === '"');
            }

            if ($ch === null) {
                break;
            } else if ($isEndOfString) {
                return $isNowDoc ?
                    new Token(TokenType::NOWDOC_STRING_VALUE, trim($str)) :
                    new Token(TokenType::STRING_VALUE, $str);
            } else if ($ch !== '\\' || $isNowDoc) {
                $str .= $ch;
                continue;
            }

            $str .= match ($ch = $this->consume()) {
                '"' => '"',
                '\\' => '\\',
                '/' => '/',
                'b' => chr(0x8),
                'f' => "\f",
                'n' => "\n",
                'r' => "\r",
                't' => "\t",
                default => '\\' . $ch,
            };
        }

        throw new InvalidTokenException('No end of string');
    }

    /**
     * Retrieves the next embedded source token from the input stream.
     *
     * @throws InvalidTokenException If an invalid token is encountered.
     * @return Token The embedded source token.
     */
    private function getEmbeddedSourceToken(): Token
    {
        $str = '';

        // Check if it's an nowdoc
        $next = mb_substr($this->currentBuffer, $this->cursorPosition, 2);
        $isEmbedded = ($next == '``');
        if(!$isEmbedded) {
            throw new InvalidTokenException('Invalid embedded token');
        }
        $this->cursorPosition += 2;

        $ch = $this->consume();

        // Skip language declaration
        while (trim($ch) != '') {
            $ch = $this->consume();
        }

        while (true) {
            $ch = $this->consume();

            $isEndOfString = false;

            if($ch == '`') {
                $next = mb_substr($this->currentBuffer, $this->cursorPosition, 2);
                $isEndOfString = ($next == '``');
                if($isEndOfString) {
                    $this->cursorPosition += 2;
                }
            }

            if ($ch === null) {
                break;
            } else if ($isEndOfString) {
                return new Token(TokenType::EMBEDDED_SOURCE_CODE, trim($str));
            } else {
                $str .= $ch;
            }
        }

        throw new InvalidTokenException('No end of string');
    }

    /**
     * Retrieves the next identifier or literal token from the input stream.
     *
     * @param string $str The starting character.
     * @return Token The identifier or literal token.
     */
    private function getIdentifierOrLiteralToken(string $str): Token
    {
        while (true) {
            $ch = $this->consume();

            if ($ch === null) {
                break;
            } else if(
                $ch == '_'
                || $ch == '-'
                || 'a' <= $ch && $ch <= 'z'
                || 'A' <= $ch && $ch <= 'Z'
                || '0' <= $ch && $ch <= '9'
            ) {
                $str .= $ch;
            } else {
                $this->cursorPosition--;
                break;
            }
        }

        return match ($str) {
            'true'  => new Token(TokenType::BOOLEAN_VALUE, true),
            'false' => new Token(TokenType::BOOLEAN_VALUE, false),
            'null'  => new Token(TokenType::NULL_VALUE),
            default => new Token(TokenType::IDENTIFIER, $str)
        };
    }

    /**
     * Retrieves the next comment token from the input stream.
     *
     * @return Token The comment token.
     */
    private function getCommentToken(): Token
    {
        $str = '';
        while (true) {
            $ch = $this->consume();
            if ($ch === null || $ch === PHP_EOL) {
                break;
            } else {
                $str .= $ch;
            }
        }
        return new Token(TokenType::COMMENT, trim($str));
    }

    /**
     * Retrieves the next number token from the input stream.
     *
     * @param string $ch The starting character.
     * @return Token The number token.
     */
    private function getNumberToken(string $ch): Token
    {
        $number = $ch;

        while (true) {
            $ch = $this->current();
            if (
                ('0' <= $ch && $ch <= '9')
                || $ch == '.'
                || $ch == '+'
                || $ch == '-'
                || $ch == 'e') {
                $number .= $ch;
                $this->consume();
                continue;
            }
            break;
        }

        return new Token(TokenType::NUMBER_VALUE, $number);
    }
}