CREATE TABLE students (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  dob TEXT NOT NULL,
  department TEXT NOT NULL,
  phone TEXT NOT NULL
);

INSERT INTO students(name, email, dob, department, phone)
VALUES ('Asha', 'asha@example.com', '2005-02-10', 'CSE', '9876543210');

SELECT * FROM students;
