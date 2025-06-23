<?php

namespace App\Util;

/**
 * Extracts title from HTML code.
 */
class HtmlTitleExtractor
{
    /**
     * Extracts contents of <title> tag from given HTML code. Removes extra
     * whitespace characters from returned string. May return null for invalid
     * HTML code or no <title> tag.
     */
    public function extractFromHtml(string $code): ?string
    {
        try {
            $document = new \DOMDocument;
            $document->loadHTML($code, \LIBXML_NOERROR);

            $elements = $document->getElementsByTagName('title');

            foreach ($elements as $element) {
                $title = preg_replace('/\s+/', ' ', trim($element->textContent));

                return ($title ? $title : null);
            }

            return null;
        }
        catch (\Throwable $e) {
            return null;
        }
    }
}
