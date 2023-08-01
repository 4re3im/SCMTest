<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('title/model', 'cup_content');
Loader::model('series/model', 'cup_content');

class CupContentSearch extends Object {

    protected $db;
    protected $search_subject = false;
    protected $search_year = false;
    protected $search_author = false;
    protected $search_region = false;
    protected $search_department = false;
    protected $search_format = false;
    protected $search_isbn = false;
    protected $search_keyword = false;
    protected $sort_criteria = array();
    protected $title_search_criteria = false;
    protected $series_search_criteria = false;
    protected $query_string = "";
    protected $query_count_string = "";
    protected $need_load_query = true;
    protected $page_size = 10;
    protected $page_number = 1;
    protected $total_count = 0;
    protected $object_results = array();
    protected $searhType = "all"; //"series", "title"

    function __construct() {
        $this->db = Loader::db();
    }

    public function setPageSize($size) {
        if (intVal($size) > 0) {
            $this->page_size = $size;
            $this->need_load_query = true;
        }
    }

    public function setPageNumber($number) {
        if (intVal($size) >= 0) {
            $this->page_number = $number;
            $this->need_load_query = true;
        }
    }

    public function filterBySubject($subject_name) {
        $subject_name = trim($subject_name);
        if (strlen($subject_name) > 0) {
            $this->search_subject = $subject_name;
            $this->need_load_query = true;
        }
    }

    public function filterByISBN($search_isbn) {
        $search_isbn = trim($search_isbn);
        if (strlen($search_isbn) > 0) {
            $this->search_isbn = $search_isbn;
            $this->need_load_query = true;
        }
    }

    public function filterByYear($years) {
        $years = trim($years);
        if (strlen($years) > 0) {
            $this->search_year = $years;
            $this->need_load_query = true;
        }
    }

    public function filterByRegion($region) {
        $region = trim($region);
        if (strlen($region) > 0) {
            $this->search_region[] = $region;
            $this->need_load_query = true;
        }
    }

    public function filterByAuthor($author_name) {
        $author_name = trim($author_name);
        if (strlen($author_name) > 0) {
            $this->search_author = $author_name;
            $this->need_load_query = true;
        }
    }

    public function filterByDepartment($department) {
        $this->search_department = $department;
        $this->need_load_query = true;
    }

    public function filterByFormat($format) {
        $this->search_format = $format;
        $this->need_load_query = true;
    }

    public function filterByKeywords($keywords) {
        $this->search_keyword = $keywords;
        $this->need_load_query = true;
    }

    public function setSortBy($field_name, $order = 'asc') {
        $this->sort_criteria[$field_name] = $order;
        $this->need_load_query = true;
    }

