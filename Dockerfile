FROM dunglas/frankenphp

# Run FrankenPHP on plain HTTP only
ENV SERVER_NAME=:80
ENV FRANKENPHP_AUTO_HTTPS=0

# Install PHP extensions you need
RUN install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache \
    gmp

# Copy your app code
COPY . /app

ARG USER=appuser

# Non root
RUN \
    # Use "adduser -D ${USER}" for alpine based distros
    useradd ${USER}; \
    mkdir -p /app/.tempest \
    # Add additional capability to bind to port 80 and 443
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    # Give write access to /config/caddy and /data/caddy
    chown -R ${USER}:${USER} /config/caddy /data/caddy /app/.tempest

USER ${USER}


# Expose HTTP port for Traefik
EXPOSE 80
