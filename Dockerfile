FROM php:7.3-cli-alpine as install_deps
RUN apk add --no-cache --update inotify-tools

FROM composer:latest as composer
WORKDIR /app
COPY composer.json composer.lock /app/
RUN composer install --no-interaction --no-suggest --no-progress --no-autoloader --no-dev
COPY . /app/
RUN composer dump-autoload

FROM install_deps
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV HOTFOLDER=/hotfolder
ENV ARCHIVE=$HOTFOLDER/archive
ENV FORM_FIELD_NAME=file
ENV PATTERN='*'
ENV PURGE_INTERVAL=3600

WORKDIR /app
ENTRYPOINT ["/app/entrypoint.sh"]

RUN mkdir -p $ARCHIVE
VOLUME $HOTFOLDER

COPY --from=composer /app /app
# Warm the cache
RUN bin/console
