<?php
/**
 * User: gxbalila
 * Date: 07/03/2019
 * Time: 2:17 PM
 */

Loader::library('gigya/datastore/BaseDS');

class GigyaInstitution extends BaseDS
{
    const SCHEMA = 'sr_institution';

    public function getValidatedInstitutions($keyword, $isPaginated = false, $options, $filter)
    {
        $formattedKeyword = $this->formatRegexKeyword($keyword);
        $query = "SELECT * FROM " . static::SCHEMA;

        // SB-1089 added by mabrigos - added filter for searching institutions
        switch ($filter) {
            case oid:
                $query .= " WHERE oid = \"" . $keyword ."\"";
                break;
            case name:
                $query .= " WHERE data.name = regex(\".*" . $formattedKeyword . ".*\") ";
                break;
            case formattedAddress:
            case addressCountry:
            case addressRegion:
                $query .= " WHERE data." . $filter . " CONTAINS \"" . $keyword ."\"";
                break;
            case edueltTeacherCode:
                $query .= " WHERE data." . $filter . " = \"" . $keyword ."\"";
                break;
            case systemID:
                $query .= " WHERE (data.systemID.idValue CONTAINS \"" . $keyword ."\" ";
                $query .= "OR data.systemID.idSystem CONTAINS \"" . $keyword ."\") ";
                break;
        }

        $query .= "AND isVerified = 'true' ";
        $query .= "ORDER BY data.name ";

        if (!$isPaginated) {
            return $this->query($query);
        }

        $start = 0;
        $page = (int)$options['page'];
        $limit = (int)$options['limit'];

        if ($page > 1) {
            $start = ($page * $limit) - $limit;
        }

        $query .= "LIMIT $limit START $start";

        return $this->query($query);
    }

    public function searchByKeyword($keyword)
    {
        // SB-160
        // SB-164 modified by machua 20190514 to cover the searching with substrings and apostrophes
        // SB-170
        // SB-171 added by machua 20190516 to escape all special characters

        $formattedKeyword = $this->formatRegexKeyword($keyword);

        $query = "SELECT oid, data.name, data.formattedAddress FROM sr_institution WHERE data.name = regex(\".*".$formattedKeyword.".*\") ORDER BY data.name";

        return $this->query($query);
    }

    public function getHomeSchoolData()
    {
        $query = "SELECT * FROM sr_institution WHERE name CONTAINS 'Home School'";
        return $this->query($query);
    }

    public function getUnvalidatedSchoolData()
    {
        $query = "SELECT * FROM sr_institution WHERE name CONTAINS 'Unvalidated'";
        return $this->queryGigya($query);
    }

    /**
     * Get all unverified institutions from Gigya
     *
     * @param $options Array
     * @param bool $isPaginated
     * @return array
     */
    public function getUnverifiedInstitutions($options, $isPaginated = false, $keyword, $filter)
    {
        $query = "SELECT oid, data.name, data.formattedAddress, data.edueltTeacherCode FROM ";
        $query .= static::SCHEMA . " WHERE isVerified = 'false'";

        if ($filter && $keyword) {
            // SB-1089 added by mabrigos - added filter for searching institutions
            switch ($filter) {
                case oid:
                    $query .= " AND oid = \"" . $keyword ."\"";
                    break;
                case name:
                    $formattedKeyword = $this->formatRegexKeyword($keyword);
                    $query .= " AND data.name = regex(\".*" . $formattedKeyword . ".*\") ";
                    break;
                case formattedAddress:
                case addressCountry:
                case addressRegion:
                    $query .= " AND data." . $filter . " CONTAINS \"" . $keyword ."\"";
                    break;
                case edueltTeacherCode:
                    $query .= " AND data." . $filter . " = \"" . $keyword ."\"";
                    break;
                case systemID:
                    $query .= " AND (data.systemID.idValue CONTAINS \"" . $keyword ."\" ";
                    $query .= "OR data.systemID.idSystem CONTAINS \"" . $keyword ."\") ";
                    break;
            }
        }

        $query .= "ORDER BY created DESC ";

        if (!$isPaginated) {
            return $this->query($query);
        }

        $start = 0;
        $page = (int)$options['page'];
        $limit = (int)$options['limit'];

        if ($page > 1) {
            $start = ($page * $limit) - $limit;
        }

        $query .= "LIMIT $limit START $start";
        $result = $this->query($query);
        return $result;
    }

    public function getByOID($oid)
    {
        $query = "SELECT * FROM sr_institution WHERE oid = '" . $oid . "'";
        return $this->query($query);
    }

    public function add($data)
    {
        return $this->save($data);
    }

    public function edit($data, $oid)
    {
        return $this->save($data, $oid);
    }

    /* SB-172
     * added by machua 20190517
     * to make the regex case insensitive as the case insensitive flag is not available in gigya
     */
    public function formatRegexKeyword($keyword)
    {
        $formattedKeyword = addcslashes($keyword, "`~!@#$%^&*()-_+=[{}]|\:;,<.>/?'");
        $formattedKeyword = strtolower($formattedKeyword);

        $keywordArray = str_split($formattedKeyword);
        $formattedArray = array();

        foreach($keywordArray as $char) {
            if (ctype_alpha($char)) {
                $formattedArray[] = '[';
                $formattedArray[] = $char;
                $formattedArray[] = strtoupper($char);
                $formattedArray[] = ']';
            } else {
                $formattedArray[] = $char;
            }
        }

        $formattedKeyword = implode('', $formattedArray);
        return $formattedKeyword;
    }
}
