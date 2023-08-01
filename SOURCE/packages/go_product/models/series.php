<?php
/*
 * Product Model
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 26, 2015
 */

class Series extends Model{

    var $db;
    
    var $product_tbl = "CMS_Series";
    
    public function __construct() {
        
        $this->db = Loader::db();
        
    }

    public function __destruct() { }
    
    public function getSeriesBySubject($subject_id){
        
        $inner_sql = "SELECT ID FROM CMS_Subject WHERE ID=$subject_id OR ParentID=$subject_id";

        $sql  = "SELECT s.ID, SeriesName as Name, CoverMedium, 'series' as RecType ";
                
        $sql .= "FROM CMS_Series_Subject cs LEFT JOIN CMS_Series s ON SeriesID=s.ID ";
        
        $sql .= "WHERE SubjectID IN ($inner_sql) AND s.Active='Y' ";
        
        $sql .= "UNION ";
        
        $sql .= "SELECT p.ID, Name, CoverMedium, 'product' as RecType ";
        
        $sql .= "FROM CMS_Product_Subject ps LEFT JOIN CMS_Product p ON ProductID=p.ID ";
        
        $sql .= "WHERE SubjectID IN( $inner_sql ) AND p.Active='Y' ";
        
         $sql .= "AND p.ID NOT IN ( SELECT DISTINCT ProductID FROM CMS_Product_Series ) ";
                
        $sql .= "ORDER BY RecType DESC,Name ASC";
        
        $series = $this->db->getAll($sql);
        
        return $series;
        
    }
    
}
?>