#!/usr/bin/env bash
set -e

# Simple environment setup script for the Laravel project

check_command() {
    if ! command -v "$1" >/dev/null 2>&1; then
        echo "Error: $1 is not installed or not in PATH." >&2
        exit 1
    fi
}

check_command php
check_command composer
check_command node
check_command npm

# Install PHP and Node dependencies
composer install
npm install

# Copy .env if it does not exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate app key and run migrations
php artisan key:generate
php artisan migrate --seed

# Build frontend assets
npm run build

# Optionally start the application if requested
if [ "$1" = "start" ] || [ "$1" = "serve" ] || [ "$1" = "--start" ]; then
    if grep -q "\"dev\"" composer.json >/dev/null 2>&1; then
        composer dev
    else
        php artisan serve
    fi
fi
