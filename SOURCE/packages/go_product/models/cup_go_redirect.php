<?php
/*
 * Product Model
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 26, 2015
 */

class CupGoRedirect extends Model{

    var $db;

    public function __construct() {

        $this->db = Loader::db();

    }

    public function __destruct() { }

    // Modified by Paul Balila for ticket ANZGO-2846, 2016-09-07
    // Build the needed array of redirect details with structure:
    // array('chapter'=>array(<redirect_details>))
    public function getRedirectByEpubName($productId){


	if(!$productId) return array();

        // Changing database reference to "tngdb" -- the Concrete5 database of TNG.
        // Modified by Paul Balila for ANZGO-2870, 2016-09-26
        /*
         * $sql = "SELECT id, page, url, type, start_date, end_date, notes, epub, chapter, title
                FROM redirect
                WHERE REPLACE(epub,':','') = ? AND chapter<>''
                ORDER BY LENGTH(chapter), chapter ASC";
         */
        $sql = "SELECT id, page, url, type, start_date, end_date, notes, epub, chapter, title
                FROM redirect
                WHERE title_id = ?
                ORDER BY LENGTH(chapter), chapter";

        $redirects = $this->db->getAll($sql,$productId);
        
        $temp_arr = array();
        foreach ($redirects as $r) {
          $temp_arr[$r['chapter']][] = array(
            'url' => $r['url'],
            'title' => $r['title'],
            'notes' => $r['notes']
          );
        }

        return $temp_arr;

    }

    public function getRedirectChapterByEpubName($productName){

	if(!$productName) return array();
        
        // Changing database reference to "tngdb" -- the Concrete5 database of TNG.
        // Modified by Paul Balila for ANZGO-2870, 2016-09-26
        $sql = "SELECT epub, chapter
                    FROM redirect
                    WHERE epub = ? AND chapter<>''
                    GROUP BY chapter
                    ORDER BY LENGTH(chapter), chapter ASC";

        $redirects = $this->db->getAll($sql,$productName);
        //echo "<pre>"; print_r($redirects); echo "</pre>"; exit;
        return $redirects;

    }

}
?>
