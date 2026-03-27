<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Initialize data from session
$products = isset($_SESSION['products_data']) ? $_SESSION['products_data'] : [
    ['product_id' => 1, 'product_name' => 'Laptop', 'category_name' => 'Electronics', 'supplier_name' => 'Tech Solutions Ltd', 'quantity' => 55, 'unit_price' => 999.99, 'reorder_level' => 10],
    ['product_id' => 2, 'product_name' => 'Desk Chair', 'category_name' => 'Furniture', 'supplier_name' => 'Furniture World', 'quantity' => 30, 'unit_price' => 199.99, 'reorder_level' => 5],
    ['product_id' => 3, 'product_name' => 'Printer Paper', 'category_name' => 'Stationery', 'supplier_name' => 'Office Supplies Co', 'quantity' => 200, 'unit_price' => 29.99, 'reorder_level' => 20],
    ['product_id' => 4, 'product_name' => 'Wireless Mouse', 'category_name' => 'Electronics', 'supplier_name' => 'Tech Solutions Ltd', 'quantity' => 100, 'unit_price' => 19.99, 'reorder_level' => 15],
    ['product_id' => 5, 'product_name' => 'Office Desk', 'category_name' => 'Furniture', 'supplier_name' => 'Furniture World', 'quantity' => 15, 'unit_price' => 299.99, 'reorder_level' => 3],
];

$stock_in_transactions = isset($_SESSION['stock_in_transactions']) ? $_SESSION['stock_in_transactions'] : [];
$stock_out_transactions = isset($_SESSION['stock_out_transactions']) ? $_SESSION['stock_out_transactions'] : [];

// Get report type from URL
$report_type = isset($_GET['type']) ? $_GET['type'] : 'inventory';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Calculate totals
$total_products = count($products);
$total_stock_value = 0;
$low_stock_count = 0;
$out_of_stock_count = 0;

foreach ($products as $product) {
    $total_stock_value += $product['quantity'] * $product['unit_price'];
    if ($product['quantity'] <= 0) {
        $out_of_stock_count++;
    } elseif ($product['quantity'] <= $product['reorder_level']) {
        $low_stock_count++;
    }
}

