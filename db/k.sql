create database surgical
CREATE USER 'surgical_user'@'localhost' IDENTIFIED BY 'kb123';
grant all privileges on *.* to 'surgical_user'@'localhost' with GRANT OPTION;
CREATE USER 'surgical_user'@'%' IDENTIFIED BY 'kb123';
grant all privileges on *.* to 'surgical_user'@'%' with GRANT OPTION;

CREATE USER 'surgical_admin'@'localhost' IDENTIFIED BY 'kb123';
grant all privileges on *.* to 'surgical_user'@'localhost' with GRANT OPTION;
CREATE USER 'surgical_admin'@'%' IDENTIFIED BY 'kb123';
grant all privileges on *.* to 'surgical_user'@'%' with GRANT OPTION;
~                                                                                                                      
~                                                                                                                      
~                                                                                                                      
~aCREATE TABLE users (
    id INTEGER  NOT NULL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(32) NULL,
    password_salt VARCHAR(32) NULL,
    real_name VARCHAR(150) NULL
)                                                                                                                      
