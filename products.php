<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Initialize products array from session
if (!isset($_SESSION['products_data'])) {
    $_SESSION['products_data'] = [
        ['product_id' => 1, 'product_name' => 'Laptop', 'category_name' => 'Electronics', 'supplier_name' => 'Tech Solutions Ltd', 'quantity' => 50, 'unit_price' => 999.99, 'reorder_level' => 10],
        ['product_id' => 2, 'product_name' => 'Desk Chair', 'category_name' => 'Furniture', 'supplier_name' => 'Furniture World', 'quantity' => 30, 'unit_price' => 199.99, 'reorder_level' => 5],
        ['product_id' => 3, 'product_name' => 'Printer Paper', 'category_name' => 'Stationery', 'supplier_name' => 'Office Supplies Co', 'quantity' => 200, 'unit_price' => 29.99, 'reorder_level' => 20],
        ['product_id' => 4, 'product_name' => 'Wireless Mouse', 'category_name' => 'Electronics', 'supplier_name' => 'Tech Solutions Ltd', 'quantity' => 100, 'unit_price' => 19.99, 'reorder_level' => 15],
        ['product_id' => 5, 'product_name' => 'Office Desk', 'category_name' => 'Furniture', 'supplier_name' => 'Furniture World', 'quantity' => 15, 'unit_price' => 299.99, 'reorder_level' => 3],
    ];
}

// Get products from session
$products = $_SESSION['products_data'];
$categories = ['Electronics', 'Furniture', 'Stationery', 'Clothing'];
$suppliers = ['Tech Solutions Ltd', 'Furniture World', 'Office Supplies Co', 'Fashion Hub'];

// Handle Add Product
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $new_product = [
        'product_id' => count($products) + 1,
        'product_name' => $_POST['product_name'] ?? '',
        'category_name' => $_POST['category_name'] ?? 'Uncategorized',
        'supplier_name' => $_POST['supplier_name'] ?? 'Unknown',
        'quantity' => intval($_POST['quantity'] ?? 0),
        'unit_price' => floatval($_POST['unit_price'] ?? 0),
        'reorder_level' => intval($_POST['reorder_level'] ?? 10),
    ];
    
    if ($new_product['product_name'] && $new_product['unit_price'] > 0) {
        array_push($products, $new_product);
        $_SESSION['products_data'] = $products;
        $message = "✅ Product added successfully!";
    } else {
        $error = "❌ Please fill all required fields!";
    }
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    foreach ($products as $key => $product) {
        if ($product['product_id'] == $id) {
            unset($products[$key]);
            $products = array_values($products);
            $_SESSION['products_data'] = $products;
            $message = "✅ Product deleted successfully!";
            break;
        }
    }
}

// Get updated products
$products = $_SESSION['products_data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Inventory System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4; }
        
        .navbar {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        input, select {
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
            display: inline-block;
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-danger {
            background: #e74c3c;
        }
        .btn-primary {
            background: #3498db;
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
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #27ae60; color: white; }
        .badge-warning { background: #f39c12; color: white; }
        .badge-danger { background: #e74c3c; color: white; }
        
        .search-box {
            margin-bottom: 20px;
        }
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
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
            <div class="alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Add Product Form -->
        <div class="card">
            <h2>➕ Add New Product</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="product_name" required placeholder="Enter product name">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_name">
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_name">
                            <option value="">Select Supplier</option>
                            <?php foreach($suppliers as $sup): ?>
                            <option value="<?php echo $sup; ?>"><?php echo $sup; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Unit Price ($) *</label>
                        <input type="number" step="0.01" name="unit_price" required placeholder="0.00">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Initial Quantity</label>
                        <input type="number" name="quantity" value="0">
                    </div>
                    <div class="form-group">
                        <label>Reorder Level</label>
                        <input type="number" name="reorder_level" value="10">
                    </div>
                </div>
                
                <button type="submit" name="add_product" class="btn">💾 Save Product</button>
            </form>
        </div>
        
        <!-- Products List -->
        <div class="card">
            <h2>📋 Products List</h2>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Search products...">
            </div>
            
            <div class="table-responsive">
                <table id="productTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product_id']; ?></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($product['supplier_name'] ?? 'N/A'); ?></td>
                            <td><strong style="font-size: 16px;"><?php echo $product['quantity']; ?></strong></td>
                            <td>$<?php echo number_format($product['unit_price'], 2); ?></td>
                            <td>
                                <?php 
                                $stock = $product['quantity'];
                                $reorder = $product['reorder_level'];
                                if ($stock <= 0): ?>
                                    <span class="badge badge-danger">Out of Stock</span>
                                <?php elseif ($stock <= $reorder): ?>
                                    <span class="badge badge-warning">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge badge-success">In Stock</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="stock_in.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-sm">+ Stock</a>
                                <a href="?delete=<?php echo $product['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#productTable tbody tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    </script>
</body>
</html>