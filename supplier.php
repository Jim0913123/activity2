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

// Handle Adding Supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addSupplier"])) {
    $supplierName = $_POST["supplierName"];
    $supplierNumber = $_POST["supplierNumber"];
    $supplierEmail = $_POST["supplierEmail"];

    $sql = "INSERT INTO supplier (name, number, email) VALUES ('$supplierName', '$supplierNumber', '$supplierEmail')";
    $conn->query($sql);
}

// Handle Deleting Supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteSupplier"])) {
    $id = $_POST["supplierId"];
    $sql = "DELETE FROM supplier WHERE id = $id";
    $conn->query($sql);
}

// Handle Updating Supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateSupplier"])) {
    $id = $_POST["supplierId"];
    $supplierName = $_POST["supplierName"];
    $supplierNumber = $_POST["supplierNumber"];
    $supplierEmail = $_POST["supplierEmail"];

    $sql = "UPDATE supplier SET name='$supplierName', number='$supplierNumber', email='$supplierEmail' WHERE id=$id";
    $conn->query($sql);
}

// Fetch Suppliers from Database
$result = $conn->query("SELECT * FROM supplier_view");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color:rgb(236, 222, 18); }
        .action-buttons button { padding: 5px 10px; margin: 2px; border: none; cursor: pointer; }
        .edit-btn { background-color: #4CAF50; color: white; }
        .addsupplier-btn { background-color:rgb(64, 107, 247); color: white; }
        .delete-btn { background-color: #f44336; color: white; }
        .add-product-form { margin-bottom: 20px; }
        .add-product-form input { padding: 8px; margin: 5px; width: 200px; }
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
<h1>Supplier Management</h1>

<!-- Add Supplier Form -->
<form method="POST">
    <input type="text" name="supplierName" placeholder="Supplier Name" required>
    <input type="text" name="supplierNumber" placeholder="Phone Number" required>
    <input type="email" name="supplierEmail" placeholder="Email" required>
    <button class="addsupplier-btn" type="submit" name="addSupplier">Add Supplier</button>
</form>

<!-- Supplier Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Number</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?= $row["ID"] ?></td>
            <td><?= $row["name"] ?></td>
            <td><?= $row["number"] ?></td>
            <td><?= $row["email"] ?></td>
            <td>
                <button class="edit-btn" onclick="showEditForm(<?= $row['ID'] ?>, '<?= $row['name'] ?>', '<?= $row['number'] ?>', '<?= $row['email'] ?>')">Edit</button>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="supplierId" value="<?= $row["ID"] ?>">
                    <button class="delete-btn" type="submit" name="deleteSupplier">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Edit Supplier Form -->
<div id="editForm" style="display:none;">
    <h2>Edit Supplier</h2>
    <form method="POST">
        <input type="hidden" name="supplierId" id="editSupplierId">
        <input type="text" name="supplierName" id="editSupplierName" required>
        <input type="text" name="supplierNumber" id="editSupplierNumber" required>
        <input type="email" name="supplierEmail" id="editSupplierEmail" required>
        <button class="edit-btn" type="submit" name="updateSupplier">Update Supplier</button>
        <button class="delete-btn" type="button" onclick="hideEditForm()">Cancel</button>
    </form>
</div>

<script>
    function showEditForm(id, name, number, email) {
        document.getElementById("editSupplierId").value = id;
        document.getElementById("editSupplierName").value = name;
        document.getElementById("editSupplierNumber").value = number;
        document.getElementById("editSupplierEmail").value = email;
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
// Close Connection
$conn->close();
?>
