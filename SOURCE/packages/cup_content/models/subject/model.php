<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('price', 'core_commerce');


class CupContentSubject extends Object
{
    protected $id               = false;
    protected $name             = false;
    protected $prettyUrl        = false;
    protected $description      = false;
    protected $isPrimary        = false;
    protected $isSecondary      = false;
    // SB-399 added by jbernardez 20191112
    protected $isEnabled        = false;
    protected $region           = false;
    protected $createdAt        = false;
    protected $modifiedAt       = false;
    protected $isPrimarySave    = false;
    protected $isSecondarySave  = false;
    // SB-399 added by jbernardez 20191112
    protected $isEnabledSave    = false;
    protected $submitData       = false;
    protected $systemErrors     = array();
    protected $errors           = array();
    protected $exisitingResult  = array();
    
    function __construct($id = false)
    {
        if ($id) {
            $db = Loader::db();
            $q = 'select * from CupContentSubject where id = ?';
            $result = $db->getRow($q, array($id));
            
            if ($result) {
                $this->id           = $result['id'];
                $this->name         = $result['name'];
                $this->prettyUrl    = $result['prettyUrl'];
                $this->description  = $result['description'];
                $this->region       = $result['region'];
                $this->createdAt    = $result['createdAt'];
                $this->modifiedAt   = $result['modifiedAt'];
                
                $this->isPrimary    = false;
                $this->isSecondary  = false;
                $this->isEnabled    = false;
                
                if (isset($result['isPrimary']) && $result['isPrimary'] == 1) {
                    $this->isPrimary = true;
                }
                
                if (isset($result['isSecondary']) && $result['isSecondary'] == 1) {
                    $this->isSecondary = true;
                }

                if (isset($result['isEnabled']) && $result['isEnabled'] == 1) {
                    $this->isEnabled = true;
                }
                
                $this->exisitingResult = $result;
            }
        }
    }
    
    public static function fetchByID($id)
    {
        $object = new CupContentSubject($id);
        if ($object->id === false){
            return false;
        } else {
            return $object;
        }
    }
    
    public static function fetchByPrettyUrl($prettyUrl)
    {
        $object = new CupContentSubject();
        $object->loadByPrettyUrl($prettyUrl);
        
        if ($object->id === false){
            return false;
        } else {
            return $object;
        }
    }
    
    public static function fetchByName($name)
    {
        $object = new CupContentSubject();
        
        $db = Loader::db();
        $q = 'SELECT * FROM CupContentSubject WHERE name like ?';
        $row = $db->getRow($q, array($name));
        
        if ($row){
            return new CupContentSubject($row['id']);
        } else {
            return false;
        }
    }
    
    public function loadByID($id)
    {
        $this->id           = false;
        $this->name         = false;
        $this->description  = false;
        $this->isPrimary    = false;
        $this->isSecondary  = false;
        // SB-399 added by jbernardez 20191112
        $this->isEnabled    = false;
        $this->region       = false;
        $this->createdAt    = false;
        $this->modifiedAt   = false;
    
        $db = Loader::db();
        $q = 'select * from CupContentSubject where id = ?';
        $result = $db->getRow($q, array($requestID));
        
        if ($result) {
            $this->id               = $result['id'];
            $this->name             = $result['name'];
            $this->prettyUrl        = $result['prettyUrl'];
            $this->description      = $result['description'];
            $this->region           = $result['region'];
            $this->createdAt        = $result['createdAt'];
            $this->modifiedAt       = $result['modifiedAt'];
            
            if (isset($result['isPrimary']) && $result['isPrimary'] == 1) {
                $this->isPrimary = true;
            }
            
            if (isset($result['isSecondary']) && $result['isSecondary'] == 1) {
                $this->isSecondary = true;
            }

            // SB-399 added by jbernardez 20191112
            if (isset($result['isEnabled']) && $result['isEnabled'] == 1) {
                $this->isEnabled = true;
            }
            
            $this->exisitingResult = $result;
        } else {
            return false;
        }
    }
    
