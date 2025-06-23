<?php

namespace App\Service;

use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig\Environment;

/**
 * Renders PDF document from Twig template using KnpSnappyBundle.
 */
class PdfExportService
{
    private $pdfRenderer;
    private $twig;

    public function __construct(Pdf $pdfRenderer, Environment $twig)
    {
        $this->pdfRenderer = $pdfRenderer;
        $this->twig = $twig;
    }

    public function render(string $view, array $parameters = []): Response
    {
        $content = $this->twig->render($view, $parameters);
        $title = $this->extractTitleFromHtml($content);

        return $this->getResponse($content, $title);
    }

    private function getResponse(string $content, ?string $title): Response
    {
        $response = new StreamedResponse(function() use ($content) {
            echo $this->pdfRenderer->getOutputFromHtml($content);
        });

        $response->headers->set(
            'Content-Type',
            'application/pdf'
        );
        $response->headers->set(
            'Content-Disposition',
            HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                sprintf('%s.pdf', $title ? $title : date('Y-m-d_H-i')),
                sprintf('%s.pdf', date('Y-m-d_H-i'))
            )
        );

        return $response;
    }

    /**
     * Extracts contents of <title> tag from given HTML code.
     * Removes extra whitespace characters and slashes from returned string.
     * May return null for invalid HTML code or no <title> tag.
     */
    private function extractTitleFromHtml(string $content): ?string
    {
        try {
            $document = new \DOMDocument;
            $document->loadHTML($content, \LIBXML_NOERROR);
            $elements = $document->getElementsByTagName('title');

            if (0 === count($elements)) {
                return null;
            }

            foreach ($elements as $element) {
                $title = preg_replace('/\s+/', ' ', trim($element->textContent));
                $title = str_replace(['/', '\\'], '-', $title);

                return ($title ? $title : null);
            }
        }
        catch (\Throwable $e) {}

        return null;
    }
}
