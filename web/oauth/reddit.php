<?
session_start();
global $client_id,$redirect_uri;

$client_id = "-----";
$client_secret = "-----";
$redirect_uri = "https://www.thefinn93.com/push/oauth/reddit";

$access_token_url = "https://oauth.reddit.com/api/v1/access_token";


function loginToReddit() {
    global $client_id,$redirect_uri;
    $state = uniqid();
    $_SESSION['reddit_state'] = $state;
    header("Location: https://ssl.reddit.com/api/v1/authorize?scope=identity&state=".$state."&redirect_uri=$redirect_uri&response_type=code&client_id=".$client_id);
}

function registerAccount($user) {
    require("../inc/settings.inc.php");
    //global $sql;
    $mysqli = new mysqli($sql['host'], $sql['user'], $sql['password'], $sql['db']);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $checkexisting = $mysqli->prepare("SELECT * FROM users WHERE name = ?");
    $checkexisting->bind_param("s",$user);
    $checkexisting->execute();
    if(!$checkexisting->fetch()) {
        $stmt = $mysqli->prepare("INSERT INTO users (name) VALUES (?)");
        $stmt->bind_param("s",$user);
        $stmt->execute();
    }
    $mysqli->close();
}

if(!isset($_GET['state'])) {
    error_log("State not set");
    loginToReddit();
} elseif($_GET['state'] != $_SESSION['reddit_state']) {
    error_log("Bad state");
    loginToReddit();
} else {
    require_once("../Requests/library/Requests.php");
    Requests::register_autoloader();
    $code = $_GET['code'];
    $access_token_url = "https://ssl.reddit.com/api/v1/access_token";
    $options = array('auth' => array($client_id, $client_secret));
    $token_request = Requests::post(
     $access_token_url,
     array(),
     array(
      'grant_type' => "authorization_code",
      'code' => $code,
      'redirect_uri' => $redirect_uri
      ),
     $options
    );
    $response = json_decode($token_request->body);
    if(!isset($response->error)) {
        $headers = array("Authorization" => "bearer ".$response->access_token);
        $me = Requests::get("https://oauth.reddit.com/api/v1/me",$headers);
        $user = json_decode($me->body);
        $_SESSION['user'] = $user->name;
        registerAccount($user->name);
        if(isset($_SESSION['continue_after_signin'])) {
            header("Location: ../".$_SESSION['continue_after_signin']);
            unset($_SESSION['continue_after_signin']);
        } else {
            header("Location: ../home");
        }
    } else {
     echo "Error: ".$response->error;
    }
}
