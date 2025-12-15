<?php

namespace App\Console\Commands;

use App\Mail\FirstLoginMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-first-login {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un email de prueba de primer login a la dirección especificada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        // Crear un usuario de prueba (no se guarda en BD)
        $testUser = new User([
            'nombre' => 'Usuario',
            'apellidos' => 'De Prueba',
            'email' => $email,
            'rut' => '12345678-9',
        ]);

        $testToken = 'test_token_' . bin2hex(random_bytes(32));
        $testUrl = config('app.frontend_url') . '/reset-password/' . $testToken;

        $this->info('Enviando email de prueba...');
        $this->info('Destinatario: ' . $email);
        $this->info('URL de prueba: ' . $testUrl);

        try {
            Mail::to($email)->send(new FirstLoginMail($testUser, $testUrl, $testToken));
            $this->info('✅ Email enviado exitosamente!');
            $this->info('Revisa tu bandeja de entrada (o Mailtrap si estás en desarrollo)');
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar email: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
