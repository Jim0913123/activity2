<?php
// Database Connection
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if you have a password
$database = "inventory1";

$conn = new mysqli($servername, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Adding Orders
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addOrder"])) {
    $ID = $_POST["supplierID"];
    $product = $_POST["Product"];
    $quantity = $_POST["quantity"];
    $orderDate = $_POST["orderDate"];

    $sql = "INSERT INTO orders (ID, orderDate, product, quantity) VALUES ('$ID', '$orderDate', '$product', '$quantity')";
    $conn->query($sql);
}

// Handle Deleting Orders
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteOrder"])) {
    $ID = $_POST["orderID"];
    $sql = "DELETE FROM orders WHERE orderID = $ID";
    $conn->query($sql);
}

// Handle Updating Orders
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateOrder"])) {
    $orderID = $_POST["orderID"];
    $newProduct = $_POST["newProduct"];
    $newQuantity = $_POST["newQuantity"]; // Get updated quantity
    $newOrderDate = $_POST["newOrderDate"];
    
    $sql = "UPDATE orders SET product='$newProduct', quantity='$newQuantity', orderDate='$newOrderDate' WHERE orderID=$orderID";
    $conn->query($sql);
}

// Fetch Orders from Database
$ordersResult = $conn->query("SELECT * FROM order_status_view");


// Fetch Suppliers for Dropdown
$suppliersResult = $conn->query("SELECT ID, name FROM supplier");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: rgb(236, 222, 18); }
        .action-buttons button { padding: 5px 10px; margin: 2px; border: none; cursor: pointer; }
        .delete-btn { background-color: #f44336; color: white; }
        .update-btn { background-color: #008CBA; color: white; }
        .add-order-form { margin-bottom: 20px; }
        .add-order-form input, .add-order-form select { padding: 8px; margin: 5px; width: 200px; }
        .add-order-form button { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .add-order-form button:hover { background-color: #45a049; }
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
<h1>Orders Management</h1>

<!-- Add Order Form -->
<div class="add-order-form">
    <h2>Add New Order</h2>
    <form method="POST">
        <label for="supplierID">Select Supplier:</label>
        <select name="supplierID" required>
            <option value="">-- Select Supplier --</option>
            <?php while ($supplier = $suppliersResult->fetch_assoc()) : ?>
                <option value="<?= $supplier['ID'] ?>"><?= $supplier['name'] ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="Product" required placeholder="Product Name">
        <input type="number" name="quantity" required placeholder="Quantity" min="1">
        <input type="date" name="orderDate" required>
        <button type="submit" name="addOrder">Add Order</button>
    </form>
</div>

<!-- Orders Table -->
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            
            <th>Product</th>
            <th>Quantity</th>
            <th>Order Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($order = $ordersResult->fetch_assoc()) : ?>
        <tr>
            <td><?= $order["orderID"] ?></td>
            <td><?= $order["product"] ?></td>
            <td><?= $order["quantity"] ?></td> <!-- Display quantity -->
            <td><?= $order["orderDate"] ?></td>
            <td class="action-buttons">
                <!-- Delete Button -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="orderID" value="<?= $order["orderID"] ?>">
                    <button class="delete-btn" type="submit" name="deleteOrder">Delete</button>
                </form>
                <!-- Update Button -->
                <button class="update-btn" onclick="showUpdateForm(<?= $order['orderID'] ?>, '<?= $order['product'] ?>', '<?= $order['orderDate'] ?>', <?= $order['quantity'] ?>)">Update</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Update Order Form (Initially Hidden) -->
<div id="updateForm" class="add-order-form" style="display:none;">
    <h2>Update Order</h2>
    <form method="POST">
        <input type="hidden" name="orderID" id="updateOrderID">
        <label>Product:</label>
        <input type="text" name="newProduct" id="updateProduct" required>
        <label>Quantity:</label>
        <input type="number" name="newQuantity" id="updateQuantity" required min="1">
        <label>Order Date:</label>
        <input type="date" name="newOrderDate" id="updateOrderDate" required>
        <button type="submit" name="updateOrder">Update Order</button>
    </form>
</div>

<script>
    function showUpdateForm(orderID, product, orderDate, quantity) {
        document.getElementById("updateForm").style.display = "block";
        document.getElementById("updateOrderID").value = orderID;
        document.getElementById("updateProduct").value = product;
        document.getElementById("updateQuantity").value = quantity;
        document.getElementById("updateOrderDate").value = orderDate;
    }
</script>
</div>
</body>
</html>

<?php
// Close Connection
$conn->close();
?>
