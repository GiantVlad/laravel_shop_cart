#!/bin/sh

url=roadrunner:2114/health?plugin=http

status_code=$(curl --write-out %{http_code} --silent --output /dev/null ${url})

echo ${status_code}

if [[ "$status_code" -ne 200 ]] ; then
  exit 1
else
  exit 0
fi
