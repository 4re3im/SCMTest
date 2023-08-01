<?php
loader::library('html2text', 'institution_management');

$subject = t("Cambridge GO - Institutional setup request - [Denied]");

$bodyHTML = <<<EOF
<html>
  <body>
    Dear {$username},<br /><br />

    Thank you for your recent request to set up your school {$schoolName} as an institution in Cambridge GO.<br /><br />

    Having looked at our records, we can see that your institution has already been registered by another teacher. If you click Join a School in GO and enter your school’s name, city and country, you will be able to join directly.<br /><br />

    We recommend that you use your school email address if you have one, as this will allow us to process your request more quickly.<br /><br />

    Many thanks,<br />
    The Cambridge GO team<br /><br />

    © Cambridge University Press & Assessment, Shaftesbury Road, Cambridge, CB2 8EA, United Kingdom
  </body>
</html>
EOF;

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();
