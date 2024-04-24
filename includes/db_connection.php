<?php

$conn = new mysqli('localhost', 'root', '', 'checklist');


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
