<?php
// Installation script for User Management System
// Run this file once to set up the database

require_once 'config.php';

echo "<h2>User Management System - Installation</h2>";

try {
    // Test database connection
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Users table already exists</p>";
        
        // Check if admin user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Admin user already exists</p>";
        } else {
            // Create admin user
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, is_admin, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['admin', 'Admin', 'User', 1, $hashed_password]);
            echo "<p style='color: green;'>✓ Admin user created (username: admin, password: admin123)</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Users table not found. Please run the database.sql file first.</p>";
        echo "<p>You can import the database.sql file through phpMyAdmin or run the following SQL commands:</p>";
        echo "<pre>";
        echo "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    password VARCHAR(255) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
        echo "</pre>";
    }
    
    // Test file permissions
    if (is_writable('.')) {
        echo "<p style='color: green;'>✓ Directory is writable</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Directory is not writable (this may cause issues with file uploads)</p>";
    }
    
    echo "<h3>Installation Complete!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>Delete this install.php file for security</li>";
    echo "<li>Visit <a href='login.php'>login.php</a> to start using the system</li>";
    echo "<li>Use admin/admin123 to login as administrator</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config.php</p>";
}
?>
