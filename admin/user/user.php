<?php
session_start();
require '../connect.php';

// Check if not logged in or not admin
// if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "admin") {
//     echo "<p style='color:red; text-align:center;'>You do not have permission to access this page!</p>";
//     exit();
// }

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle adding & updating user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_user'])) {
    $id = $_POST['user_id'] ?? '';
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($id) {
        // Update user (do not update password)
        $stmt = $conn->prepare("UPDATE users SET user_name=?, phone=?, address=? WHERE user_id=?");
        $stmt->bind_param("sssi", $username, $phone, $address, $id);
    } else {
        // Add new user (hash password)
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (user_name, password, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $phone, $address);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Saved successfully!'); window.location='user.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Deleted successfully!'); window.location='user.php';</script>";
    } else {
        echo "<script>alert('Error while deleting: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Get user list
$result = $conn->query("SELECT user_id, user_name, phone, address FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<div class="container">
    <h2>User Management</h2>

    <!-- Add / Edit User Form -->
    <form method="POST">
        <input type="hidden" name="user_id" id="user_id">
        <input type="text" name="username" id="username" placeholder="Username" required>
        <input type="text" name="phone" id="phone" placeholder="Phone number" required>
        <input type="text" name="address" id="address" placeholder="Address">
        <input type="password" name="password" id="password" placeholder="Password">
        <button type="submit" name="save_user" class="btn">Save</button>
    </form>

    <!-- User List Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Phone number</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= $row['user_name'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['address'] ?></td>
            <td>
                <button class="btn edit-btn" onclick="editUser('<?= $row['user_id'] ?>', '<?= $row['user_name'] ?>', '<?= $row['phone'] ?>', '<?= $row['address'] ?>')">✏ Edit</button>
                <a href="?delete=<?= $row['user_id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure to delete?');">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="buttons">
        <a href="http://localhost/SDLC/admin/index.php">⬅ Back</a>
    </div>
</div>

<script>
    function editUser(id, username, phone, address) {
        document.getElementById("user_id").value = id;
        document.getElementById("username").value = username;
        document.getElementById("phone").value = phone;
        document.getElementById("address").value = address;
        document.getElementById("password").removeAttribute("required");
    }
</script>

</body>
</html>

<?php $conn->close(); ?>
