<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Initialize categories
if (!isset($_SESSION['categories_data'])) {
    $_SESSION['categories_data'] = [
        ['category_id' => 1, 'category_name' => 'Electronics', 'description' => 'Electronic items and gadgets'],
        ['category_id' => 2, 'category_name' => 'Furniture', 'description' => 'Office and home furniture'],
        ['category_id' => 3, 'category_name' => 'Stationery', 'description' => 'Office supplies and stationery'],
        ['category_id' => 4, 'category_name' => 'Clothing', 'description' => 'Apparel and accessories'],
    ];
}

$categories = $_SESSION['categories_data'];
$message = "";

// Add Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    
    if ($name) {
        $new = ['category_id' => count($categories) + 1, 'category_name' => $name, 'description' => $desc];
        array_push($categories, $new);
        $_SESSION['categories_data'] = $categories;
        $message = "✅ Category added!";
    }
}

// Delete Category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    foreach ($categories as $key => $cat) {
        if ($cat['category_id'] == $id) {
            unset($categories[$key]);
            $categories = array_values($categories);
            $_SESSION['categories_data'] = $categories;
            $message = "✅ Category deleted!";
            break;
        }
    }
}

$categories = $_SESSION['categories_data'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Categories</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f4f4f4; }
        .navbar { background: #2c3e50; color: white; padding: 15px 30px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .card h2 { border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .btn { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-danger { background: #e74c3c; padding: 5px 10px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #34495e; color: white; }
        .alert { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .search-box input { width: 100%; padding: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>📦 Inventory System</h2>
        <div>
            <span>👤 <?php echo $_SESSION['full_name']; ?></span>
            <a href="../dashboard.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="categories.php">Categories</a>
            <a href="suppliers.php">Suppliers</a>
            <a href="stock_in.php">Stock In</a>
            <a href="stock_out.php">Stock Out</a>
            <a href="reports.php">Reports</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>➕ Add Category</h2>
            <form method="POST">
                <input type="text" name="category_name" placeholder="Category Name" required>
                <textarea name="description" rows="3" placeholder="Description"></textarea>
                <button type="submit" name="add_category" class="btn">Save Category</button>
            </form>
        </div>
        
        <div class="card">
            <h2>📋 Categories List</h2>
            <div class="search-box">
                <input type="text" id="search" placeholder="🔍 Search...">
            </div>
            <table id="catTable">
                <thead><tr><th>ID</th><th>Name</th><th>Description</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach($categories as $c): ?>
                    <tr>
                        <td><?php echo $c['category_id']; ?></td>
                        <td><?php echo htmlspecialchars($c['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['description']); ?></td>
                        <td><a href="?delete=<?php echo $c['category_id']; ?>" class="btn-danger" style="padding: 5px 10px; text-decoration: none; border-radius: 3px;" onclick="return confirm('Delete?')">Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.getElementById('search').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#catTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
            });
        });
    </script>
</body>
</html>