<?php
set_time_limit(300); // allows script to run up to 5 minutes
$apiKey = '49508d6563f14f9ea1dd4809b4aab271'; // Replace with your OpenCage API key
include __DIR__ . '/../includes/connect_db.php';


$query = "SELECT * FROM location WHERE Latitude = 0 OR Longitude = 0 OR Latitude IS NULL OR Longitude IS NULL";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $locationID = $row['LocationID'];
    $address = urlencode($row['Address'] . ', ' . $row['City'] . ', Algeria');

    $url = "https://api.opencagedata.com/geocode/v1/json?q=$address&key=$apiKey";

    $json = file_get_contents($url);
    $data = json_decode($json, true);

    if (isset($data['results'][0])) {
        $lat = $data['results'][0]['geometry']['lat'];
        $lng = $data['results'][0]['geometry']['lng'];

        // Update the database
        $update = $conn->query("UPDATE location SET Latitude = '$lat', Longitude = '$lng' WHERE LocationID = $locationID");

        echo "Updated LocationID $locationID: $lat, $lng<br>";
        sleep(1); // Respect API rate limits
    } else {
        echo "No coordinates found for LocationID $locationID<br>";
    }
}

$conn->close();
?>
