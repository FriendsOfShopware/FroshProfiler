#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag | tail -n 1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf ShyimProfiler ShyimProfiler-*.zip

# Build new release
mkdir -p ShyimProfiler
git archive ${commit} | tar -x -C ShyimProfiler
composer install --no-dev -n -o -d ShyimProfiler
zip -r ShyimProfiler.zip ShyimProfiler
