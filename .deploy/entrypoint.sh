#!/bin/bash
cd /home/container/downstream || exit

# Make internal Docker IP address available to processes.
INTERNAL_IP=$(ip route get 1 | awk '{print $NF;exit}')
export INTERNAL_IP

# Replace Startup Variables
MODIFIED_STARTUP=$(eval echo "$(echo "${STARTUP}" | sed -e 's/{{/${/g' -e 's/}}/}/g')")
echo ":/home/container$ ${MODIFIED_STARTUP}"


composer install
php artisan key:generate

cd vue_app/

yarn --prod
yarn build
cd ..

# Run the Server
eval "${MODIFIED_STARTUP}"