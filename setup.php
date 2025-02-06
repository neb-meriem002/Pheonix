<?php
// Database connection settings
$host = 'localhost';
$username = 'root'; // Change this to your MySQL username
$password = '';     // Change this to your MySQL password
$dbname = 'todo_list'; // Database name (if pre-existing)

// Connect to MySQL server
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load SQL script from file
$sqlScript = file_get_contents('init_db.sql');
if (!$sqlScript) {
    die("Failed to load SQL script.");
}

// Execute the SQL script
if ($conn->multi_query($sqlScript)) {
    echo "Database and tables created successfully!";
    do {
        // Process all results if there are multiple queries
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
} else {
    echo "Error executing script: " . $conn->error;
}

// Close the connection
$conn->close();
?>