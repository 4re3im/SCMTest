<?php

/**
 * Header Heading Content for Education Theme
 */

define('DEFAULT_LOCALE', 'DEFAULT_LOCALE');
define('AUSTRALIA', 'en_AU');
define('NEW_ZEALAND', 'en_NZ');

$countryCode = $_SERVER["HTTP_CF_IPCOUNTRY"];
if (!isset($_SESSION['locale_change'])) {
    if (empty($countryCode) || $countryCode == 'AU') {
        $currentLocation = AUSTRALIA;
    } elseif ($countryCode == 'NZ') {
        $currentLocation = NEW_ZEALAND;
    } else {
        $currentLocation = $countryCode;
    }
    $_SESSION[DEFAULT_LOCALE] = $currentLocation;
}
?>

<div class="frame_content">
    <div class="btn_location">
        Location:
        <select id="global_current_locate">
            <option value="en_AU" <?php echo ($_SESSION[DEFAULT_LOCALE] == AUSTRALIA) ? "selected" : ""; ?>>
                Australia
            </option>
            <option value="en_NZ" <?php echo ($_SESSION[DEFAULT_LOCALE] == NEW_ZEALAND) ? "selected" : ""; ?>>
                New Zealand
            </option>
            <option value="<?php echo $countryCode; ?>"
                <?php echo ($_SESSION[DEFAULT_LOCALE] != NEW_ZEALAND && $_SESSION[DEFAULT_LOCALE] != AUSTRALIA) ?
                        "selected" : ""; ?>>
                Rest of the world
            </option>
        </select>
        <input type="hidden" id="server_geoip" value="<?php echo $_SERVER["HTTP_CF_IPCOUNTRY"]; ?>"/>
        <input type="hidden" id="session_geoip" value="<?php echo $_SESSION[DEFAULT_LOCALE]; ?>"/>
    </div>
    <div class="clr empty"></div>
</div>
<div style="clear: both; width:0px; height:0px;">&nbsp;</div>
