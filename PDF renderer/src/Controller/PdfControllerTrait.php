<?php

namespace App\Controller;

use App\Service\PdfExportService;
use Symfony\Component\HttpFoundation\Response;

trait PdfControllerTrait
{
    private $pdfExportService;

    /**
     * @required
     */
    public function setPdfExportService(PdfExportService $pdfExportService)
    {
        $this->pdfExportService = $pdfExportService;
    }

    protected function renderPdf(string $view, array $parameters = []): Response
    {
        if (!$this->pdfExportService) {
            throw new \RuntimeException;
        }

        return $this->pdfExportService->render($view, $parameters);
    }
}
