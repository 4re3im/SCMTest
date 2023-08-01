<?php 
header("Access-Control-Allow-Origin: http://go-dev.aws.cambridge.edu.au");
header("Access-Control-Allow-Credentials: TRUE");
header("Access-Control-Allow-Methods: ACL, CANCELUPLOAD, CHECKIN, CHECKOUT, COPY, DELETE, GET, HEAD, LOCK, MKCALENDAR, MKCOL, MOVE, OPTIONS, POST, PROPFIND, PROPPATCH, PUT, REPORT, SEARCH, UNCHECKOUT, UNLOCK, UPDATE, VERSION-CONTROL");
header("Access-Control-Allow-Headers: Overwrite, Destination, Content-Type, Depth, User-Agent, Translate, Range, Content-Range, Timeout, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control, Location, Lock-Token, If");
header("Access-Control-Expose-Headers: DAV, content-length, Allow");

session_start();

$_SESSION['form-accesscode_s1'] = 
$_SESSION['form-accesscode_s2'] = 
$_SESSION['form-accesscode_s3'] = 
$_SESSION['form-accesscode_s4'] = "";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        var contactusmore=false;
    </script>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Activation Page | Cambridge Australia</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700,600' 
        rel='stylesheet' type='text/css'>

<?php
print $this->controller->outputHeaderItems();
?>
</head>

<body>   
    <div style="width: 1px;height: 1px;">
        <?php
        include(DIR_PACKAGES . '/go_theme/elements/svg-defs.svg');
        ?>
    </div>