<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Initialize products array if not exists
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

// Initialize transactions if not exists
if (!isset($_SESSION['stock_in_transactions'])) {
    $_SESSION['stock_in_transactions'] = [];
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_stock'])) {
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $notes = $_POST['notes'] ?? '';
    
    if ($product_id > 0 && $quantity > 0) {
        // Find and update product
        $product_found = false;
        foreach ($products as $key => $product) {
            if ($product['product_id'] == $product_id) {
                $products[$key]['quantity'] += $quantity;
                $product_found = true;
                $product_name = $product['product_name'];
                break;
            }
        }
        
        if ($product_found) {
            // Save updated products back to session
            $_SESSION['products_data'] = $products;
            
            // Save transaction
            $transaction = [
                'id' => count($_SESSION['stock_in_transactions']) + 1,
                'date' => date('Y-m-d H:i:s'),
                'product' => $product_name,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'notes' => $notes,
                'type' => 'IN'
            ];
            array_unshift($_SESSION['stock_in_transactions'], $transaction);
            
            $message = "✅ Successfully added " . $quantity . " units to " . $product_name;
        } else {
            $error = "❌ Product not found!";
        }
    } else {
        $error = "❌ Please select a product and enter valid quantity!";
    }
}

// Get updated products
$products = $_SESSION['products_data'];
$transactions = $_SESSION['stock_in_transactions'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock In - Inventory System</title>
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
        .navbar h2 { font-size: 24px; }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .navbar a:hover { background: #34495e; }
        
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
        }
        select, input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn {
            background: #27ae60;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
        }
        .btn:hover { background: #229954; }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
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
        
        .badge-success {
            background: #27ae60;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
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
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 10px; text-align: center; }
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
            <div class="alert-success">✅ <?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert-danger">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>📦 Add Stock to Inventory</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Select Product</label>
                    <select name="product_id" required>
                        <option value="">-- Select a Product --</option>
                        <?php foreach($products as $product): ?>
                        <option value="<?php echo $product['product_id']; ?>">
                            <?php echo $product['product_name']; ?> (Current Stock: <?php echo $product['quantity']; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Quantity to Add</label>
                    <input type="number" name="quantity" min="1" required placeholder="Enter quantity">
                </div>
                
                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <input type="text" name="notes" placeholder="e.g., New shipment received, PO #12345">
                </div>
                
                <button type="submit" name="add_stock" class="btn">➕ Add Stock</button>
            </form>
        </div>
        
        <div class="card">
            <h2>📋 Recent Stock In Transactions</h2>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Search transactions...">
            </div>
            
            <?php if (count($transactions) > 0): ?>
            <table id="transactionTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $t): ?>
                    <tr>
                        <td><?php echo $t['date']; ?></td>
                        <td><?php echo $t['product']; ?></td>
                        <td><span class="badge-success">+<?php echo $t['quantity']; ?></span></td>
                        <td><?php echo $t['notes'] ?: '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #666; padding: 20px;">No stock in transactions yet. Add some stock above!</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#transactionTable tbody tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    </script>
</body>
</html>