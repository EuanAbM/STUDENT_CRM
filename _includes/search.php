<?php
require 'dbconnect.inc';

$searchTerm = $_POST['search'] ?? '';
$searchTerm = mysqli_real_escape_string($conn, $searchTerm);

$sql = "SELECT * FROM student WHERE studentid LIKE '%$searchTerm%' OR firstname LIKE '%$searchTerm%' OR lastname LIKE '%$searchTerm%' OR postcode LIKE '%$searchTerm%'";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_array($result)) {
    // Output each row in a format that matches your table
}
?>