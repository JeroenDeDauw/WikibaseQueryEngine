#! /bin/sh

set -x

if [ "$DB" = 'mysql' ] || [ "$DB" = 'mysqli' ]
then
	mysql --user root < tests/createMySQLTestDB.sql
fi

if [ "$DB" = 'pgsql' ]
then
	psql -c 'DROP DATABASE IF EXISTS qe_pg_tests_tmp;' -U postgres
	psql -c 'DROP DATABASE IF EXISTS qe_pg_tests;' -U postgres
	psql -c 'create database qe_pg_tests_tmp;' -U postgres
	psql -c 'create database qe_pg_tests;' -U postgres

	sudo service postgresql stop
	sudo service postgresql start $POSTGRESQL_VERSION
fi