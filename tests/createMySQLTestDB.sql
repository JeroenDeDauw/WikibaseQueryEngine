CREATE DATABASE IF NOT EXISTS qe_tests;
CREATE DATABASE IF NOT EXISTS qe_tests_tmp;

CREATE USER 'qe_tester'@'localhost' IDENTIFIED BY 'mysql_is_evil';
GRANT ALL PRIVILEGES ON qe_tests.* TO 'qe_tester'@'localhost';
GRANT ALL PRIVILEGES ON qe_tests_tmp.* TO 'qe_tester'@'localhost';