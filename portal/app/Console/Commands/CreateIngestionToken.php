<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateIngestionToken extends Command
{
    protected $signature = 'ingest:create-token
        {--name=n8n : Nombre identificable del token}
        {--email=n8n@magazine.local : Correo de la cuenta de servicio}
        {--replace : Revocar tokens anteriores con el mismo nombre}';

    protected $description = 'Crea un token de servicio para que n8n ingrese noticias y empleos';

    public function handle(): int
    {
        $name = trim((string) $this->option('name'));
        $email = trim((string) $this->option('email'));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Debes indicar un nombre y un correo válidos.');

            return self::FAILURE;
        }

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            ['name' => 'Servicio n8n', 'password' => Hash::make(Str::random(64))],
        );

        $existingTokens = $user->tokens()->where('name', $name);

        if ($existingTokens->exists() && ! $this->option('replace')) {
            $this->error('Ya existe un token con ese nombre. Usa --replace para revocarlo y crear uno nuevo.');

            return self::FAILURE;
        }

        if ($this->option('replace')) {
            $existingTokens->delete();
        }

        $token = $user->createToken($name, ['articles:ingest', 'jobs:ingest'])->plainTextToken;

        $this->info('Token creado. Guárdalo ahora en las credenciales de n8n; no volverá a mostrarse.');
        $this->line($token);

        return self::SUCCESS;
    }
}
