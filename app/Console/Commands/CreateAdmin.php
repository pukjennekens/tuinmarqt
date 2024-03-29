<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name     = $this->ask('Wat is je naam?');
        $email    = $this->ask('Wat is je e-mailadres?');
        $password = $this->secret('Wat is je wachtwoord?');

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => bcrypt($password),
        ]);

        $this->info('Gebruiker aangemaakt. ID: ' . $user->id);
    }
}
