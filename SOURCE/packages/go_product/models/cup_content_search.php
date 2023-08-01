<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentSearch extends Object 
{
    protected $db;
    protected $keyword = false;
    protected $searchSubject = false;
    protected $searchYearLevel = false;
    protected $searchRegion = false;
    // SB-342 added by jbernardez 20190918
    // added as this was not declared in the code but was being used
    protected $searchTitleSeries = '';
    protected $titleSearchCriteria = false;
    protected $seriesSearchCriteria = false;
    protected $queryString = '';
    protected $needLoadQuery = true;
    protected $objectResults = array();

    function __construct()
    {
        $this->db = Loader::db();
    }
    
    public function filterBySubject($subjectName, $region = null, $yearLevel = null)
    {
        $subjectName = trim($subjectName);

        if (strlen($subjectName) > 0) {
            $this->searchSubject = $subjectName;
            $this->searchRegion = $region;
            $this->searchYearLevel = $yearLevel;
            $this->needLoadQuery = true;
        }
    }
    
    public function filterByTitleSeries($subjectName, $region = null, $yearLevel = null)
    {
        $subjectName = trim($subjectName);

        if (strlen($subjectName) > 0) {
            $this->searchTitleSeries = $subjectName;
            $this->searchRegion = $region;
            $this->searchYearLevel = $yearLevel;
            $this->needLoadQuery = true;
        }
    }
    
    public function filterByKeyWord($keyword)
    {
        $this->keyword = $keyword;
        $this->needLoadQuery = true;
    }
    
    public function createQuery()
    {
        $newTitlesCount = 0;
        
        if ($this->searchSubject) {
            $qkeywords = $this->db->quote($this->searchSubject);
            $this->titleSearchCriteria[] = 'ti.id in (SELECT titleID FROM `CupContentTitleSubjects` WHERE subject LIKE ' . $qkeywords . ')';
            $this->seriesSearchCriteria[] = 'sr.id in (SELECT seriesID FROM `CupContentSeriesSubjects` WHERE subject LIKE "%' . $this->searchSubject . '%")';
        } elseif($this->searchTitleSeries) {
            $qkeywords = $this->db->quote($this->searchTitleSeries);
            $this->titleSearchCriteria[] = 'series LIKE '.$qkeywords;
        } elseif($this->keyword) {
            $keywordYear = str_replace(' ', '', str_replace('year', '', strtolower($this->keyword)));

            $this->titleSearchCriteria[] = '(name LIKE "%' . $this->keyword . '%" OR name IN(SELECT name FROM CupContentTitle cct 
                                                LEFT JOIN CupContentTitleAuthors ccta ON cct.ID=ccta.titleID WHERE author LIKE "%' . $this->keyword . '%") 
                                                OR ID IN (SELECT titleID FROM CupContentTitleSubjects WHERE subject LIKE "%' . $this->keyword . '%")
                                                OR regions LIKE "%' . $this->keyword . '%" OR ti.yearLevels LIKE "%[' . $keywordYear . ']%")';

            $this->seriesSearchCriteria[] = '(name LIKE "%' . $this->keyword . '%" OR name IN(SELECT series FROM CupContentTitle cct 
                                                LEFT JOIN CupContentTitleAuthors ccta ON cct.ID=ccta.titleID WHERE author LIKE "%' . $this->keyword . '%") 
                                                OR ID IN (SELECT seriesID FROM CupContentSeriesSubjects WHERE subject LIKE "%' . $this->keyword . '%")
                                                OR regions LIKE "%' . $this->keyword . '%" OR sr.yearLevels LIKE "%[' . $keywordYear . ']%")';
        }
        
        $this->seriesSearchCriteria[] = 'name IN(SELECT DISTINCT(series) FROM CupContentTitle WHERE isGoTitle=1)';
        
        $this->titleSearchCriteria[] = 'isEnabled = 1';
        $this->titleSearchCriteria[] = 'isGoTitle = 1';

        if ($this->searchSubject)  $this->titleSearchCriteria[] = 'series = \'\'';
        if ($this->searchRegion) $this->titleSearchCriteria[] = 'regions LIKE "%' . $this->searchRegion . '%"';
        if ($this->searchYearLevel) $this->titleSearchCriteria[] = 'yearLevels LIKE "%[' . $this->searchYearLevel . ']%"';
        
        $this->seriesSearchCriteria[] = 'isEnabled = 1';
        $this->seriesSearchCriteria[] = 'compGoUrl IS NOT NULL';

        // ANZUAT-93
        // if region == 'Australia' then include every region in the query
        // by definition, if all areas are ticked, then region is 'Australia'
        if ($this->searchRegion == 'Australia') {
            if ($this->searchRegion) $this->seriesSearchCriteria[] = "regions = '[Australian Capital Territory][New South Wales][Northern Territory][Queensland][South Australia][Tasmania][Victoria][Western Australia][Australia]' ";
        } else {
            if ($this->searchRegion) $this->seriesSearchCriteria[] = 'regions LIKE "%' . $this->searchRegion . '%"';
        }
        if ($this->searchYearLevel) $this->seriesSearchCriteria[] = 'yearLevels LIKE "%[' . $this->searchYearLevel . ']%"';

        $tmpTitleQuerySearchCriteria = implode(' AND ', $this->titleSearchCriteria);
        $tmpSeriesQuerySearchCriteria = implode(' AND ', $this->seriesSearchCriteria);

        // Modified by Ariel Tabag, 2016-04-06
        // For ticket ANZUAT-91
        $newTitlesCount = '(SELECT COUNT(*) FROM CupContentTitle WHERE series=sr.name AND isGoTitle=1 AND new_product_flag = 1)';
        $tmpTitleQuery = 'SELECT id,name,isbn13,\'CupContentTitle\' as class_name,search_priority,prettyURL,new_product_flag,yearLevels FROM `CupContentTitle` ti';
        $tmpSeriesQuery = 'SELECT id,name,seriesID as isbn13,\'CupContentSeries\' as class_name,search_priority,prettyURL,' . $newTitlesCount . ' as new_product_flag,yearLevels FROM `CupContentSeries` sr';

        if (strlen($tmpTitleQuerySearchCriteria) > 0) {
            $tmpTitleQuery .= " WHERE {$tmpTitleQuerySearchCriteria}";
            $tmpSeriesQuery .= " WHERE {$tmpSeriesQuerySearchCriteria}";
        }
        
        if ($this->searchSubject || $this->keyword) {
            $this->queryString = "SELECT * FROM ({$tmpTitleQuery} UNION ALL {$tmpSeriesQuery}) as result_table";
        } 

        if ($this->searchTitleSeries) {
            $this->queryString = "SELECT * FROM ({$tmpTitleQuery}) as result_table";
        }

        if ($this->searchTitleSeries) {
            // Modified by Paul Balila, 2016-04-06
            // For ticket ANZUAT-83
            // default: $this->queryString .= " ORDER BY ID,name";
            $this->queryString .= " ORDER BY search_priority DESC, name ASC;";
        } else {
            // Modified by Paul Balila 2016-02-26
            // For ticket ANZGO-2270
            // Rule of ordering:
            // 1. Alphabetical
            // else $this->queryString .= " ORDER BY class_name ASC";

            // Modified by Paul Balila 2016-04-04
            // For ticket ANZUAT-83
            // Rule of ordering:
            // 1. Search priority
            // 2. Alphabetical
            $this->queryString .= " ORDER BY search_priority DESC, name ASC;";
        }
        
        return $this->queryString;
    }
    
    protected function prepareResults()
    {
        if ($this->needLoadQuery) {
            $this->createQuery();
            $this->objectResults = $this->db->GetAll($this->queryString);
        }
    }
    
    public function getResults()
    {
        $this->prepareResults();
        return $this->objectResults;
    }
}