    public function createQuery() {
        if ($this->search_subject) {
            $qkeywords = $this->db->quote($this->search_subject);

            // Nick Ingarsia, 29/5/14
            // Changed query to use natural join instead. Improves speed.
            $this->title_search_criteria[] = 'ts.subject = ' . $qkeywords;
            // Nick Ingarsia, 2/6/14
            // Changed query to use natural join instead. Improves speed.
            $this->series_search_criteria[] = 'ss.subject = ' . $qkeywords;
        }

        if ($this->search_author) {
            $qkeywords = $this->db->quote('%' . $this->search_author . '%');
            // Nick Ingarsia, 29/5/14
            // Changed query to use natural join instead. Improves speed.
            $this->title_search_criteria[] = 'ta.author like ' . $qkeywords;

            $seriesNames = array();

            // Nick Ingarsia, 29/5/14
            // Changed query to use natural join instead. Improves speed.
            $tq = "select distinct(series) as seriesName
					FROM CupContentTitle sct, CupContentTitleAuthors scta
					WHERE sct.id  = scta.titleID
					AND sct.type = 'part of series'
					AND scta.author like ?";

            $seriesResult = $this->db->getAll($tq, '%' . $this->search_author . '%');
            foreach ($seriesResult as $row) {
                $seriesNames[] = $this->db->quote($row['seriesName']);
            }
            if (count($seriesNames) > 0) {
                $seriesNames = implode(', ', $seriesNames);
                $this->series_search_criteria[] = 'sr.name in (' . $seriesNames . ')';
            }
        }

        if ($this->search_department) {
            $qkeywords = $this->db->quote('%[' . $this->search_department . ']%');
            $this->title_search_criteria[] = 'ti.divisions like ' . $qkeywords;
            $this->series_search_criteria[] = 'sr.divisions like ' . $qkeywords;
        }

        if ($this->search_year) {
            $qkeywords = $this->db->quote('%[' . $this->search_year . ']%');
            $this->title_search_criteria[] = 'ti.yearLevels like ' . $qkeywords;
            $this->series_search_criteria[] = 'sr.yearLevels like ' . $qkeywords;
        }

        if ($this->search_format) {
            if (strcmp($this->search_format, '[Digital Resource]') == 0) {
                $q = "Select name FROM CupContentFormat WHERE isDigital = 1";
                $res = $this->db->getAll($q);
                $format_names = array();
                foreach ($res as $each) {
                    $format_names[] = $this->db->quote($each['name']);
                }
                $format_names[] = $this->db->quote("Place Holder No Match");
                $format_names = implode(", ", $format_names);

                // Nick Ingarsia, 29/5/14
                // Changed query to use natural join instead. Improves speed.
                $this->title_search_criteria[] = 'tf.format in (' . $format_names . ')';
                $this->title_search_criteria[] = 'ti.id in (Select titleID FROM `CupContentTitleFormats` WHERE format IN (' . $format_names . '))';
                $this->searhType = "title";
            } else {
                $qkeywords = $this->db->quote($this->search_format);
                // Nick Ingarsia, 29/5/14
                // Changed query to use natural join instead. Improves speed.
                $this->title_search_criteria[] = 'tf.format = ' . $qkeywords;
                $this->series_search_criteria[] = 'sr.id in (Select seriesID FROM `CupContentSeriesFormats` WHERE format like ' . $qkeywords . ')';
            }
        }

        if ($this->search_region) {
            if (is_array($this->search_region)) {
                foreach ($this->search_region as $region) {
                    $qkeywords = $this->db->quote('%[' . $region . ']%');
                    $this->title_search_criteria[] = 'ti.regions like ' . $qkeywords;
                    $this->series_search_criteria[] = 'sr.regions like ' . $qkeywords;
                }
            }
        }

        if ($this->search_isbn) {
            $qkeywords = $this->db->quote('%' . $this->search_isbn . '%');
            $this->title_search_criteria[] = 'ti.isbn13 like ' . $qkeywords;
            $this->searhType = "title";
        }

        if ($this->search_keyword) {
            if (strlen(trim($this->search_keyword)) > 0) {
                $this->search_keyword = trim($this->search_keyword);
                $tq = array();
                $sq = array();
                $stq = array();
                foreach (explode(" ", $this->search_keyword) as $word) {
                    $qWord = $this->db->quote('%' . $word . '%');
                    $tq[] = 'ti.displayName like ' . $qWord;
                    $sq[] = 'sr.name like ' . $qWord;
                    $stq[] = 'displayName like ' . $qWord;
                }
                $this->title_search_criteria[] = '(' . implode(' AND ', $tq) . ')';
                //$this->series_search_criteria[] = '('.implode(' AND ', $sq).')';


                $tmp_series_criteria = array();
                $tmp_series_criteria[] = '(' . implode(' AND ', $sq) . ')';

                $tmp_query = "SELECT DISTINCT(series) FROM CupContentTitle WHERE (" . implode(' AND ', $stq) . ")";
                $res = $this->db->getAll($tmp_query);
                $sr_names = array();
                foreach ($res as $each) {
                    $word = $each['series'];
                    $qWord = $this->db->quote($word);
                    $sr_names[] = 'sr.name like ' . $qWord;
                }

                /*
                  $qkeywords = $this->db->quote('%' . $this->search_keyword . '%');
                  $this->title_search_criteria[] = 'ti.displayName like '.$qkeywords;
                  $this->series_search_criteria[] = 'sr.name like '.$qkeywords;
                 */


                $qkeywords = $this->db->quote('%' . $this->search_keyword . '%');

                // Nick Ingarsia, 29/5/14
                // Changed query to use natural join instead. Improves speed.

                $tmp_query = "SELECT id
								FROM CupContentTitle t, CupContentTitleAuthors a
								WHERE type = 'stand alone'
								AND t.id = a.titleID
								AND a.author like " . $qkeywords;

                $ids = array();
                $res = $this->db->getAll($tmp_query);
                foreach ($res as $row) {
                    $ids[] = $row['id'];
                }

                if (count($ids) > 0) {
                    $this->title_search_criteria[] = 'ti.id IN (' . implode(', ', $ids) . ')';
                }

                // Nick Ingarsia, 29/5/14
                // Changed query to use natural join instead. Improves speed.

                $tmp_query = "SELECT series
								FROM CupContentTitle t, CupContentTitleAuthors a
								WHERE type = 'part of series'
								and t.id = a.titleID
								and a.author like  " . $qkeywords;

                $res = $this->db->getAll($tmp_query);
                foreach ($res as $row) {
                    $qWord = $this->db->quote($row['series']);
                    $sr_names[] = 'sr.name like ' . $qWord;
                }

                if (count($sr_names) > 0) {
                    $tmp_series_criteria[] = '(' . implode(' OR ', $sr_names) . ')';
                }

                if (count($tmp_series_criteria) > 0) {
                    $this->series_search_criteria[] = '(' . implode(' OR ', $tmp_series_criteria) . ')';
                }
            }
        }

        $this->title_search_criteria[] = 'isEnabled = 1';
        if (strcmp($this->searhType, 'all') == 0) {
            $this->title_search_criteria[] = "ti.type like 'stand alone'";
        }
        $this->series_search_criteria[] = 'isEnabled = 1';


        $tmp_title_query_search_criteria = implode(' AND ', $this->title_search_criteria);
        $tmp_series_query_search_criteria = implode(' AND ', $this->series_search_criteria);



        $tmp_title_query = 'SELECT id, name, \'CupContentTitle\' as class_name, search_priority FROM `CupContentTitle` ti';
        $tmp_series_query = 'SELECT id, name, \'CupContentSeries\' as class_name, search_priority FROM `CupContentSeries` sr';

        // Nick Ingarsia, 29/5/14
        // Added natural join to subject, authors and formats table by default.
        // These are much faster than doing "select from where XX in (select XX) statements"
        // These were really slowing down the search and catalogue browsing

        if ($this->search_subject) {
            $tmp_title_query .= ', CupContentTitleSubjects ts';
            $tmp_series_query .= ', CupContentSeriesSubjects ss';
        }
        if ($this->search_author) {
            $tmp_title_query .= ', CupContentTitleAuthors ta';
        }
        if ($this->search_format) {
            $tmp_title_query .= ', CupContentTitleFormats tf';
        }


        if (strlen($tmp_title_query_search_criteria) > 0) {

            $tmp_title_query .= " WHERE 1 = 1";
            $tmp_series_query .= " WHERE 1 = 1";

            if ($this->search_subject) {
                $tmp_title_query .= " AND ti.id = ts.titleID";
                $tmp_series_query .= " AND sr.id = ss.seriesID";
            }

            if ($this->search_author) {
                $tmp_title_query .= " AND ti.id = ta.titleID";
            }

            if ($this->search_format) {
                $tmp_title_query .= " AND ti.id = tf.titleID";
            }

            $tmp_title_query .= " AND {$tmp_title_query_search_criteria}";

            $tmp_series_query .= " AND {$tmp_series_query_search_criteria}";
        }

        $sort_query = array();
        if ($this->sort_criteria) {
            foreach ($this->sort_criteria as $field => $order) {
                $sort_query[] = "{$field} {$order}";
            }
        }
        $sort_query = implode(", ", $sort_query);


        $this->query_string = "SELECT * FROM ({$tmp_title_query} UNION ALL {$tmp_series_query}) as result_table";
        $this->query_count_string = "SELECT count(id) as total FROM ({$tmp_title_query} UNION ALL {$tmp_series_query}) as result_table";
        if (strcmp($this->searhType, 'title') == 0) {
            $this->query_string = "SELECT * FROM ({$tmp_title_query}) as result_table";
            $this->query_count_string = "SELECT count(id) as total FROM ({$tmp_title_query}) as result_table";
        } elseif (strcmp($this->searhType, 'series') == 0) {
            $this->query_string = "SELECT * FROM ({$tmp_series_query}) as result_table";
            $this->query_count_string = "SELECT count(id) as total FROM ({$tmp_series_query}) as result_table";
        }

        if (strlen($sort_query) > 0) {
            $this->query_string .= " ORDER BY {$sort_query}";
        } else {
            //$this->query_string .= " ORDER BY class_name asc";
            $this->query_string .= " ORDER BY search_priority desc, class_name asc";
        }

        $limit_from = ($this->page_number - 1) * $this->page_size;
        $limit_size = $this->page_size;

        $limit_string = " LIMIT {$limit_from}, {$limit_size}";

        $this->query_string .= $limit_string;

        return $this->query_string;
    }

