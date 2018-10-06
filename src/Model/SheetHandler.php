<?php

namespace Vinorcola\ImportBundle\Model;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Handle a sheet of data with must be layered af a table with header line.
 *
 * The header line is expected to be the first line of the sheet.
 */
class SheetHandler
{
    /**
     * @var Worksheet
     */
    private $sheet;

    /**
     * @var string
     */
    private $highestColumns;

    /**
     * @var int
     */
    private $highestRows;

    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * @var int
     */
    private $currentRow = 1;

    /**
     * SheetReader constructor.
     *
     * @param Worksheet $sheet
     */
    public function __construct(Worksheet $sheet)
    {
        $this->sheet = $sheet;
        $this->highestColumns = $this->sheet->getHighestDataColumn();
        $this->highestRows = $this->sheet->getHighestDataRow();

        // Load headers.
        for ($column = 'A'; $this->isColumnBeforeOrEquals($column, $this->highestColumns); ++$column) {
            $this->headers[$column] = $this->sheet->getCell($column . '1')->getValue();
        }
    }

    /**
     * @return Worksheet
     */
    public function getSheet(): Worksheet
    {
        return $this->sheet;
    }

    /**
     * Get the total number of lines in the file.
     *
     * @return int
     */
    public function getNbLines(): int
    {
        return $this->highestRows - 1;
    }

    /**
     * Get the number of remaining lines to read.
     *
     * @return int
     */
    public function getNbRemainingLines(): int
    {
        return $this->getNbLines() - $this->currentRow;
    }

    /**
     * Get the index of the current line in the file.
     *
     * @return int
     */
    public function getCurrentLineIndex(): int
    {
        return $this->currentRow - 1;
    }

    /**
     * Jump to the given line.
     *
     * Line 0 being the header line and line 1 being the first data line.
     *
     * @param int $line
     */
    public function jumpToLine(int $line): void
    {
        $this->currentRow = max($line + 1, 1);
    }

    /**
     * Get the current line of the data.
     *
     * @return array|null
     */
    public function getCurrentLine(): ?array
    {
        if ($this->currentRow === 1) {
            return $this->headers;
        }
        if ($this->currentRow > $this->highestRows) {
            return null;
        }

        $values = [];
        for ($column = 'A'; $this->isColumnBeforeOrEquals($column, $this->highestColumns); ++$column) {
            $values[$this->headers[$column]] = $this->sheet->getCell($column . $this->currentRow)->getValue();
        }

        return $values;
    }

    /**
     * Get next line of the data.
     *
     * @return array|null
     */
    public function getNextLine(): ?array
    {
        ++$this->currentRow;

        return $this->getCurrentLine();
    }

    /**
     * Write some content into the file.
     *
     * Content must be a keyed array. Keys will be used against the headers of the file to set the content in the
     * corresponding column. If a key does not match any existing header, then a new column is created at the end of the
     * data with the new header.
     *
     * @param array $content
     */
    public function write($content): void
    {
        foreach ($content as $header => $value) {
            if (!key_exists($header, $this->headers)){
                $this->writeHeader($header);
            }
            $this->sheet->getCell(array_search($header, $this->headers) . $this->currentRow)->setValue($value);
        }
    }

    /**
     * Write an unknown header.
     *
     * @param string $header
     */
    private function writeHeader(string $header): void
    {
        ++$this->highestColumns;
        $this->headers[$this->highestColumns] = $header;
        $this->sheet->getCell($this->highestColumns . '1')->setValue($header);
    }

    /**
     * Indicate if a column is before or equals the limit column.
     *
     * @param string $column
     * @param string $limitColumn
     * @return bool
     */
    private function isColumnBeforeOrEquals(string $column, string $limitColumn): bool
    {
        if (strlen($column) < strlen($limitColumn)) {
            return true;
        }
        if (strlen($column) === strlen($limitColumn) && $column <= $limitColumn) {
            return true;
        }

        return false;
    }
}
