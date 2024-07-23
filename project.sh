#!/bin/sh

install=false

while [[ "$#" -gt 0 ]]; do
    case $1 in
        -i|--install) install=true; shift ;;
#        -u|--uglify) uglify=1 ;;
        *) echo "Unknown parameter passed: $1"; exit 1 ;;
    esac
    shift
done
if [[ "$install" == true ]]; then
      echo 'Installation';
      docker-compose --env-file .env.install up --build;
    else
      echo 'Run';
      docker-compose --env-file .env.local up;
fi