    protected function prepareResults() {
        if ($this->need_load_query) {
            $this->createQuery();

            // Nick Ingarsia, 29/5/14
            // Just a debug flag to turn of caching, for testing purposes.
            // For live production, it should always be set to 'true'.
            // $useCache = false;
            $useCache = true;

            $this->total_count = 0;

            $hashKey_count = "CupContentSearch_" . sha1($this->query_count_string);
            $records = Cache::get($hashKey_count, false);

            if ($records !== false && $useCache === true) {
                $this->total_count = $records;
            } else {
                $count_row = $this->db->getRow($this->query_count_string);
                if ($count_row && isset($count_row['total'])) {
                    $this->total_count = $count_row['total'];
                }
                Cache::set($hashKey_count, false, $this->total_count, 60);
            }





            $this->object_results = array();

            $hashKey_records = "CupContentSearch_" . sha1($this->query_string);
            $records = Cache::get($hashKey_records, false);

            if ($records !== false && $useCache === true) {
                $this->object_results = $records;
            } else {
                $results = $this->db->getAll($this->query_string);
                foreach ($results as $row) {
                    $class_name = $row['class_name'];
                    $record_id = $row['id'];
                    $this->object_results[] = $class_name::fetchByID($record_id);
                }
                Cache::set($hashKey_records, false, $this->object_results, 60);
            }
        }
    }

