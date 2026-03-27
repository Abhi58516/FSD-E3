    </div>
    <script>
    // Auto-hide alerts after 3 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if(alert.style.display !== 'none') {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 2000);
            }
        });
    }, 1000);
    
    // Search functionality
    function searchTable() {
        var input = document.getElementById('searchInput');
        if(!input) return;
        var filter = input.value.toLowerCase();
        var tables = document.querySelectorAll('table');
        tables.forEach(function(table) {
            var rows = table.getElementsByTagName('tr');
            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var found = false;
                for (var j = 0; j < cells.length; j++) {
                    if (cells[j] && cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? '' : 'none';
            }
        });
    }
    
    // Confirm delete
    function confirmDelete() {
        return confirm('Are you sure you want to delete this item?');
    }
    </script>
    </div>
</body>
</html>