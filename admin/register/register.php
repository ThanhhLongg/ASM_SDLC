<?php
// Include database connection
require '../connect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input data
    $full_name = trim($_POST["full_name"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    // Hash password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL insert statement
    $sql = "INSERT INTO users (user_name, password_hash, phone, address) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $hashed_password, $phone, $address);

        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color:green;'>Đăng ký thành công! Bạn sẽ được chuyển hướng đến trang đăng nhập trong giây lát...</p>";
            header("refresh:2; url=../login/login.php"); 
            exit();
        } else {
            echo "<p style='color:red;'>Lỗi khi thực hiện câu lệnh SQL: " . mysqli_error($conn) . "</p>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<p style='color:red;'>Lỗi chuẩn bị câu lệnh SQL: " . mysqli_error($conn) . "</p>";
    }
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<div class="container">
    <h2>Create a new account</h2>

    <form action="register.php" method="POST">
        <label>Fullname:</label>
        <input type="text" name="full_name" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Phone:</label>
        <input type="text" name="phone">

        <label>Address:</label>
        <textarea name="address"></textarea>

        <button type="submit">Sign up</button>
    </form>
</div>
</body>
</html>
