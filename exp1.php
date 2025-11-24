<?php
// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "gk");
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// -----------------------------------------
// INSERT EXPENSE
// -----------------------------------------
if (isset($_POST['add'])) {
    $title   = $_POST['title'];
    $amount  = $_POST['amount'];
    $category = $_POST['category'];
    $date    = $_POST['date'];

    $conn->query("INSERT INTO expenses(title, amount, category, expense_date) 
                  VALUES('$title', '$amount', '$category', '$date')");
    echo "<script>alert('Expense Added');</script>";
}

// -----------------------------------------
// DELETE EXPENSE
// -----------------------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM expenses WHERE id=$id");
    echo "<script>alert('Expense Deleted');</script>";
}

// -----------------------------------------
// UPDATE EXPENSE
// -----------------------------------------
if (isset($_POST['update'])) {
    $id       = $_POST['id'];
    $title    = $_POST['title'];
    $amount   = $_POST['amount'];
    $category = $_POST['category'];
    $date     = $_POST['date'];

    $conn->query("UPDATE expenses 
                  SET title='$title', amount='$amount', category='$category', expense_date='$date' 
                  WHERE id=$id");
    echo "<script>alert('Expense Updated');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expense Tracker CRUD</title>

    <!-- BOOTSTRAP CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fa;
        }
        .container-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .operation-box {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="container-box">

        <h2><span style="color:#007bff;">Expense Tracker</span> — CRUD Operations</h2>

        <!-- MENU -->
        <form method="get" class="mb-4">
            <label class="form-label fw-bold">Select Operation:</label>
            <select name="action" class="form-select" onchange="this.form.submit()">
                <option value="">-- Choose --</option>
                <option value="insert" <?php if(isset($_GET['action']) && $_GET['action']=='insert') echo 'selected'; ?>>Insert Expense</option>
                <option value="view"   <?php if(isset($_GET['action']) && $_GET['action']=='view') echo 'selected'; ?>>View All Expenses</option>
                <option value="update" <?php if(isset($_GET['action']) && $_GET['action']=='update') echo 'selected'; ?>>Update Expense</option>
                <option value="delete" <?php if(isset($_GET['action']) && $_GET['action']=='delete') echo 'selected'; ?>>Delete Expense</option>
            </select>
        </form>

        <hr>

        <?php
        // --------------------- INSERT FORM ---------------------
        if (isset($_GET['action']) && $_GET['action'] == "insert") {
        ?>
            <h3 class="text-primary">Add Expense</h3>
            <form method="post" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input class="form-control" type="text" name="title" placeholder="e.g., Grocery Shopping" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input class="form-control" type="number" step="0.01" name="amount" placeholder="e.g., 1200.50" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input class="form-control" type="text" name="category" placeholder="e.g., Food, Travel, Bills" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input class="form-control" type="date" name="date" required>
                </div>
                <button type="submit" name="add" class="btn btn-success">Add Expense</button>
            </form>
        <?php
        }

        // --------------------- VIEW EXPENSES ---------------------
        if (isset($_GET['action']) && $_GET['action'] == "view") {
            $result = $conn->query("SELECT * FROM expenses ORDER BY expense_date DESC");

            echo "<h3 class='text-primary mb-3'>All Expenses</h3>";
            echo "<table class='table table-bordered table-striped table-hover'>
                    <thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Category</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>";

            $total = 0;
            while ($row = $result->fetch_assoc()) {
                $total += $row['amount'];
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['amount']}</td>
                        <td>{$row['category']}</td>
                        <td>{$row['expense_date']}</td>
                     </tr>";
            }
            echo "</tbody></table>";

            echo "<p class='fw-bold'>Total Expenses: ₹ " . number_format($total, 2) . "</p>";
        }

        // --------------------- UPDATE LIST ---------------------
        if (isset($_GET['action']) && $_GET['action'] == "update" && !isset($_GET['edit'])) {
            $result = $conn->query("SELECT * FROM expenses ORDER BY expense_date DESC");

            echo "<h3 class='text-primary'>Select Expense to Update</h3><ul class='list-group mt-3'>";
            while ($row = $result->fetch_assoc()) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                        {$row['id']} - {$row['title']} (₹ {$row['amount']})
                        <a class='btn btn-warning btn-sm' href='?action=update&edit={$row['id']}'>Edit</a>
                      </li>";
            }
            echo "</ul>";
        }

        // --------------------- UPDATE FORM ---------------------
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $res = $conn->query("SELECT * FROM expenses WHERE id=$id");
            $data = $res->fetch_assoc();
        ?>
            <h3 class="text-primary mt-4">Edit Expense</h3>
            <form method="post" class="mt-3">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input class="form-control" type="text" name="title" value="<?= $data['title'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input class="form-control" type="number" step="0.01" name="amount" value="<?= $data['amount'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input class="form-control" type="text" name="category" value="<?= $data['category'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input class="form-control" type="date" name="date" value="<?= $data['expense_date'] ?>" required>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Update Expense</button>
            </form>
        <?php
        }

        // --------------------- DELETE LIST ---------------------
        if (isset($_GET['action']) && $_GET['action'] == "delete") {
            $result = $conn->query("SELECT * FROM expenses ORDER BY expense_date DESC");

            echo "<h3 class='text-danger'>Select Expense to Delete</h3><ul class='list-group mt-3'>";
            while ($row = $result->fetch_assoc()) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                        {$row['id']} - {$row['title']} (₹ {$row['amount']})
                        <a class='btn btn-danger btn-sm' 
                           href='?delete={$row['id']}' 
                           onclick=\"return confirm('Are you sure you want to delete this expense?');\">
                           Delete
                        </a>
                      </li>";
            }
            echo "</ul>";
        }
        ?>
    </div>
</div>

</body>
</html>
