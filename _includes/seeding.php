<?php
require '../vendor/autoload.php';
require 'dbconnect.inc';

$faker = Faker\Factory::create();

$recordCount = isset($_GET['records']) ? intval($_GET['records']) : 5;

$stmt = $conn->prepare("INSERT INTO student (studentid, password, dob, firstname, lastname, house, town, county, country, postcode, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

for ($i = 0; $i < $recordCount; $i++) {
    $studentId = $faker->unique()->numerify('########');
    $password = password_hash('password', PASSWORD_DEFAULT);
    $dob = $faker->date($format = 'Y-m-d', $max = '2003-12-31');
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $house = $faker->buildingNumber;
    $town = $faker->city;
    $county = $faker->state;
    $country = $faker->country;
    $postcode = $faker->postcode;

    // Generate a fake profile image URL (using Unsplash random image)
    $image = "https://source.unsplash.com/random/150x150";

    // Bind parameters and execute the prepared statement
    $stmt->bind_param("sssssssssss", $studentId, $password, $dob, $firstName, $lastName, $house, $town, $county, $country, $postcode, $image);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>
