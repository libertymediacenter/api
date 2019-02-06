#!/usr/bin/env bash

export PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin

echo "Setting nginx config for env..."
: "${NGINX_APP_MODE:?Need to set NGINX_APP_MODE non-empty}"
: "${NGINX_APP_BOOTSTRAP:?Need to set NGINX_APP_BOOTSTRAP non-empty}"

sed -i.bak "s/##NGINX_APP_BOOTSTRAP##/${NGINX_APP_BOOTSTRAP}/g" /etc/nginx/nginx.conf

echo "Check nginx config..."
nginx -t

echo "Starting nginx..."
nginx

multitail -ci green /var/log/nginx/access.log -ci red -I /var/log/nginx/error.log
