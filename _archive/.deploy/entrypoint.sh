#!/bin/bash

# Make internal Docker IP address available to processes.
INTERNAL_IP=$(ip route get 1 | awk '{print $NF;exit}')
export INTERNAL_IP

# Replace Startup Variables
# MODIFIED_STARTUP=$(eval echo "$(echo "${STARTUP}" | sed -e 's/{{/${/g' -e 's/}}/}/g')")
# echo ":/home/container$ ${MODIFIED_STARTUP}"


php artisan key:generate


# Run the Server
supervisord -c .deploy/config/supervisor.conf