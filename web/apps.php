<?
session_start();
if(!isset($_SESSION['user'])) {
    $_SESSION['continue_after_signin'] = "apps";
    header("Location: signin");
    die("Please <a href=\"signin\">sign in</a> before continuing");
}
require("inc/settings.inc.php");
require("inc/users.inc.php");
require("inc/apps.inc.php");
require("inc/nocsrf.inc.php");

if(isset($_REQUEST['action'])) {
    if(NoCSRF::check('nocsrf', $_REQUEST)) {
        if($_REQUEST['action'] == "add") {
            $name = "unnamed token";
            if(isset($_REQUEST['name'])) {
                $name = $_REQUEST['name'];
            }
            echo json_encode(array("token" => NoCSRF::generate('nocsrf'), "result" => addToken(userid($_SESSION['user']), $name)));
        } elseif($_REQUEST['action'] == "rename") {
            if(isset($_REQUEST['token']) && isset($_REQUEST['name'])) {
                echo json_encode(array("token" => NoCSRF::generate('nocsrf'), "result" => renameToken($_REQUEST['token'], $_REQUEST['name'])));
            } else {
                echo json_encode(array("token" => NoCSRF::generate('nocsrf'), "error" => "Token or new name not set"));
            }
        } elseif($_REQUEST['action'] == "remove") {
            if(isset($_REQUEST['token'])) {
                echo json_encode(array("token" => NoCSRF::generate('nocsrf'), "result" => removeToken($_REQUEST['token'])));
            } else {
                echo json_encode(array("token" => NoCSRF::generate('nocsrf'), "error" => "please specify the token to remove"));
            }
        }
    } else {
        echo json_encode(array("token" => NoCSRF::generate('nocsrf')));
    }
} else {
    $newapp = "New App";
    if(isset($_REQUEST['name'])) {
        $newapp = $_REQUEST['name'];
    }
?><html>
<head>
<title>apps</title>
<script type="text/javascript">
    appname = "<? echo addslashes($newapp); ?>";
    NoCSRF = "<? echo NoCSRF::generate('nocsrf'); ?>";
</script>
</head>
<body>
    <? require("inc/menubar.inc.php"); ?><br />
    Actions: <button onclick="addapp()">Add</button>
    <table id="applist">
        <?
        foreach(approvedApps(userid($_SESSION['user'])) as $app) {
            echo "<tr><td><b>".$app['name']."</b></td><td><a href=\"javascript: void(0);\" onclick=\"rename('".$app['token']."','".$app['name']."')\">rename</a></td><td><a href=\"javascript: void(0);\" onclick=\"remove('".$app['token']."')\">remove</a></td><td><code>".$app['token']."</code></td></tr>";
        }
        ?>
    </table>
    <br /><br /><br /><br /><br /><br /><br />
    <pre>
        Usage: GET or POST /push/send?token={token}&title={title}&message={message}[&format={json|xml}]
    </pre>
    <script type="text/javascript" src="js/apps.js">
    </script>
</body>
</html>
<? } ?>
