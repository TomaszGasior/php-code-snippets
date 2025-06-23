<?php

namespace App\Util;

use DateTimeInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

/**
 * XML-based spreadsheet with small memory footprint, with no buffering required.
 *
 * This is a simple implementation of "SpreadsheetML", Microsoft Excel 2002/2003 XML format.
 * Generated file should be saved with ".xml" extension and opened explicitly in LibreOffice Calc
 * or Microsoft Excel (don't just doubleclick the file, use "Open with" menu).
 *
 * Using XML spreadsheet instead of CSV makes it possble to avoid Microsoft Excel
 * specific issues like inconsistent handling of text encoding or cells delimiters.
 *
 * Example usage:
 * ```
 * $spreadsheet = new XmlSpreadsheet('Sheet name');
 *
 * $spreadsheet->addRow(
 *     ['Text', 'Date', 'Number', 'Boolean']
 * );
 * $spreadsheet->addRow(
 *     ['Anna', new \DateTime('1995-03-18 19:30'), 30, true],
 *     [XmlSpreadsheet::TYPE_STRING, XmlSpreadsheet::TYPE_DATETIME, XmlSpreadsheet::TYPE_NUMBER, XmlSpreadsheet::TYPE_BOOLEAN]
 * );
 * $spreadsheet->addRow(
 *     ['John', new \DateTime('1980-12-30'), 45, false],
 *     [XmlSpreadsheet::TYPE_STRING, XmlSpreadsheet::TYPE_DATETIME, XmlSpreadsheet::TYPE_NUMBER, XmlSpreadsheet::TYPE_BOOLEAN]
 * );
 *
 * $output = fopen('filename.xml', 'w');
 * $spreadsheet->save($output);
 * ```
 *
 * Format specification:
 * https://learn.microsoft.com/en-us/previous-versions/office/developer/office-xp/aa140066(v=office.10)
 */
#[Exclude]
class XmlSpreadsheet
{
    public const TYPE_STRING = 'String';
    public const TYPE_NUMBER = 'Number';
    public const TYPE_BOOLEAN = 'Boolean';
    public const TYPE_DATETIME = 'DateTime';

    /**
     * @var resource|null
     */
    private $output;

    /**
     * @param int[] $columnWidths
     */
    public function __construct(string $worksheetName, array $columnWidths = [])
    {
        $this->output = tmpfile();

        $worksheetName = $this->escapeString($worksheetName);

        $this->write(
            <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
            <Styles><Style ss:ID="s99"><NumberFormat ss:Format="yyyy\-mm\-dd\ hh:mm;@"/></Style></Styles>
            <Worksheet ss:Name="{$worksheetName}">
            <Table>
            XML
        );

        foreach ($columnWidths as $columnWidth) {
            $this->write('<Column ss:Width="' . (int) $columnWidth . '"/>');
        }
    }

    /**
     * @param string|bool|DateTimeInterface[] $cells
     * @param string[] $types Data types for each cell as TYPE_* constants. Make sure keys
     *                        in $types array match keys in $cells array. String is default.
     */
    public function addRow(array $cells, array $types = []): void
    {
        $this->write('<Row>');

        foreach ($cells as $i => $value) {
            $type = $types[$i] ?? self::TYPE_STRING;
            $value = $this->formatCellValue($value, $type);

            $this->write($type === self::TYPE_DATETIME ? '<Cell ss:StyleID="s99">' : '<Cell>');
            $this->write('<Data ss:Type="' . $type . '">' . $value . '</Data></Cell>');
        }

        $this->write('</Row>');
    }

    /**
     * @param resource $output
     */
    public function save($output): void
    {
        if (!is_resource($output)) {
            throw new InvalidArgumentException();
        }

        $this->write('</Table></Worksheet></Workbook>');

        rewind($this->output);
        stream_copy_to_stream($this->output, $output);

        fclose($this->output);
        $this->output = null;
    }

    private function write(string $string): void
    {
        if (null === $this->output) {
            throw new RuntimeException('The XML spreadsheet document has been finished.');
        }

        fwrite($this->output, $string);
    }

    private function formatCellValue(mixed $value, string $type): string
    {
        if ($type === self::TYPE_STRING || $type === self::TYPE_NUMBER) {
            return $this->escapeString((string) $value);
        }

        if ($type === self::TYPE_DATETIME) {
            if (!($value instanceof DateTimeInterface)) {
                throw new InvalidArgumentException('DateTimeInterface object expected for DATETIME cell type.');
            }

            return $value->format('Y-m-d\TH:i:s.v');
        }

        if ($type === self::TYPE_BOOLEAN) {
            return $value ? '1' : '0';
        }

        throw new InvalidArgumentException('Invalid cell data type.');
    }

    private function escapeString(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
