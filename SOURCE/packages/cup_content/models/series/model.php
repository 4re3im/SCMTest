<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

define('SERIES_IMAGES_FOLDER',
    DIR_BASE . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'cup_content' . DIRECTORY_SEPARATOR .
    'images' . DIRECTORY_SEPARATOR . 'series' . DIRECTORY_SEPARATOR);

class CupContentSeries extends Object
{

    protected $id = false;
    protected $seriesID = false;
    protected $name = false;
    protected $prettyUrl = false;
    protected $shortDescription = false;
    protected $longDescription = false;
    protected $divisions = false;
    protected $regions = false;
    protected $yearLevels = false;
    protected $compGoUrl = false;
    protected $compHotUrl = false;
    protected $compSiteUrl = false;
    protected $partnerSiteName = false;
    protected $partnerSiteUrl = false;
    protected $tagline = false;
    protected $reviews = false;
    protected $isEnabled = false;
    protected $search_priority = false;
    protected $formats = array();
    protected $subjects = array();
    protected $createdAt = false;
    protected $modifiedAt = false;
    protected $divisions_save = false;
    protected $regions_save = false;
    protected $yearLevels_save = false;
    protected $submit_data = false;
    protected $system_errors = array();
    protected $errors = array();
    protected $exisiting_result = array();

    // Added by gxbalila
    // GCAP-790
    protected $thumbnail_url = null;
    const GLOBAL_GO_IMG_SIZE = 260;

    // GCAP-1272 Added by Shane Camus 04/07/2021
    protected $trialID = false;

    function __construct($id = false)
    {
        if ($id) {
            // $cacheObj = self::getFromCache($id);
            $cacheObj = false;
            if ($cacheObj !== false) {
                $this->copyFromObject($cacheObj);
            } else {

                $db = Loader::db();
                $q = "SELECT * FROM CupContentSeries WHERE id = ?";
                $result = $db->getRow($q, array($id));

                if ($result) {

                    $this->id = $result['id'];
                    $this->seriesID = $result['seriesID'];
                    $this->name = $result['name'];
                    $this->prettyUrl = $result['prettyUrl'];
                    $this->shortDescription = $result['shortDescription'];
                    $this->longDescription = $result['longDescription'];
                    $this->divisions = $result['divisions'];
                    $this->regions = $result['regions'];
                    $this->yearLevels = $result['yearLevels'];

                    $this->compGoUrl = $result['compGoUrl'];
                    $this->compHotUrl = $result['compHotUrl'];
                    $this->compSiteUrl = $result['compSiteUrl'];

                    $this->partnerSiteName = $result['partnerSiteName'];
                    $this->partnerSiteUrl = $result['partnerSiteUrl'];


                    $this->tagline = $result['tagline'];
                    $this->reviews = $result['reviews'];

                    $this->isEnabled = $result['isEnabled'];

                    $this->search_priority = $result['search_priority'];

                    $this->createdAt = $result['createdAt'];
                    $this->modifiedAt = $result['modifiedAt'];

                    $this->prettyUrl = $result['prettyUrl'];

                    if (strlen($this->divisions) > 0) {
                        //[F][1][2][3]..[12]
                        $tmp = trim($this->divisions, '[]');
                        $this->divisions = explode('][', $tmp);
                    }

                    if (strlen($this->regions) > 0) {
                        //[Australia][New Zealand][Queensland][Victoria]
                        $tmp = trim($this->regions, '[]');
                        $this->regions = explode('][', $tmp);
                    }

                    if (strlen($this->yearLevels) > 0) {
                        //[F][1][2][3]..[12]
                        $tmp = trim($this->yearLevels, '[]');
                        $this->yearLevels = explode('][', $tmp);
                    }


                    $this->formats = array();
                    $tmp_query = "SELECT * FROM CupContentSeriesFormats WHERE seriesID = ?";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->formats[] = $each_row['format'];
                        }
                    }

                    $this->subjects = array();
                    $tmp_query = "SELECT * FROM CupContentSeriesSubjects WHERE seriesID = ?";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->subjects[] = $each_row['subject'];
                        }
                    }

