#!/bin/bash

FILE=status.lock

if [ -f "$FILE" ]; then
  if [[ $1 == "run" ]]; then
      docker compose --env-file .env.local up;
  fi
  if [[ $1 == "php" ]]; then
      docker compose --env-file .env.local exec -it "php" /bin/bash;
  fi
else
  echo "Container not build. Run command ./container.sh build";
  if [[ $1 == "build" ]]; then
      touch status.lock;
      docker compose --env-file .env.local up --build;
  else
      echo "Container is build";
      exit;
  fi
fi
