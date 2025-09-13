#!/bin/bash
set -e

#php artisan native:build linux x64

VERSION=$(grep NATIVEPHP_APP_VERSION .env | cut -d '=' -f2 | tr -d '"')

cp dist/IhandCashier-$VERSION.AppImage packaging/arch/IhandCashier-$VERSION.AppImage

# Build paket Arch
cd packaging/arch
makepkg -si
