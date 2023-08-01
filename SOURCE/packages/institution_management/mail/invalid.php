<?php
loader::library('html2text', 'institution_management');

$supportLink = 'https://cambridgegohelp.cambridge.org/hc/articles/4413380649234-Adding-teachers-to-your-school-s-account-';
$contactLink = 'https://cambridgegohelp.cambridge.org/hc/requests/new';
$subject = t("Cambridge GO - Institutional setup request - [Denied]");

$bodyHTML = <<<EOF
<html>
<body>
Dear {$username},<br /><br />
Attention required: Your request to set up your school in Cambridge GO<br /><br />

Thank you for your recent request to set up your school {$schoolName} as an institution in Cambridge GO.<br /><br />
We have evaluated your request and unfortunately we have been unable to process it. <b>Please can you resend your application</b>, making sure to:<br />
<ul>
  <li>Use a school email address, rather than a personal email address.</li>
  <li>Use a valid website address for the school.</li>
  <li>Apply on behalf of a single school, rather than a chain of schools.</li>
</ul>

If you have any queries about how to register your school, please visit our <a href='{$supportLink}'>support pages</a>, or <a href='{$contactLink}'>contact us</a> if you are unable to meet these guidelines (for example, you need to change your email address) and we will work with you to register your school.<br /> <br />

Best wishes,<br />
The Cambridge GO team<br /><br />

© Cambridge University Press & Assessment, Shaftesbury Road, Cambridge, CB2 8EA, United Kingdom
</body>
</html>
EOF;

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();