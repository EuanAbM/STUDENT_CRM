<?php
require 'dbconnect.inc';

$searchTerm = $_POST['search'] ?? '';
$searchTerm = mysqli_real_escape_string($conn, $searchTerm);

$sql = "SELECT * FROM student WHERE studentid LIKE '%$searchTerm%' OR firstname LIKE '%$searchTerm%' OR lastname LIKE '%$searchTerm%' OR postcode LIKE '%$searchTerm%'";
$result = mysqli_query($conn, $sql);

$output = '<table class="table table-striped">';
while($row = mysqli_fetch_assoc($result)) {
    $output .= '<tr>
                    <td>' . $row['studentid'] . '</td>
                    <td>' . $row['firstname'] . '</td>
                    <td>' . $row['lastname'] . '</td>
                    <td>' . $row['dob'] . '</td>
                    <td>' . $row['postcode'] . '</td>
                </tr>';
}
$output .= '</table>';
echo $output;

?>