    public function getResults() {
        $this->prepareResults();
        return $this->object_results;
    }

    public function getTotal() {
        $this->prepareResults();
        return $this->total_count;
    }

    public function getPages() {
        //echo "Current Page number: ".$this->page_number."\n<br/>\n";
        //echo "Total Page: ".$this->total_count."\n<br/>\n";
        $page_range = 7;
        $currentPagenumber = $this->page_number;
        $this->prepareResults();
        $page = array();
        $last_page_number = ceil(($this->total_count + 0.0) / $this->page_size);

        //echo "Last Record: ".$last_page_number."\n<br/>\n";

        if ($last_page_number > 1) {
            $dest_page_number = $this->page_number - 1;
            if ($dest_page_number < 1) {
                $dest_page_number = false;
            }
            $page['Previous'] = $dest_page_number;
        }

        if ($page_range >= $last_page_number) {
            for ($i = 1; $i <= $last_page_number; $i++) {
                if ($i == $this->page_number) {
                    $page[$i] = false;
                } else {
                    $page[$i] = $i;
                }
            }
        } else {

            if (($currentPagenumber - ($page_range / 2)) < 0) {
                for ($i = 1; $i <= $page_range; $i++) {
                    if ($i == $this->page_number) {
                        $page[$i] = false;
                    } else {
                        $page[$i] = $i;
                    }
                }

                $page[".. ."] = false;
                $page[$last_page_number] = $last_page_number;
            } elseif (($currentPagenumber + ($page_range / 2)) > $last_page_number) {
                $tmp_start_idx = $currentPagenumber - ceil($page_range / 2);

                /*
                  if($tmp_start_idx < 1){
                  $tmp_start_idx = 1;
                  }
                 */
                /*
                  if($tmp_start_idx == 2){
                  $page[1] = 1;
                  $page[". .."] = false;
                  $tmp_start_idx = 3;
                  }else */
                if ($tmp_start_idx != 1) {
                    $page[1] = 1;
                    $page[". .."] = false;
                }

                for ($i = $tmp_start_idx; $i <= $last_page_number; $i++) {
                    if ($i == $currentPagenumber) {
                        $page[$i] = false;
                    } else {
                        $page[$i] = $i;
                    }
                }
            } else {
                $tmp_start_idx = $currentPagenumber - ceil($page_range / 2) + 1;
                $tmp_end_idx = $currentPagenumber + ceil($page_range / 2) - 1;


                $page[1] = 1;
                if ($tmp_start_idx != 2) {
                    $page[". .."] = false;
                }

                for ($i = $tmp_start_idx; $i <= $tmp_end_idx; $i++) {
                    if ($i == $this->page_number) {
                        $page[$i] = false;
                    } else {
                        $page[$i] = $i;
                    }
                }

                if ($tmp_end_idx != $last_page_number - 1) {
                    $page[".. ."] = false;
                }

                $page[$last_page_number] = $last_page_number;
            }
        }


        if ($last_page_number > 1) {
            $dest_page_number = $this->page_number + 1;
            if ($dest_page_number > $last_page_number) {
                $dest_page_number = false;
            }
            $page['Next'] = $dest_page_number;
        }

        return $page;
    }

}
