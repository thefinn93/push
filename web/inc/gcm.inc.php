<?
function sendGCM($title, $message, $to) {
    // BROWSER API key from Google APIs
    $apiKey = "AIzaSyCQky7_0vEhv9Uc8Iw1vfqjH1ycA4GQW-w";
    
    // Set POST variables
    $url = 'https://android.googleapis.com/gcm/send';

    $fields = array(
                    'registration_ids'  => $to,
                    'data'              => array( "message" => $message, "title" => $title ),
                    );

    $headers = array(
                        'Authorization: key=' . $apiKey,
                        'Content-Type: application/json'
                    );

    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt( $ch, CURLOPT_URL, $url );

    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

    // Execute post
    $result = curl_exec($ch);

    // Close connection
    curl_close($ch);

    return $result;
}
