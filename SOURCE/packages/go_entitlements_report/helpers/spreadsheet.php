<?php

/**
 * Spreadsheet Helper
 */

class SpreadsheetHelper
{
    private $file;
    private $spreadsheet;

    public function __construct($fileID = null, $skipHeader = 2)
    {
        Loader::library('SpreadsheetReader/php-excel-reader/excel_reader2', 'go_entitlements_report');
        Loader::library('SpreadsheetReader/SpreadsheetReader', 'go_entitlements_report');

        if (!$fileID) {
            return false;
        }

        $this->file = File::getByID($fileID);

        $spreadsheet = new SpreadsheetReader($this->file->getPath());
        $this->spreadsheet = new LimitIterator($spreadsheet, $skipHeader);
    }

    public function getRows($offset, $limit)
    {
        if (!$this->spreadsheet) {
            return [];
        }

        try {
            if (!$limit) {
                return $this->removeEmptyRow(iterator_to_array($this->spreadsheet, true));
            }
            $iterator = new LimitIterator($this->spreadsheet, $offset, $limit);
            return $this->removeEmptyRow(iterator_to_array($iterator, true));
        } catch (Exception $e) {
            return [];
        }
    }

    public function removeEmptyRow($records)
    {
        return array_filter($records, function ($record) {
            $isValidRow = false;
            foreach ($record as $column) {
                if ($column) {
                    $isValidRow = true;
                    break;
                }
            }

            return $isValidRow;
        });
    }

    public function getRowsCount()
    {
        return count($this->getRows(0, -1));
    }

    public function getSpreadsheetFileID()
    {
        if (!$this->file) {
            return 0;
        }

        return $this->file->getFileID();
    }

    public function getSpreadsheetPath()
    {
        if (!$this->file) {
            return '';
        }

        return $this->file->getPath();
    }
}