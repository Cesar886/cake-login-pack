#!/usr/bin/env bash
set -euo pipefail

# Comprobaciones básicas
if ! command -v composer >/dev/null 2>&1; then
  echo "Falta composer. Instálalo: sudo apt install composer"
  exit 1
fi

# Crear proyecto CakePHP
APP_DIR="app"
if [ -d "$APP_DIR" ]; then
  echo "El directorio '$APP_DIR' ya existe. Omitiendo create-project."
else
  echo "Creando proyecto CakePHP en '$APP_DIR'..."
  composer create-project --prefer-dist cakephp/app "$APP_DIR"
fi

# Entrar al proyecto
cd "$APP_DIR"

# Generar salt si no existe
if ! grep -q "Security.salt" config/app_local.php; then
  echo "Generando Security.salt..."
  bin/cake security salt
fi

# Copiar archivos del overlay
echo "Aplicando archivos del login..."
PACK_ROOT="$(cd .. && pwd)"
cp -f "$PACK_ROOT/overlay/config/routes.php" config/routes.php
mkdir -p src/Controller templates/Users templates/Pages webroot/js config
cp -f "$PACK_ROOT/overlay/src/Controller/UsersController.php" src/Controller/UsersController.php
cp -f "$PACK_ROOT/overlay/templates/Users/login.php" templates/Users/login.php
cp -f "$PACK_ROOT/overlay/templates/Pages/home.php" templates/Pages/home.php
cp -f "$PACK_ROOT/overlay/webroot/js/auth-guard.js" webroot/js/auth-guard.js
cp -f "$PACK_ROOT/overlay/config/users.php" config/users.php

echo "Listo. Ejecuta ahora:"
echo "cd app && bin/cake server -H 0.0.0.0 -p 8000"
