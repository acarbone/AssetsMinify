#!/usr/bin/env bash

if [ $# -lt 1 ]; then
	echo "usage: $0 <version>"
	exit 1
fi

VERSION=$1

PLUGIN=$( cd "$( dirname $( dirname "${BASH_SOURCE[0]}" ))" && pwd )
SVN=$( cd "$( dirname "${PLUGIN}" )/svn" && pwd )
TRUNK=$SVN/trunk

svn delete $TRUNK/*
cp -r $PLUGIN/* $TRUNK
rm -rf $TRUNK/bin $TRUNK/docs $TRUNK/tests $TRUNK/.gitignore $TRUNK/.travis.yml $TRUNK/composer.json $TRUNK/composer.lock $TRUNK/phpunit.xml $TRUNK/.git
mkdir $SVN/tags/$VERSION
cp -r $TRUNK/* $SVN/tags/$VERSION