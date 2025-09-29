# CakePHP Login Pack - Sistema de Autenticación

Sistema de autenticación simple desarrollado con CakePHP 5, optimizado para desarrollo local y despliegue en Vercel.

## 🚀 Características

- ✅ Sistema de login sin base de datos
- ✅ Panel de administración para gestión de usuarios  
- ✅ Roles de usuario (admin/user)
- ✅ Interfaz moderna y responsive
- ✅ Compatible con Vercel (serverless)
- ✅ Almacenamiento en sessionStorage/localStorage

## 📦 Despliegue en Vercel

### Paso 1: Preparar el repositorio
```bash
git add .
git commit -m "Configuración para Vercel"
git push origin main
```

### Paso 2: Configurar en Vercel
1. Ve a [vercel.com](https://vercel.com) y crea un nuevo proyecto
2. Conecta tu repositorio de GitHub
3. Configura las variables de entorno:

| Variable | Valor | Entorno |
|----------|-------|---------|
| `DEBUG` | `false` | Production |
| `SECURITY_SALT` | `tu-clave-secreta-aqui` | All |

### Paso 3: Deploy
Vercel detectará automáticamente la configuración desde `vercel.json` y desplegará tu aplicación.

## 🛠 Desarrollo Local

### Requisitos
- PHP 8.x, Composer
- Puerto 8000 libre

### Pasos rápidos
```bash
# 1) Clonar y entrar al directorio
git clone [tu-repo] && cd cake-login-pack

# 2) Instalar dependencias
cd app && composer install

# 3) Iniciar servidor
php -S localhost:8000 -t webroot
# Abre: http://localhost:8000
```

## 👥 Usuarios por defecto

- **Admin**: `admin@site.com` / `admin`
- **Usuario**: `demo@site.com` / `123456`

## ➕ Añadir usuarios manualmente

Edita `app/config/users.php`:
```php
return [
  'demo@site.com' => ['password' => '123456', 'name' => 'Demo', 'role' => 'user'],
  'admin@site.com'=> ['password' => 'admin',  'name' => 'Admin', 'role' => 'admin'],
  'nuevo@site.com'=> ['password' => 'secreto','name' => 'Nuevo', 'role' => 'user'],
];
```

## 📁 Estructura del proyecto

```
/
├── api/
│   └── index.php          # Entry point para Vercel
├── app/                   # Aplicación CakePHP
│   ├── config/
│   │   └── users.php      # Base de datos de usuarios
│   ├── src/Controller/    # Controladores
│   └── templates/         # Vistas
├── vercel.json           # Configuración de Vercel
├── .vercelignore         # Archivos a ignorar
└── .env.example          # Variables de entorno
```

## 🔗 Enlaces útiles

- [Documentación de Vercel PHP](https://vercel.com/docs/runtimes/php)
- [CakePHP 5 Documentation](https://book.cakephp.org/5/)
