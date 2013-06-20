#! /bin/bash

set -x

cd ..

if [ "$1" == "QueryEngineStandalone" ]
then
	mkdir phase3/extensions
	cd phase3/extensions
else
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/core.git phase3 --depth 1
	cd phase3

	mysql -e 'create database its_a_mw;'
	php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass nyan TravisWiki admin

	cd extensions
fi

composer create-project wikibase/query-engine:dev-master WikibaseQueryEngine --keep-vcs

if [ "$1" != "QueryEngineStandalone" ]
then
	php ../maintenance/update.php --quick
fi