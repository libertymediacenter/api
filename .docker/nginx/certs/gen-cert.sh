#!/usr/bin/env bash

openssl req -x509 -nodes -days 730 -newkey rsa:2048 \
 -keyout libertymediacenter.local.key -out libertymediacenter.local.crt -config req.cnf -sha256
