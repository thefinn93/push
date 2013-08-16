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
}
if(!isset($_REQUEST['title'])) {
    $output['error'][] = "Please specify a title";
}
if(!isset($_REQUEST['message'])) {
    $output['error'][] = "Please specify a message";
}


if(count($output['error']) == 0) {
    unset($output['error']);
    $user = userKeys(tokenToUser($_REQUEST['token']));
    $output['result'] = sendGCM($_REQUEST['title'], $_REQUEST['message'], $user);
    
    
    // And log it
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $stmt = $mysqli->prepare("INSERT INTO notifications (`to`, `from`, `title`, `message`) VALUES (?, ?, ?, ?);");
    $stmt->bind_param("isss", $user, $_REQUEST['token'], $_REQUEST['title'], $_REQUEST['message']);
    $stmt->execute();
}

// Stolen from http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/, thanks bro
function generate_xml_from_array($array, $node_name) {
	$xml = '';
	if (is_array($array) || is_object($array)) {
		foreach ($array as $key=>$value) {
			if (is_numeric($key)) {
				$key = $node_name;
			}
			$xml .= '<' . 1 . '>' . "\n" . generate_xml_from_array($value, $node_name) . '</' . $key . '>' . "\n";
		}
	} else {
		$xml = htmlspecialchars($array, ENT_QUOTES) . "\n";
	}

	return $xml;
}

function toXML($array, $node_block='nodes', $node_name='node') {
	$xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

	$xml .= '<' . $node_block . '>' . "\n";
	$xml .= generate_xml_from_array($array, $node_name);
	$xml .= '</' . $node_block . '>' . "\n";

	return $xml;
}
// End theft

$prettyout = "<html><body><pre>".print_r($output, true)."</pre></body></html>";

if(isset($_REQUEST['format'])) {
    if($_REQUEST['format'] == "json") {
        $prettyout = json_encode($output);
    } elseif($_REQUEST['format'] == "xml") {
        $prettyout = toXML($output);
    }
}
echo $prettyout;
