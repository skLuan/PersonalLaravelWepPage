<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Public registration is closed; this is the only way to add a user.
 * Note: blog admin access also needs User::canManageBinshopsBlogPosts().
 */
class CreateUser extends Command
{
    protected $signature = 'user:create
                            {email : Login email}
                            {--name= : Display name (defaults to the part before @)}
                            {--password= : Password (asked interactively if omitted)}';

    protected $description = 'Create a user that can log in (registration is closed)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $name = $this->option('name') ?: strstr($email, '@', true);
        $password = $this->option('password') ?? $this->secret('Password');

        $validator = Validator::make(
            compact('email', 'name', 'password'),
            [
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', Password::defaults()],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // hashed by the model cast
        ]);
        $user->markEmailAsVerified();

        $this->info("User #{$user->id} <{$user->email}> created.");

        return self::SUCCESS;
    }
}
