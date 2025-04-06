<?php
session_start();
require 'connect.php'; // Connect to database

// Default admin account
$admin_username = "admin";
$admin_password = "123";

// Get data from form
$full_name = trim($_POST["full_name"] ?? "");
$password = trim($_POST["password"] ?? "");

// Check if admin
if ($full_name === $admin_username && $password === $admin_password) {
    $_SESSION["username"] = $full_name; // Save login session
    echo "admin"; // Return value for JavaScript to handle
    exit;
}

// Check user account in database
$sql = "SELECT password FROM users WHERE user_name = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $full_name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $hashed_password);
        mysqli_stmt_fetch($stmt);

        // Check password
        if (password_verify($password, $hashed_password)) {
            $_SESSION["username"] = $full_name;
            echo "user"; // Response for JavaScript to handle
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
