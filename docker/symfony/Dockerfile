# The latest PHP fpm-bullseye image
FROM php:8.3-fpm-bullseye
ARG USER=docker
ARG USER_PWD=docker
ARG UID=1000
ARG GID=1000
ARG WORKDIR=/app

ENV NODE_VERSION=18.0.0

# Docker user
RUN useradd ${USER} --uid=${UID} --shell /bin/bash --create-home \
  && usermod --append --groups sudo ${USER} \
  && echo 'ALL ALL = (ALL) NOPASSWD: ALL' >> /etc/sudoers \
  && echo "${USER}:${USER_PWD}" | chpasswd

RUN mkdir ${WORKDIR}
RUN chmod -R g+w ${WORKDIR}
RUN chown -R ${USER}:${USER} ${WORKDIR}

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# dev tools (git / zip)
RUN apt-get -y update
RUN apt-get -y install git p7zip-full

# Install nvm and Node.js
USER ${USER}
WORKDIR ${WORKDIR}

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash \
    && export NVM_DIR="${HOME}/.nvm" \
    && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" \
    && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" \
    && nvm install ${NODE_VERSION} \
    && nvm use ${NODE_VERSION} \
    && npm install --global yarn

# php extension
USER root
RUN apt-get -y install libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql
RUN apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Clear packages artifact
RUN apt-get clean && apt-get --yes --quiet autoremove --purge && \
        rm -rf  /var/lib/apt/lists/* /tmp/* /var/tmp/* \
                /usr/share/doc/* /usr/share/groff/* /usr/share/info/* /usr/share/linda/* \
                /usr/share/lintian/* /usr/share/locale/* /usr/share/man/*

USER ${USER}
WORKDIR ${WORKDIR}
