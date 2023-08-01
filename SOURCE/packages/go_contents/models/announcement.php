<?php
/**
 * Description of announcement
 *
 * @author mabrigos
 */
class AnnouncementModel
{
    private $db;
    
    public function __construct()
    {
        $this->db = Loader::db();
    }
    
    public function get()
    {
        $sql = 'SELECT * FROM announcements order by id limit 1';
        $result = $this->db->Execute($sql);
        $firstResult = $result->GetAll();
        return ($result->NumRows() > 0) ? $firstResult[0] : FALSE;
    }
    
    public function save($data)
    {
        $actMode = $data['announcementMode'];
        $bannMsg = $data['bannerMessage'];
        $country = trim($data['countryArray']) === '' ? 'ALL' : trim($data['countryArray']);
        $meta = array(
            'country' => $country,
            'default_content'=> $data['defaultMsg']
        );

        if (!$data['enableDefault'] || $data['defaultMsg'] === "") {
            $enableDefault = 0;
        } else {
            $enableDefault = 1;
        }

        $date = date('Y-m-d H:i:s');
        $sql = "UPDATE announcements SET content = ?, is_active = ?, metadata = cast(? AS JSON), ";
        $sql .= "is_default_active = ?, updated_at = ? WHERE id = 1";
        $result = $this->db->Execute($sql, array($bannMsg, (int)$actMode, json_encode($meta), $enableDefault, $date));
    }
}
