<?php
  
    $servername = getenv("servername");
    $username = getenv("username");
    $password = getenv("password");
    $dbname = getenv("dbname");
  
    $conn = new mysqli($servername, $username, $password ,$dbname);
  
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
?>