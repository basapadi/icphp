#!/bin/bash
set -e

#php artisan native:build linux x64

VERSION=$(grep '^NATIVEPHP_APP_VERSION=' .env | cut -d '=' -f2 | sed 's/["'\'' ]//g' | tr -d '\r' | xargs)

cp dist/IhandCashier-$VERSION.AppImage packaging/arch/IhandCashier-$VERSION.AppImage

sed -i "s/^pkgver=.*/pkgver=$VERSION/" packaging/arch/PKGBUILD

# Build paket Arch
cd packaging/arch
makepkg -si
