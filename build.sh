#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag --sort=-creatordate | head -1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf FroshProfiler FroshProfiler-*.zip

# Build new release
mkdir -p FroshProfiler
git archive ${commit} | tar -x -C FroshProfiler
composer install --no-dev -n -o -d FroshProfiler
zip -r FroshProfiler-${commit}.zip FroshProfiler
