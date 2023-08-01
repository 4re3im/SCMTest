<?php

/**
 * ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
 * Spreadsheet Helper
 */

class SpreadsheetHelper
{
    private $file;
    private $spreadsheet;

    public function __construct($fileID = null, $skipHeader = 2)
    {
        Loader::library('SpreadsheetReader/php-excel-reader/excel_reader2', 'global_go_provisioning');
        Loader::library('SpreadsheetReader/SpreadsheetReader', 'global_go_provisioning');

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
            // SB-626 added by mabrigos 20200708
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