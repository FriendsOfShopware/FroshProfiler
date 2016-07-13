#!/bin/bash

lastTag=$(git tag | tail -n 1)
customTag=$1

if [ "$customTag" != "" ]; then lastTag=$customTag; fi
if [ "$lastTag" = "" ]; then lastTag="master"; fi

rm -f ShyimProfiler-${lastTag}.zip
rm -rf ShyimProfiler
mkdir -p ShyimProfiler
git archive $lastTag | tar -x -C ShyimProfiler

cd ShyimProfiler
composer install --no-dev -n -o
cd ../
zip -r ShyimProfiler-${lastTag}.zip ShyimProfiler
