<?php
session_start();
require 'connect.php'; // Kết nối database

// Tài khoản admin mặc định
$admin_username = "admin";
$admin_password = "123";

// Lấy dữ liệu từ form
$full_name = trim($_POST["full_name"] ?? "");
$password = trim($_POST["password"] ?? "");

// Kiểm tra nếu là admin
if ($full_name === $admin_username && $password === $admin_password) {
    $_SESSION["username"] = $full_name; // Lưu session đăng nhập
    echo "admin"; // Trả về giá trị để JavaScript xử lý
    exit;
}

// Kiểm tra tài khoản user trong database
$sql = "SELECT password FROM users WHERE user_name = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $full_name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $hashed_password);
        mysqli_stmt_fetch($stmt);

        // Kiểm tra mật khẩu
        if (password_verify($password, $hashed_password)) {
            $_SESSION["username"] = $full_name;
            echo "user"; // Phản hồi để JavaScript xử lý
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
