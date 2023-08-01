<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentSubject extends Object {
    
    protected $id = FALSE;
    protected $name = FALSE;
    protected $region = FALSE;
    protected $createdAt = FALSE;
    protected $prettyUrl = FALSE;
    protected $modifiedAt = FALSE;
    protected $description = FALSE;

    protected $exisiting_result = array();

    function __construct($id = false) {

        if($id){
            $db = Loader::db();
            $sql = "select * from CupContentSubject where id = ?";	
            $result = $db->getRow($sql, array($id));

            if($result){

                $this->id 			= $result['id'];
                $this->name			= $result['name'];
                $this->prettyUrl		= $result['prettyUrl'];
                $this->description		= $result['description'];
                $this->region		= $result['region'];
                $this->createdAt		= $result['createdAt'];
                $this->modifiedAt		= $result['modifiedAt'];

                $this->isPrimary = FALSE;
                $this->isSecondary = FALSE;

                if(isset($result['isPrimary']) && $result['isPrimary'] == 1){
                    $this->isPrimary = true;
                }

                if(isset($result['isSecondary']) && $result['isSecondary'] == 1){
                    $this->isSecondary = true;
                }

                $this->exisiting_result = $result;
            }
        }
    }

    public static function fetchByID($id){
        $object = new CupContentSubject($id);
        if($object->id === FALSE){
            return FALSE;
        }else{
            return $object;
        }
    }

    public static function fetchByPrettyUrl($prettyUrl){

        $object = new CupContentSubject();
        $object->loadByPrettyUrl($prettyUrl);

        if($object->id === FALSE){
            return FALSE;
        }else{
            return $object;
        }
    }

    public static function fetchByName($name){

        $object = new CupContentSubject();

        $db = Loader::db();
        $sql = "SELECT * FROM CupContentSubject WHERE name like ?";
        $row = $db->getRow($sql, array($name));

        if($row){
            return new CupContentSubject($row['id']);
        }else{
            return FALSE;
        }
    }


    public static function fetchPrimarySubject($is_secondary=1){

        $object = new CupContentSubject();

        $db = Loader::db();
        $sql = "SELECT * FROM CupContentSubject WHERE isSecondary=?";
        $row = $db->getAll($sql, array($is_secondary));

        if($row){
            return $row;
        }else{
            return FALSE;
        }
    }

    public static function fetchSubjectByGoSubject(){

        $object = new CupContentSubject();

        $db = Loader::db();
        
        //temporary use
        $go_subject = "
            'Arts',
            'Business, Economics, and Legal',
            'English',
            'Geography', 
            'Health & PE', 
            'History',
            'Mathematics', 
            'Religion',
            'Sciences',
            'Study Guides',
            'Technology ( inc. Food )',
            'Vocational',
            'Humanities'
        ";

        $sql = "SELECT * FROM CupContentSubject WHERE name IN($go_subject) ORDER BY name";

        $row = $db->getAll($sql);
    
        if($row){
            return $row;
        }else{
            return FALSE;
        }
    }
    
    public static function fetchPrimarySubjectList($is_secondary=1){

        $object = new CupContentSubject();

        $db = Loader::db();
        //$sql = "SELECT prettyUrl, name FROM CupContentSubject WHERE isSecondary=?";
        //temporary use
        $go_subject = "
            'Arts',
            'Business, Economics, and Legal',
            'English',
            'Geography', 
            'Health & PE', 
            'History',
            'Mathematics', 
            'Religion',
            'Sciences',
            'Study Guides',
            'Technology ( inc. Food )',
            'Vocational',
            'Humanities'
        ";

        $sql = "SELECT prettyUrl, name FROM CupContentSubject WHERE name IN($go_subject) ORDER BY name";
        
        $row = $db->getAssoc($sql);

        if($row){
            return $row;
        }else{
            return FALSE;
        }
    }

    public function loadByPrettyUrl($prettyUrl){

        $this->id = FALSE;
        $this->name = FALSE;
        $this->prettyUrl = FALSE;
        $this->description = FALSE;
        $this->isPrimary = FALSE;
        $this->isSecondary = FALSE;
        $this->region = FALSE;
        $this->createdAt = FALSE;
        $this->modifiedAt = FALSE;

        $db = Loader::db();
        if($prettyUrl){
            $sql = "SELECT * FROM CupContentSubject WHERE prettyUrl = ?";
            $result = $db->getRow($sql, array($prettyUrl));
        }else{
            $sql = "SELECT * FROM CupContentSubject";
            $result = $db->getRow($sql);
        }

        if($result){

            $this->id 			= $result['id'];
            $this->name			= $result['name'];
            $this->prettyUrl		= $result['prettyUrl'];
            $this->description		= $result['description'];
            $this->region               = $result['region'];
            $this->createdAt		= $result['createdAt'];
            $this->modifiedAt		= $result['modifiedAt'];

            if(isset($result['isPrimary']) && $result['isPrimary'] == 1){
                $this->isPrimary = true;
            }

            if(isset($result['isSecondary']) && $result['isSecondary'] == 1){
                $this->isSecondary = true;
            }

            $this->exisiting_result = $result;

        }else{
                return false;
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }
			
}