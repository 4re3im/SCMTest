<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('price', 'core_commerce');

/*
define('FORMAT_IMAGES_FOLDER', DIR_PACKAGES.DIRECTORY_SEPARATOR.'cup_content'.DIRECTORY_SEPARATOR.
				'images'.DIRECTORY_SEPARATOR.'formats'.DIRECTORY_SEPARATOR);
*/

define('FORMAT_IMAGES_FOLDER', DIR_BASE . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'cup_content' . DIRECTORY_SEPARATOR .
    'images' . DIRECTORY_SEPARATOR . 'formats' . DIRECTORY_SEPARATOR);

class CupContentFormat extends Object
{
    protected $id = FALSE;
    protected $name = FALSE;
    protected $prettyUrl = FALSE;
    protected $shortDescription = FALSE;
    protected $longDescription = FALSE;
    protected $isDigital = FALSE;
    protected $createdAt = FALSE;
    protected $modifiedAt = FALSE;

    protected $submit_data = false;
    protected $system_errors = array();
    protected $errors = array();

    protected $exisiting_result = array();

    function __construct($id = false)
    {
        if ($id) {
            $db = Loader::db();
            $q = "SELECT * FROM CupContentFormat WHERE id = ?";
            $result = $db->getRow($q, array($id));

            if ($result) {

                $this->id = $result['id'];
                $this->name = $result['name'];
                $this->prettyUrl = $result['prettyUrl'];
                $this->shortDescription = $result['shortDescription'];
                $this->longDescription = $result['longDescription'];
                $this->isDigital = $result['isDigital'];
                $this->createdAt = $result['createdAt'];
                $this->modifiedAt = $result['modifiedAt'];

                $this->exisiting_result = $result;

            }
        }
    }

    public static function fetchByID($id)
    {
        $object = new CupContentFormat($id);
        if ($object->id === FALSE) {
            return FALSE;
        } else {
            return $object;
        }
    }

    public static function fetchByPrettyUrl($prettyUrl)
    {
        $object = new CupContentFormat();
        $object->loadByPrettyUrl($prettyUrl);

        if ($object->id === FALSE) {
            return FALSE;
        } else {
            return $object;
        }
    }

    public static function fetchByName($format_name)
    {
        $object = new CupContentFormat();
        $object->loadByName($format_name);

        if ($object->id === FALSE) {
            return FALSE;
        } else {
            return $object;
        }
    }

    public function loadByID($id)
    {
        $this->id = FALSE;
        $this->name = FALSE;
        $this->prettyUrl = FALSE;
        $this->shortDescription = FALSE;
        $this->longDescription = FALSE;
        $this->createdAt = FALSE;
        $this->modifiedAt = FALSE;

        $db = Loader::db();
        $q = "SELECT * FROM CupContentFormat WHERE id = ?";
        $result = $db->getRow($q, array($id));

        if ($result) {

            $this->id = $result['id'];
            $this->name = $result['name'];
            $this->prettyUrl = $result['prettyUrl'];
            $this->shortDescription = $result['shortDescription'];
            $this->longDescription = $result['longDescription'];
            $this->createdAt = $result['createdAt'];
            $this->modifiedAt = $result['modifiedAt'];

            $this->exisiting_result = $result;
            return true;
        } else {
            return false;
        }
    }

    public function loadByPrettyUrl($prettyUrl)
    {
        $this->id = FALSE;
        $this->name = FALSE;
        $this->prettyUrl = FALSE;
        $this->shortDescription = FALSE;
        $this->longDescription = FALSE;
        $this->isDigital = FALSE;
        $this->createdAt = FALSE;
        $this->modifiedAt = FALSE;

        $db = Loader::db();
        $q = "SELECT * FROM CupContentFormat WHERE prettyUrl LIKE ?";
        $result = $db->getRow($q, array($prettyUrl));

        if ($result) {

            $this->id = $result['id'];
            $this->name = $result['name'];
            $this->prettyUrl = $result['prettyUrl'];
            $this->shortDescription = $result['shortDescription'];
            $this->longDescription = $result['longDescription'];
            $this->isDigital = $result['isDigital'];
            $this->createdAt = $result['createdAt'];
            $this->modifiedAt = $result['modifiedAt'];

            $this->exisiting_result = $result;
            return true;
        } else {
            return false;
        }
    }

