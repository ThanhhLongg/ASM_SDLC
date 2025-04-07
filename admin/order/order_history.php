<?php
require '../connect.php';  // Kết nối cơ sở dữ liệu

// Kiểm tra xem có tham số 'user_id' trong URL hay không
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Lấy thông tin người dùng từ cơ sở dữ liệu
    $sql_user = "SELECT user_name, phone, address FROM users WHERE user_id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    // Nếu có người dùng, tiếp tục lấy đơn hàng
    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();

        // Lấy lịch sử đơn hàng của người dùng
        $sql_user_orders = "SELECT o.order_id, o.order_date, o.total_amount, od.product_id, od.quantity, od.unit_price, p.product_name
                            FROM orders o
                            JOIN order_details od ON o.order_id = od.order_id
                            JOIN products p ON od.product_id = p.product_id
                            WHERE o.user_id = ?";
        $stmt_user_orders = $conn->prepare($sql_user_orders);
        $stmt_user_orders->bind_param("i", $user_id);
        $stmt_user_orders->execute();
        $result_user_orders = $stmt_user_orders->get_result();

        // Hiển thị thông tin người dùng và lịch sử đơn hàng
        echo "<h3>Lịch Sử Mua Hàng của Người Dùng: " . $user['user_name'] . "</h3>";
        echo "<p><strong>Số điện thoại:</strong> " . $user['phone'] . "</p>";
        echo "<p><strong>Địa chỉ:</strong> " . $user['address'] . "</p>";

        // Hiển thị các đơn hàng của người dùng
        if ($result_user_orders->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>Mã Đơn Hàng</th><th>Ngày Đặt</th><th>Sản Phẩm</th><th>Số Lượng</th><th>Giá</th><th>Tổng Tiền</th></tr></thead>";
            echo "<tbody>";
            while ($order = $result_user_orders->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $order['order_id'] . "</td>";
                echo "<td>" . $order['order_date'] . "</td>";
                echo "<td>" . $order['product_name'] . "</td>";
                echo "<td>" . $order['quantity'] . "</td>";
                echo "<td>" . number_format($order['unit_price'], 0, ',', '.') . "đ</td>";
                echo "<td>" . number_format($order['quantity'] * $order['unit_price'], 0, ',', '.') . "đ</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Không có đơn hàng nào cho người dùng này.</p>";
        }

        $stmt_user_orders->close();
    } else {
        echo "<p>Không tìm thấy thông tin người dùng.</p>";
    }

    $stmt_user->close();
} else {
    echo "<p>Không có tham số user_id trong URL.</p>";
}

$conn->close();
?>
