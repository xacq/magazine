# Magazine by GreenPC

![Magazine by GreenPC](public/images/magazine-greenpc-logo-v1.png)

## Estado del proyecto

✅ En desarrollo

Prototipo de plataforma editorial y bolsa de empleo construido con Laravel 12.

## Requisitos locales

- PHP 8.2 o superior
- Composer
- Extensión SQLite de PHP

## Iniciar el proyecto

```powershell
composer install
php artisan migrate --seed
php artisan serve
```

Abre <http://127.0.0.1:8000>.

## Estructura principal

- `app/Models`: noticias y vacantes.
- `app/Http/Controllers`: páginas públicas.
- `database/migrations`: estructura de la base de datos.
- `database/seeders`: contenido demostrativo.
- `resources/views`: plantillas Blade.
- `public/css/site.css`: diseño visual.
- `routes/web.php`: rutas públicas.

El siguiente hito será incorporar autenticación, panel editorial y API privada
para recibir borradores desde n8n.
