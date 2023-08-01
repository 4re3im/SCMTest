<?php

/**
 * Model for the Go series
 * @author jbernardez
 */

class GoSeriesEditorModel extends Model
{
    private $db;

    public function __construct()
    {
        $this->db = Loader::db();
    }

    public function getTitleIDBySeriesID($seriesID)
    {
        $sql = 'SELECT titleID FROM CupContentSeriesTitlesReference WHERE seriesID = ?';
        $titleID = $this->db->GetRow($sql, $seriesID);

        if (!$titleID['titleID']) {
            return false;
        }

        return $titleID['titleID'];
    }

    public function saveIsSeriesTitle($seriesID, $titleID)
    {
        $sql = 'INSERT INTO CupContentSeriesTitlesReference (seriesID, titleID) VALUES (?, ?)';
        $res = $this->db->prepare($sql);
        $result = $this->db->Execute($res, array($seriesID, $titleID));

        if ($result) {
            return $this->db->Insert_ID();
        } else {
            return false;
        }
    }
}
