<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Inventory System</title>
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
        .navbar h2 { font-size: 24px; }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 12px;
            border-radius: 5px;
        }
        .navbar a:hover { background: #34495e; }
        
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-card h3 { color: #666; margin-bottom: 10px; font-size: 14px; }
        .stat-card .number { font-size: 28px; font-weight: bold; color: #2c3e50; }
        
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
        
        /* IMPORTANT - This makes buttons visible */
        .button-group {
            margin-top: 20px;
            padding: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-right: 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-primary {
            background: #3498db;
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-success {
            background: #27ae60;
            color: white;
            border: none;
        }
        .btn-success:hover {
            background: #229954;
        }
        
        ul, ol { margin-left: 20px; margin-top: 10px; }
        li { margin: 8px 0; }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 10px; text-align: center; }
            .stats-grid { grid-template-columns: 1fr; }
            .btn { display: block; margin: 10px 0; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>📦 Inventory System</h2>
        <div>
            <span>👤 <?php echo $_SESSION['full_name']; ?></span>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="categories.php">Categories</a>
            <a href="suppliers.php">Suppliers</a>
            <a href="stock_in.php">Stock In</a>
            <a href="stock_out.php">Stock Out</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>System Status</h3>
                <div class="number">✅ Active</div>
            </div>
            <div class="stat-card">
                <h3>Logged in as</h3>
                <div class="number"><?php echo $_SESSION['full_name']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Current Time</h3>
                <div class="number"><?php echo date('H:i:s'); ?></div>
            </div>
            <div class="stat-card">
                <h3>Current Date</h3>
                <div class="number"><?php echo date('Y-m-d'); ?></div>
            </div>
        </div>
        
        <div class="card">
            <h2>✅ System Information</h2>
            <p><strong>Your inventory system is working!</strong></p>
            <p>You can now:</p>
            <ul>
                <li>Add products to inventory</li>
                <li>Manage categories and suppliers</li>
                <li>Record stock in/out transactions</li>
                <li>Generate reports</li>
            </ul>
            
            <!-- BUTTONS ARE HERE - VISIBLE -->
            <div class="button-group">
                <a href="products.php" class="btn btn-primary" style="background: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    🚀 Go to Products
                </a>
                <a href="stock_in.php" class="btn btn-success" style="background: #27ae60; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    📦 Add Stock
                </a>
            </div>
        </div>
        
        <div class="card">
            <h2>📋 Quick Setup Guide</h2>
            <ol>
                <li>First, add some categories in <strong>Categories</strong> page</li>
                <li>Add suppliers in <strong>Suppliers</strong> page</li>
                <li>Add products in <strong>Products</strong> page</li>
                <li>Record stock movements in <strong>Stock In/Out</strong> pages</li>
                <li>View reports in <strong>Reports</strong> page</li>
            </ol>
        </div>
    </div>
</body>
</html>