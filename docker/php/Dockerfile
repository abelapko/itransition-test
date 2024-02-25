FROM yiisoftware/yii2-php:7.4-apache as php

# install decimal extension for work with money
RUN apt-get update -y && \
  apt-get install -y libmpdec-dev && \
  pecl install decimal && \
  docker-php-ext-enable decimal

# Add docker-compose-wait tool -------------------
ENV WAIT_VERSION 2.7.2
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/$WAIT_VERSION/wait /wait
RUN chmod +x /wait

CMD /wait
