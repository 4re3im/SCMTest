<?php
defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE . " " . t("Dynamic English Skills trial");
$body = t(" Hi, 

	Trial request. Details below.

	First name : %s
	Last name : %s
	Email address : %s
	Confirm email : %s
	Comments : %s

	**************************************************
	End of message

", $firstName, $lastName, $email, $confirmEmail, $comment);