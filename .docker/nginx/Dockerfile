FROM nginx:1.15

ARG INSTALL_DIR="/opt"

COPY .docker/nginx.conf /etc/nginx/nginx.conf

# Installing dependencies
COPY . ${INSTALL_DIR}
COPY .docker/nginx/docker-entrypoint.sh /docker-entrypoint.sh
COPY .docker/nginx/certs /etc/nginx/certs

RUN set -xe \
 && chmod +x /docker-entrypoint.sh \
 && apt-get update \
 && apt-get install -y \
    multitail

WORKDIR ${INSTALL_DIR}

ENTRYPOINT "/docker-entrypoint.sh"
