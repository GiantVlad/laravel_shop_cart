#!/bin/sh
url=http://localhost:80/health
status_code=$(curl --write-out %{http_code} --silent --output /dev/null ${url} || echo "000")
if [ "$status_code" = "200" ] || [ "$status_code" = "302" ]; then
  exit 0
else
  exit 1
fi
