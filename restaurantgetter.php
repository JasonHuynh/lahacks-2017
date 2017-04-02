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

function getRestaurant($username, $hashword) {
  $checkuser = $conn->query("select count(user) from users where user == $username and hash == $hashword");
  $name = "";
  if ($checkuser == 0) {
    echo "User does not exist or Wrong Password";
  } else {
    echo "Login successful";
    $id = $conn->query("select id from users where user = $username and hash = $hashword");
    $usable = FALSE;
    while ($usable != TRUE) {
      //yelp api get restaurant stuff in here
      //get restaurant name
      $name = "SmartAlecs";
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
