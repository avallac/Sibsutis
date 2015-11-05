CREATE TABLE tbl_cases (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    labNum INTEGER NOT NULL,
    name VARCHAR(128) NOT NULL,
    rule TEXT NOT NULL
);

INSERT INTO tbl_cases (labNum, name, rule) VALUES (1, 'pass1','bla bla');