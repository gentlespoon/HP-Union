#!/bin/sh

echo "======== Start Updating ========\n"
echo "\n\n> mkdir ../tmp\n"
mkdir ../tmp
echo "================================\n"

echo "\n\n> wget master.zip -O ./tmp/hp-union.zip\n"
wget https://github.com/gentlespoon/hp-union.com/archive/master.zip -O ../tmp/hp-union.zip
echo "================================\n"

echo "\n\n> unzip ../tmp/hpunion.zip -d ../tmp\n"
unzip ../tmp/hp-union.zip -d ../tmp
echo "================================\n"

echo "\n\n> cp -r ../tmp/hp-union.com-master/* ../\n"
cp -r ../tmp/hp-union.com-master/* ../
echo "================================\n"

echo "\n\n> rm ../tmp -rf\n"
rm ../tmp -rf
echo "======= Finished Update ========\n"
