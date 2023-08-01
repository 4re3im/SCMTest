<?php
/**
 * Description of announcement
 *
 * @author mabrigos
 */
class AnnouncementModel
{
    private $db;
    private $type;
    const ANNOUNCEMENT_ID = 1;
    const SURVEY_ID = 2; 

    public function __construct($type)
    {
        $this->db = Loader::db();
        $this->type = $type;
    }
    
    public function get()
    {
        $notifId = $this->type === self::ANNOUNCEMENT_ID ? self::ANNOUNCEMENT_ID : self::SURVEY_ID;
        $sql = 'SELECT * FROM announcements WHERE id = ?';
        $result = $this->db->Execute($sql, [$notifId]);
        $firstResult = $result->GetAll();
        return ($result->NumRows() > 0) ? $firstResult[0] : FALSE;
    }
    
    public function save($data)
    {
        $enableDefault = 0;
        $date = date('Y-m-d H:i:s');
        $content = $data['content'];
        $isActive = $data['isActive'];
        $meta = $this->generateMetadata($data);
        $notifId = $this->type === self::ANNOUNCEMENT_ID ? self::ANNOUNCEMENT_ID : self::SURVEY_ID;

        if (isset($data['enableDefault']) && $data['defaultMsg'] !== "") {
            $enableDefault = 1;
        }

        $sql = "UPDATE announcements SET content = ?, is_active = ?, metadata = cast(? AS JSON), ";
        $sql .= "is_default_active = ?, updated_at = ? WHERE id = ?";
        $this->db->Execute($sql, array(
            $content,
            (int)$isActive,
            json_encode($meta),
            $enableDefault,
            $date,
            $notifId
        ));
    }

    public function generateMetadata($data) {
        $country = trim($data['countryArray']) === '' ? 'ALL' : trim($data['countryArray']);
        if ($this->type === self::ANNOUNCEMENT_ID) {
            return array(
                'country' => $country,
                'default_content' => $data['defaultMsg']
            );
        }
        return array(
            'country' => $country,
            'cookie' => (int)$data['cookie'],
            'role' => $data['role']
        );
    }
}
