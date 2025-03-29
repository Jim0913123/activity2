<?php
$servername = "localhost";
$username = "root"; // Update credentials if needed
$password = "";
$database = "inventory1";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM poar";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POAR Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-4">Purchase Order and Receiving (POAR) Form</h2>
        
        <div class="row mb-3">
            <div class="col"><input type="text" id="emp_id" class="form-control" placeholder="Employee ID"></div>
            <div class="col"><input type="text" id="supplier_id" class="form-control" placeholder="Supplier ID"></div>
            <div class="col"><input type="text" id="category" class="form-control" placeholder="Category"></div>
            <div class="col"><input type="date" id="date" class="form-control"></div>
            <div class="col"><input type="text" id="brand" class="form-control" placeholder="Brand"></div>
        </div>
        
        <button class="btn btn-success" id="submit_poar">Submit</button>
        
        <h3 class="mt-4">POAR Records</h3>
        <table class="table table-bordered mt-2">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Emp ID</th>
                    <th>Supplier ID</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Brand</th>
                </tr>
            </thead>
            <tbody id="poar_table">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['POAR_ID'] ?></td>
                        <td><?= $row['Emp_ID'] ?></td>
                        <td><?= $row['Supplier_ID'] ?></td>
                        <td><?= $row['Category'] ?></td>
                        <td><?= $row['Date'] ?></td>
                        <td><?= $row['Brand'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $("#submit_poar").click(function() {
                var emp_id = $("#emp_id").val();
                var supplier_id = $("#supplier_id").val();
                var category = $("#category").val();
                var date = $("#date").val();
                var brand = $("#brand").val();

                if (emp_id == "" || supplier_id == "" || category == "" || date == "" || brand == "") {
                    alert("All fields are required!");
                    return;
                }

                $.ajax({
                    url: "insert_poar.php",
                    type: "POST",
                    data: {
                        emp_id: emp_id,
                        supplier_id: supplier_id,
                        category: category,
                        date: date,
                        brand: brand
                    },
                    success: function(response) {
                        if (response == "success") {
                            alert("POAR entry added successfully!");
                            location.reload(); // Refresh page to update table
                        } else {
                            alert("Error: " + response);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
