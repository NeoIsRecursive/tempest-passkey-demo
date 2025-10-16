#!/bin/bash

./tempest cache:clear --force --internal --all
./tempest discovery:generate
./tempest migrate:up --force

exec docker-php-entrypoint --config /etc/frankenphp/Caddyfile --adapter caddyfile
