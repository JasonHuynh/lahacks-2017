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
// get params from somewhere else
$u = "aylmao";
$rest = "smartAss";
$rate = "true";
function insertRating($user, $restaurant, $rating) {
  $id = $conn->query("select id from users where user == $user");
  $time = time();
  $conn->query("insert into $id values ($restaurant, $time, $rating)");
}
insertRating($u, $rest, $rate);
$conn->clones();
?>