// Calculate stock movement totals
$total_in = 0;
$total_out = 0;
foreach ($stock_in_transactions as $t) {
    $total_in += $t['quantity'];
}
foreach ($stock_out_transactions as $t) {
    $total_out += $t['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Inventory System</title>
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
        
        .container { padding: 20px; max-width: 1400px; margin: 0 auto; }
        
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card h3 { font-size: 14px; opacity: 0.9; margin-bottom: 10px; }
        .stat-card .number { font-size: 28px; font-weight: bold; }
        
        .report-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .report-btn {
            padding: 10px 20px;
            background: #ecf0f1;
            color: #2c3e50;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .report-btn.active {
            background: #3498db;
            color: white;
        }
        .report-btn:hover {
            background: #3498db;
            color: white;
        }
        
        .date-range {
            display: flex;
            gap: 15px;
            align-items: end;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .date-group {
            display: flex;
            flex-direction: column;
        }
        .date-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .date-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #3498db; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-info { background: #3498db; color: white; }
        .btn-warning { background: #f39c12; color: white; }
        
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
        tr:hover {
            background: #f5f5f5;
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
        .badge-info { background: #3498db; color: white; }
        
        .total-row {
            background: #ecf0f1;
            font-weight: bold;
        }
        
        @media print {
            .navbar, .report-buttons, .date-range, .action-buttons, .btn {
                display: none;
            }
            .container { padding: 0; }
            .card { box-shadow: none; }
            body { background: white; }
        }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 10px; text-align: center; }
            .stats-grid { grid-template-columns: 1fr; }
            .date-range { flex-direction: column; align-items: stretch; }
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
        <!-- Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="number"><?php echo $total_products; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Inventory Value</h3>
                <div class="number">$<?php echo number_format($total_stock_value, 2); ?></div>
            </div>
            <div class="stat-card">
                <h3>Low Stock Items</h3>
                <div class="number"><?php echo $low_stock_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Out of Stock</h3>
                <div class="number"><?php echo $out_of_stock_count; ?></div>
            </div>
        </div>
        
        <!-- Report Navigation -->
        <div class="card">
            <h2>📊 Report Generator</h2>
            <div class="report-buttons">
                <a href="?type=inventory" class="report-btn <?php echo $report_type == 'inventory' ? 'active' : ''; ?>">📋 Inventory Status</a>
                <a href="?type=movement" class="report-btn <?php echo $report_type == 'movement' ? 'active' : ''; ?>">📈 Stock Movement</a>
                <a href="?type=lowstock" class="report-btn <?php echo $report_type == 'lowstock' ? 'active' : ''; ?>">⚠️ Low Stock Alert</a>
                <a href="?type=valuation" class="report-btn <?php echo $report_type == 'valuation' ? 'active' : ''; ?>">💰 Stock Valuation</a>
                <a href="?type=summary" class="report-btn <?php echo $report_type == 'summary' ? 'active' : ''; ?>">📊 Summary Report</a>
            </div>
            
            <?php if ($report_type == 'movement'): ?>
            <form method="GET" class="date-range">
                <input type="hidden" name="type" value="movement">
                <div class="date-group">
                    <label>From Date</label>
                    <input type="date" name="from_date" value="<?php echo $from_date; ?>">
                </div>
                <div class="date-group">
                    <label>To Date</label>
                    <input type="date" name="to_date" value="<?php echo $to_date; ?>">
                </div>
                <div class="date-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
            <?php endif; ?>
            
            <div class="action-buttons">
                <button onclick="window.print()" class="btn btn-primary">🖨️ Print Report</button>
                <button onclick="exportToCSV()" class="btn btn-success">📊 Export to CSV</button>
            </div>
        </div>
        
        <!-- Report Content -->
        <div class="card" id="reportContent">
            <?php if ($report_type == 'inventory'): ?>
                <h2>📋 Inventory Status Report</h2>
                <p>Generated on: <?php echo date('Y-m-d H:i:s'); ?></p>
                
                 <table id="reportTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Current Stock</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_value = 0;
                        foreach($products as $p): 
                            $value = $p['quantity'] * $p['unit_price'];
                            $total_value += $value;
                        ?>
                        <tr>
                            <td><?php echo $p['product_id']; ?></td>
                            <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($p['category_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($p['supplier_name'] ?? 'N/A'); ?></td>
                            <td><?php echo $p['quantity']; ?></td>
                            <td>$<?php echo number_format($p['unit_price'], 2); ?></td>
                            <td>$<?php echo number_format($value, 2); ?></td>
                            <td><?php echo $p['reorder_level']; ?></td>
                            <td>
                                <?php if ($p['quantity'] <= 0): ?>
                                    <span class="badge badge-danger">Out of Stock</span>
                                <?php elseif ($p['quantity'] <= $p['reorder_level']): ?>
                                    <span class="badge badge-warning">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge badge-success">In Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="total-row">
                        <td colspan="6"><strong>Total Inventory Value</strong></td>
                        <td colspan="3"><strong>$<?php echo number_format($total_value, 2); ?></strong></td>
                    </tfoot>
                </table>
                
            <?php elseif ($report_type == 'movement'): ?>
                <h2>📈 Stock Movement Report</h2>
                <p>Period: <?php echo date('d M Y', strtotime($from_date)); ?> to <?php echo date('d M Y', strtotime($to_date)); ?></p>
                
                <?php
                // Filter transactions by date
                $filtered_in = array_filter($stock_in_transactions, function($t) use ($from_date, $to_date) {
                    $date = substr($t['date'], 0, 10);
                    return $date >= $from_date && $date <= $to_date;
                });
                $filtered_out = array_filter($stock_out_transactions, function($t) use ($from_date, $to_date) {
                    $date = substr($t['date'], 0, 10);
                    return $date >= $from_date && $date <= $to_date;
                });
                
                $all_transactions = array_merge($filtered_in, $filtered_out);
                usort($all_transactions, function($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });
                
                $total_in = 0;
                $total_out = 0;
                foreach($filtered_in as $t) { $total_in += $t['quantity']; }
                foreach($filtered_out as $t) { $total_out += $t['quantity']; }
                ?>
                
                <div class="stats-grid" style="margin-bottom: 20px;">
                    <div class="stat-card" style="background: #27ae60;">
                        <h3>Total Stock In</h3>
                        <div class="number">+<?php echo $total_in; ?></div>
                    </div>
                    <div class="stat-card" style="background: #e74c3c;">
                        <h3>Total Stock Out</h3>
                        <div class="number">-<?php echo $total_out; ?></div>
                    </div>
                    <div class="stat-card" style="background: #3498db;">
                        <h3>Net Movement</h3>
                        <div class="number"><?php echo $total_in - $total_out; ?></div>
                    </div>
                    <div class="stat-card" style="background: #f39c12;">
                        <h3>Total Transactions</h3>
                        <div class="number"><?php echo count($all_transactions); ?></div>
                    </div>
                </div>
                
                <?php if (count($all_transactions) > 0): ?>
                <table id="reportTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($all_transactions as $t): ?>
                        <tr>
                            <td><?php echo $t['date']; ?></td>
                            <td><?php echo htmlspecialchars($t['product']); ?></td>
                            <td>
                                <?php if($t['type'] == 'IN'): ?>
                                    <span class="badge badge-success">IN</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">OUT</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $t['quantity']; ?></td>
                            <td><?php echo htmlspecialchars($t['notes'] ?: '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #666;">No transactions found for this period.</p>
                <?php endif; ?>
                
            <?php elseif ($report_type == 'lowstock'): ?>
                <h2>⚠️ Low Stock Alert Report</h2>
                <p>Products with stock at or below reorder level</p>
                
                <?php
                $low_stock_products = array_filter($products, function($p) {
                    return $p['quantity'] <= $p['reorder_level'];
                });
                ?>
                
                <?php if (count($low_stock_products) > 0): ?>
                <table id="reportTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Current Stock</th>
                            <th>Reorder Level</th>
                            <th>Needed to Restock</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($low_stock_products as $p): 
                            $needed = max(0, $p['reorder_level'] - $p['quantity']);
                        ?>
                        <tr>
                            <td><?php echo $p['product_id']; ?></td>
                            <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($p['category_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($p['supplier_name'] ?? 'N/A'); ?></td>
                            <td><strong style="color: #e74c3c;"><?php echo $p['quantity']; ?></strong></td>
                            <td><?php echo $p['reorder_level']; ?></td>
                            <td><strong style="color: #27ae60;"><?php echo $needed; ?></strong></td>
                            <td>
                                <?php if ($p['quantity'] <= 0): ?>
                                    <span class="badge badge-danger">Out of Stock</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Low Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="stock_in.php?product_id=<?php echo $p['product_id']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">Restock</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #27ae60;">✅ Great! No low stock items found.</p>
                <?php endif; ?>
                
            <?php elseif ($report_type == 'valuation'): ?>
                <h2>💰 Stock Valuation Report</h2>
                <p>Detailed breakdown of inventory value by category</p>
                
                <?php
                // Group by category
                $categories_summary = [];
                foreach($products as $p) {
                    $cat = $p['category_name'] ?? 'Uncategorized';
                    if (!isset($categories_summary[$cat])) {
                        $categories_summary[$cat] = ['value' => 0, 'count' => 0, 'items' => []];
                    }
                    $value = $p['quantity'] * $p['unit_price'];
                    $categories_summary[$cat]['value'] += $value;
                    $categories_summary[$cat]['count']++;
                    $categories_summary[$cat]['items'][] = $p;
                }
                ?>
                
                <table id="reportTable">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Products Count</th>
                            <th>Total Value</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories_summary as $cat => $data): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($cat); ?></strong></td>
                            <td><?php echo $data['count']; ?></td>
                            <td>$<?php echo number_format($data['value'], 2); ?></td>
                            <td><?php echo round(($data['value'] / $total_stock_value) * 100, 2); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="total-row">
                        <td><strong>Total</strong></td>
                        <td><strong><?php echo $total_products; ?></strong></td>
                        <td><strong>$<?php echo number_format($total_stock_value, 2); ?></strong></td>
                        <td><strong>100%</strong></td>
                    </tfoot>
                </table>
                
            <?php else: ?>
                <h2>📊 Executive Summary Report</h2>
                <p>Complete inventory snapshot</p>
                
                <div class="stats-grid" style="margin-bottom: 20px;">
                    <div class="stat-card" style="background: #2c3e50;">
                        <h3>Total Products</h3>
                        <div class="number"><?php echo $total_products; ?></div>
                    </div>
                    <div class="stat-card" style="background: #27ae60;">
                        <h3>Total Value</h3>
                        <div class="number">$<?php echo number_format($total_stock_value, 2); ?></div>
                    </div>
                    <div class="stat-card" style="background: #e74c3c;">
                        <h3>Low Stock</h3>
                        <div class="number"><?php echo $low_stock_count; ?></div>
                    </div>
                    <div class="stat-card" style="background: #f39c12;">
                        <h3>Out of Stock</h3>
                        <div class="number"><?php echo $out_of_stock_count; ?></div>
                    </div>
                </div>
                
                <h3 style="margin: 20px 0 10px 0;">Stock Movement Summary</h3>
                <div class="stats-grid" style="margin-bottom: 20px;">
                    <div class="stat-card" style="background: #27ae60;">
                        <h3>Total Stock In</h3>
                        <div class="number">+<?php echo $total_in; ?></div>
                    </div>
                    <div class="stat-card" style="background: #e74c3c;">
                        <h3>Total Stock Out</h3>
                        <div class="number">-<?php echo $total_out; ?></div>
                    </div>
                    <div class="stat-card" style="background: #3498db;">
                        <h3>Net Change</h3>
                        <div class="number"><?php echo $total_in - $total_out; ?></div>
                    </div>
                    <div class="stat-card" style="background: #f39c12;">
                        <h3>Transactions</h3>
                        <div class="number"><?php echo count($stock_in_transactions) + count($stock_out_transactions); ?></div>
                    </div>
                </div>
                
                <h3 style="margin: 20px 0 10px 0;">Top Products by Value</h3>
                <?php
                $top_products = $products;
                usort($top_products, function($a, $b) {
                    return ($b['quantity'] * $b['unit_price']) - ($a['quantity'] * $a['unit_price']);
                });
                $top_products = array_slice($top_products, 0, 5);
                ?>
                <table id="reportTable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; foreach($top_products as $p): 
                            $value = $p['quantity'] * $p['unit_price'];
                        ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                            <td><?php echo $p['quantity']; ?></td>
                            <td>$<?php echo number_format($p['unit_price'], 2); ?></td>
                            <td>$<?php echo number_format($value, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function exportToCSV() {
            let table = document.getElementById('reportTable');
            if (!table) {
                alert('No data to export');
                return;
            }
            
            let csv = [];
            let rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                let row = [];
                let cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length; j++) {
                    let text = cols[j].innerText;
                    // Remove any HTML tags
                    text = text.replace(/<[^>]*>/g, '');
                    // Escape quotes
                    text = text.replace(/"/g, '""');
                    // Wrap in quotes
                    row.push('"' + text + '"');
                }
                csv.push(row.join(','));
            }
            
            let csvContent = csv.join('\n');
            let blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement('a');
            let url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', 'inventory_report_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>