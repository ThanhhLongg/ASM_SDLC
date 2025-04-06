<?php
// Connect to the database
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    // Check required fields
    if (empty($full_name) || empty($password)) {
        echo "Full name and password must not be empty!";
        exit();
    }

    // Check if username already exists
    $check_sql = "SELECT user_name FROM users WHERE user_name = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $full_name);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            echo "Username already exists! Please choose another one.";
            mysqli_stmt_close($check_stmt);
            exit();
        }
        mysqli_stmt_close($check_stmt);
    } else {
        echo "Error checking username: " . mysqli_error($conn);
        exit();
    }

    // Hash the password before storing into the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the database
    $sql = "INSERT INTO users (user_name, password, phone, address) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $hashed_password, $phone, $address);
        if (mysqli_stmt_execute($stmt)) {
            echo "Registration successful! Redirecting...";
            header("refresh:2; url=http://localhost/SDLC/login.html");
            exit();
        } else {
            echo "Error during registration: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>
