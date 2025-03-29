<?php
// Database Connection
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if you have a password
$database = "inventory1"; // Fixed database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total products
$totalProductsQuery = "SELECT COUNT(*) AS total FROM products";
$totalProductsResult = $conn->query($totalProductsQuery);

if (!$totalProductsResult) {
    die("Query failed: " . $conn->error);
}

$totalProducts = $totalProductsResult->fetch_assoc()['total'];

// Fetch low stock alerts (Assuming threshold < 10)
$lowStockQuery = "SELECT COUNT(*) AS low_stock FROM products WHERE quantity < 10";
$lowStockResult = $conn->query($lowStockQuery);

if (!$lowStockResult) {
    die("Query failed: " . $conn->error);
}

$lowStock = $lowStockResult->fetch_assoc()['low_stock'];

$query = "SELECT name, price, sold_date FROM transactions"; 
$transactionsResult = $conn->query($query);

if (!$transactionsResult) {
    die("Query failed: " . $conn->error); // Debugging output
}

$result = $conn->query("SELECT * FROM product_view");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; }
        #sidebar { width: 250px; background: #333; color: white; padding: 20px; height: 100vh; }
        #sidebar ul { list-style: none; padding: 0; }
        #sidebar ul li { margin: 10px 0; }
        #sidebar ul li a { color: white; text-decoration: none; display: block; padding: 8px; background: #444; }
        #content { flex: 1; padding: 20px; }
        .card { background: #f4f4f4; padding: 20px; margin-bottom: 10px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: yellow; }
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
        <li><a href="supplier.php">Order to Supplier</a></li>
        <li><a href="por2.php">POAR</a></li>
    </ul>
</div>

<div id="content">
    <h1>PC BEE INVENTORY SYSTEM</h1>
    <div class="card">Total Products: <strong><?php echo $totalProducts; ?></strong></div>
    <div class="card">Low Stock Alerts: <strong><?php echo $lowStock; ?></strong></div>
    
    
    <h2>Recent Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Sold Date</th>  
            </tr>
        </thead>
        <tbody>
        <?php if ($transactionsResult && $transactionsResult->num_rows > 0) : ?>
    <?php while ($row = $transactionsResult->fetch_assoc()) : ?>
        <tr>
             <td><?php echo htmlspecialchars($row['name']); ?></td>
             <td>$<?php echo number_format($row['price'], 2); ?></td>
             <td><?php echo $row['sold_date']; ?></td>
             </tr>
             <?php endwhile; ?>
             <?php else : ?>
             <tr><td colspan="3">No transactions found.</td></tr>
             <?php endif; ?>
        </tbody>
    </table>
</div>

</body> 
</html>

<?php $conn->close(); ?>