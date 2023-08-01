<?php
loader::library('html2text', 'institution_management');

$subject = t("Cambridge GO - Institutional setup request - [Denied]");

$bodyHTML = <<<EOF
<html>
  <body>
    Dear {$username},<br /><br />

    Thank you for your recent request to set up a school {$schoolName} as an institution in Cambridge GO.<br /><br />

    Unfortunately, Cambridge staff members cannot set up schools as this does not meet with our compliance processes. However, you can join our generic school account. Please click Join a School and use the school joining code <code>GB-CAMBRI-ABCDEF</code> to do this.<br /><br />

    If you are requesting to join a school to make use of our site licence products, we can send you special access codes that will let you do this without needing to set up a school. <br /><br />

    The Cambridge Go product management team can assist if you have any further queries. <br /><br />

    Many Thanks,<br />
    The Cambridge GO team<br /><br />

    © Cambridge University Press & Assessment, Shaftesbury Road, Cambridge, CB2 8EA, United Kingdom
  </body>
</html>
EOF;

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();