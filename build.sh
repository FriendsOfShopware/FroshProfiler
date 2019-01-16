#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag --sort=-creatordate | head -1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf ${PLUGIN_NAME} ${PLUGIN_NAME}-*.zip

# Build new release
mkdir -p ${PLUGIN_NAME}
git archive ${commit} | tar -x -C ${PLUGIN_NAME}
composer install --no-dev -n -o -d ${PLUGIN_NAME}
( find ./${PLUGIN_NAME} -type d -name ".git" && find ./${PLUGIN_NAME} -name ".gitignore" && find ./${PLUGIN_NAME} -name ".gitmodules" ) | xargs rm -r
rm -r ./${PLUGIN_NAME}/vendor/shyim/var-dumper/Tests/
zip -x "*build.sh*" -x "*.MD" -r FroshProfiler-${commit}.zip ${PLUGIN_NAME}
