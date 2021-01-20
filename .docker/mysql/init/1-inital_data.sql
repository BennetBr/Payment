INSERT INTO accounts (firstname, lastname, balance)
VALUES
('Elon', 'Musk', 15560),
('Jeff', 'Bezos', 11469.87),
('Bill', 'Gates', 9800),

('Larry', 'Lässig', 248.2),
('Mike', 'Mittellos', 30.13)
;

INSERT INTO transactions (account_from, account_to, amount)
VALUES
(NULL, 1, 15560),
(NULL, 2, 11300),
(NULL, 3, 9600),
(NULL, 4, 448.2),
(NULL, 5, 200),
(5, 2, 169.87),
(4, 3, 200)
;