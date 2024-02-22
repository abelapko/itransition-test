FROM yiisoftware/yii2-php:7.4-apache as php

# install decimal extension for work with money
RUN apt-get update -y && \
  apt-get install -y libmpdec-dev && \
  pecl install decimal && \
  docker-php-ext-enable decimal