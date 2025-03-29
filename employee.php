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

// Handle Adding Employees
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addEmployee"])) {
    $Emp_Last_Name = $_POST["Emp_Last_Name"];
    $Emp_First_Name = $_POST["Emp_First_Name"];
    $Emp_role = $_POST["Emp_role"];

    $stmt = $conn->prepare("INSERT INTO employee (Emp_Last_Name, Emp_First_Name, Emp_role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $Emp_Last_Name, $Emp_First_Name, $Emp_role);
    $stmt->execute();
    $stmt->close();
}

// Handle Deleting Employees
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteEmployee"])) {
    $Emp_ID = $_POST["Emp_ID"];
    
    $stmt = $conn->prepare("DELETE FROM employee WHERE Emp_ID = ?");
    $stmt->bind_param("i", $Emp_ID);
    $stmt->execute();
    $stmt->close();
}

// Handle Updating Employees
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateEmployee"])) {
    $Emp_ID = $_POST["Emp_ID"];
    $Emp_Last_Name = $_POST["Emp_Last_Name"];
    $Emp_First_Name = $_POST["Emp_First_Name"];
    $Emp_role = $_POST["Emp_role"];

    $stmt = $conn->prepare("UPDATE employee SET Emp_Last_Name = ?, Emp_First_Name = ?, Emp_role = ? WHERE Emp_ID = ?");
    $stmt->bind_param("sssi", $Emp_Last_Name, $Emp_First_Name, $Emp_role, $Emp_ID);
    $stmt->execute();
    $stmt->close();
}

// Fetch Employees from Database
$result = $conn->query("SELECT * FROM employee");
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
        .add-employee-form { margin-bottom: 20px; }
        .add-employee-form input, .add-employee-form select { padding: 8px; margin: 5px; width: 200px; }
        .add-employee-form button { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .add-employee-form button:hover { background-color: #45a049; }
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
    
    <!-- Add Employee Form -->
    <div class="add-employee-form">
        <h2>Add New Employee</h2>
        <form method="POST">
            <input type="text" name="Emp_Last_Name" placeholder="Employee Last Name" required>
            <input type="text" name="Emp_First_Name" placeholder="Employee First Name" required>
            <select name="Emp_role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="Manager">Manager</option>
                <option value="Staff">Staff</option>
                <option value="Intern">Intern</option>
            </select>
            <button type="submit" name="addEmployee">Add Employee</button>
        </form>
    </div>

    <!-- Employee Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee Last Name</th>
                <th>Employee First Name</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= htmlspecialchars($row["Emp_ID"]) ?></td>
                <td><?= htmlspecialchars($row["Emp_Last_Name"]) ?></td>
                <td><?= htmlspecialchars($row["Emp_First_Name"]) ?></td>
                <td><?= htmlspecialchars($row["Emp_role"]) ?></td>
                <td class="action-buttons">
                    <button class="edit-btn" onclick="showEditForm(<?= $row['Emp_ID'] ?>, '<?= htmlspecialchars($row['Emp_Last_Name']) ?>', '<?= htmlspecialchars($row['Emp_First_Name']) ?>', '<?= htmlspecialchars($row['Emp_role']) ?>')">Edit</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="Emp_ID" value="<?= $row["Emp_ID"] ?>">
                        <button class="delete-btn" type="submit" name="deleteEmployee">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Edit Employee Form -->
    <div class="edit-form" id="editForm">
        <h2>Edit Employee</h2>
        <form method="POST">
            <input type="hidden" name="Emp_ID" id="editEmployeeId">
            <input type="text" name="Emp_Last_Name" id="editLastName" placeholder="Employee Last Name" required>
            <input type="text" name="Emp_First_Name" id="editFirstName" placeholder="Employee First Name" required>
            <select name="Emp_role" id="editRole" required>
                <option value="Manager">Manager</option>
                <option value="Staff">Staff</option>
                <option value="Intern">Intern</option>
            </select>
            <button type="submit" name="updateEmployee">Update Employee</button>
            <button type="button" onclick="hideEditForm()">Cancel</button>
        </form>
    </div>

    <script>
        function showEditForm(id, lastName, firstName, role) {
            document.getElementById("editEmployeeId").value = id;
            document.getElementById("editLastName").value = lastName;
            document.getElementById("editFirstName").value = firstName;
            document.getElementById("editRole").value = role;
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