                    $this->exisiting_result = $result;

                    // Added by gxbalila
                    // GCAP-790
                    $this->thumbnail_url = $result['thumbnail_url'];

                    // GCAP-1272 Added by Shane Camus 04/07/2021
                    $this->trialID = $result['trial_id'];
                }
                $this->setToCache();
            }
        }
    }

    public static function getFromCache($id)
    {
        $hashKey = "CupContentSeries_" . $id;
        $obj = Cache::get($hashKey, false);
        /*
          if($obj instanceof CupContentSeries){
          $obj->name .= "[Cached]";
          }
         */
        return $obj;
    }

    public function copyFromObject($object)
    {
        //exit("copyFromObject");
        $this->id = $object->id;
        $this->seriesID = $object->seriesID;
        $this->name = $object->name;
        $this->prettyUrl = $object->prettyUrl;
        $this->shortDescription = $object->shortDescription;
        $this->longDescription = $object->longDescription;
        $this->divisions = $object->divisions;
        $this->regions = $object->regions;
        $this->yearLevels = $object->yearLevels;

        $this->compGoUrl = $object->compGoUrl;
        $this->compHotUrl = $object->compHotUrl;
        $this->compSiteUrl = $object->compSiteUrl;

        $this->partnerSiteName = $object->partnerSiteName;
        $this->partnerSiteUrl = $object->partnerSiteUrl;

        $this->tagline = $object->tagline;
        $this->reviews = $object->reviews;

        $this->isEnabled = $object->isEnabled;

        $this->search_priority = $object->search_priority;

        $this->createdAt = $object->createdAt;
        $this->modifiedAt = $object->modifiedAt;

        $this->divisions = $object->divisions;

        $this->regions = $object->regions;

        $this->yearLevels = $object->yearLevels;

        $this->formats = $object->formats;

        $this->subjects = $object->subjects;

        // GCAP-1272 Added by Shane Camus 04/07/2021
        $this->trialID = $object->trialID;
    }

    public function setToCache()
    {
        $hashKey = "CupContentSeries_" . $this->id;
        $save_result = Cache::set($hashKey, false, $this, 300);
    }

    public static function fetchByID($id)
    {
        // $cacheObj = self::getFromCache($id);
        $cacheObj = false;
        if ($cacheObj !== false) {
            return $cacheObj;
        } else {
            $object = new CupContentSeries($id);
            if ($object->id === false) {
                return false;
            } else {
                return $object;
            }
        }
    }

    public static function fetchBySeriesID($series_id)
    {
        // $cacheObj = self::getFromCache($series_id);
        // Do not get from the cache
        $cacheObj = false;
        if ($cacheObj !== false) {
            $tmp = new CupContentSeries();
            $tmp->copyFromObject($cacheObj);
            return $tmp;
        } else {
            $db = Loader::db();
            $q = "SELECT * FROM CupContentSeries WHERE seriesID = ?";
            $result = $db->getRow($q, array($series_id));
            if ($result) {
                return new CupContentSeries($result['id']);
            } else {
                return false;
            }
        }
    }

    public static function fetchByPrettyUrl($prettyUrl)
    {
        $object = new CupContentSeries();
        $object->loadByPrettyUrl($prettyUrl);

        if ($object->id === false) {
            return false;
        } else {
            return $object;
        }
    }

    public static function fetchByName($name)
    {
        $object = new CupContentSeries();
        $object->loadByName($name);

        if ($object->id === false) {
            return false;
        } else {
            return $object;
        }
    }

    public function loadByID($requestID)
    {
        $this->id = false;
        $this->seriesID = false;
        $this->name = false;
        $this->shortDescription = false;
        $this->longDescription = false;
        $this->division = false;
        $this->regions = false;
        $this->yearLevels = false;

        $this->compGoUrl = false;
        $this->compHotUrl = false;
        $this->compSiteUrl = false;

        $this->partnerSiteName = false;
        $this->partnerSiteUrl = false;

        $this->tagline = false;
        $this->reviews = false;

        $this->isEnabled = false;

        $this->search_priority = false;

        $this->formats = array();
        $this->subjects = array();

        $this->createdAt = false;
        $this->modifiedAt = false;

        $this->prettyUrl = false;

        // GCAP-1272 Added by Shane Camus 04/07/2021
        $this->trialID = null;

        $db = Loader::db();
        $q = "SELECT * FROM CupContentSeries WHERE id = ?";
        $result = $db->getRow($q, array($requestID));

        if ($result) {

            $this->id = $result['id'];
            $this->seriesID = $result['seriesID'];
            $this->name = $result['name'];
            $this->prettyUrl = $result['prettyUrl'];
            $this->shortDescription = $result['shortDescription'];
            $this->longDescription = $result['longDescription'];
            $this->divisions = $result['divisions'];
            $this->regions = $result['regions'];
            $this->yearLevels = $result['yearLevels'];

            $this->compGoUrl = $result['compGoUrl'];
            $this->compHotUrl = $result['compHotUrl'];
            $this->compSiteUrl = $result['compSiteUrl'];

            $this->partnerSiteName = $result['partnerSiteName'];
            $this->partnerSiteUrl = $result['partnerSiteUrl'];


            $this->tagline = $result['tagline'];
            $this->reviews = $result['reviews'];

            $this->isEnabled = $result['isEnabled'];

            $this->search_priority = $result['search_priority'];

            $this->createdAt = $result['createdAt'];
            $this->modifiedAt = $result['modifiedAt'];

            $this->prettyUrl = $result['prettyUrl'];

            if (strlen($this->divisions) > 0) {
                //[F][1][2][3]..[12]
                $tmp = trim($this->divisions, '[]');
                $this->divisions = explode('][', $tmp);
            }

            if (strlen($this->regions) > 0) {
                //[Australia][New Zealand][Queensland][Victoria]
                $tmp = trim($this->regions, '[]');
                $this->regions = explode('][', $tmp);
            }

            if (strlen($this->yearLevels) > 0) {
                //[F][1][2][3]..[12]
                $tmp = trim($this->yearLevels, '[]');
                $this->yearLevels = explode('][', $tmp);
            }


            $this->formats = array();
            $tmp_query = "SELECT * FROM CupContentSeriesFormats WHERE seriesID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->formats[] = $each_row['format'];
                }
            }

            $this->subjects = array();
            $tmp_query = "SELECT * FROM CupContentSeriesSubjects WHERE seriesID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->subjects[] = $each_row['subject'];
                }
            }

            // Added by gxbalila
            // GCAP-790
            $this->thumbnail_url = $result['thumbnail_url'];

            // GCAP-1272 Added by Shane Camus 04/07/2021
            $this->trialID = $result['trialID'];

            return true;
        } else {
            return false;
        }
    }

    public function loadByName($name)
    {
        $db = Loader::db();
        $q = "SELECT * FROM CupContentSeries WHERE name = ?";
        $result = $db->getRow($q, array($name));

        if ($result) {
            return $this->loadByID($result['id']);
        } else {
            return false;
        }
    }

    public function loadByPrettyUrl($prettyUrl)
    {
        $db = Loader::db();
        $q = "SELECT * FROM CupContentSeries WHERE prettyUrl = ?";
        $result = $db->getRow($q, array($prettyUrl));

        if ($result) {
            return $this->loadByID($result['id']);
        } else {
            return false;
        }
    }

    public function getUrl()
    {
        return '/eduaction/series/' . $this->prettyUrl;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    public function getTitleObjects()
    {
        Loader::model('title/list', 'cup_content');
        $list = new CupContentTitleList();
        $list->filterBySeries($this->name);
        $list->filterByIsEnabled();
        $list->sortBy('search_priority', 'desc');
        $list->setItemsPerPage(999);
        return $list->getPage();
    }

    // GCAP-1272 Modified by Shane Camus 04/07/2021
    public function getAssoc()
    {
        $temp = array(
            'id' => $this->id,
            'seriesID' => $this->seriesID,
            'trialID' => $this->trialID,
            'name' => $this->name,
            'prettyUrl' => $this->prettyUrl,
            'shortDescription' => $this->shortDescription,
            'longDescription' => $this->longDescription,
            'divisions' => $this->divisions,
            'regions' => $this->regions,
            'yearLevels' => $this->yearLevels,
            'compGoUrl' => $this->compGoUrl,
            'compHotUrl' => $this->compHotUrl,
            'compSiteUrl' => $this->compSiteUrl,
            'partnerSiteName' => $this->partnerSiteName,
            'partnerSiteUrl' => $this->partnerSiteUrl,
            'tagline' => $this->tagline,
            'reviews' => $this->reviews,
            'isEnabled' => $this->isEnabled,
            'search_priority' => $this->search_priority,
            'formats' => $this->formats,
            'subjects' => $this->subjects,
            'createdAt' => $this->createdAt,
            'modifiedAt' => $this->modifiedAt
        );

        if ($temp['id'] === false) {
            $temp['id'] = '';
        }

        return $temp;
    }

    public function setSubmitData($post)
    {
        $this->submit_data = $post;
    }

    public function save()
    {
        if ($this->validataion()) {

            Loader::helper('tools', 'cup_content');
            $this->prettyUrl = CupContentToolsHelper::string2prettyURL($this->name);

            $this->divisions_save = "";
            if (is_array($this->divisions) && count($this->divisions) > 0) {
                $tmp_string = implode('][', $this->divisions);
                $this->divisions_save = '[' . $tmp_string . ']';
            }

            $this->regions_save = "";
            if (is_array($this->regions) && count($this->regions) > 0) {
                $tmp_string = implode('][', $this->regions);
                $this->regions_save = '[' . $tmp_string . ']';
            }

            $this->yearLevels_save = "";
            if (is_array($this->yearLevels) && count($this->yearLevels) > 0) {
                $tmp_string = implode('][', $this->yearLevels);
                $this->yearLevels_save = '[' . $tmp_string . ']';
            }

            // GCAP-1272 Added by Shane Camus 04/12/2021
            $this->trialID = $this->trialID === '' ? null : $this->trialID;

            if ($this->id > 0) { //update
                $this->modifiedAt = date('Y-m-d H:i:s');

                $db = Loader::db();

                $q = "UPDATE CupContentSeries SET seriesID = ?, trial_id = ?,
							name = ?, prettyUrl = ?, shortDescription = ?, 
							longDescription = ?, divisions = ?, regions = ?,
							yearLevels = ?, compGoUrl = ?, compHotUrl = ?, 
							compSiteUrl = ?,partnerSiteName = ?, partnerSiteUrl = ?, 
							tagline = ?, reviews = ?, 
							isEnabled = ?, search_priority = ?,
							createdAt = ?, modifiedAt = ?
						WHERE id = ?";
                $v = array(
                    $this->seriesID,
                    $this->trialID,
                    $this->name,
                    $this->prettyUrl,
                    $this->shortDescription,
                    $this->longDescription,
                    $this->divisions_save,
                    $this->regions_save,
                    $this->yearLevels_save,
                    $this->compGoUrl,
                    $this->compHotUrl,
                    $this->compSiteUrl,
                    $this->partnerSiteName,
                    $this->partnerSiteUrl,
                    $this->tagline,
                    $this->reviews,
                    $this->isEnabled,
                    $this->search_priority,
                    $this->createdAt,
                    $this->modifiedAt,
                    $this->id
                );
                $r = $db->prepare($q);
                $res = $db->Execute($r, $v);
                if ($res) {
                    $this->afterUpdate();
                    if ($this->saveFormats() && $this->saveSubjects()) {
                        $this->loadByID($this->id);
                        $this->setToCache();
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else { //insert
                return $this->saveNew();
            }
        } else {
            return false;
        }
    }

    protected function afterUpdate()
    {
        if (strcmp($this->name, $this->exisiting_result['name']) != 0) {

            $db = Loader::db();
            $q = "UPDATE CupContentTitle SET series = ? WHERE series = ?";
            $v = array($this->name, $this->exisiting_result['name']);
            $r = $db->prepare($q);
            $res = $db->Execute($r, $v);
        }
    }

    protected function saveFormats($series_id = false)
    {
        $is_saved = true;

        if ($series_id === false) {
            $series_id = $this->id;
        }

        if (is_array($this->formats)) {
            $db = Loader::db();
            $exisiting_formats = array();
            $tmp_query = "SELECT * FROM CupContentSeriesFormats WHERE seriesID = ?";
            $tmp_results = $db->getAll($tmp_query, array($series_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $exisiting_formats[] = $each_row['format'];
                }
            }

            foreach ($this->formats as $each_format) {
                if (!in_array($each_format, $exisiting_formats)) {
                    $tmp_query = "INSERT INTO CupContentSeriesFormats (seriesID, format) VALUES (?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($series_id, $each_format));
                    if (!$tmp_result) {
                        $this->errors[] = "Format [{$each_format}] could not be added.";
                        $is_saved = false;
                    }
                }
            }

            foreach ($exisiting_formats as $each_format) {
                if (!in_array($each_format, $this->formats)) {
                    $tmp_query = "DELETE FROM CupContentSeriesFormats WHERE seriesID = ? AND format = ?";
                    $tmp_result = $db->Execute($tmp_query, array($series_id, $each_format));
                    if (!$tmp_result) {
                        $this->errors[] = "Format [{$each_format}] could not be deleted.";
                        $is_saved = false;
                    }
                }
            }
        } else {
            $is_saved = false;
            $this->errors[] = "Format data error could not be saved.";
        }

        return $is_saved;
    }

    protected function saveSubjects($series_id = false)
    {
        $is_saved = true;

        if ($series_id === false) {
            $series_id = $this->id;
        }


        if (is_array($this->subjects)) {

            $db = Loader::db();
            $existing_subjects = array();
            $tmp_query = "SELECT * FROM CupContentSeriesSubjects WHERE seriesID = ?";
            $tmp_results = $db->getAll($tmp_query, array($series_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $existing_subjects[] = $each_row['subject'];
                }
            }

            foreach ($this->subjects as $each_subject) {
                if (!in_array($each_subject, $existing_subjects)) {
                    $tmp_query = "INSERT INTO CupContentSeriesSubjects (seriesID, subject) VALUES (?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($series_id, $each_subject));
                    if (!$tmp_result) {
                        $this->errors[] = "Subject [{$each_subject}] could not be added.";
                        $is_saved = false;
                    }
                }
            }

            foreach ($existing_subjects as $each_subject) {
                if (!in_array($each_subject, $this->subjects)) {
                    $tmp_query = "DELETE FROM CupContentSeriesSubjects WHERE seriesID = ? AND subject = ?";
                    $tmp_result = $db->Execute($tmp_query, array($this->id, $each_subject));
                    if (!$tmp_result) {
                        $this->errors[] = "Subject [{$each_subject}] could not be deleted.";
                        $is_saved = false;
                    }
                }
            }
        } else {
            $is_saved = false;
            $this->errors[] = "Subject data error could not be saved.";
        }

        return $is_saved;
    }

    public function saveNew()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->modifiedAt = $this->createdAt;

        $db = Loader::db();
        $q = "INSERT INTO CupContentSeries (seriesID, trial_id, name, prettyUrl,
                shortDescription, longDescription,
                divisions, regions, yearLevels, compGoUrl,
                compHotUrl, compSiteUrl, partnerSiteName, partnerSiteUrl,
                tagline, reviews, isEnabled, search_priority,
                createdAt, modifiedAt)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $v = array(
            $this->seriesID,
            $this->trialID,
            $this->name,
            $this->prettyUrl,
            $this->shortDescription,
            $this->longDescription,
            $this->divisions_save,
            $this->regions_save,
            $this->yearLevels_save,
            $this->compGoUrl,
            $this->compHotUrl,
            $this->compSiteUrl,
            $this->partnerSiteName,
            $this->partnerSiteUrl,
            $this->tagline,
            $this->reviews,
            $this->isEnabled,
            $this->search_priority,
            $this->createdAt,
            $this->modifiedAt
        );
        $r = $db->prepare($q);
        $res = $db->Execute($r, $v);

        if ($res) {
            $new_id = $db->Insert_ID();

            if ($this->saveFormats($new_id) && $this->saveSubjects($new_id)) {
                $this->loadByID($new_id);
                $this->setToCache();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete()
    {
        if ($this->id > 0) {
            $db = Loader::db();
            $tmp_query = "DELETE FROM CupContentSeriesFormats WHERE seriesID = ?";
            $tmp_result = $db->Execute($tmp_query, array($this->id));
            if (!$tmp_result) {
                $this->errors[] = "Formats could not be deleted.";
            }

            $tmp_query = "DELETE FROM CupContentSeriesSubjects WHERE seriesID = ?";
            $tmp_result = $db->Execute($tmp_query, array($this->id));
            if (!$tmp_result) {
                $this->errors[] = "Subjects could not be deleted.";
            }

            if (count($this->errors) > 0) {
                return false;
            }

            $q = "DELETE FROM CupContentSeries WHERE id = ?";

            $result = $db->Execute($q, array($this->id));
            if ($result) {
                return true;
            } else {
                $this->errors[] = "Error occurs when deleting this Series";
                return false;
            }
        } else {
            $this->errors[] = "id is missing";
            return false;
        }
    }

    public function validataion()
    {
        $this->name = trim($this->name);

        $this->errors = array();

        if (strlen($this->name) < 1) {
            $this->errors[] = "Name is required";
        } else {
            $db = Loader::db();
            $params = array($this->name);
            $q = "SELECT count(id) AS count FROM CupContentSeries WHERE name LIKE ?";
            if ($this->id > 0) {
                $q .= ' AND id <> ?';
                $params[] = $this->id;
            }
            $db_result = $db->getRow($q, $params);

            if ($db_result['count'] > 0) {
                $this->errors[] = "Name has been used";
            }
        }

        if (strlen($this->seriesID) < 1) {
            $this->errors[] = "seriesID is required";
        } else {
            $db = Loader::db();
            $params = array($this->seriesID);
            $q = "SELECT count(id) AS count FROM CupContentSeries WHERE seriesID = ?";
            if ($this->id > 0) {
                $q .= ' AND id <> ?';
                $params[] = $this->id;
            }
            $db_result = $db->getRow($q, $params);

            if ($db_result['count'] > 0) {
                $this->errors[] = "SeriesID has been used";
            }
        }

        if (strlen($this->shortDescription) < 1) {
            $this->errors[] = "Short Description is required";
        }

        if (strlen($this->longDescription) < 1) {
            $this->errors[] = "Long Description is required";
        }

        if (count($this->regions) < 1) {
            $this->errors[] = "Region is required";
        }

        if (count($this->divisions) < 1) {
            $this->errors[] = "Division is required";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Modified by gxbalila
     * 
     * GCAP-786
     * GCAP-790
     * @param $filename
     */
    public function saveImage($filename)
    {
        $legacySizes = [60, 90, 180];
        $imgHelper = Loader::helper('image', 'cup_content');

        if (!is_dir(SERIES_IMAGES_FOLDER)) {
            mkdir(SERIES_IMAGES_FOLDER, 0777, true);
        }

        $dest_filename_original = SERIES_IMAGES_FOLDER . $this->seriesID . '.png';
        copy($filename, $dest_filename_original);
        chmod($dest_filename_original, 0777);

        // Save image in legacy mode
        foreach ($legacySizes as $size) {
            $path = SERIES_IMAGES_FOLDER . $this->seriesID . '_' . $size . '.jpg';
            $imgHelper::resize2width($filename, $size, $path);
            chmod($path, 0777);
        }
    }

    /**
     * Added by gxbalila
     * GCAP-790
     * 
     * Create copy of image with safe file name and resize to 260px as width.
     * @param $filename
     * @return $safeFileName
     */
    public function saveGlobalGoImage($filename)
    {
        $imgHelper = Loader::helper('image', 'cup_content');
        $formattedDateTime = date('YmdHis');
        $randomString = openssl_random_pseudo_bytes(10);
        $hex = bin2hex($randomString);
        $safeFileName = $formattedDateTime . '-' . $hex . '-' . $this->id . '.jpg';
        $path = SERIES_IMAGES_FOLDER . $safeFileName;

        $imgHelper::resize2width($filename, static::GLOBAL_GO_IMG_SIZE, $path);
        chmod($path, 0777);

        return $safeFileName;
    }

    /**
     * Added by gxbalila
     * GCAP-790
     * 
     * Save Global GO thumbnail URL in database
     * @param $filename
     */
    public function saveThumbnailURL($filename = null)
    {
        $db = Loader::db();

        if ($filename !== null) {
            $sourcePath = SERIES_IMAGES_FOLDER . $filename;
            $path = 'files/cup_content/images/series/' . $filename;

            if (!file_exists($sourcePath)) {
                $path = null;
            }
        } else {
            $path = null;
        }

        $query = "UPDATE CupContentSeries SET thumbnail_url = ? WHERE id = ?";
        $db->Execute($query, [$path, $this->id]);
    }

    public function getImageURL($size = false)
    {
        $url = DIR_REL . '/packages/cup_content/images/';
        $series_image_url = DIR_REL . '/files/cup_content/images/series/';
        if ($size) {
            $filename = $this->seriesID . '_' . $size . '.jpg';
            if (!file_exists(SERIES_IMAGES_FOLDER . $filename)) {
                $filename = "title_na_" . $size . '.jpg';
            } else {
                $url = $series_image_url;
            }
        } else {
            $filename = $this->seriesID;
            if (!file_exists(SERIES_IMAGES_FOLDER . $filename)) {
                $filename = "imagena";
            } else {
                $url = $series_image_url;
            }
        }


        $url .= $filename . "?" . time();
        return $url;
    }

    public function deleteImage()
    {
        /* Remove all images */
        $files = array();
        $files[] = SERIES_IMAGES_FOLDER . $this->seriesID;
        $files[] = SERIES_IMAGES_FOLDER . $this->seriesID . '_180.jpg';
        $files[] = SERIES_IMAGES_FOLDER . $this->seriesID . '_90.jpg';
        $files[] = SERIES_IMAGES_FOLDER . $this->seriesID . '_60.jpg';

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function hasImage()
    {
        $filename = $this->seriesID;
        return file_exists(SERIES_IMAGES_FOLDER . $filename);
    }

    // SB-389 added by jbernardez 20191106
    // this has been added as there are files created with .png
    // and older files uploaded without the .png
    // this is to check on both, to copy image properly
    public function hasImagePNG()
    {
        $filename = $this->seriesID;
        return file_exists(SERIES_IMAGES_FOLDER . $filename . '.png');
    }

    public function isContainNewProduct()
    {
        Loader::model('title/list', 'cup_content');
        $list = new CupContentTitleList();
        $list->filterBySeries($this->name);
        $list->filterByIsEnabled();
        $list->filterByNewProduct();
        $list->sortBy('search_priority', 'desc');
        $list->setItemsPerPage(999);
        if (count($list->getPage()) > 0) {
            return true;
        }
        return false;
    }

    public static function convertPost($post)
    {
        $default_values = array(
            'id' => '',
            'name' => '',
            'shortDescription' => '',
            'longDescription' => '',
            'yearLevels' => '',
            'formats' => array(),
            'subjects' => array(),
            'divisions' => array(),
            'regions' => array(),
            'compGoUrl' => '',
            'compHotUrl' => '',
            'compSiteUrl' => '',
            'partnerSiteName' => '',
            'partnerSiteUrl' => '',
            'tagline' => '',
            'reviews' => '',
            'isEnabled' => ''
        );

        $post = array_merge($default_values, $post);

        if ($post['yearLevels'] == "") {
            $post['yearLevels'] = array();
        } elseif (is_string($post['yearLevels'])) {
            $year_value = $post['yearLevels'];
            $tmp_yearLevel = array();
            if (strlen($year_value) > 1) {
                if (!in_array($year_value, $tmp_yearLevel)) {
                    $tmp_yearLevel[] = $year_value;
                }

                $tmp = explode('-', $year_value);
                $min = 1;
                $max = $tmp[1];
                if ($tmp[0] == 'F') {
                    if (!in_array('F', $tmp_yearLevel)) {
                        $tmp_yearLevel[] = 'F';
                    }
                    $min = 1;
                } else {
                    $min = $tmp[0];
                }

                for ($i = $min; $i <= $max; $i++) {
                    if (!in_array($i, $tmp_yearLevel)) {
                        $tmp_yearLevel[] = $i;
                    }
                }
            } else {
                if (!in_array($year_value, $tmp_yearLevel)) {
                    $tmp_yearLevel[] = $year_value;
                }
            }
            $post['yearLevels'] = $tmp_yearLevel;
        } elseif (is_array($post['yearLevels'])) {
            $tmp_yearLevel = array();
            foreach ($post['yearLevels'] as $year_value) {
                if (strlen($year_value) > 1) {
                    if (!in_array($year_value, $tmp_yearLevel)) {
                        $tmp_yearLevel[] = $year_value;
                    }

                    $tmp = explode('-', $year_value);
                    $min = 1;
                    $max = $tmp[1];
                    if ($tmp[0] == 'F') {
                        if (!in_array('F', $tmp_yearLevel)) {
                            $tmp_yearLevel[] = 'F';
                        }
                        $min = 1;
                    } else {
                        $min = $tmp[0];
                    }

                    for ($i = $min; $i <= $max; $i++) {
                        if (!in_array($i, $tmp_yearLevel)) {
                            $tmp_yearLevel[] = $i;
                        }
                    }
                } else {
                    if (!in_array($year_value, $tmp_yearLevel)) {
                        $tmp_yearLevel[] = $year_value;
                    }
                }
            }
            $post['yearLevels'] = $tmp_yearLevel;
        }


        if (in_array('New South Wales', $post['regions']) || in_array('New South Wales',
                $post['regions']) || in_array('Northern Territory', $post['regions']) || in_array('Queensland',
                $post['regions']) || in_array('South Australia', $post['regions']) || in_array('Tasmania',
                $post['regions']) || in_array('Victoria', $post['regions']) || in_array('Western Australia',
                $post['regions']) || in_array('Australian Capital Territory', $post['regions'])) {

            $post['regions'][] = 'Australia';
        }

        if (in_array('Australia', $post['regions']) && in_array('New Zealand', $post['regions'])) {
            $post['regions'][] = 'Australia & New Zealand';
        }

        return $post;
    }

    // Nick Ingarsia, 30/5/14
    // Return the series ID
    public function getID()
    {
        return $this->id;
    }

    // GCAP-1272 Added by Shane Camus 04/08/2021
    public static function fetchByTrialId($trialId)
    {
        $db = Loader::db();
        $q = "select * from CupContentSeries where trial_id = ?";
        $result = $db->getRow($q, array($trialId));

        return $result;
    }
}