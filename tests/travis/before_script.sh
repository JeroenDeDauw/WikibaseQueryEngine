#! /bin/sh

set -x

if [ "$DB" = 'mysql' ] || [ "$DB" = 'mysqli' ]
then
	mysql --user root < tests/createMySQLTestDB.sql
fi

if [ "$DB" = 'pgsql' ]
then
	sudo /etc/init.d/postgresql stop

	# Travis@support: Try adding a sleep of a few seconds between starting PostgreSQL
	# and the first command that accesses PostgreSQL
	sleep 3

	sudo /etc/init.d/postgresql start
	sleep 3

	psql -c 'create database qe_pg_tests_tmp;' -U postgres
	psql -c 'create database qe_pg_tests;' -U postgres

	sudo service postgresql stop
	sudo service postgresql start $POSTGRESQL_VERSION
fi