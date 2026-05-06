const express = require('express');
const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const app = express();
const db = new sqlite3.Database('students.db');

app.use(express.json());
app.use(express.static(path.join(__dirname)));

db.run(`CREATE TABLE IF NOT EXISTS students(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT, email TEXT, dob TEXT, department TEXT, phone TEXT
)`);

app.post('/students', (req, res) => {
  const {name, email, dob, department, phone} = req.body;
  db.run('INSERT INTO students(name,email,dob,department,phone) VALUES(?,?,?,?,?)',
    [name, email, dob, department, phone],
    function(err){
      if(err) return res.status(500).json({error: err.message});
      res.json({message:'Student saved', id:this.lastID});
    });
});

app.get('/students', (req, res) => {
  db.all('SELECT * FROM students', [], (err, rows) => {
    if(err) return res.status(500).json({error: err.message});
    res.json(rows);
  });
});

app.listen(3000, ()=>console.log('Server running on http://localhost:3000'));
