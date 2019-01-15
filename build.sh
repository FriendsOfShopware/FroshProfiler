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
( find ./FroshProfiler -type d -name ".git" && find ./FroshProfiler -name ".gitignore" && find ./FroshProfiler -name ".gitmodules" ) | xargs rm -r
zip -x "*build.sh*" -x "*.MD" -r FroshProfiler-${commit}.zip FroshProfiler
