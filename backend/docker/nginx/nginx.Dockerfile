FROM my-it-backend-php:latest AS source

FROM --platform=linux/amd64 nginx:1.21.6-alpine
COPY http.conf /etc/nginx/conf.d/default.conf
RUN set -x ; \
  addgroup -g 82 -S www-data ; \
  adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1
COPY --from=source --chown=www-data:www-data /var/www/html/public /var/www/html/public
