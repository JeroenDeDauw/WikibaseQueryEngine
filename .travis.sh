#! /bin/bash

set -x

if [ "$1" == "yeah" ]
then
	composer install
	phpunit --testsuite=QueryEngine
else
	cd ..
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/core.git phase3 --depth 1

	cd phase3
	mysql -e 'create database its_a_mw;'

	cd extensions
	composer create-project wikibase/query-engine:dev-master WikibaseQueryEngine --keep-vcs

	cd ..
	php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass nyan TravisWiki admin

	cd extensions/WikibaseQueryEngine
	phpunit
fi
