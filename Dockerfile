FROM php:8.3-apache

# Composerを導入する（PHPMailerなどの依存ライブラリ管理に使用）
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# イメージをビルドするときに、srcをイメージ内部へコピーする
COPY src/ /var/www/html/

# 依存ライブラリ（PHPMailer等）をインストールする
RUN composer install --no-dev --working-dir=/var/www/html

# Apache設定の一部をユーザーが上書きする仕組みを許可する
RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# infra-net上のCloudflare Tunnelからこのコンテナへ8081番ポートで到達できるようにする
RUN sed -i 's/Listen 80/Listen 8081/g' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8081>/g' /etc/apache2/sites-enabled/000-default.conf

EXPOSE 8081