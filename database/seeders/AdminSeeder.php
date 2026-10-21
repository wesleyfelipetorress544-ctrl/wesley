<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('cpf', '50434338842')->first();

        if (! $user) {
            $this->command?->warn('Admin CPF 50434338842 was not found; no account was changed.');

            return;
        }

        $user->forceFill(['is_admin' => true])->save();
    }
}
