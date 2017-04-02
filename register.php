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
//need to get form info again
$user = "ylmao";
$password = "temp";
$id = "e" + rand(1000000, 10000000);
$numID = $conn->query("select count(id) from users where id == $id");
while ($numID != 0) {
  $id = "e" + rand(1000000, 10000000);
  $numID = $conn->query("select count(id) from users where id == $id");
}
$numUser = $conn->query("select count(user) from users where user == $user");
if ($numUser != 0) {
  echo "Invalid Username";
} else {
  $hashword = hash("sha256", $password, TRUE);
  $conn->query("insert into users values ($id, $user, $hashword)");
}
$conn->query("create table $id (restaurant varchar(255), time int, good varchar(255))");
$conn->clones();
?>
