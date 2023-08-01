<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('announcement', 'go_notifications');
class DashboardGoNotificationsSurveyController extends DashboardBaseController 
{
    public $helpers = array('form');
    protected $pkgHandle = 'go_notifications';
    private $notifType = 2;

    public function view() 
    {
        $html = Loader::helper('html');
        $this->addFooterItem(
            '<script type="text/javascript" src="' .
            (string)$html->javascript('announcements.js', $this->pkgHandle)->href . '?v=1.3"></script>'
        );

        $announcementModel = new AnnouncementModel($this->notifType);
        $surveyData = $announcementModel->get();
        $isActive = $surveyData['is_active'];
        $url = $surveyData['content'];
        $metadata = json_decode($surveyData['metadata']);

        $this->set('isActive', $isActive);
        $this->set('url', $url);
        $this->set('country', trim($metadata->country));
        $this->set('cookie', $metadata->cookie);
        $this->set('role', $metadata->role);
    }

    // SB-997 added by mabrigos 01042022
    public function save()
    {
        $announcementModel = new AnnouncementModel($this->notifType);
        if ($this->isPost()) {
            $announcementModel->save($this->post());
        }
    }
}