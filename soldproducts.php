<?php
// Database Connection
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if you have a password
$database = "inventory1";

$conn = new mysqli('localhost', 'root', '', 'inventory1');

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Adding Products
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addProduct"])) {
    $productName = $_POST["productName"];
    $price = $_POST["price"];
    $productCategory = $_POST["productCategory"];

    if (!empty($productCategory)) {
        $sql = "INSERT INTO products (name, price, category) VALUES ('$productName', '$price', '$productCategory')";
        $conn->query($sql);
    }
}

// Handle Deleting Products
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteProduct"])) {
    $id = $_POST["productId"];
    $sql = "DELETE FROM products WHERE id = $id";
    $conn->query($sql);
}

// Handle Updating Products
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateProduct"])) {
    $id = $_POST["productId"];
    $productName = $_POST["productName"];
    $price = $_POST["price"];
    $productCategory = $_POST["productCategory"];

    if (!empty($productCategory)) {
        $sql = "UPDATE products SET name='$productName', price='$price', category='$productCategory' WHERE id=$id";
        $conn->query($sql);
    }
}

// Handle Marking Products as Sold
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["markAsSold"])) {
    $id = $_POST["productId"];
    
    $productQuery = $conn->query("SELECT * FROM products WHERE id = $id");
    if ($productQuery->num_rows > 0) {
        $product = $productQuery->fetch_assoc();
        
        // Insert into sold_products table
        $sql = "INSERT INTO sold_products (product_id, name, price, category, sold_date) 
                VALUES ('{$product['id']}', '{$product['name']}', '{$product['price']}', '{$product['category']}', NOW())";
        $conn->query($sql);
        
        // Delete from products table
        $conn->query("DELETE FROM products WHERE id = $id");
    }
}

// Fetch Products from Database
$result = $conn->query("SELECT * FROM products");

// Fetch Sold Products from Database
$soldProductsResult = $conn->query("SELECT * FROM sold_products");

// Fetch Categories from Database
$categoriesResult = $conn->query("SELECT DISTINCT category FROM products");
$categories = [];   
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row["category"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: rgb(236, 222, 18); }
        .action-buttons button { padding: 5px 10px; margin: 2px; border: none; cursor: pointer; }
        .edit-btn { background-color: #4CAF50; color: white; }
        .delete-btn { background-color: #f44336; color: white; }
        .sold-btn { background-color: #008CBA; color: white; }
        .add-product-form { margin-bottom: 20px; }
        .add-product-form input, .add-product-form select { padding: 8px; margin: 5px; width: 200px; }
        .add-product-form button { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .add-product-form button:hover { background-color: #45a049; }
        .edit-form { display: none; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; }
        #sidebar { width: 250px; background: #333; color: white; padding: 20px; height: 100vh; }
        #sidebar ul { list-style: none; padding: 0; }
        #sidebar ul li { margin: 10px 0; }
        #sidebar ul li a { color: white; text-decoration: none; display: block; padding: 8px; background: #444; }
        #content { flex: 1; padding: 20px; }
    </style>
</head>
<body>
<div id="sidebar">
    <header>ADMIN</header>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="category.php">Category</a></li>
        <li><a href="index1.php">Product</a></li>
        <li><a href="supplier.php">Supplier</a></li>
        <li><a href="soldproducts1.php">Sold Product</a></li>
        <li><a href="employee.php">Employee</a></li>
        <li><a href="index1.php">Add Products</a></li>
        <li><a href="order.php">Order to Supplier</a></li>
        <li><a href="por2.php">POAR</a></li>
    </ul>
</div>

<div id="content">
<h1>Inventory System Dashboard</h1>


<!-- Inventory Table -->
<h2>Current Inventory</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= $row["name"] ?></td>
            <td><?= $row["price"] ?></td>
            <td><?= $row["category"] ?></td>
            <td class="action-buttons">
                <button class="edit-btn" onclick="showEditForm(<?= $row['id'] ?>, '<?= $row['name'] ?>', <?= $row['price'] ?>, '<?= $row['category'] ?>')">Edit</button>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="productId" value="<?= $row["id"] ?>">
                    <button class="delete-btn" type="submit" name="deleteProduct">Delete</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="productId" value="<?= $row["id"] ?>">
                    <button class="sold-btn" type="submit" name="markAsSold">Mark as Sold</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>



<!-- Edit Product Form -->
<div class="edit-form" id="editForm">
    <h2>Edit Product</h2>
    <form method="POST">
        <input type="hidden" name="productId" id="editProductId">
        <input type="text" name="productName" id="editProductName" placeholder="Product Name" required>
        <input type="text" name="price" id="editProductPrice" placeholder="Price" required>
        <select name="productCategory" id="editProductCategory" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?= $category ?>"><?= $category ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="updateProduct">Update Product</button>
        <button type="button" onclick="hideEditForm()">Cancel</button>
    </form>
</div>

<script>
    function showEditForm(id, name, price, category) {
        document.getElementById("editProductId").value = id;
        document.getElementById("editProductName").value = name;
        document.getElementById("editProductPrice").value = price;
        document.getElementById("editProductCategory").value = category;
        document.getElementById("editForm").style.display = "block";
    }

    function hideEditForm() {
        document.getElementById("editForm").style.display = "none";
    }
</script>
</div>
</body>
</html>

<?php
$conn->close();
?>