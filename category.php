<?php
// Include the database connection file

$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if you have a password
$database = "inventory1";

$conn = new mysqli($servername, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$Cat_ID = isset($_POST['Cat_ID']) ? $_POST['Cat_ID'] : '';
if (isset($_POST['searchrec'])) {
    $sql = "SELECT * FROM Category WHERE Cat_ID ='$Cat_ID'";
    $result = $conn->query($sql);
}

if (isset($result) && $result->num_rows > 0) {
?>
<table>
    <tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            $hid = $row["TCat_ID"];
            $hfn = $row["Cat_name"];
            $hmn = $row["Cat_desc"];
        }
    } else {
       
    }
    ?>
</table>

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
        .add-category-form { margin-bottom: 20px; }
        .add-category-form input { padding: 8px; margin: 5px; width: 200px; }
        .add-category-form button { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .add-category-form button:hover { background-color: #45a049; }
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
        <h1>PC BEE INVENTORY SYSTEM</h1>
        <h2>Inventory System Dashboard</h2>

        <!-- Add Category Form -->
        <div class="add-category-form">
            <h2>Search Category</h2>
            <form method="POST">
                <input type="text" name="cat_ID" placeholder="ID" required>
                <button type="submit" name="search_ID">Search ID</button>
            </form>
        </div>

        <!-- Inventory Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (isset($result)) {
                    while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row["cat_ID"] ?></td>
                        <td><?= htmlspecialchars($row["cat_name"], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row["cat_desc"], ENT_QUOTES) ?></td>
                        <td class="action-buttons">
                            <button class="edit-btn" onclick="showEditForm(<?= $row['cat_ID'] ?>, '<?= htmlspecialchars($row['cat_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['cat_desc'], ENT_QUOTES) ?>')">Edit</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="cat_ID" value="<?= $row["cat_ID"] ?>">
                                <button class="delete-btn" type="submit" name="delete_category">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; 
                } ?>
            </tbody>
        </table>

        <!-- Edit Category Form -->
        <div class="edit-form" id="editForm">
            <h2>Edit Category</h2>
            <form method="POST">
                <input type="hidden" name="cat_ID" id="Edit_cat_ID">
                <input type="text" name="cat_name" id="Edit_cat_name" placeholder="Category Name" required>
                <input type="text" name="cat_desc" id="Edit_cat_desc" placeholder="Description" required>
                <button type="submit" name="update_category">Update Category</button>
                <button type="button" onclick="hideEditForm()">Cancel</button>
            </form>
        </div>

        <script>
            function showEditForm(id, name, desc) {
                document.getElementById("Edit_cat_ID").value = id;
                document.getElementById("Edit_cat_name").value = name;
                document.getElementById("Edit_cat_desc").value = desc;
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
