<?php defined('C5_EXECUTE') or die("Access Denied.");

// SB-972 modified by mabrigos 03-11-21
Loader::model('announcement', 'go_contents');
class DashboardNotificationAnnounceController extends DashboardBaseController 
{
    public $helpers = array('form');
    protected $pkgHandle = 'go_contents';

    public function view() 
    {
        $html = Loader::helper('html');
        $this->addFooterItem(
            '<script type="text/javascript" src="' .
            (string)$html->javascript('announcements.js', $this->pkgHandle)->href . '?v=1.2"></script>'
        );

        $announcementModel = new AnnouncementModel();
        $announcementData = $announcementModel->get();
        $announcementMode = $announcementData['is_active'];
        $bannerMessage = $announcementData['content'];
        $defaultMode = $announcementData['is_default_active'];
        $metadata = json_decode($announcementData['metadata']);

        $this->set('announcementMode', $announcementMode);
        $this->set('bannerMessage', $bannerMessage);
        $this->set('defaultMode', $defaultMode);
        $this->set('country', trim($metadata->country));
        $this->set('default_content', $metadata->default_content);
    }

    // SB-997 added by mabrigos 01042022
    public function save()
    {
        $announcementModel = new AnnouncementModel();
        if ($this->isPost()) {
            $announcementModel->save($this->post());
        }
    }
}