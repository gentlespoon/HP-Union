#!/bin/sh

mkdir /overlay/svr/hp-union.com/tmp
wget https://github.com/gentlespoon/hp-union.com/archive/master.zip -O /overlay/svr/hp-union.com/tmp/hp-union.zip
unzip /overlay/svr/hp-union.com/tmp/hp-union.zip -d /overlay/svr/hp-union.com/tmp/
cp -r /overlay/svr/hp-union.com/tmp/hp-union.com-master/* /overlay/svr/hp-union.com
rm /overlay/svr/hp-union.com/tmp -rf
