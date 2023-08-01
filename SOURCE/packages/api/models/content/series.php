<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../base_model.php';

class ContentSeries extends BaseModel
{
    public $id;
    public $seriesID;
    public $name;
    public $prettyUrl;
    public $shortDescription;
    public $longDescription;
    public $divisions;
    public $regions;
    public $yearLevels;
    public $compGoUrl;
    public $compHotUrl;
    public $compSiteUrl;
    public $partnerSiteName;
    public $partnerSiteUrl;
    public $tagline;
    public $reviews;
    public $isEnabled;
    public $search_priority;
    public $createdAt;
    public $modifiedAt;
    public $image;
    public $formats;
    public $subjects;
    public $_table = 'CupContentSeries';


    /**
     * Loads series details using seriesID
     * @param $seriesID
     */
    public function loadBySeriesID($seriesID)
    {
        $this->Load('seriesID = ?', array($seriesID));
        $this->_saved = true;
    }

    /**
     * Saves data including related tables.
     * @return bool|int
     */
    public function save()
    {
        if (is_array($this->divisions)) {
            $this->__set('divisions', $this->braceArray($this->divisions));
        }

        if (is_array($this->regions)) {
            $this->__set('regions', $this->braceArray($this->regions));
        }

        if (is_array($this->yearLevels)) {
            $this->__set('yearLevels', $this->braceArray($this->yearLevels));
        }

        $ok = parent::save();

        if (!$ok) {
            return $ok;
        }

        $this->updatePrimaryKey();
        $this->saveFormats();
        $this->saveSubjects();
        $this->saveImage();

        return $ok;
    }

    /**
     * Updates primary key
     * Typically used after saving.
     */
    public function updatePrimaryKey()
    {
        $series = new ContentSeries();
        $series->loadBySeriesID($this->seriesID);
        $this->id = $series->id;
    }

    /**
     * Saves series formats.
     * @return bool
     */
    public function saveFormats()
    {
        $this->deleteFormats();
        $sql = 'INSERT INTO CupContentSeriesFormats (seriesID, format) VALUES ';

        if (!$this->formats) {
            return true;
        }

        $parameters = [];
        $values = '';
        foreach ($this->formats as $format) {
            $values .= '(?,?),';
            array_push($parameters, $this->id, $format);
        }

        $sql .= substr($values, 0, -1);
        $this->db->Execute($sql, $parameters);
    }

    /**
     * Deletes series formats.
     */
    public function deleteFormats()
    {
        $sql = 'DELETE FROM CupContentSeriesFormats WHERE seriesID = ?';
        $this->db->Execute($sql, array($this->id));
    }

    /**
     * Saves series subjects
     * @return bool
     */
    public function saveSubjects()
    {
        $this->deleteSubjects();
        $sql = 'INSERT INTO CupContentSeriesSubjects (seriesID, subject) VALUES ';

        if (!$this->subjects) {
            return true;
        }

        $parameters = [];
        $values = '';
        foreach ($this->subjects as $subject) {
            $values .= '(?,?),';
            array_push($parameters, $this->id, $subject);
        }

        $sql .= substr($values, 0, -1);
        $this->db->Execute($sql, $parameters);
    }

    /**
     * Deletes series subjects
     */
    public function deleteSubjects()
    {
        $sql = 'DELETE FROM CupContentSeriesSubjects WHERE seriesID = ?';
        $this->db->Execute($sql, array($this->id));
    }

    public function saveImage()
    {
        if (!$this->image) {
            return true;
        }

        $seriesPath = sprintf(
            '%s%sfiles%scup_content%simages%sseries%s',
            DIR_BASE,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR
        );

        $destinationFilename = $this->seriesID;

        $file180w = $this->seriesID . '_180.jpg';
        $file90w = $this->seriesID . '_90.jpg';
        $file60w = $this->seriesID . '_60.jpg';

        if (!is_dir($seriesPath)) {
            mkdir($seriesPath, 0777, true);
        }

        $destinationFilename = $seriesPath . $destinationFilename . '.png';
        file_put_contents($destinationFilename, base64_decode($this->image['data']));
        chmod($destinationFilename, 0777);

        $file180w = $seriesPath . $file180w;
        $file90w = $seriesPath . $file90w;
        $file60w = $seriesPath . $file60w;

        Loader::library('internal/image');
        GraphicsImageHelper::resizeToWidth($destinationFilename, 180, $file180w);
        GraphicsImageHelper::resizeToWidth($destinationFilename, 90, $file90w);
        GraphicsImageHelper::resizeToWidth($destinationFilename, 60, $file60w);
        chmod($file180w, 0777);
        chmod($file90w, 0777);
        chmod($file60w, 0777);
    }
}