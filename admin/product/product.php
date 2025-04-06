<?php
session_start();
require '../connect.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch categories from the categories table
$categories_result = $conn->query("SELECT category_id, category_name FROM categories");

// Handle add & update product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_product'])) {
    $id = $_POST['product_id'] ?? '';
    $name = trim($_POST['product_name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $category_id = trim($_POST['category_id']);
    $stock_quantity = trim($_POST['stock_quantity']);
    $image_url = trim($_POST['image_url']); // Get image from dropdown

    if ($id) {
        // Update product
        $stmt = $conn->prepare("UPDATE products SET product_name=?, price=?, description=?, category_id=?, stock_quantity=?, image_url=? WHERE product_id=?");
        $stmt->bind_param("sdssisi", $name, $price, $description, $category_id, $stock_quantity, $image_url, $id);
    } else {
        // Add new product
        $stmt = $conn->prepare("INSERT INTO products (product_name, price, description, category_id, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssis", $name, $price, $description, $category_id, $stock_quantity, $image_url);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Saved successfully!'); window.location='product.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle delete product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Deleted successfully!'); window.location='product.php';</script>";
    } else {
        echo "<script>alert('Error deleting: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch product list
$result = $conn->query("SELECT product_id, product_name, price, description, category_id, stock_quantity, image_url FROM products");

// Fetch image list from the folder
$image_folder = 'ImageWeb';
$image_options = '';
$images = array_diff(scandir($image_folder), array('.', '..'));
foreach ($images as $img) {
    $image_options .= "<option value=\"$img\">$img</option>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <link rel="stylesheet" href="product.css">
</head>
<body>

<div class="container">
    <h2>Product Management</h2>

    <!-- Add / Edit Product Form -->
    <form method="POST">
        <input type="hidden" name="product_id" id="product_id">
        <input type="text" name="product_name" id="product_name" placeholder="Product Name" required>
        <input type="text" name="price" id="price" placeholder="Price" required>
        <textarea name="description" id="description" placeholder="Description" required></textarea>

        <!-- Dropdown select category -->
        <select name="category_id" id="category_id" required>
            <option value="">-- Select Category --</option>
            <?php while ($category = $categories_result->fetch_assoc()): ?>
                <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <input type="number" name="stock_quantity" id="stock_quantity" placeholder="Stock Quantity" required>

        <!-- Dropdown select image -->
        <select name="image_url" id="image_url" required>
            <option value="">-- Select Product Image --</option>
            <?= $image_options ?>
        </select>

        <button type="submit" name="save_product" class="btn">Save</button>
    </form>

    <!-- Product List Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Category ID</th>
            <th>Stock Quantity</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['product_id'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['description'] ?></td>
            <td><?= $row['category_id'] ?></td>
            <td><?= $row['stock_quantity'] ?></td>
            <td><img src="/SDLC/ImageWeb/<?= $row['image_url'] ?>" alt="product image" width="50" height="50"></td>
            <td>
                <button class="btn edit-btn" onclick="editProduct('<?= $row['product_id'] ?>', '<?= $row['product_name'] ?>', '<?= $row['price'] ?>', '<?= $row['description'] ?>', '<?= $row['category_id'] ?>', '<?= $row['stock_quantity'] ?>', '<?= $row['image_url'] ?>')">✏ Edit</button>
                <a href="?delete=<?= $row['product_id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete?');">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="buttons">
        <a href="http://localhost/SDLC/admin/index.php">⬅ Back</a>
    </div>
</div>

<script>
    function editProduct(id, name, price, description, category_id, stock_quantity, image_url) {
        document.getElementById("product_id").value = id;
        document.getElementById("product_name").value = name;
        document.getElementById("price").value = price;
        document.getElementById("description").value = description;
        document.getElementById("category_id").value = category_id;
        document.getElementById("stock_quantity").value = stock_quantity;
        document.getElementById("image_url").value = image_url;
    }
</script>

</body>
</html>

<?php $conn->close(); ?>
