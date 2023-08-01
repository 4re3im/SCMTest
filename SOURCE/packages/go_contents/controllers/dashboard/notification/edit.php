<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Description of edit
 *
 * @author paulbalila
 */
Loader::model('go_dashboard','go_contents');
Loader::helper('notification_table_nav','go_contents');
class DashboardNotificationEditController extends Controller {
    private $dm;
    public function on_start() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('jquery.ui.css'));
        $this->addHeaderItem($html->javascript('jquery.ui.js'));
        $this->addHeaderItem($html->javascript('go-dashboard.js','go_theme'));
        $this->addHeaderItem($html->css('go-dashboard.css','go_theme'));
        $this->dm = new GoDashboardModel();
    }
    
    public function view($id = FALSE) {
        $v = View::getInstance();
        if(!$id) {
            header('Location: ' . $v->url('/dashboard/notification/actions/?get_edit=' . TRUE));
        } else {
            $notif = $this->dm->get_notification($id);
            $titles = $this->dm->get_title_notifications($notif['linkedTitles']);
            $notif['titles'] = $titles;
            $notif['nContent'] = htmlspecialchars_decode($notif['nContent']);
            $this->set('notif',$notif);
        }
    }
    
    public function do_edit($id) {
        $v = View::getInstance();
        $notif = $_POST['notif'];
        $notif['dateModified'] = date('Y-m-d H:i s');
        $notif['nDate'] = date('Y-m-d H:i s',  strtotime($notif['nDate']));
        $notif['nContent'] = htmlspecialchars($notif['nContent']);
                
        if($notif['nType'] == "0") {
            $notif['linkedTitles'] = "0";
        } else {
            $notif['linkedTitles'] = "|" . implode("|", $notif['linkedTitles']) . "|";
        }
        $flag = $this->dm->update_notification($id, $notif);
        $msg = ($flag) ? $notif['nTitle'] : 0;
        header('Location: ' . $v->url('/dashboard/notification/actions/?edit=' . $msg));
        exit;
    }
}
