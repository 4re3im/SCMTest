<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('subject/model', 'cup_content');
class CupContentSubjectList extends DatabaseItemList
{ 
    protected $queryCreated = 0;
    protected $attributeFilters = array();
    // SB- 399 modified by jbernardez 20191112
    protected $autoSortColumns = array(
        'name',
        'prettyUrl',
        'isPrimary',
        'isSecondary',
        'isEnabled',
        'modifiedAt',
        'createdAt'
    );
    protected $itemsPerPage = 10;
    protected $attributeClass = ''; //'CoreCommerceProductAttributeKey';
    
    public function get($itemsToGet = 0, $offset = 0)
    {
        $list = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, $offset);

        foreach ($r as $row) {
            $obj = new CupContentSubject($row['id']);
            $obj->prspDisplayOrder = $row['prspDisplayOrder'];
            $list[] = $obj;
        }
        return $list;
    }
    
    public function getTotal()
    {
        $this->createQuery();
        return parent::getTotal();
    }
    
    protected function setBaseQuery()
    {
        $this->setQuery('SELECT sj.* from CupContentSubject sj');
    }
    
    protected function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }
    
    public function filterByName($name, $comparison = '=')
    {
        $this->filter('sj.name',$name,$comparison);
    }
    
    public function filterByDepartment($department)
    {
        if (strtolower($department) == 'primary') {
            $this->filter('sj.isPrimary', 1, "=");
        } elseif (strtolower($department) == 'secondary') {
            $this->filter('sj.isSecondary', 1, "=");
        }
    }
    
    public function filterByRegion($region = 'AU')
    {
        if (strcmp($region,'AU') == 0) {
            $this->filter(false, "sj.region in ('ALL', 'AU')");
        } else {
            $this->filter(false, "sj.region in ('ALL', 'NZ')");
        }
    }

    public function filterWithAvailableTitleSamplePage($region = false)
    {
        if ($region) {
            if (strcmp($region, 'All Australia') == 0){
                $region = 'Australia';
            }

            $this->filter(false, 'sj.name IN
                    (SELECT DISTINCT(ccts.subject) FROM CupContentTitleSubjects ccts
                        WHERE
                        ccts.titleID IN (SELECT DISTINCT(titleID) FROM CupContentTitleSamplePages WHERE is_page_proof = 1)
                            AND
                        ccts.titleID IN (SELECT DISTINCT(id) FROM CupContentTitle WHERE regions like  "%['.$region.']%")
                    )');

        } else {
            $this->filter(false, 'sj.name IN
                    (SELECT DISTINCT(ccts.subject) FROM CupContentTitleSubjects ccts
                        WHERE
                        ccts.titleID IN (SELECT DISTINCT(titleID) FROM CupContentTitleSamplePages WHERE is_page_proof = 1)
                    )');
        }
    }


    public function filterWithAvailableTitleInspectionCopy()
    {
        $this->filter(false, 'sj.name IN 
                                (SELECT DISTINCT(ccts.subject) FROM CupContentTitleSubjects ccts
                                    WHERE 
                                    ccts.titleID IN (SELECT DISTINCT(id) FROM CupContentTitle WHERE hasInspectionCopy = 1)
                                )');
    }
    
    // Filters by "keywords"
    // SB-401 / SB-399 - modified by jbernardez 20191119
    public function filterByKeywords($keywords, $isEnabled = false)
    {
        $db = Loader::db();
        $qkeywords = $db->quote('%' . $keywords . '%');

        // SB-401 / SB-399 - added by jbernardez 20191119
        $isEnabledFilter = '';
        if ($isEnabled) {
            $isEnabledFilter = ' AND isEnabled = 1 ';
        }
        
        // SB-401 / SB-399 - added by jbernardez 20191119
        $this->filter(false, '( sj.name like ' . $qkeywords . ' or sj.description like ' . $qkeywords . ')' . $isEnabledFilter);
    }

    public static function getExclusionList()
    {
        return array(
            "primary" => array(

            ),
            "secondary" => array(
                "Outdoor and Environmental Studies"
            )
        );
    }
}