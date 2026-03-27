<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inventory System</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card h3 { font-size: 14px; color: #7f8c8d; margin-bottom: 10px; }
        .stat-card .number { font-size: 28px; font-weight: bold; color: #2c3e50; }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: #3498db;
            color: white;
            padding: 15px 20px;
            font-weight: bold;
            font-size: 18px;
        }
        .card-body { padding: 20px; }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px 10px 0 0;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .btn-success {
            background: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background: #229954;
            transform: translateY(-2px);
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
            <span style="margin-right: 15px;">👤 <?php echo $_SESSION['full_name']; ?></span>
            <a href="dashboard.php">Dashboard</a>
            <a href="pages/products.php">Products</a>
            <a href="pages/categories.php">Categories</a>
            <a href="pages/suppliers.php">Suppliers</a>
            <a href="pages/stock_in.php">Stock In</a>
            <a href="pages/stock_out.php">Stock Out</a>
            <a href="pages/reports.php">Reports</a>
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
            <div class="card-header">✅ System Information</div>
            <div class="card-body">
                <p><strong>Your inventory system is working!</strong></p>
                <p>You can now:</p>
                <ul>
                    <li>Add products to inventory</li>
                    <li>Manage categories and suppliers</li>
                    <li>Record stock in/out transactions</li>
                    <li>Generate reports</li>
                </ul>
                <div style="margin-top: 20px;">
                    <a href="pages/products.php" class="btn btn-primary">🚀 Go to Products</a>
                    <a href="pages/stock_in.php" class="btn btn-success">📦 Add Stock</a>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">📋 Quick Setup Guide</div>
            <div class="card-body">
                <ol>
                    <li>First, add some categories in <strong>Categories</strong> page</li>
                    <li>Add suppliers in <strong>Suppliers</strong> page</li>
                    <li>Add products in <strong>Products</strong> page</li>
                    <li>Record stock movements in <strong>Stock In/Out</strong> pages</li>
                    <li>View reports in <strong>Reports</strong> page</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>