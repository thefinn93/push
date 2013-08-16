<?
require("inc/settings.inc.php");
session_start();
if(!isset($_SESSION['user'])) {
    $_SESSION['continue_after_signin'] = "devices";
    header("Location: signin");
    die("Please <a href=\"signin\">sign in</a> before continuing");
}
require("inc/devices.inc.php");
require("inc/users.inc.php");
require("inc/nocsrf.inc.php");
if(isset($_REQUEST['action'])) {
    if(NoCSRF::check('nocsrf', $_REQUEST)) {
        if($_REQUEST['action'] == "makecode") {
            echo json_encode(array("nocsrf" => NoCSRF::generate('nocsrf'), "result" => makecode(userid($_SESSION['user']))));
        } elseif($_REQUEST['action'] == "listdevices") {
            $out = "";
            foreach(devicelist(userid($_SESSION['user'])) as $device) {
                $out .= "<tr><td>".$device['name']."</td><td><button onclick=\"testnotify('".$device['id']."')\" id=\"".$device['id']."\"</button>Send notification</button></td></tr>";
            }
            echo json_encode(array("nocsrf" => NoCSRF::generate('nocsrf'), "result" => $out));
        } elseif($_REQUEST['action'] == "test") {
            require("inc/gcm.inc.php");
            sendGCM("Test notification", "Testing notifications to your device", array($_REQUEST['id']));
            echo json_encode(array("nocsrf" => NoCSRF::generate('nocsrf'), "result" => $_REQUEST['id']));
        }
    }
        
} else {
?><html>
<head>
<title>devices</title>
<script type="text/javascript">
NoCSRF = "<? echo NoCSRF::generate('nocsrf'); ?>";
</script>
<script type="text/javascript" src="qrcode/qrcode.js"></script>
<script type="text/javascript" src="js/devices.js">
</script>
</head>
<body>
    <? require("inc/menubar.inc.php"); ?><br />
    Activities: <button onclick="pairdevice()">New Device</button>
    <div id="pairingbox" style="display: none">
        <h2>First, install the app on your device. You can get it from <a href="/push.apk">here</a>.<br />
        Once it is installed, open the app, select menu->register and scan the QR code below:</h2><br />
        <span id="qr"></span>
    </div>
    <table id="devicelist"></table>
</body>
</html>
<? } ?>
