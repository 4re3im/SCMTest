<?php  defined('C5_EXECUTE') or die(_("Access Denied."));


$templateObject = $code_detail["template"];
$replacements = $code_detail["replacements"];


$subject = $templateObject->title;
$subject = strtr($subject, $replacements);

$bodyHTML = $templateObject->content_html;
$bodyHTML = strtr($bodyHTML, $replacements);

$body = $templateObject->content_text;
$body = strtr($body, $replacements);

?>