    public function loadByName($format_name)
    {
        $this->id = FALSE;
        $this->name = FALSE;
        $this->prettyUrl = FALSE;
        $this->shortDescription = FALSE;
        $this->longDescription = FALSE;
        $this->createdAt = FALSE;
        $this->modifiedAt = FALSE;

        $db = Loader::db();
        $q = "SELECT * FROM CupContentFormat WHERE name LIKE ?";
        $result = $db->getRow($q, array($format_name));

        if ($result) {

            $this->id = $result['id'];
            $this->name = $result['name'];
            $this->prettyUrl = $result['prettyUrl'];
            $this->shortDescription = $result['shortDescription'];
            $this->longDescription = $result['longDescription'];
            $this->isDigital = $result['isDigital'];
            $this->createdAt = $result['createdAt'];
            $this->modifiedAt = $result['modifiedAt'];

            $this->exisiting_result = $result;
            return true;
        } else {
            return false;
        }
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

    public function getAssoc()
    {
        $temp = array(
            'id' => $this->id,
            'name' => $this->name,
            'prettyUrl' => $this->prettyUrl,
            'shortDescription' => $this->shortDescription,
            'longDescription' => $this->longDescription,
            'isDigital' => $this->isDigital,
            'createdAt' => $this->createdAt,
            'modifiedAt' => $this->modifiedAt
        );

        if ($temp['id'] === FALSE) {
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

            if ($this->id > 0) {    //update
                $this->modifiedAt = date('Y-m-d H:i:s');

                $db = Loader::db();
                $q = "UPDATE CupContentFormat SET name = ?, prettyUrl = ?, shortDescription = ?, longDescription = ?, isDigital = ?, createdAt = ?, modifiedAt = ? WHERE id = ?";
                $v = array($this->name, $this->prettyUrl, $this->shortDescription, $this->longDescription, $this->isDigital, $this->createdAt,
                    $this->modifiedAt, $this->id);
                $r = $db->prepare($q);
                $res = $db->Execute($r, $v);
                if ($res) {
                    $this->afterUpdate();
                    $this->loadByID($this->id);
                    return true;
                } else {
                    return false;
                }
            } else {    //insert
                return $this->saveNew();
            }
        } else {
            return false;
        }
    }

    public function saveNew()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->modifiedAt = $this->createdAt;

        $db = Loader::db();
        $q = "INSERT INTO CupContentFormat (name, prettyUrl, shortDescription, longDescription, isDigital, createdAt, modifiedAt) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $v = array($this->name, $this->prettyUrl, $this->shortDescription, $this->longDescription, $this->isDigital,
            $this->createdAt, $this->modifiedAt);
        $r = $db->prepare($q);
        $res = $db->Execute($r, $v);

