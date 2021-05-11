<?php
$servername = "localhost";
$name = "name";
$phno = "phno";
$dbname = "kare";

// Create connection
$conn = new mysqli($servername, "root", "password", $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT  cxname, cxphno FROM test;";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo " - Name: " . $row["cxname"]. " Phone no: " . $row["cxphno"]. "<br>";
  }
} else {
  echo "0 results";
}
$conn->close();
