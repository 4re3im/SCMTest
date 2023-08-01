<?php

class InstituteFormHelper
{
    private $isVerified = false;

    public function sanitizeData($data)
    {
        foreach ($data as $index => $value) {
            if ($index === 'data') {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $vKey => $val) {
                    $value[$vKey] = trim($val);
                }
            } else {
                $data[$index] = trim($value);
            }
        }
        return $data;
    }

    public function setIsVerified($flag)
    {
        $this->isVerified = $flag;
    }

    public function formatForGigya($data)
    {
        $filteredData = array_filter($data);

        $filteredData['formattedAddress'] = implode("\r", array(
            $filteredData['name'],
            $filteredData['addressLine1'],
            $filteredData['addressLine2'],
            $filteredData['addressCity'] . ' ' . $filteredData['addressRegion']. ' ' . $filteredData['addressRegionCode'],
            $filteredData['addressCountry']
        ));

        $filteredData['formattedAddress'] = $filteredData['formattedAddress'] . "\r\r\r\r\r\r";

        unset($filteredData['addressLine1'], $filteredData['addressLine2']);

        $filteredData['isVerified'] = (int)$filteredData['isVerified'] === 1;

        return $filteredData;
    }

    public function generateJSON($data) {
        $filteredData = $this->formatForGigya($data);
        return json_encode($filteredData);
    }
}