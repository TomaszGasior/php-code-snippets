<?php

namespace App\Util;

use Generator;
use RuntimeException;

/**
 * Abstraction layer for handling asynchronously fetched plain text
 * (like in case of AI's API responses).
 *
 * Example usage:
 * ```
 * $generator = (function(): Generator {
 *     yield 'I ';
 *     yield 'like ';
 *     yield 'cakes.';
 * })();
 *
 * $streamedText = new StreamedText($generator);
 *
 * ...
 *
 * return new StreamedTextResponse($streamedText);
 * ```
 */
class StreamedText
{
    private string $text = '';

    /**
     * @param string[]|Generator $source Generator providing text chunks.
     */
    public function __construct(private Generator $source)
    {
    }

    /**
     * @return string[]|Generator
     */
    public function stream(): Generator
    {
        if (!$this->source->valid()) {
            throw new RuntimeException('There is no more text to stream.');
        }

        foreach ($this->source as $string) {
            if (!is_string($string)) {
                throw new RuntimeException('Invalid type returned by source generator, string expected.');
            }

            $this->text .= $string;

            yield $string;
        }
    }

    public function get(): string
    {
        if ($this->source->valid()) {
            foreach ($this->stream() as $s) {}
        }

        return $this->text;
    }
}
