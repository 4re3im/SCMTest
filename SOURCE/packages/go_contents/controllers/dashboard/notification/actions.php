<?php
/**
 * Description of controller
 *
 * @author paulbalila
 */
Loader::model('go_dashboard','go_contents');
Loader::helper('notification_table_nav','go_contents');
class DashboardNotificationActionsController extends Controller {
    private $dm;
    private $widget;
    private $default_table_count = 10;
    public function on_start() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('jquery.ui.css'));
        $this->addHeaderItem($html->javascript('jquery.ui.js'));
        $this->addHeaderItem($html->javascript('go-dashboard.js','go_theme'));
        $this->addHeaderItem($html->css('go-dashboard.css','go_theme'));
        $this->dm = new GoDashboardModel();
        $this->widget = new NotificationTableNavHelper();
    }
    
    public function view() {
        $notifications = $this->dm->get_notifications($this->default_table_count);
        $notif_count = $this->dm->get_notif_count();
        $this->set('notif',$notifications);
        $this->set('table_widget',  $this->widget->render($notif_count, $this->default_table_count));
        if($status = $_GET['add']) {
            $msg = ($status) ? 'Notification added.' : 'Notification not added.';
            $this->set('status',$msg);
        }
        
        if($status = $_GET['get_edit']) {
            $msg = ($status) ? 'Please select a notification to edit.' : '';
            $this->set('status',$msg);
        }
        
        if($status = $_GET['edit']) {
            $msg = ($status) ? 'Edit successful in ' . $status : '';
            $this->set('status',$msg);
        }
    }
    
    public function add() {
        $v = View::getInstance();
        $notif = $_POST['notif'];
        $notif['nStatus'] = 1;
        $notif['dateCreated'] = date('Y-m-d H:i s');
        $notif['nDate'] = date('Y-m-d H:i s',  strtotime($notif['nDate']));
        $notif['nContent'] = htmlspecialchars($notif['nContent']);
        $notif['dateModified'] = $notif['dateCreated'];
                
        if($notif['nType'] == "0") {
            $notif['linkedTitles'] = "0";
        } else {
            $notif['linkedTitles'] = "|" . implode("|", $notif['linkedTitles']) . "|";
        }
        $flag = $this->dm->insert_notification($notif);
        $msg = ($flag) ? 1 : 0;
        
        header('Location: ' . $v->url('/dashboard/notification/actions/?add=' . $msg));
    }
       
    public function search($type) {
        $data = $_POST['data'];
        $notif_count = $this->dm->get_notif_count();
        $matches = $this->dm->get_notifications($notif_count, FALSE, $type, $data);
        echo $this->widget->refresh_table($matches);
        // echo $matches;
        exit;
    }
    
    public function refresh_nav($page) {
        $notif_count = $this->dm->get_notif_count();
        if($paginate = $_POST['page']) {
            $this->default_table_count = $paginate;
            $page = 1;
        }
        echo $this->widget->render($notif_count, $this->default_table_count, $page);
        exit;
    }
    
    public function get_titles() {
        $param = $_GET['term'];
        $search_results = $this->dm->get_titles($param);
        echo json_encode($search_results);
        exit;
    }
        
    public function paginate() {
        $count = $_POST['data'];
        $notifications = $this->dm->get_notifications($count);
        echo $this->widget->refresh_table($notifications);
        // echo $notifications;
        exit;
    }
    
    public function navigate($page) {
        if($pagination = $_POST['pagination']) {
            $this->default_table_count = $pagination;
        }
        
        $notifications = $this->dm->get_notifications((($page * $pagination) - $this->default_table_count), $this->default_table_count,FALSE,FALSE,FALSE);
        echo $this->widget->refresh_table($notifications);
        // echo $notifications;
        exit;
    }
    
    public function delete() {
        $data = $_POST['tick-notifs'];
        $page = $_POST['page'];
        $flag = $this->dm->delete_notifications($data);
        if($flag) {
            $notifications = $this->dm->get_notifications($page);
            echo $this->widget->refresh_table($notifications);
        }
        exit;
    }
}
