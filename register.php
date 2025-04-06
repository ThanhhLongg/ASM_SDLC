<?php
// Kết nối database
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    // Kiểm tra trường bắt buộc
    if (empty($full_name) || empty($password)) {
        echo "Họ tên và mật khẩu không được để trống!";
        exit();
    }

    // Kiểm tra xem tài khoản đã tồn tại chưa
    $check_sql = "SELECT user_name FROM users WHERE user_name = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $full_name);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            echo "Tên tài khoản đã tồn tại! Vui lòng chọn tên khác.";
            mysqli_stmt_close($check_stmt);
            exit();
        }
        mysqli_stmt_close($check_stmt);
    } else {
        echo "Lỗi khi kiểm tra tài khoản: " . mysqli_error($conn);
        exit();
    }

    // Mã hóa mật khẩu trước khi lưu vào database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Chèn dữ liệu vào database
    $sql = "INSERT INTO users (user_name, password, phone, address) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $hashed_password, $phone, $address);
        if (mysqli_stmt_execute($stmt)) {
            echo "Đăng ký thành công! Đang chuyển hướng...";
            header("refresh:2; url=http://localhost/SDLC/login.html");
            exit();
        } else {
            echo "Lỗi khi đăng ký: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Lỗi khi chuẩn bị câu lệnh: " . mysqli_error($conn);
    }
}

// Đóng kết nối database
mysqli_close($conn);
?>
