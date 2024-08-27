#!/bin/bash

FILE=bulid.lock

if [ -f "$FILE" ]; then
  if [[ $1 == "start" ]]; then
      docker compose --env-file .env.build up;
  fi
  if [[ $1 == "restart" ]]; then
      docker compose --env-file .env.build restart;
  fi
  if [[ $1 == "stop" ]]; then
      docker compose --env-file .env.build stop $2;
  fi
  if [[ $1 == "php" ]]; then
      docker compose --env-file .env.build exec -it "php" /bin/bash;
  fi
else
  echo "Container not build. Run command ./container.sh build";
  if [[ $1 == "build" ]]; then
      touch bulid.lock;
      docker compose --env-file .env.build up --build;
  else
      echo "Container is build";
      exit;
  fi
fi
