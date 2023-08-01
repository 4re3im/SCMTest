<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
loader::library('html2text', 'cup_competition');

$eventAssoc = $entryObj->getEventObject()->getAssoc();
$entryAssoc = $entryObj->getAssoc();

$replace = array(
				'%%FIRST_NAME%%'	=> $entryAssoc['first_name'],
				'%%LAST_NAME%%'		=> $entryAssoc['last_name'],
				'%%EMAIL%%' 		=> $entryAssoc['email'],
				'%%EVENT_NAME%%'	=> $eventAssoc['name'],
				'%%EVENT_START_DATE%%'	=> date('d/M/Y', strtotime($eventAssoc['start_time'])),
				'%%EVENT_END_DATE%%'	=> date('d/M/Y', strtotime($eventAssoc['end_time'])),
				'%%EVENT_START_DATETIME%%'	=> date('d/M/Y h:i:s A', strtotime($eventAssoc['start_time'])),
				'%%EVENT_END_DATETIME%%'	=> date('d/M/Y h:i:s A', strtotime($eventAssoc['end_time'])),
			);

$subject = strtr($eventAssoc['config']['APPROVAL_EMAIL_SUBJECT'], $replace);

$bodyHTML = strtr($eventAssoc['config']['APPROVAL_EMAIL_BODY'], $replace);

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();
?>