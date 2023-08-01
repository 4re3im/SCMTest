<?php
loader::library('html2text', 'institution_management');

$supportLink = 'https://cambridgegohelp.cambridge.org/hc/articles/4413380649234-Adding-teachers-to-your-school-s-account-';
$youtubeVideosLink = 'https://www.youtube.com/playlist?list=PL2HgNIO5uPKBHFSHSXC_lI7sFZ-IeuBiK';
$subject = t("Cambridge GO - Institutional setup request - [Denied]");

$bodyHTML = <<<EOF
<html>
  <body>
    Dear {$username},<br /><br />

    Thank you for your recent request to set up your business {$schoolName} as an institution in Cambridge GO.<br /><br />

    For compliance reasons, only educational institutions can be set up as schools in Cambridge GO. If you are requesting to join a school to make use of our site licence products in customer demonstrations, we can send you special access codes that will let you do this. Please ask your Sales contact to request this from the Cambridge GO product management team. <br /><br />

    If you need to show customers the functionality available to admins, we suggest you use our <a href="{$supportLink}">support pages</a> and <a href="{$youtubeVideosLink}">YouTube videos</a>.<br /><br />

    Many Thanks,<br />
    The Cambridge GO team<br /><br />

    © Cambridge University Press & Assessment, Shaftesbury Road, Cambridge, CB2 8EA, United Kingdom
  </body>
</html>
EOF;

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();