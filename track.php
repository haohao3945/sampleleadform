<?php
// Include the database configuration file
require_once 'config.php';

// Create a connection to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the tracking_events table exists, and create it if not
$tableExists = $conn->query("SHOW TABLES LIKE 'tracking_events'")->num_rows > 0;
if (!$tableExists) {
    $createTableSql = "CREATE TABLE tracking_events (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        event_type VARCHAR(50),
        event_time DATETIME,
        t_value INT(11)
    )";
    if ($conn->query($createTableSql) === TRUE) {
        echo "Table tracking_events created successfully";
    } else {
        die("Error creating table: " . $conn->error);
    }
}

// Get variables from GET request
$event = isset($_GET['event']) ? $_GET['event'] : ''; // e.g., 'view', '30sec', '60sec'
$t = isset($_GET['t']) ? $_GET['t'] : 0; // Time variable in seconds

// Track the event and save it in the database
if ($event) {
    $currentDate = date('Y-m-d H:i:s'); // Get current timestamp

    // Insert tracking data into the database
    $sql = "INSERT INTO tracking_events (event_type, event_time, t_value) VALUES ('$event', '$currentDate', '$t')";
    if ($conn->query($sql) === TRUE) {
        echo "Event tracked successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
