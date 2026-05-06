CREATE TABLE student_logs(id INTEGER PRIMARY KEY AUTOINCREMENT, action_type TEXT, action_time TEXT DEFAULT CURRENT_TIMESTAMP);
CREATE TRIGGER log_student_insert AFTER INSERT ON students BEGIN INSERT INTO student_logs(action_type) VALUES('INSERT'); END;
CREATE TRIGGER log_student_update AFTER UPDATE ON students BEGIN INSERT INTO student_logs(action_type) VALUES('UPDATE'); END;
CREATE VIEW daily_activity AS SELECT DATE(action_time) AS log_date, action_type, COUNT(*) AS total FROM student_logs GROUP BY DATE(action_time), action_type;