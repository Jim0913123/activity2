<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "inventory1";

// Database connection
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories for dropdown
$categories = [];
$result = $conn->query("SELECT Cat_name FROM category"); // Change `category` to your actual table name
while ($row = $result->fetch_assoc()) {
    $categories[] = $row['Cat_name'];
}

// Handle AJAX Requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == "create") {
        $emp_id = $_POST['emp_id'];
        $supplier_id = $_POST['supplier_id'];
        $category = $_POST['category'];
        $order_date = $_POST['order_date'];
        $brand = $_POST['brand'];

        // Prepare SQL statement
        $sql = "INSERT INTO poar (Emp_ID, ID, Cat_name, order_date, brand) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iisss", $emp_id, $supplier_id, $category, $order_date, $brand);
            echo ($stmt->execute()) ? "POAR added successfully!" : "Error: " . $stmt->error;
            $stmt->close();
        } else {
            echo "Error in SQL statement: " . $conn->error;
        }
    }

    if ($action == "fetch") {
        $sql = "SELECT * FROM poar ORDER BY poar_id DESC";
        $result = $conn->query($sql);
        $poar_data = [];
        while ($row = $result->fetch_assoc()) {
            $poar_data[] = $row;
        }
        echo json_encode($poar_data);
    }

    if ($action == "update" && isset($_POST['poar_id'])) {
        $poar_id = $_POST['poar_id'];
        $Emp_ID = $_POST['emp_id'];
        $ID = $_POST['supplier_id'];
        $Cat_name = $_POST['category'];
        $order_date = $_POST['order_date'];
        $brand = $_POST['brand'];

        $sql = "UPDATE poar SET Emp_ID=?, ID=?, Cat_name=?, order_date=?, brand=? WHERE poar_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssi", $Emp_ID, $ID, $Cat_name, $order_date, $brand, $poar_id);
        echo ($stmt->execute()) ? "POAR updated successfully!" : "Error: " . $stmt->error;
    }

    if ($action == "delete" && isset($_POST['poar_id'])) {
        $poar_id = $_POST['poar_id'];
        $sql = "DELETE FROM poar WHERE poar_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $poar_id);
        echo ($stmt->execute()) ? "POAR deleted successfully!" : "Error: " . $stmt->error;
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POAR Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
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
<div class="container mt-4">
    <h2 class="mb-3">Purchase Order and Receiving (POAR) Form</h2>
    <div class="row mb-3">
        <div class="col-md-2"><input type="text" id="emp_id" class="form-control" placeholder="Employee ID"></div>
        <div class="col-md-2"><input type="text" id="supplier_id" class="form-control" placeholder="Supplier ID"></div>
        <div class="col-md-2">
            <select id="category" class="form-control">
                <option value="">Select Category</option>
                <option value="">Select Category</option>
            <option value="Mouse">Mouse</option>
            <option value="Keyboard">Keyboard</option>
            <option value="HDD">HDD</option>
            <option value="RAM">RAM</option>
            <option value="SSD">SSD</option>
            <option value="Storage">Storage</option>
            <option value="Memory">Memory</option>
            <option value="MotherBoard">MotherBoard</option>

                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><input type="date" id="order_date" class="form-control"></div>
        <div class="col-md-2"><input type="text" id="brand" class="form-control" placeholder="Brand"></div>
        <input type="hidden" id="poar_id">
    </div>
    <button id="submit_poar" class="btn btn-success">Submit</button>
    <h4 class="mt-4">POAR Records</h4>
    <table class="table table-bordered">
        <thead class="table-warning">
            <tr>
                <th>ID</th>
                <th>Emp ID</th>
                <th>Supplier ID</th>
                <th>Category</th>
                <th>Date</th>
                <th>Brand</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="poar_table">
            <tr><td colspan="7" class="text-center">Loading...</td></tr>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    loadPOAR();

    $("#submit_poar").click(function () {
        let poar_id = $("#poar_id").val();
        let emp_id = $("#emp_id").val();
        let supplier_id = $("#supplier_id").val();
        let category = $("#category").val();
        let order_date = $("#order_date").val();
        let brand = $("#brand").val();
        let action = poar_id ? "update" : "create";

        $.post("", {
            action: action,
            poar_id: poar_id,
            emp_id: emp_id,
            supplier_id: supplier_id,
            category: category,
            order_date: order_date,
            brand: brand
        }, function (response) {
            alert(response);
            clearForm();
            loadPOAR();
        });
    });

    function loadPOAR() {
        $.post("", { action: "fetch" }, function (data) {
            let poar = JSON.parse(data);
            let rows = "";
            poar.forEach(function (record) {
                rows += `<tr>
                    <td>${record.poar_id}</td>
                    <td>${record.Emp_ID}</td>
                    <td>${record.ID}</td>
                    <td>${record.Cat_name}</td>
                    <td>${record.order_date}</td>
                    <td>${record.brand}</td>
                    <td>
                        <button class='btn btn-warning btn-edit' data-id='${record.poar_id}' data-emp='${record.Emp_ID}' data-supplier='${record.ID}' data-category='${record.Cat_name}' data-date='${record.order_date}' data-brand='${record.brand}'>Edit</button>
                        <button class='btn btn-danger btn-delete' data-id='${record.poar_id}'>Delete</button>
                    </td>
                </tr>`;
            });
            $("#poar_table").html(rows);
        });
    }
});
</script>
</body>
</html>
