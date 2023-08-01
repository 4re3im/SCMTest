<?php
/**
 * Description of go_dashboard
 *
 * @author paulbalila
 */
class GoDashboardModel
{
    private $db;
    
    public function __construct()
    {
        $this->db = Loader::db();
    }
    
    public function get_titles($param = FALSE)
    {
        // ANZGO-3764 modified by jbernardez 20181011
        $sql = "SELECT cct.`id`, cct.`displayName` FROM `CupContentTitle` AS cct WHERE `displayName` LIKE ?";
        $result = $this->db->GetAll($sql, array("%$param%"));
        $search_results = array();
        foreach ($result as $r) {
            $search_results[] = array('value'=>$r['id'],'label'=>$r['displayName']);
        }
        return (count($search_results) > 0) ? $search_results : array('No matches found... :(');
    }
    
    public function get_notifications($count,$limit = FALSE, $search = FALSE, $search_param = FALSE, $test = FALSE)
    {
        $sql = "SELECT * FROM `CupGoNotifications` AS cct ";
        // ANZGO-3764 modified by jbernardez 20181011
        $params = [];

        if($search) {
            switch ($search) {
                case 'nDate':
                    $sql .= "WHERE (DATE_FORMAT(cct.`nDate`,'%Y-%m-%d') = STR_TO_DATE(?,'%M %d,%Y') 
                        OR DATE_FORMAT(cct.`dateCreated`,'%Y-%m-%d') = STR_TO_DATE(?,'%M %d,%Y') 
                        OR DATE_FORMAT(cct.`dateModified`,'%Y-%m-%d') = STR_TO_DATE(?,'%M %d,%Y'))";

                    // ANZGO-3764 modified by jbernardez 20181011
                    array_push($params, $search_param, $search_param, $search_param);

                    break;

                default:
                    $sql .= "WHERE cct.? LIKE ? ";
                    // ANZGO-3764 modified by jbernardez 20181011
                    array_push($params, $search, "%$search_param%");
                    break;
            }
            
        }
        
        $sql .= "ORDER BY cct.`dateModified` DESC ";
        
        if (!$search) {
            if ($limit) {
                $sql .= "LIMIT " . $count . ", " . $limit;
            } else {
                $sql .= "LIMIT " . $count;
            }
        }

        if($test) {
            $result = $sql;
        } else {

            // ANZGO-3764 modified by jbernardez 20181011
            $result = $this->db->GetAll($sql, $params);
        }
        return $result;
    }
    
    public function get_notification($id)
    {
        $sql = "SELECT * FROM `CupGoNotifications` WHERE `nID` = ?";
        
        return $this->db->GetRow($sql, array($id));
    }
    
    public function get_title_notifications($ids)
    {
        if($ids != "0") {
            $id_arr = explode("|", $ids);
            foreach (array_keys($id_arr, "") as $key) {
                unset($id_arr[$key]);
            }

            // ANZGO-3764 modified by jbernardez 20181011
            $sql = "SELECT cct.`id`,cct.`displayName` FROM `CupContentTitle` AS cct WHERE cct.`id` IN (?)";
            return $this->db->GetAll($sql, array(implode("','", $id_arr)));
        } else {
            return $ids;
        }
    }
    
    public function get_notif_count()
    {
        $sql = "SELECT COUNT(*) AS total FROM `CupGoNotifications`";
        $count = $this->db->GetRow($sql);
        return $count['total'];
    }
    
    public function insert_notification($notif)
    {
        unset($notif['nType']);
        $sql = "INSERT INTO `CupGoNotifications` (nTitle,nDate,nContent,nStatus,dateCreated,linkedTitles,dateModified) VALUES (?,STR_TO_DATE(?,'%Y-%m-%d %H:%i %s'),?,?,STR_TO_DATE(?,'%Y-%m-%d %H:%i %s'),?,STR_TO_DATE(?,'%Y-%m-%d %H:%i %s'))";
        $sql_params = array(
            $notif['nTitle'],
            $notif['nDate'],
            $notif['nContent'],
            $notif['nStatus'],
            $notif['dateCreated'],
            $notif['linkedTitles'],
            $notif['dateModified']
        );
        return $this->db->Execute($sql, $sql_params);
    }
    
    public function update_notification($id,$notif)
    {
        unset($notif['nType']);
        $index = 0;
        
        $sql = "UPDATE `CupGoNotifications` SET ";
        foreach ($notif as $key => $value) {
            switch ($key) {
                case 'nContent':
                    $value = "'" . htmlspecialchars($value) . "'";
                    break;
                case 'nDate':
                case 'dateModified':
                    $value = "STR_TO_DATE('" . $value . "','%Y-%m-%d %H:%i %s') ";
                    break;
                default:
                    $value = "'" . $value . "'";
                    break;
            }
            
            if($index == (count($notif) - 1)) {
                $sql .= $key . " = " . $value . " ";
            } else {
                $sql .= $key . " = " . $value . ", ";
            }
            $index++;
        }

        // ANZGO-3764 modified by jbernardez 20181011
        $sql .= "WHERE `nID` = ?";
        return $this->db->Execute($sql, array($id));
    }
    
    public function delete_notifications($ids)
    {
        // ANZGO-3764 modified by jbernardez 20181011
        $sql = "DELETE FROM `CupGoNotifications` WHERE `nID` IN (?)";
        return $this->db->Execute($sql, array(implode(",", $ids)));
    }
}
