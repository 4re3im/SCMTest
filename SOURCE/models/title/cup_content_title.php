<?php

/**
 * ANZGO-3481 , Added by John Renzo S. Sunico, 10/11/2017
 * CupContentTitleModel Global Handler
 */

Loader::model('base_model');

class CupContentTitleModel extends BaseModel
{
    public $_table = "CupContentTitle";

    /**
     * Returns object if title is found
     * @param $directory
     * @return CupContentTitleModel
     */
    public function getTitleByEpubDirectory($directory)
    {
        $sql = "SELECT titleID FROM CupGoTabs WHERE Private_TabText LIKE ? LIMIT 1";
        $result = $this->db->GetRow($sql, array($this->paddingLikeBoth($directory)));

        if ($result) {
            $this->loadByID($result['titleID']);
        }

        return $this;
    }

    /**
     * Loads title using ID
     * @param $titleID
     * @return void
     */
    public function loadByID($titleID)
    {
        $titleID = intval($titleID);
        $this->Load("id = $titleID");
    }

    /**
     * Returns id, name, isbn13 in array format
     * @return array
     */
    public function getShortAssocDescription()
    {
        if ($this->getID()) {
            return array(
                'id' => $this->getID(),
                'name' => $this->getName(),
                'isbn13' => $this->getISBN13()
            );
        }

        return array();
    }

}
