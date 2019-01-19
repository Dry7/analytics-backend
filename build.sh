#!/bin/bash
docker build -t analytics-backend-test:v1 -f docker/php/Dockerfile --build-arg DOCKERFILE_PATH=docker/php .