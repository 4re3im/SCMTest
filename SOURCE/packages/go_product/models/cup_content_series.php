<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentSeries extends Object {
    
    protected $id = FALSE;
    protected $name = FALSE;
    protected $region = FALSE;
    protected $subject = FALSE;
    protected $createdAt = FALSE;
    protected $prettyUrl = FALSE;
    protected $modifiedAt = FALSE;
    protected $description = FALSE;
    protected $subject_pretty_url = FALSE;

    protected $exisiting_result = array();

    function __construct($id = false) {

        if($id){
            $db = Loader::db();
            $sql = "select * from CupContentSeries where id = ?";	
            $result = $db->getRow($sql, array($id));

            if($result){

                $this->id 			= $result['id'];
                $this->name			= $result['name'];
                $this->prettyUrl		= $result['prettyUrl'];
                $this->regions		= $result['region'];
                $this->createdAt		= $result['createdAt'];
                $this->modifiedAt		= $result['modifiedAt'];

                $this->exisiting_result = $result;
            }
        }
    }

    public static function fetchByID($id){
        $object = new CupContentSeries($id);
        if($object->id === FALSE){
            return FALSE;
        }else{
            return $object;
        }
    }

    public static function fetchByPrettyUrl($prettyUrl){

        $object = new CupContentSeries();
        $object->loadByPrettyUrl($prettyUrl);

        if($object->id === FALSE){
            return FALSE;
        }else{
            return $object;
        }
    }

    public static function fetchByName($name){

        $object = new CupContentSeries();

        $db = Loader::db();
        $sql = "SELECT * FROM CupContentSeries WHERE name like ?";
        $row = $db->getRow($sql, array($name));
        
        if($row){
            return new CupContentSeries($row['id']);
        }else{
            return FALSE;
        }
    }

    public function loadByPrettyUrl($prettyUrl){
        $db = Loader::db();
        $sql = "SELECT ccs.*, ccss.subject,ccsu.prettyUrl AS subject_pretty_url "
                . "FROM CupContentSeries ccs JOIN CupContentSeriesSubjects ccss ON ccs.id = ccss.seriesId "
                . "JOIN CupContentSubject ccsu ON ccss.subject = ccsu.name "
                . "WHERE ccs.prettyUrl = ?";

        $result = $db->getRow($sql, array($prettyUrl));
        
        if($result){

            $this->id 			= $result['id'];
            $this->name			= $result['name'];
            $this->prettyUrl		= $result['prettyUrl'];
            $this->region               = $result['region'];
            $this->createdAt		= $result['createdAt'];
            $this->modifiedAt		= $result['modifiedAt'];
            $this->subject              = $result['subject'];
            $this->subject_pretty_url   = $result['subject_pretty_url'];

            $this->exisiting_result     = $result;

        }else{
                return false;
        }
    }
    
    public function getSeriesSubjectPrettyUrl() {
        return $this->subject_pretty_url;
    }
    
    public static function getSeriesSubjects($id) {
        $db = Loader::db();
        $sql = "SELECT ccsb.`subject`,ccs.`prettyUrl` "
                . "FROM CupContentSeriesSubjects AS ccsb "
                . "JOIN CupContentSubject AS ccs ON ccsb.`subject` = ccs.`name` WHERE `seriesID` = ?";
        return $db->getAll($sql, array($id));
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