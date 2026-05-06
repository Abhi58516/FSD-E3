BEGIN TRANSACTION;
UPDATE accounts SET balance = balance - 500 WHERE user_id = 1;
UPDATE accounts SET balance = balance + 500 WHERE user_id = 2;
COMMIT;
-- On failure:
ROLLBACK;