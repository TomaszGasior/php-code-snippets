<?php

namespace App\Controller;

use App\Util\HtmlTitleExtractor;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Provides useful renderPdf() method for your controller using KnpSnappyBundle.
 */
trait PdfControllerTrait
{
    private $pdfRenderer;
    private $titleExtractor;

    /**
     * @required
     */
    public function setPdfRenderer(Pdf $pdfRenderer)
    {
        $this->pdfRenderer = $pdfRenderer;
    }

    /**
     * @required
     */
    public function setTitleExtractor(HtmlTitleExtractor $titleExtractor)
    {
        $this->titleExtractor = $titleExtractor;
    }

    /**
     * Render a PDF document from Twig template.
     */
    protected function renderPdf(string $view, array $parameters = [],
                                 Response $response = null): Response
    {
        if (!$this->pdfRenderer) {
            throw new \LogicException;
        }

        $content = $this->renderView($view, $parameters);

        $title = '';
        if ($this->titleExtractor && $title = $this->titleExtractor->extractFromHtml($content)) {
            $title = str_replace(['/', '\\'], '-', $title);
        }

        if (null === $response) {
            $response = new Response;
        }

        $response->setContent($this->pdfRenderer->getOutputFromHtml($content));

        $headers = $response->headers;

        $headers->set('Content-Type', 'application/pdf');
        $headers->set('Content-Disposition', $headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.pdf', $title ? $title : date('Y-m-d')),
            sprintf('%s.pdf', date('Y-m-d')),
        ));

        return $response;
    }
}
