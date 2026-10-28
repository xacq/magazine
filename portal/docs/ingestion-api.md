# API de ingestión para n8n

La API recibe lotes normalizados de noticias y vacantes. Nunca publica automáticamente: los registros nuevos quedan en `pending_review`.

## Autenticación

Crear o rotar el token desde la terminal del servidor:

```bash
php artisan ingest:create-token --name=n8n-production
php artisan ingest:create-token --name=n8n-production --replace
```

Guardar el valor mostrado como credencial de tipo Header/Bearer en n8n. El token tiene las abilities `articles:ingest` y `jobs:ingest`.

Encabezados para cada solicitud:

```http
Authorization: Bearer TOKEN
Accept: application/json
Content-Type: application/json
Idempotency-Key: identificador-unico-de-la-ejecucion
```

`Idempotency-Key` es opcional, pero debe enviarse en producción. Repetir la misma clave y el mismo cuerpo devuelve el resultado anterior sin escribir nuevamente. Reutilizar la clave con otro cuerpo devuelve HTTP 409.

Dentro de Docker, si Laravel se ejecuta directamente en Windows, usar `http://host.docker.internal:8000`. En producción debe usarse HTTPS.

## Noticias

```http
POST /api/v1/ingest/articles/batch
```

```json
{
  "source": "Medio autorizado",
  "items": [
    {
      "external_id": "article-2026-001",
      "title": "Título editorial",
      "excerpt": "Resumen original para el portal.",
      "body": "Primer párrafo en texto plano.\n\nSegundo párrafo.",
      "category": "Tecnología",
      "image_url": "https://example.com/licensed-image.jpg",
      "source_url": "https://example.com/news/article-2026-001",
      "published_at": "2026-10-28T10:00:00-05:00",
      "metadata": {
        "language": "es",
        "image_license": "authorized"
      }
    }
  ]
}
```

`external_id` debe ser estable. Si la fuente no entrega uno, n8n debe generar un hash determinista de su URL canónica.

## Empleos

```http
POST /api/v1/ingest/jobs/batch
```

```json
{
  "source": "Bolsa autorizada",
  "items": [
    {
      "external_id": "job-2026-001",
      "company": "Empresa Demo",
      "title": "Desarrollador Laravel",
      "sector": "Tecnología",
      "location": "Guayaquil, Ecuador",
      "work_mode": "Híbrido",
      "employment_type": "Tiempo completo",
      "salary_min": 1200,
      "salary_max": 1800,
      "currency": "USD",
      "summary": "Resumen original de la oportunidad.",
      "description": "Descripción en texto plano.",
      "apply_url": "https://example.com/jobs/job-2026-001/apply",
      "source_url": "https://example.com/jobs/job-2026-001",
      "published_at": "2026-10-28T09:00:00-05:00",
      "expires_at": "2026-11-28T23:59:59-05:00",
      "metadata": {
        "country": "EC"
      }
    }
  ]
}
```

`work_mode` acepta `Presencial`, `Híbrido` o `Remoto`. Cada lote admite entre 1 y 50 elementos.

## Respuesta

```json
{
  "run_id": 12,
  "idempotency_key": "identificador-unico-de-la-ejecucion",
  "type": "articles",
  "source": "Medio autorizado",
  "status": "completed",
  "counts": {
    "created": 8,
    "updated": 2,
    "duplicates": 3,
    "pending_updates": 1
  },
  "items": [],
  "idempotent_replay": false
}
```

Cuando una noticia publicada o una vacante abierta cambia en la fuente, la API conserva la versión visible y guarda la nueva información como actualización pendiente dentro de `metadata`.

## Códigos HTTP relevantes

- `200`: lote procesado o replay idempotente.
- `401`: token ausente o inválido.
- `403`: token sin la ability requerida.
- `409`: conflicto de idempotencia o ejecución aún en proceso.
- `422`: JSON inválido o campos requeridos ausentes.
- `429`: límite de 60 solicitudes por minuto excedido.
