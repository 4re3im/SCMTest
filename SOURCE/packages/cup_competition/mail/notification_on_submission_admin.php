<?php  defined('C5_EXECUTE') or die(_("Access Denied."));


$event_name = $eventObj->name;
$first_name = $entryObj->first_name;
$last_name = $entryObj->last_name;
$user_email = $entryObj->email;


$subject = SITE." - {$eventObj->name} Form Entry Notification";

$body = <<<EOF
New competition entry for "{$event_name}" has been received.

First Name: {$first_name}
Last Name: {$last_name}
Email: {$user_email}

EOF;

?>