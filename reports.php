<?php
require_once 'config.php';
requireLogin();
$page_title = "Reports";
include 'header.php';

$report_type = isset($_GET['type']) ? $_GET['type'] : 'inventory';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-bar"></i> Generate Reports
    </div>
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label>Report Type</label>
                <select name="type" id="reportType" onchange="this.form.submit()">
                    <option value="inventory" <?php echo $report_type == 'inventory' ? 'selected' : ''; ?>>Inventory Status</option>
                    <option value="movement" <?php echo $report_type == 'movement' ? 'selected' : ''; ?>>Stock Movement</option>
                    <option value="lowstock" <?php echo $report_type == 'lowstock' ? 'selected' : ''; ?>>Low Stock Items</option>
                </select>
            </div>
            
            <div id="dateRange" style="display: <?php echo $report_type == 'movement' ? 'flex' : 'none'; ?>; gap: 10px;">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" name="from_date" value="<?php echo $from_date; ?>">
                </div>
                
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" name="to_date" value="<?php echo $to_date; ?>">
                </div>
                
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>
        
        <div style="margin-top: 10px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-file-alt"></i> Report Results
    </div>
    <div class="card-body">
        <?php if ($report_type == 'inventory'): ?>
            <?php
            $query = "SELECT p.*, c.category_name, s.supplier_name,
                     (p.quantity * p.unit_price) as total_value
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.category_id
                     LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                     ORDER BY p.product_name";
            $result = mysqli_query($conn, $query);
            $total_value = 0;
            ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Total Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $total_value += $row['total_value'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>$<?php echo number_format($row['unit_price'], 2); ?></td>
                        <td>$<?php echo number_format($row['total_value'], 2); ?></td>
                        <td>
                            <?php if ($row['quantity'] <= $row['reorder_level']): ?>
                                <span class="badge badge-warning">Low Stock</span>
                            <?php else: ?>
                                <span class="badge badge-success">Normal</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f0f0f0; font-weight: bold;">
                        <td colspan="5">Total Inventory Value:</td>
                        <td colspan="2">$<?php echo number_format($total_value, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
        <?php elseif ($report_type == 'movement'): ?>
            <?php
            $query = "SELECT st.*, p.product_name 
                     FROM stock_transactions st
                     JOIN products p ON st.product_id = p.product_id
                     WHERE DATE(st.transaction_date) BETWEEN '$from_date' AND '$to_date'
                     ORDER BY st.transaction_date DESC";
            $result = mysqli_query($conn, $query);
            $total_in = 0;
            $total_out = 0;
            ?>
            
            <table>
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
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        if ($row['transaction_type'] == 'IN') $total_in += $row['quantity'];
                        else $total_out += $row['quantity'];
                    ?>
                    <tr>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['transaction_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td>
                            <?php if($row['transaction_type'] == 'IN'): ?>
                                <span class="badge badge-success">IN</span>
                            <?php else: ?>
                                <span class="badge badge-danger">OUT</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f0f0f0; font-weight: bold;">
                        <td colspan="3">Summary:</td>
                        <td>IN: <?php echo $total_in; ?> | OUT: <?php echo $total_out; ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
        <?php elseif ($report_type == 'lowstock'): ?>
            <?php
            $query = "SELECT p.*, c.category_name, s.supplier_name,
                     (p.reorder_level - p.quantity) as needed
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.category_id
                     LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                     WHERE p.quantity <= p.reorder_level
                     ORDER BY needed DESC";
            $result = mysqli_query($conn, $query);
            ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Needed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?></td>
                        <td><strong style="color: #e74c3c;"><?php echo $row['quantity']; ?></strong></td>
                        <td><?php echo $row['reorder_level']; ?></td>
                        <td><strong style="color: #27ae60;"><?php echo max(0, $row['needed']); ?></strong></td>
                        <td>
                            <a href="stock_in.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-primary btn-sm">
                                Restock
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('reportType').addEventListener('change', function() {
    var dateRange = document.getElementById('dateRange');
    dateRange.style.display = this.value == 'movement' ? 'flex' : 'none';
});
</script>

<?php include 'footer.php'; ?>