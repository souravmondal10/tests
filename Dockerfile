FROM ubuntu

MAINTAINER Sourav Mondal "souravmondal10@gmail.com"


WORKDIR /var/www/html

RUN apt-get update
RUN apt -y install software-properties-common
RUN add-apt-repository ppa:ondrej/php -y
RUN apt-get update
RUN apt -y install php7.4
RUN apt-get install -y php7.4-mysql php7.4-redis
RUN touch ./process_output.log
COPY ./testapp.php ./testapp.php
COPY ../infrastructure/config.php ./config.php


CMD [ "php", "./testapp.php" ]
