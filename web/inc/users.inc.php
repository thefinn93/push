<?
function userid($username) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $stmt = $mysqli->prepare("SELECT id FROM gcm.users WHERE name = ?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $out = NULL;
    $stmt->bind_result($out);
    $stmt->fetch();
    return $out;
}

function username($id) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $stmt = $mysqli->prepare("SELECT name FROM gcm.users WHERE id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $out = NULL;
    $stmt->bind_result($out);
    $stmt->fetch();
    return $out;
}
