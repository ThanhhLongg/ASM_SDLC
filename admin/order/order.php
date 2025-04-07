<?php
require '../connect.php';

// Kiểm tra xem người dùng có phải admin không (dựa trên session hoặc quyền hạn)
// session_start();
// if ($_SESSION['role'] !== 'admin') {
//     // Nếu không phải admin, chuyển hướng về trang khác (hoặc trang login)
//     header("Location: login.php");
//     exit;
// }

// Lấy danh sách người dùng (admin có thể xem tất cả người dùng)
$sql_users = "SELECT user_id, user_name, phone, address FROM users";
$result_users = $conn->query($sql_users);

// Lấy lịch sử đơn hàng của tất cả người dùng
$sql_orders = "SELECT o.order_id, o.user_id, o.order_date, o.total_amount, od.product_id, od.quantity, od.unit_price, p.product_name
               FROM orders o
               JOIN order_details od ON o.order_id = od.order_id
               JOIN products p ON od.product_id = p.product_id";
$result_orders = $conn->query($sql_orders);

// Tạo mảng lưu lịch sử đơn hàng
$orders = [];
while ($row = $result_orders->fetch_assoc()) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Mua Hàng của Người Dùng</title>
    <link rel="stylesheet" href="order.css">
</head>
<body>
    <div class="order-container">
        <h2>Lịch Sử Mua Hàng của Người Dùng</h2>

        <!-- Hiển thị danh sách người dùng -->
        <h3>Danh sách Người Dùng</h3>
        <table>
            <thead>
                <tr>
                    <th>Tên Người Dùng</th>
                    <th>Số Điện Thoại</th>
                    <th>Địa Chỉ</th>
                    <th>Xem Lịch Sử Đơn Hàng</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result_users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['user_name']; ?></td>
                        <td><?php echo $user['phone']; ?></td>
                        <td><?php echo $user['address']; ?></td>
                        <td><a href="order_history.php?user_id=<?php echo $user['user_id']; ?>">Xem</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="buttons">
        <a href="http://localhost/SDLC/admin/index.php">⬅ Back</a>
        </div>

        <!-- Xem lịch sử mua hàng của một người dùng khi click vào link -->
        <?php
        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            echo "<h3>Lịch Sử Đơn Hàng của Người Dùng</h3>";
            echo "<p><strong>Người Dùng:</strong> " . $user_id . "</p>";

            // Lấy đơn hàng của người dùng
            $sql_user_orders = "SELECT o.order_id, o.order_date, o.total_amount, od.product_id, od.quantity, od.unit_price, p.product_name
                                FROM orders o
                                JOIN order_details od ON o.order_id = od.order_id
                                JOIN products p ON od.product_id = p.product_id
                                WHERE o.user_id = ?";
            $stmt_user_orders = $conn->prepare($sql_user_orders);
            $stmt_user_orders->bind_param("i", $user_id);
            $stmt_user_orders->execute();
            $result_user_orders = $stmt_user_orders->get_result();

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
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
