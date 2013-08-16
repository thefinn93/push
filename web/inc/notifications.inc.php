<?
function recentNotifications($count) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    if(!$query = $mysqli->query("SELECT * FROM notifications WHERE `to` = ".userid($_SESSION['user'])." ORDER BY time DESC LIMIT 0,$count")) {
        die($mysqli->error);
    }
    $out = array();
    $applist = array();
    while($row = $query->fetch_assoc()) {
        if(!isset($applist[$row['from']])) {
            $appquery = $mysqli->prepare("SELECT name FROM tokens WHERE token = ?");
            $appquery->bind_param("s",$row['from']);
            $appquery->execute();
            $name = $row['from'];
            $appquery->bind_result($name);
            $appquery->fetch();
            $applist[$row['from']] = $name;
            $appquery->fetch();
            $appquery->close();
        }
        $out[] = array("title" => htmlspecialchars($row['title']), "message" => htmlspecialchars($row['message']), "from" => htmlspecialchars($applist[$row['from']]));
    }
    return $out;
}
