
<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
loader::library('html2text', 'cup_competition');

$subject = SITE." - Contact Us";

$bodyHTML = <<<EOF
<html>
<body>
    Salutation: {$salutation} <br />
    First Name: {$firstName} <br />
    Last Name: {$lastName} <br />
    Position: {$position} <br />
    School: {$school} <br />
    City: {$city} <br />
    Country: {$country} <br />
    Postcode: {$postCode} <br />
    Phone Number: {$phone} <br />
    Email: {$email} <br />
    Query: {$query} <br />
    Subject: {$subject} <br />
    Level: {$level} <br />
    Will Receive Email: {$noEmail} <br />
</body>
</html>
EOF;

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();
