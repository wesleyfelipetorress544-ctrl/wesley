<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('biblioteca:criar-admin', function () {
    $name = $this->ask('Nome do administrador');
    $email = $this->ask('E-mail');
    $cpf = preg_replace('/\D+/', '', (string) $this->ask('CPF'));
    $password = $this->secret('Senha (mínimo de 8 caracteres)');
    if (! $name || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($cpf) !== 11 || strlen((string) $password) < 8) {
        $this->error('Dados inválidos. Informe nome, e-mail válido, CPF com 11 dígitos e senha com pelo menos 8 caracteres.');
        return 1;
    }
    if (\App\Models\User::where('email', $email)->orWhere('cpf', $cpf)->exists()) {
        $this->error('Já existe uma conta com esse e-mail ou CPF.');
        return 1;
    }
    $admin = new \App\Models\User(['name' => $name, 'email' => $email, 'cpf' => $cpf, 'password' => $password]);
    $admin->is_admin = true;
    $admin->save();
    $this->info('Conta de administrador criada com sucesso.');
    return 0;
})->purpose('Criar uma conta de administrador da biblioteca');

