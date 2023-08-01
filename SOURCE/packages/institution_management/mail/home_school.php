<?php
loader::library('html2text', 'institution_management');

$subject = t("Cambridge GO - Institutional setup request - [Denied]");

$bodyHTML = <<<EOF
<html>
  <body>
    Dear {$username},<br /><br />

    Thank you for your recent request to set up your school {$schoolName} as an institution in Cambridge GO.<br /><br />

    Unfortunately, we have not yet been able to process your request, because we have been unable to verify your school’s details through internet search. We cross-check all school applications by internet search in order to ensure that they are genuine.<br /><br />

    If you represent a home school, it is unlikely that you will require school administration functionality in order to use our products, but please contact <a href="mailto: educs@cambridge.org">educs@cambridge.org</a> if you do.<br /><br />

    Many thanks,<br />
    The Cambridge GO team<br /><br />

    © Cambridge University Press & Assessment, Shaftesbury Road, Cambridge, CB2 8EA, United Kingdom
  </body>
</html>
EOF;

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();