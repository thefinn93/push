<?
function devicelist($user) {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $query = $mysqli->query("SELECT name,id FROM clients WHERE user = $user");
    $out = array();
    while($row = $query->fetch_assoc()) {
        $out[] = array("name" => htmlspecialchars($row['name']), "id" => htmlspecialchars($row['id']));
    }
    return $out;
}

function makecode($user) {
    global $sql;
    $code = uniqid();
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $stmt = $mysqli->prepare("INSERT INTO gcm.pairingcodes (code,user) VALUES (?,?)");
    $stmt->bind_param("si",$code,$user);
    $stmt->execute();
    return $code;
}

function pair($code,$id,$name="") {
    global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $stmt = $mysqli->prepare("SELECT user FROM gcm.pairingcodes WHERE code = ? AND madeat >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->bind_param("s",$code);
    $stmt->execute();
    $out = false;
    $user = NULL;
    $stmt->bind_result($user);
    if($stmt->fetch()) {
        error_log("Associating device $id with $user");
        $stmt->close();
        // Update the client list
        if(!$update = $mysqli->prepare("UPDATE gcm.clients SET clients.user = ?, clients.name = ? WHERE clients.id = ?")) {
            die($mysqli->error);
        }
        $update->bind_param("iss", $user, $name, $id);
        if($update->execute()) {
            $out = true;
        }
        $update->close();
        // Delete the code from the database
        $delete = $mysqli->prepare("DELETE FROM gcm.pairingcodes WHERE pairingcodes.code = ?");
        $delete->bind_param("s",$code);
        $delete->execute();
        $delete->close();
    } else {
        error_log("No such code $code");
        $stmt->close();
    }
    // Clean up the pairing codes database. Delete everything older than 24 hours
    $mysqli->query("DELETE FROM gcm.pairingcodes WHERE pairingcodes.madeat <= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    
    return $out;
}
