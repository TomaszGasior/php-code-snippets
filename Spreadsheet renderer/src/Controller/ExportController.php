<?php

namespace App\Controller;

use App\Entity\Product;
use App\Export\SpreadsheetExportRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

class ExportController extends AbstractController
{
    #[Route('/export/{_format}', requirements: ['_format' => 'csv|ods|xls|xlsx'])]
    public function export(
        SpreadsheetExportRenderer $renderer,
        string $_format,
        EntityManagerInterface $entityManager
    ): Response {
        $products = $entityManager->getRepository(Product::class)->findAll();

        $response = new StreamedResponse(
            function () use ($renderer, $products, $_format) {
                $renderer->render($_format, $products);
            }
        );

        $response->headers->set(
            'Content-Disposition',
            HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                sprintf('%s.%s', date('Y-m-d_H-i'), $_format)
            )
        );

        return $response;
    }
}
