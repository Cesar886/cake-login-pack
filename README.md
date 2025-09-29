# Login CakePHP rápido (sin BD)

Este paquete **prepara un proyecto CakePHP desde cero** y agrega un login mínimo que guarda la sesión en `sessionStorage`
y preferencias en `localStorage`. Permite **añadir usuarios** editando `config/users.php`.

## Requisitos
- Ubuntu o similar
- PHP 8.x, Composer
- Puertos libres 8000 (servidor embebido) o Nginx si decides usarlo

## Pasos rápidos
```bash
# 1) Descomprime el ZIP y entra
unzip cake-login-pack.zip -d ~/ && cd ~/cake-login-pack

# 2) Ejecuta el setup (crea el proyecto Cake y aplica archivos)
bash setup.sh

# 3) Inicia el servidor embebido
cd app
bin/cake server -H 0.0.0.0 -p 8000
# Abre: http://TU_IP:8000
```

## Añadir usuarios
Edita `app/config/users.php` y agrega entradas:
```php
return [
  'demo@site.com' => ['password' => '123456', 'name' => 'Demo', 'role' => 'user'],
  'admin@site.com'=> ['password' => 'admin',  'name' => 'Admin', 'role' => 'admin'],
  'otro@site.com' => ['password' => 'secreto','name' => 'Otro', 'role' => 'user'],
];
```

## Producción con Nginx (opcional)
Archivo de ejemplo en `deploy/nginx.conf`. Ajusta la ruta de PHP-FPM según tu versión.
