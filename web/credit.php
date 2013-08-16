<?
require "inc/settings.inc.php";
require "inc/gcm.inc.php";
$output = array("error" => array());

function tokenToUser($key) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $stmt = $mysqli->prepare("SELECT user FROM tokens WHERE token = ? AND active = 1");
    $stmt->bind_param("s",$key);
    $stmt->execute();
    $user = 0;
    $stmt->bind_result($user);
    $stmt->fetch();
    $stmt->close();
    $mysqli->close();
    return $user;
}

function userKeys($user) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $res = $mysqli->query("SELECT id FROM clients WHERE user = $user");
    $out = array();
    while($row = $res->fetch_assoc()) {
        $out[] = $row['id'];
    }
    return $out;
}

if(!isset($_REQUEST['token'])) {
    $output['error'][] = "Please specify a token";
} elseif(tokenToUser($_REQUEST['token']) == 0) {
    $output['error'][] = "Invalid token";
} else {
    echo "fucking infinity";
}
