ARG PHP_VERSION
FROM php:${PHP_VERSION}
USER root

ARG RASPPIDASH_VERSION
ENV DEBIAN_FRONTEND="noninteractive"

RUN apt update -y \
 && apt install -y --no-install-recommends gnupg \
 && apt clean

RUN echo "deb https://archive.raspberrypi.org/debian/ bullseye main" > /etc/apt/sources.list.d/raspi.list \
 && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 82B129927FA3303E \
 && apt update -y \
 && apt install -y --no-install-recommends git systemd libraspberrypi-bin sudo usbutils\
 && apt clean \
 && adduser www-data sudo \
 && echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers \
 && adduser www-data video \
 && ln -s /bin/systemctl /sbin/shutdown

RUN git clone --depth 1 --branch ${RASPPIDASH_VERSION} https://github.com/femto-code/Raspberry-Pi-Dashboard.git /var/www/html \
 && chown -R www-data:www-data /var/www/html \
 && chmod 755 /var/www/html
