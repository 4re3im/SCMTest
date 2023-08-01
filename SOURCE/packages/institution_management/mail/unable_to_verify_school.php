<?php
loader::library('html2text', 'institution_management');

$subject = t("Cambridge GO - Institutional setup request - [Denied]");

$bodyHTML = <<<EOF
<html>
  <body>
    Dear {$username},<br /><br />

    Thank you for your recent request to set up your school {$schoolName} as an institution in Cambridge GO.<br /><br />

    Unfortunately, we have not yet been able to process your request, because we have been unable to verify your school’s details through internet search. We cross-check all school applications by internet search in order to ensure that they are genuine.<br /><br />

    Please contact <a href="mailto: educs@cambridge.org">educs@cambridge.org</a> with an accurate school address and website details so that we can proceed. If you are teaching at a chain school, please use the name and address of your campus.<br /><br />

    Many thanks,<br />
    The Cambridge GO team<br /><br />

    © Cambridge University Press & Assessment, Shaftesbury Road, Cambridge, CB2 8EA, United Kingdom
  </body>
</html>
EOF;

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();