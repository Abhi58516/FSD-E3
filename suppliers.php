<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Sample supplier data
$suppliers = [
    1 => ['id' => 1, 'name' => 'Tech Solutions Ltd', 'contact' => 'John Doe', 'email' => 'john@tech.com', 'phone' => '1234567890'],
    2 => ['id' => 2, 'name' => 'Furniture World', 'contact' => 'Jane Smith', 'email' => 'jane@furniture.com', 'phone' => '1234567891'],
    3 => ['id' => 3, 'name' => 'Office Supplies Co', 'contact' => 'Bob Johnson', 'email' => 'bob@office.com', 'phone' => '1234567892'],
];

$message = "";

// Add new supplier
if (isset($_POST['add'])) {
    $new_id = count($suppliers) + 1;
    $suppliers[$new_id] = [
        'id' => $new_id,
        'name' => $_POST['name'],
        'contact' => $_POST['contact'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone']
    ];
    $message = "✅ Supplier added successfully!";
}

// Delete supplier
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    unset($suppliers[$id]);
    $message = "✅ Supplier deleted successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Suppliers - Inventory System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        
        .navbar {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 12px;
            border-radius: 5px;
        }
        .navbar a:hover { background: #34495e; }
        
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-danger {
            background: #e74c3c;
            padding: 5px 10px;
            font-size: 12px;
            text-decoration: none;
            color: white;
            border-radius: 3px;
            display: inline-block;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #34495e;
            color: white;
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .alert {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 10px; text-align: center; }
            .form-row { grid-template-columns: 1fr; }
        }
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
        
        <!-- Add Supplier Form -->
        <div class="card">
            <h2>➕ Add New Supplier</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Supplier Name *</label>
                        <input type="text" name="name" required placeholder="Enter supplier name">
                    </div>
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" name="contact" placeholder="Contact person">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="email@example.com">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" placeholder="Phone number">
                    </div>
                </div>
                <button type="submit" name="add" class="btn">💾 Save Supplier</button>
            </form>
        </div>
        
        <!-- Suppliers List -->
        <div class="card">
            <h2>📋 Suppliers List</h2>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Search suppliers...">
            </div>
            
            <table id="supplierTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($suppliers as $s): ?>
                    <tr>
                        <td><?php echo $s['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($s['contact']); ?></td>
                        <td><?php echo htmlspecialchars($s['email']); ?></td>
                        <td><?php echo htmlspecialchars($s['phone']); ?></td>
                        <td>
                            <a href="?delete=<?php echo $s['id']; ?>" class="btn-danger" onclick="return confirm('Delete this supplier?')">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#supplierTable tbody tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    </script>
</body>
</html>