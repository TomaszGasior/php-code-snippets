<?php

namespace App\Export;

use App\Entity\Product;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Renders XLS/XLSX/ODS/CSV spreadsheet using `phpoffice/phpspreadsheet`.
 */
class SpreadsheetExportRenderer
{
    public function __construct(private TranslatorInterface $translator) {}

    /**
     * @param Product[] $products
     */
    public function render(string $format, array $products): void
    {
        $spreadsheet = $this->getSpreadsheet($products);

        $writer = IOFactory::createWriter($spreadsheet, ucfirst(strtolower($format)));
        $writer->save('php://output');
    }

    /**
     * @param Product[] $products
     */
    private function getSpreadsheet(array $products): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->fromArray($this->getHeadings());
        $worksheet->fromArray($this->getRows($products), null, 'A2');

        $worksheet->setAutoFilter($worksheet->calculateWorksheetDimension());
        $worksheet->getStyle('1')->getFont()->setBold(true);
        $worksheet->freezePane('A2');

        foreach (array_values(ExportColumn::cases()) as $i => $column) {
            $coordinate = Coordinate::stringFromColumnIndex($i + 1);

            $worksheet->getColumnDimension($coordinate)->setAutoSize(true);

            $worksheet->getStyle($coordinate)->getNumberFormat()->setFormatCode(
                $this->getColumnFormatting($column)
            );
        }

        return $spreadsheet;
    }

    private function getHeadings(): array
    {
        $headings = [];

        foreach (ExportColumn::cases() as $column) {
            $headings[] = match ($column) {
                default => $this->translate('heading.' . $column->value),
            };
        }

        return $headings;
    }

    /**
     * @param Product[] $products
     */
    private function getRows(array $products): array
    {
        $data = [];

        foreach ($products as $product) {
            $row = [];

            foreach (ExportColumn::cases() as $column) {
                $row[] = match ($column) {
                    ExportColumn::ID => $product->getId(),
                    ExportColumn::NAME => $product->getName(),
                    ExportColumn::PRICE => $product->getPrice(),
                    default => $product->{'get' . $column->value}(),
                };
            }

            $data[] = $row;
        }

        return $data;
    }

    private function getColumnFormatting(ExportColumn $column): string
    {
        return match ($column) {
            ExportColumn::PRICE => '0.00',
            default => '@',
        };
    }

    private function translate(string $id): string
    {
        return $this->translator->trans($id, [], 'export');
    }
}
