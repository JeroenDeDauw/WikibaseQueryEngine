#! /bin/bash

set -x

if ["$STANALONE" -eq "yes"]
then
	composer install
	phpunit --testsuite=QueryEngine
else
	cd ..
	pwd
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/core.git phase3 --depth 1
	cd phase3
	mysql -e 'create database its_a_mw;'
	php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass nyan TravisWiki admin
	cd extensions
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/extensions/Diff.git
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/extensions/DataValues.git
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/extensions/Ask.git
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/extensions/Wikibase.git
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/extensions/WikibaseQueryEngine.git
	cd WikibaseQueryEngine
	phpunit
fi
