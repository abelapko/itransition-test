FROM yiisoftware/yii2-php:7.4-apache as php

# Install tool for fix DOS to Unix files format
RUN apt-get update -y && \
  apt-get install -y dos2unix

# Add docker-compose-wait tool -------------------
ENV WAIT_VERSION 2.7.2
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/$WAIT_VERSION/wait /wait
RUN chmod +x /wait

CMD /wait
