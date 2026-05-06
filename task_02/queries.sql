SELECT * FROM students ORDER BY name;
SELECT * FROM students WHERE department='CSE' ORDER BY dob;
SELECT department, COUNT(*) AS total FROM students GROUP BY department;