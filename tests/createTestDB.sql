CREATE DATABASE qengine_tests;
CREATE USER 'qengine_tester'@'localhost' IDENTIFIED BY 'mysql_is_evil';
GRANT ALL PRIVILEGES ON qengine_tests.* TO 'qengine_tester'@'localhost';