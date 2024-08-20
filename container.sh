#!/bin/bash

FILE=bulid.lock

if [ -f "$FILE" ]; then
  if [[ $1 == "start" ]]; then
      docker compose --env-file .env.local up;
  fi
  if [[ $1 == "restart" ]]; then
      docker compose --env-file .env.local restart;
  fi
  if [[ $1 == "stop" ]]; then
      docker compose --env-file .env.local stop $2;
  fi
  if [[ $1 == "php" ]]; then
      docker compose --env-file .env.local exec -it "php" /bin/bash;
  fi
else
  echo "Container not build. Run command ./container.sh build";
  if [[ $1 == "build" ]]; then
      touch bulid.lock;
      docker compose --env-file .env.local up --build;
  else
      echo "Container is build";
      exit;
  fi
fi
