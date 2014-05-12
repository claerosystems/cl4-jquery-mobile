#!/bin/bash
# checkout the correct versions of the module (currently v3.3.2 for most Kohana modules and kohana_v3.3/master for cl4)
echo "-- submodule init & update" &&
git submodule init && git submodule update &&
echo "-- module > Kohana Auth" &&
cd modules/auth && git checkout 3.3/master && git pull && git checkout v3.3.2 &&
echo "-- module > Kohana Cache" &&
cd ../cache &&git checkout3.3/master && git pull && git checkout v3.3.0 &&
echo "-- module > Kohana Codebench" &&
cd ../codebench && git checkout3.3/master && git pull && git checkout v3.3.0 &&
echo"-- module > Kohana Database" &&
cd ../database &&git checkout3.3/master && git pull && git checkout v3.3.0 &&
echo"-- module > Kohana Image" &&
cd ../image &&git checkout3.3/master && git pull && git checkout v3.3.0 &&
echo"-- module > Kohana Minion" &&
cd ../minion &&git checkout3.3/master && git pull && git checkout v3.3.0 &&
echo"-- module > Kohana ORM" &&
cd ../orm &&git checkout3.3/master && git pull && git checkout v3.3.0 &&
echo"-- module > Kohana Unit Test" &&
cd ../unittest &&git checkout3.3/master && git pull && git checkout v3.3.0 &&
echo"-- module > Kohana Userguide" &&
cd ../userguide &&git checkout3.3/master && git pull && git checkout v3.3.0 &&
echo"-- module > cl4" &&
cd ../cl4 && git checkout master && git pull && git checkout kohana_v3.3/master &&
echo"-- module > cl4-jquery-mobile" &&
cd ../cl4base && git checkout master && git pull && git checkout kohana_v3.3/master &&
echo"-- module > cl4docroot" &&
cd ../../html/cl4 && git checkout master && git pull && git checkout kohana_v3.3/master &&
echo"-- module > Kohana System" &&
cd ../../system && git checkout3.1/master && git pull && git checkout v3.3.0 &&
cd ../..