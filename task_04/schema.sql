CREATE TABLE customers(id INTEGER PRIMARY KEY, name TEXT);
CREATE TABLE products(id INTEGER PRIMARY KEY, name TEXT, price REAL);
CREATE TABLE orders(id INTEGER PRIMARY KEY, customer_id INT, product_id INT, qty INT, total REAL);
SELECT c.name,p.name,o.qty,o.total FROM orders o JOIN customers c ON o.customer_id=c.id JOIN products p ON o.product_id=p.id ORDER BY o.total DESC;
SELECT MAX(total) AS highest_value_order FROM orders;
SELECT customer_id, COUNT(*) AS orders_count FROM orders GROUP BY customer_id ORDER BY orders_count DESC LIMIT 1;