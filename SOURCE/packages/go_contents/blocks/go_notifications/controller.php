<?php

/**
 * Description of GoNotificationsBlockController
 *
 * @author paulbalila
 */
defined('C5_EXECUTE') or die(_("Access Denied."));

class GoNotificationsBlockController extends BlockController {

    protected $btTable = "btGoNotifications";
    protected $btInterfaceWidth = "1000";
    protected $btInterfaceHeight = "1000";
    
    protected $posted = array();

    public function getBlockTypeName() {
        return t('Go Notifications');
    }

    public function getBlockTypeDescription() {
        return t('Displays Cambridge GO notifications.');
    }
    
    public function add() {
        
    }
    
    public function view() {
        $jh = Loader::helper('json');
        $this->set('content', $jh->decode($this->content));
    }
    
    public function save($data) {
        $jh = Loader::helper('json');
        $args['content'] = $jh->encode($data['content']);
        parent::save($args);
    }
}
