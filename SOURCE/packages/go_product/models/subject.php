<?php
/*
 * Product Model
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 26, 2015
 */

class Subject extends Model{

    var $db;
    
    var $product_tbl = "CMS_Subject";
    
    public function __construct() {
        
        $this->db = Loader::db();
        
    }

    public function __destruct() { }
    
    public function getActiveSubjects(){
        
        $sql  = "SELECT c.ID, c.SubjectName, p.SubjectName as ParentSubjectName, p.HeaderSortOrder,";
        
        $sql .= "IF(p.SubjectName IN('Maths','English','Humanities','Study Guides'),CONCAT(p.SubjectName,': ',c.SubjectName),c.SubjectName) as DisplayName,";
        
        $sql .= "c.ID FROM $this->product_tbl c LEFT JOIN $this->product_tbl p ON c.ParentID=p.ID ";
        
        $sql .= "WHERE p.Active='Y' AND p.Header='Y' ORDER BY p.HeaderSortOrder, c.SubjectName";
        
        $subjects = $this->db->getAll($sql);
        
        return $subjects;
        
    }
    
}
?>