    public function loadByPrettyUrl($prettyUrl)
    {
        $this->id           = false;
        $this->name         = false;
        $this->prettyUrl    = false;
        $this->description  = false;
        $this->isPrimary    = false;
        $this->isSecondary  = false;
        // SB-399 added by jbernardez 20191112
        $this->isEnabled    = false;
        $this->region       = false;
        $this->createdAt    = false;
        $this->modifiedAt   = false;
    
        $db = Loader::db();
        $q = 'select * from CupContentSubject where prettyUrl = ?';
        $result = $db->getRow($q, array($prettyUrl));
        
        if ($result){
            
            $this->id               = $result['id'];
            $this->name             = $result['name'];
            $this->prettyUrl        = $result['prettyUrl'];
            $this->description      = $result['description'];
            $this->region           = $result['region'];
            $this->createdAt        = $result['createdAt'];
            $this->modifiedAt       = $result['modifiedAt'];
            
            if (isset($result['isPrimary']) && $result['isPrimary'] == 1){
                $this->isPrimary = true;
            }
            
            if (isset($result['isSecondary']) && $result['isSecondary'] == 1){
                $this->isSecondary = true;
            }

            // SB-399 added by jbernardez 20191112
            if (isset($result['isEnabled']) && $result['isEnabled'] == 1){
                $this->isEnabled = true;
            }
            
            $this->exisitingResult = $result;
        } else {
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
    
    public function getUrl()
    {
        $ch = Loader::helper('cup_content_html', 'cup_content');
        return $ch->url('/education/subjects/'.$this->prettyUrl);
    }
    
    public function getAssoc()
    {
        $temp = array(
            'id'            => $this->id,
            'name'          => $this->name,
            'prettyUrl'     => $this->prettyUrl,
            'description'   => $this->description,
            'isPrimary'     => $this->isPrimary,
            'isSecondary'   => $this->isSecondary,
            // SB-399 added by jbernardez 20191112
            'isEnabled'     => $this->isEnabled,
            'region'        => $this->region,
            'createdAt'     => $this->createdAt,
            'modifiedAt'    => $this->modifiedAt
        );
                
        if ($temp['id'] === false) {
            $temp['id'] = '';
        }
        
        return $temp;
    }
    
    public function setSubmitData($post)
    {
        $this->submitData = $post;
    }
    
    public function save()
    {
        if ($this->validataion()) {
        
            $this->isPrimarySave = 0;
            $this->isSecondarySave = 0;
            // SB-399 modified by jbernardez 20191112
            $this->isEnabledSave = 0;

            if ($this->isPrimary) {
                $this->isPrimarySave = 1;
            }
            
            if ($this->isSecondary) {
                $this->isSecondarySave = 1;
            }

            // SB-399 modified by jbernardez 20191112
            if ($this->isEnabled) {
                $this->isEnabledSave = 1;
            }
            
            Loader::helper('tools', 'cup_content');
            $this->prettyUrl = CupContentToolsHelper::string2prettyURL($this->name);
            
            // UPDATE
            if ($this->id > 0) {
                $this->modifiedAt = date('Y-m-d H:i:s');
                
                $db = Loader::db();
                // SB-399 modified by jbernardez 20191112
                $q = 'UPDATE CupContentSubject SET name = ?, prettyUrl = ?, description = ?, 
                    isPrimary = ?, isSecondary = ?, isEnabled = ?, region = ?, createdAt = ?, modifiedAt = ? 
                    WHERE id = ?';
                $v = array(
                    $this->name,
                    $this->prettyUrl,
                    $this->description,
                    $this->isPrimarySave,
                    $this->isSecondarySave,
                    $this->isEnabledSave,
                    $this->region,
                    $this->createdAt,
                    $this->modifiedAt,
                    $this->id
                );
                $r = $db->prepare($q);
                $res = $db->Execute($r, $v);
                if ($res) {
                    $this->afterUpdate();
                    $this->loadByID($this->id);
                    return true;
                } else {
                    return false;
                }
            // INSERT
            } else {
                return $this->saveNew();
            }
        } else {
            return false;
        }
    }
    
    protected function afterUpdate()
    {
        if (strcmp($this->name, $this->exisitingResult['name']) != 0) {
            
            $db = Loader::db();
            $q = 'UPDATE CupContentTitleSubjects SET subject = ? WHERE subject = ?';
            $v = array($this->name, $this->exisitingResult['name']);
            $r = $db->prepare($q);
            $res = $db->Execute($r, $v);
            
            $q = 'UPDATE CupContentSeriesSubjects SET subject = ? WHERE subject = ?';
            $v = array($this->name, $this->exisitingResult['name']);
            $r = $db->prepare($q);
            $res = $db->Execute($r, $v);
        }
    }
    
    public function saveNew()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->modifiedAt = $this->createdAt;
                
        $db = Loader::db();
        // SB-399 modified by jbernardez 20191112
        $q = 'INSERT INTO CupContentSubject 
            (name, prettyUrl, description, isPrimary, isSecondary, isEnabled, region, createdAt, modifiedAt) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $v = array(
            $this->name,
            $this->prettyUrl,
            $this->description,
            $this->isPrimarySave,
            $this->isSecondarySave,
            $this->isEnabledSave,
            $this->region,
            $this->createdAt,
            $this->modifiedAt
        );
        $r = $db->prepare($q);
        $res = $db->Execute($r, $v);
        
        if ($res) {
            $this->loadByID($db->Insert_ID());
            return true;
        } else {
            return false;
        }
    }
    
    public function delete()
    {
        if ($this->id > 0){
            $db = Loader::db();
            $q = "DELETE FROM CupContentSubject WHERE id = ?";
                
            $result = $db->Execute($q, array($this->id));
            if ($result) {
                return true;
            } else {
                $this->errors[] = "Error occurs when deleting this subject";
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
            $this->errors[] = 'Name is required';
        } else {
            $db = Loader::db();
            $params = array($this->name);
            $q = 'SELECT count(id) as count FROM CupContentSubject WHERE name LIKE ?';
            if ($this->id > 0){
                $q .= ' AND id <> ?';
                $params[] = $this->id;
            }
            $db_result = $db->getRow($q, $params);
        
            if ($db_result['count'] > 0){
                $this->errors[] = 'Name has been used';
            }
        }
        
        if (!$this->isPrimary && !$this->isSecondary) {
            $this->errors[] = 'Education department is required';
        }
        
        if (count($this->errors) > 0){
            return false;
        }
        
        return true;
    }
}