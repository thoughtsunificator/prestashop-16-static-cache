FROM prestashop/prestashop:1.6

# To run files with the same group as your primary user
ARG GROUP_ID
ARG USER_ID

RUN apt-get update \
  && apt-get install -y libmemcached11 libmemcachedutil2 build-essential libmemcached-dev libz-dev memcached \
  && pecl install memcached-2.2.0  \
  && docker-php-ext-enable memcached

RUN pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug

RUN pecl install apcu-4.0.11 \
	&& docker-php-ext-enable apcu

RUN groupmod -g $GROUP_ID www-data \
	&& usermod -u $USER_ID -g $GROUP_ID www-data

COPY .docker/wait-for-it.sh /tmp/
COPY .docker/docker_run_git.sh /tmp/

RUN chmod +x "/tmp/docker_run_git.sh"
RUN chmod +x "/tmp/wait-for-it.sh"

CMD ["/tmp/docker_run_git.sh"]

