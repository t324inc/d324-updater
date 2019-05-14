#!/bin/bash

BASEDIR=$(pwd);

echo "$(tput setaf 2)Checking composer version and updating if needed:$(tput sgr 0)";
composer self-update;
echo "$(tput setaf 2)Checking d324-updater version and updating if needed:$(tput sgr 0)";
composer update t324inc/d324-updater;

#running the updater;
bash ${BASEDIR}/vendor/t324inc/d324-updater/scripts/update/d324-updater.sh
