 <?php
/**
 * createJsonInactiveUsersLive_NoSpace.php
 *
 * Json FIle Generator [Formatted version]
 *
 * Author     mtanada
 * Created    2019-07-01
 *
 */

// Get data from a File
$file = fopen('inactiveLive_2.csv', 'r');

$data = array();

while ($row = fgetcsv($file)) {
   $data[] = $row;
}
fclose($file);

array_shift($data);

$text = '[';
foreach ($data as $key => $value) {
   if ($value[1] === 'compoundHashedPassword' ) {
   $text .= '{"lang":"en","UID":"go' . $value[0]. '","loginIDs":{"emails":"'. $value[0]. '@anonymous.com"},"profile":{"firstName":"Anonymous","lastName":"Anonymous","email":"' . $value[0] . '@anonymous.com"},"emails":"' . $value[0] . '@anonymous.com","data":{"systemIDs":[{"idType":"GO","idValue":"' . $value[0] . '"}],"eduelt":{"instituteRole":[{"institute":"","isVerified":false,"role":"student","title":null}]}},"isActive":false,"isVerified":true,"isRegistered":true,"password":{"compoundHashedPassword":"'.$value[2].'"}},'."\n";
   } else {
     $text .= '{"lang":"en","UID":"go'.$value[0].'","loginIDs":{"emails":"'.$value[0].'@anonymous.com"},"profile":{"firstName":"Anonymous","lastName":"Anonymous","email":"'.$value[0].'@anonymous.com"},"emails":"'.$value[0].'@anonymous.com","data":{"systemIDs":[{"idType":"GO","idValue":"'.$value[0].'"}],"eduelt":{"instituteRole":[{"institute":"","isVerified":false,"role":"student","title":null}]}},"isActive":false,"isVerified":true,"isRegistered":true,"password":{"hashedPassword":"'.$value[2].'","hashSettings":{"algorithm":"md5","salt":"ZX3D56","format":"$password::$salt"}}},'."\n";
   }
}
$text = substr($text, 0, -2);
$text .= ']';

$file2 = fopen("jsonInactiveUsersToMigrate_2.json","w");

fwrite($file2, $text);

fclose($file2);