        if ($res) {
            $this->loadByID($db->Insert_ID());
            return true;
        } else {
            return false;
        }
    }

    protected function afterUpdate()
    {
        if (strcmp($this->name, $this->exisiting_result['name']) != 0) {

            $db = Loader::db();
            $q = "UPDATE CupContentTitleFormats SET format = ? WHERE format = ?";
            $v = array($this->name, $this->exisiting_result['name']);
            $r = $db->prepare($q);
            $res = $db->Execute($r, $v);

            $q = "UPDATE CupContentSeriesFormats SET format = ? WHERE format = ?";
            $v = array($this->name, $this->exisiting_result['name']);
            $r = $db->prepare($q);
            $res = $db->Execute($r, $v);
        }
    }

    public function delete()
    {
        if ($this->id > 0) {
            $db = Loader::db();
            $q = "DELETE FROM CupContentFormat WHERE id = ?";

            $result = $db->Execute($q, array($this->id));
            if ($result) {
                return true;
            } else {
                $this->errors[] = "Error occurs when deleting this format";
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
            $q = "SELECT count(id) AS count FROM CupContentFormat WHERE name LIKE ?";
            if ($this->id > 0) {
                $q .= ' AND id <> ?';
                $params[] = $this->id;
            }
            $db_result = $db->getRow($q, $params);

            if ($db_result['count'] > 0) {
                $this->errors[] = "Name has been used";
            }
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }


    public function saveImage($filename)
    {

        $dest_filename_original = $this->id;

        //$dest_filename_30 = $this->id.'_30.gif';

        if (!is_dir(FORMAT_IMAGES_FOLDER)) {
            //echo "folder does not exist";
            mkdir(FORMAT_IMAGES_FOLDER, 0777, true);
        }

        $dest_filename_original = FORMAT_IMAGES_FOLDER . $dest_filename_original;
        copy($filename, $dest_filename_original);
        chmod($dest_filename_original, 0777);

        $globalFilename = FORMAT_IMAGES_FOLDER . $this->id . '.png';
        copy($filename, $globalFilename);
        chmod($globalFilename, 0777);

        /*
        $dest_filename_30 = FORMAT_IMAGES_FOLDER.$dest_filename_30;

        $imgHelper = Loader::helper('image', 'cup_content');
        $res = $imgHelper::resize2width($filename, 30, $dest_filename_30, 'gif');

        if(!$res){
            echo "failed to save: ";
            echo $dest_filename_30;
            exit();
        }
        */
    }

    public function saveSmallImage($filename)
    {

        $dest_filename_original = $this->id . "s";

        //$dest_filename_30 = $this->id.'_30.gif';

        if (!is_dir(FORMAT_IMAGES_FOLDER)) {
            //echo "folder does not exist";
            mkdir(FORMAT_IMAGES_FOLDER, 0777, true);
        }

        $dest_filename_original = FORMAT_IMAGES_FOLDER . $dest_filename_original;
        copy($filename, $dest_filename_original);
        chmod($dest_filename_original, 0777);

        /*
        $dest_filename_30 = FORMAT_IMAGES_FOLDER.$dest_filename_30;

        $imgHelper = Loader::helper('image', 'cup_content');
        $res = $imgHelper::resize2width($filename, 30, $dest_filename_30, 'gif');

        if(!$res){
            echo "failed to save: ";
            echo $dest_filename_30;
            exit();
        }
        */
    }

    public function getImageURL()
    {
        $ch = Loader::helper('cup_content_html', 'cup_content');

        $url = REL_DIR_PACKAGES . '/cup_content/images/';

        $formats_image_url = DIR_REL . '/files/cup_content/images/formats/'; //FORMAT_IMAGES_FOLDER;

        $filename = $this->id;
        if (!file_exists(FORMAT_IMAGES_FOLDER . $filename)) {
            $filename = "format_na.gif";
        } else {
            //$url .= 'formats/';
            $url = $formats_image_url;
        }


        $url .= $filename;

        //return $ch->url($url);
        return $url;
    }

    public function getSmallImageURL()
    {
        $ch = Loader::helper('cup_content_html', 'cup_content');

        $url = REL_DIR_PACKAGES . '/cup_content/images/';

        $formats_image_url = DIR_REL . '/files/cup_content/images/formats/'; //FORMAT_IMAGES_FOLDER;

        $filename = $this->id . "s";
        if (!file_exists(FORMAT_IMAGES_FOLDER . $filename)) {
            $filename = "format_nas.gif";
        } else {
            //$url .= 'formats/';
            $url = $formats_image_url;
        }


        $url .= $filename;

        //return $ch->url($url);
        return $url;
    }

    public function deleteImage()
    {
        /* Remove all images */
        $files = array();
        $files[] = FORMAT_IMAGES_FOLDER . $this->id;
        $files[] = FORMAT_IMAGES_FOLDER . $this->id . "s";

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function hasImage()
    {
        $filename = $this->id;
        return file_exists(FORMAT_IMAGES_FOLDER . $filename);
    }

}
