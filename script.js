$(document).ready(function() {
    
    // Toggle sidebar on mobile
    $('.menu-toggle').click(function() {
        $('.sidebar').toggleClass('active');
    });
    
    // Dropdown toggle
    $('.dropdown-toggle').click(function(e) {
        e.preventDefault();
        $(this).next('.dropdown-menu').slideToggle();
        $(this).find('i:last-child').toggleClass('fa-chevron-down fa-chevron-up');
    });
    
    // Close modal
    $('.close-modal, .modal').click(function(e) {
        if (e.target == this) {
            $(this).hide();
        }
    });
    
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
    
    // Confirm delete
    $('.delete-btn').click(function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
    
    // Search functionality
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Print report
    $('#printReport').click(function() {
        window.print();
    });
    
    // Export to CSV
    $('#exportCSV').click(function() {
        var csv = [];
        var rows = $('table tr');
        
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll('td, th');
            for (var j = 0; j < cols.length; j++) {
                row.push(cols[j].innerText);
            }
            csv.push(row.join(','));
        }
        
        downloadCSV(csv.join('\n'), 'report.csv');
    });
    
    function downloadCSV(csv, filename) {
        var csvFile = new Blob([csv], {type: 'text/csv'});
        var downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }
    
    // Form validation
    $('form').submit(function(e) {
        var isValid = true;
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill all required fields');
        }
    });
    
    // Numeric input validation
    $('input[type="number"]').on('input', function() {
        if ($(this).val() < 0) {
            $(this).val(0);
        }
    });
    
    // Load dashboard stats via AJAX
    if ($('#dashboard-stats').length) {
        loadDashboardStats();
    }
});

function loadDashboardStats() {
    $.ajax({
        url: 'ajax/get_stats.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#total-products').text(data.total_products);
            $('#total-categories').text(data.total_categories);
            $('#total-suppliers').text(data.total_suppliers);
            $('#low-stock').text(data.low_stock);
        },
        error: function() {
            console.log('Error loading stats');
        }
    });
}

// Live stock update
function updateStock(productId, quantity, type) {
    $.ajax({
        url: 'ajax/update_stock.php',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            type: type
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error updating stock: ' + response.message);
            }
        }
    });
}

// Search products
function searchProducts(query) {
    $.ajax({
        url: 'ajax/search_products.php',
        type: 'GET',
        data: {q: query},
        success: function(data) {
            $('#product-table-body').html(data);
        }
    });
}