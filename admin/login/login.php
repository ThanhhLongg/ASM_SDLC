<?php
session_start(); 

// Include database connection
require '../connect.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["full_name"]); 
    $password = trim($_POST["password"]);

    // Kiểm tra người dùng có tồn tại hay không
    $sql = "SELECT user_id, user_name, password_hash FROM users WHERE user_name = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $user_id, $db_username, $hashed_password);
            mysqli_stmt_fetch($stmt);

            // Kiểm tra mật khẩu
            if (password_verify($password, $hashed_password)) {
                // Lưu thông tin vào session
                $_SESSION["user_id"] = $user_id;
                $_SESSION["username"] = $db_username;

                // Kiểm tra nếu là admin
                if ($db_username === 'admin') {
                    $_SESSION["is_admin"] = true;
                    header("Location: ../user/user.php"); // Chuyển hướng đến trang quản lý user
                } else {
                    $_SESSION["is_admin"] = false;
                    header("Location: ../index.php"); // Chuyển hướng đến trang chính
                }
                exit();
            } else {
                echo "<p style='color:red;'>Invalid password!</p>";
            }
        } else {
            echo "<p style='color:red;'>User not found!</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p style='color:red;'>Error preparing statement: " . mysqli_error($conn) . "</p>";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>

    <form action="login.php" method="POST">
        <input type="text" name="full_name" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account?</p>
    <button class="register-btn" onclick="window.location.href='../register/register.php'">Create New Account</button>
</div>
</body>
</html>
