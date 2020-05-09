FROM nginx:mainline-alpine

RUN set -xe \
    && apk update \
    && apk upgrade \
    && rm -rf /tmp/* /var/cache/apk/*

COPY app.conf /etc/nginx/conf.d/default.conf
