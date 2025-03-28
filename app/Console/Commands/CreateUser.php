<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     */
    protected $description = 'Create a new user via the CLI interactively';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Prompt for user details
        $name     = $this->ask("What's the user's name?");
        $email    = $this->ask("What's the user's email?");
        $password = $this->secret("Set a secure password");
        $role     = $this->choice("Choose a role", ['product_owner', 'developer', 'tester']);

        // Check if the email is already taken
        if (User::where('email', $email)->exists()) {
            $this->error('❌ A user with this email already exists.');
            return 1;
        }

        // Create the user
        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => $role,
        ]);

        // Output success message
        $this->info("✅ User [{$user->email}] created successfully with role: {$user->role}");

        return 0;
    }
}
