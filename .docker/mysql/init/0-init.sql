CREATE TABLE accounts (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(45) NOT NULL,
    lastname VARCHAR(45) NOT NULL,
    active TINYINT (1) NOT NULL DEFAULT 1,

    balance DECIMAL(65,2) NOT NULL DEFAULT 0
) CHARSET utf8;

CREATE TABLE transactions (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    account_from INT(11),
    account_to INT(11),
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(65,2) NOT NULL
) CHARSET utf8;


delimiter //

/* Sanity check to prevent negative transactions */
CREATE TRIGGER transaction_negative_check BEFORE INSERT ON transactions
FOR EACH ROW
BEGIN
    IF NEW.amount < 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Negative transactions are not allowed! Consider re-designing this query!';
    END IF;
END;//

/* Sanity check to prevent transactions without an involved account */
CREATE TRIGGER transaction_account_check BEFORE INSERT ON transactions
FOR EACH ROW
BEGIN
    IF NEW.account_from IS NULL AND NEW.account_to IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Transactions need at least one "to" or "from" account!';
    END IF;
END;//

delimiter ;