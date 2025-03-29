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

// Fetch Sold Products from Database
$soldProductsResult = $conn->query("SELECT * FROM sold_products ORDER BY sold_date DESC");
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sold Products</title>
    <style>
         body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; }
        #sidebar { width: 250px; background: #333; color: white; padding: 20px; height: 100vh; }
        #sidebar ul { list-style: none; padding: 0; }
        #sidebar ul li { margin: 10px 0; }
        #sidebar ul li a { color: white; text-decoration: none; display: block; padding: 8px; background: #444; }
        #content { flex: 1; padding: 20px; }
    
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background-color: #f4f4f4; 
        }
        h1 { 
            color: #333; 
            text-align: center; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            background-color: #fff; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            background-color: #4CAF50; 
            color: white; 
        }
        tr:hover { 
            background-color: #f5f5f5; 
        }
        .sold-date { 
            color: #777; 
            font-size: 0.9em; 
        }
        .no-products { 
            text-align: center; 
            color: #777; 
            padding: 20px; 
        }
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
<h1>Sold Products</h1>

<!-- Sold Products Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Sold Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($soldProductsResult->num_rows>0) : ?>
            <?php while ($row = $soldProductsResult->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row["product_id"] ?></td>
                    <td><?= $row["name"] ?></td>
                    <td><?= $row["price"] ?></td>
                    <td><?= $row["category"] ?></td>
                    <td class="sold-date"><?= date('M d, Y h:i A', strtotime($row["sold_date"])) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="5" class="no-products">No products have been sold yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
   
</body>
</html>

<?php
$conn->close();
?>