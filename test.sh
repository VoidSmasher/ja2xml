#!/bin/sh

#git
git pull origin test;
git submodule sync;
git submodule update --init;

#sh
sh install.sh
sh daemons.sh

# make current hash file
git log -1 | awk '{ if ($0 ~ /commit/) print $2  }'>commit.hash