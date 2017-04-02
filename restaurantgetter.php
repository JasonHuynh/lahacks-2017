<?php
$servername = "stickyrice.westus.cloudapp.azure.com";
$username = "stickyrice_user";
$password = "$picyRice";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$use = "USE DATABASE flanBase";
if ($conn->query($use) == TRUE) {
  echo "Database used successfully";
} else {
  echo "Error using database: " . $conn->error;
}

//here we gotta get form submission from login
$user = "helladope";//something;
$password = "dankestofmemes";//password;
$hashthing = hash("sha256", $password, TRUE);

require_once('lib/OAuth.php');

$CONSUMER_KEY = vMljsHOVzFDR-UhABpz3Og;
$CONSUMER_SECRET = GwiJIXf2oXtjA216AoZN5Us7L5k;
$TOKEN = -Ce78AxvNaXk5O8CbEGNs7JGPScWWulZ;
$TOKEN_SECRET = nOzcqm1wXjAUBH9GXQtYO4jtW_w;

$API_HOST = 'api.yelp.com';
$SEARCH_PATH = '/v2/search/';
$BUSINESS_PATH = '/v2/business/';

// gets the array of resturuants
function search($term, $location, $limit) {
    $url_params = array();
    
    $url_params[0] = $term;
    $url_params[1] = $location;
    $url_params[2] = $limit;
    $search_path = $GLOBALS['SEARCH_PATH'] . "?" . http_build_query($url_params)

    return request($GLOBALS['API_HOST'], $search_path);
}

// helper to get a buisness from its id
function get_business($business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] . urlencode($business_id);
    
    return request($GLOBALS['API_HOST'], $business_path);
}

// Yelp API Magic
function request($host, $path) {
    $unsigned_url = "https://" . $host . $path;
    // Token object built using the OAuth library
    $token = new OAuthToken($GLOBALS['TOKEN'], $GLOBALS['TOKEN_SECRET']);
    // Consumer object built using the OAuth library
    $consumer = new OAuthConsumer($GLOBALS['CONSUMER_KEY'], $GLOBALS['CONSUMER_SECRET']);
    // Yelp uses HMAC SHA1 encoding
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
    $oauthrequest = OAuthRequest::from_consumer_and_token(
        $consumer, 
        $token, 
        'GET', 
        $unsigned_url
    );
    
    // Sign the request
    $oauthrequest->sign_request($signature_method, $consumer, $token);
    
    // Get the signed URL
    $signed_url = $oauthrequest->to_url();
    
    // Send Yelp API Call
    try {
        $ch = curl_init($signed_url);
        if (FALSE === $ch)
            throw new Exception('Failed to initialize');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        if (FALSE === $data)
            throw new Exception(curl_error($ch), curl_errno($ch));
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($data, $http_status);
        curl_close($ch);
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }
    return $data;
}

// gets a resturuant
function getRestaurant($username, $hashword, $term, $location, $limit) {
  $checkuser = $conn->query("select count(user) from users where user == $username and hash == $hashword");
  $name = "";
  if ($checkuser == 0) {
    echo "User does not exist or Wrong Password";
  } else {
    echo "Login successful";
    $id = $conn->query("select id from users where user = $username and hash = $hashword");
    $usable = FALSE;
    while ($usable != TRUE) {

      // get the current location
      $user_ip = getenv('REMOTE_ADDR');
      $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
      $areacode = $geo["geoplugin_areaCode"];

      // get the list
      $resturuantList = search($term, $areacode, $limit);
      $resturuantList = json_decode($resturuantList);
      $resturuantCount  = count($resturuantList);

      // select a random resturuant
      $randomint = rand(0, $resturuantCount);
      $resturuant = $resturuantList[$randomint];

      // get the restuturant name
      $buisnesses = $decodedRes[0];
      $buisnessName = $buisnesses->name;

      $name = $buisnessName;
      
      $contains = $conn->query("select count(restaurant) from $id where restaurant == $name") > 0;
      if ($contains == FALSE) {
        $usable = TRUE;
        $time = time();
        $good = "true";
        $conn->query("insert into $id values ($name, $time, $good)");
      } else {
        $time = time();
        $good = "false";
        $countBad = $conn->query("select count(restaurant) from $id where restaurant == $name and good == $good");
        if ($countBad == 0) {
          $delay = $time - 604800;
          $numBad = $conn->query("select count(restaurant) from $id where $time - time < $delay");
          if ($numBad == 0) {
            $usable = true;
          } else {
            $usable = false;
          }
        } else {
          $usable = false;
        }
      }
    }
  }
  return $name;
}
getRestaurant($user, $hashthing);

$conn->clones();
?>
