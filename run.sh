#!/bin/bash
HOST=172.17.0.1

docker run --rm -p 8181:80 \
  --env APP_NAME2=AnalyticsBackend1 \
  --env DB_HOST=$HOST \
  --env REDIS_HOST=$HOST \
  --env INFLUX_HOST=$HOST \
  --env ELASTICSEARCH_HOST=$HOST \
  --env RABBITMQ_HOST=$HOST \
  --name analytics-backend-test \
  analytics-backend-test:v1