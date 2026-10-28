<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\JobOffer;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Editor Demo',
            'email' => 'editor@example.com',
        ]);

        Article::query()->insert([
            [
                'title' => 'Guayaquil acelera su transición hacia una economía más digital',
                'slug' => 'guayaquil-acelera-economia-digital',
                'excerpt' => 'Empresas, profesionales y centros de formación se preparan para una nueva etapa de servicios tecnológicos y trabajo especializado.',
                'body' => '<p>La transformación digital está cambiando la manera en que las empresas contratan, venden y organizan sus operaciones. Para los profesionales, este escenario abre nuevas oportunidades, pero también exige aprendizaje continuo.</p><p>Este artículo es contenido demostrativo para visualizar el futuro flujo editorial. En producción, cada dato será contrastado y estará acompañado por sus fuentes.</p>',
                'category' => 'Economía digital',
                'image_url' => 'https://images.unsplash.com/photo-1628004566999-83b23fdc411f?auto=format&fit=crop&w=1600&q=85',
                'source_name' => 'Contenido demostrativo',
                'source_url' => null,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cinco habilidades que ganan terreno en los procesos de selección',
                'slug' => 'habilidades-procesos-seleccion',
                'excerpt' => 'Análisis de datos, comunicación y automatización aparecen entre las competencias más transversales.',
                'body' => '<p>Los perfiles híbridos combinan conocimiento técnico con capacidad de comunicación y criterio de negocio. Aprender a trabajar con datos y herramientas de automatización ya no es exclusivo de los equipos de tecnología.</p>',
                'category' => 'Carrera',
                'image_url' => 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=1200&q=82',
                'source_name' => 'Contenido demostrativo',
                'source_url' => null,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subHours(7),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'El trabajo remoto entra en una etapa más especializada',
                'slug' => 'trabajo-remoto-especializado',
                'excerpt' => 'Las oportunidades se concentran en perfiles capaces de colaborar, documentar y entregar resultados medibles.',
                'body' => '<p>El trabajo remoto continúa evolucionando. Las empresas buscan profesionales autónomos, con comunicación escrita sólida y experiencia en herramientas digitales.</p>',
                'category' => 'Trabajo remoto',
                'image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=82',
                'source_name' => 'Contenido demostrativo',
                'source_url' => null,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cómo preparar un portafolio que explique el valor de tu trabajo',
                'slug' => 'portafolio-que-explique-tu-valor',
                'excerpt' => 'Una selección breve de casos bien explicados puede ser más efectiva que una colección extensa de proyectos.',
                'body' => '<p>Un buen portafolio presenta el problema, el proceso, las decisiones y el resultado. La claridad suele pesar tanto como el acabado visual.</p>',
                'category' => 'Guías',
                'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=82',
                'source_name' => 'Contenido demostrativo',
                'source_url' => null,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        JobOffer::query()->insert([
            [
                'company' => 'Andes Digital', 'title' => 'Desarrollador PHP / Laravel',
                'slug' => 'desarrollador-php-laravel-andes-digital', 'sector' => 'Tecnología',
                'location' => 'Guayaquil, Guayas', 'work_mode' => 'Híbrido', 'employment_type' => 'Tiempo completo',
                'salary_min' => 1400, 'salary_max' => 1900, 'currency' => 'USD',
                'summary' => 'Construye productos web y APIs para empresas de la región.',
                'description' => '<p>Buscamos experiencia con PHP moderno, Laravel, bases de datos relacionales y Git.</p><ul><li>Dos años de experiencia deseable.</li><li>Capacidad para documentar soluciones.</li><li>Trabajo colaborativo.</li></ul><p>Vacante ficticia para demostración visual.</p>',
                'apply_url' => 'https://example.com', 'status' => 'open', 'is_featured' => true,
                'published_at' => now()->subHours(3), 'expires_at' => now()->addDays(25),
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'company' => 'Nexo Comercial', 'title' => 'Analista de marketing digital',
                'slug' => 'analista-marketing-digital-nexo', 'sector' => 'Marketing',
                'location' => 'Quito, Pichincha', 'work_mode' => 'Remoto', 'employment_type' => 'Tiempo completo',
                'salary_min' => 900, 'salary_max' => 1200, 'currency' => 'USD',
                'summary' => 'Planifica campañas, analiza resultados y mejora la adquisición digital.',
                'description' => '<p>Perfil analítico con experiencia en campañas, contenido y medición de conversiones.</p><p>Vacante ficticia para demostración visual.</p>',
                'apply_url' => 'https://example.com', 'status' => 'open', 'is_featured' => false,
                'published_at' => now()->subHours(9), 'expires_at' => now()->addDays(20),
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'company' => 'Pacífico Logística', 'title' => 'Coordinador de operaciones',
                'slug' => 'coordinador-operaciones-pacifico', 'sector' => 'Operaciones',
                'location' => 'Durán, Guayas', 'work_mode' => 'Presencial', 'employment_type' => 'Tiempo completo',
                'salary_min' => 1100, 'salary_max' => 1400, 'currency' => 'USD',
                'summary' => 'Coordina rutas, indicadores y equipos para una operación de distribución.',
                'description' => '<p>Se requiere organización, manejo de indicadores y experiencia coordinando equipos.</p><p>Vacante ficticia para demostración visual.</p>',
                'apply_url' => 'https://example.com', 'status' => 'open', 'is_featured' => false,
                'published_at' => now()->subDay(), 'expires_at' => now()->addDays(18),
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'company' => 'Estudio Norte', 'title' => 'Diseñador de producto junior',
                'slug' => 'disenador-producto-junior-estudio-norte', 'sector' => 'Diseño',
                'location' => 'Ecuador', 'work_mode' => 'Remoto', 'employment_type' => 'Contrato',
                'salary_min' => 800, 'salary_max' => 1050, 'currency' => 'USD',
                'summary' => 'Apoya la investigación, prototipado y diseño de interfaces digitales.',
                'description' => '<p>Buscamos portafolio, atención al detalle y fundamentos de experiencia de usuario.</p><p>Vacante ficticia para demostración visual.</p>',
                'apply_url' => 'https://example.com', 'status' => 'open', 'is_featured' => false,
                'published_at' => now()->subDays(2), 'expires_at' => now()->addDays(15),
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
