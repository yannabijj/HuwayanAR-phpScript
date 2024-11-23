<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$port = "3307"; // Add the port explicitly
$username = "root";
$password = "ruby";
$dbname = "unityar";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the response
header("Content-Type: text/plain");

// Check if the 'search' parameter exists
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT name FROM destinations WHERE name LIKE ? LIMIT 10");
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $destinations = [];

        // Fetch results
        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row['name'];
        }

        // Return results as newline-separated values
        echo implode("\n", $destinations);
    } else {
        echo "Error: Could not execute search query.";
    }

    $stmt->close();
}
// Check if the 'destination' parameter exists
elseif (isset($_GET['destination'])) {
    $destination = $_GET['destination'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT x, y, z FROM destinations WHERE name = ? LIMIT 1");
    $stmt->bind_param("s", $destination);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Return coordinates as a comma-separated string
            echo $row['x'] . "," . $row['y'] . "," . $row['z'];
        } else {
            echo "Error: Destination not found.";
        }
    } else {
        echo "Error: Could not execute destination query.";
    }

    $stmt->close();
} else {
    echo "Error: Invalid parameters. Please provide 'search' or 'destination'.";
}

// Close the connection
$conn->close();
?>
