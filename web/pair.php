<?
if(!isset($_REQUEST['code']) || !isset($_REQUEST['id'])) {
    echo "error";
} else {
    require("inc/settings.inc.php");
    require("inc/devices.inc.php");
    $name = "";
    if(isset($_REQUEST['name'])) {
        $name = $_REQUEST['name'];
    }
    if(pair($_REQUEST['code'], $_REQUEST['id'], $name)) {
        echo "success";
    } else {
        echo "fail";
    }
}
