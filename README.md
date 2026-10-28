# n8n local con Docker

![Magazine by GreenPC](portal/public/images/magazine-greenpc-logo-v1.png)

## Estado del proyecto

- Estado: En desarrollo
- Plataforma: n8n + Laravel
- Logotipo actual: cargado desde la marca de GreenPC

Esta configuración ejecuta n8n en Docker y conserva sus workflows y credenciales
en el volumen `n8n_data`.

## Iniciar

```powershell
docker compose up -d
```

Después abre <http://localhost:5678>.

## Consultar el estado y los logs

```powershell
docker compose ps
docker compose logs -f n8n
```

## Detener

```powershell
docker compose stop
```

## Actualizar

Primero cambia la versión de la imagen en `compose.yaml` y luego ejecuta:

```powershell
docker compose pull
docker compose up -d
```

La carpeta `local-files` está disponible dentro de n8n como `/files`.

## Portal editorial en Laravel

La aplicación web está en la carpeta `portal`. Incluye una primera portada,
noticias y una bolsa de empleo con datos de demostración.

```powershell
cd portal
php artisan serve
```

La aplicación estará disponible en <http://127.0.0.1:8000>.

