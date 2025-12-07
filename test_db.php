<?php
require_once 'config.php';

echo "<h1>Database Connection and Table Test</h1>";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "<p>Connected to database successfully.</p>";
}

// Check if database exists
$db_check = $conn->query("SHOW DATABASES LIKE 'kidsspace_users_db'");
if ($db_check->num_rows == 0) {
    echo "<p>Error: Database 'kidsspace_users_db' does not exist.</p>";
} else {
    echo "<p>Database 'kidsspace_users_db' exists.</p>";
}

// Check if users table exists
$table_check = $conn->query("SHOW TABLES LIKE 'users'");
if ($table_check->num_rows == 0) {
    echo "<p>Error: Table 'users' does not exist in the database.</p>";
} else {
    echo "<p>Table 'users' exists.</p>";
    
    // Show table structure
    echo "<h2>Table Structure:</h2>";
    $structure = $conn->query("DESCRIBE users");
    if ($structure->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while($row = $structure->fetch_assoc()) {
            echo "<tr><td>" . $row["Field"]. "</td><td>" . $row["Type"]. "</td><td>" . $row["Null"]. "</td><td>" . $row["Key"]. "</td><td>" . $row["Default"]. "</td><td>" . $row["Extra"]. "</td></tr>";
        }
        echo "</table>";
    }
    
    // Query to get all users
    $sql = "SELECT id, name, email, role FROM users";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<h2>Users in the database:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["email"]. "</td><td>" . $row["role"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in the database.</p>";
    }
}

$conn->close();
?>
