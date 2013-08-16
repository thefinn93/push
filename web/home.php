<?
session_start();
if(!isset($_SESSION['user'])) {
    $_SESSION['continue_after_signin'] = "home";
    header("Location: signin");
    die("Please <a href=\"signin\">sign in</a> before continuing");
}
require("inc/settings.inc.php");
require("inc/users.inc.php");
require("inc/notifications.inc.php");
?><html>
<head>
<title>home</title>
</head>
<body>
    <? require("inc/menubar.inc.php"); ?>
<table id="recentnotifications">
    <?
    foreach(recentNotifications(10) as $notification) {
        echo "<tr><td><b>".$notification['title']."</b></td><td>".$notification['message']."</td><td>".$notification['from']."</td></tr>";
    }
    ?>
</table>
</body>
</html>
