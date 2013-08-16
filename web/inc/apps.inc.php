<?
function approvedApps($user) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $query = $mysqli->query("SELECT name,token FROM tokens WHERE user = $user AND active = 1");
    $out = [];
    while($row = $query->fetch_assoc()) {
        $out[] = array("token" => htmlspecialchars($row['token']), "name" => htmlspecialchars($row['name']));
    }
    return $out;
}

function userOwnsToken($user, $token) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $stmt = $mysqli->prepare("SELECT user FROM tokens WHERE user = ? AND token = ? AND active = 1");
    $stmt->bind_param("is", $user, $token);
    $stmt->execute();
    return $stmt->fetch();
    
}

function maketoken() { // Thank you Chad Birch on Stack Overflow (http://stackoverflow.com/a/853898/403940)
    $valid_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $length = 20;
    
    $random_string = "";
    $num_valid_chars = strlen($valid_chars);
    for ($i = 0; $i < $length; $i++)
    {
        $random_pick = mt_rand(1, $num_valid_chars);
        $random_char = $valid_chars[$random_pick-1];
        $random_string .= $random_char;
    }
    return $random_string;
}

function addToken($user, $name) {
    global $sql;
    $token = maketoken();
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $stmt = $mysqli->prepare("INSERT INTO tokens(`token`,`user`,`name`,`active`) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sis", $token, $user, $name);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return approvedApps($user);
}

function renameToken($token, $name) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    if(userOwnsToken(userid($_SESSION['user']), $token)) {
        $stmt = $mysqli->prepare("UPDATE tokens SET  name = ? WHERE token = ? AND active = 1;");
        $stmt->bind_param("ss", $name, $token);
        $stmt->execute();
        $stmt->close();
    }
    $mysqli->close();
    return approvedApps(userid($_SESSION['user']));
}

function removeToken($token) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    if(userOwnsToken(userid($_SESSION['user']), $token)) {
        $stmt = $mysqli->prepare("UPDATE tokens SET active = 0 WHERE token = ?;");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();
    }
    $mysqli->close();
    return approvedApps(userid($_SESSION['user']));
}
