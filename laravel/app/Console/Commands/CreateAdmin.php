<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Creates an administrator, or gives admin rights and a new password to an
 * existing account. The password is typed at the prompt, so it never ends up
 * in Git, in the shell history or in a seeder.
 */
class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email? : E-mail administrátora} {--name= : Meno (predvolene časť e-mailu pred @)}';

    protected $description = 'Vytvorí administrátora alebo nastaví existujúcemu účtu nové heslo a práva administrátora';

    public function handle(): int
    {
        $email = mb_strtolower(trim($this->argument('email') ?? $this->ask('E-mail administrátora')));

        $emailCheck = Validator::make(['email' => $email], ['email' => ['required', 'email', 'max:255']]);
        if ($emailCheck->fails()) {
            $this->error($emailCheck->errors()->first('email'));

            return self::FAILURE;
        }

        $user = User::firstWhere('email', $email);
        if ($user && ! $this->confirm("Účet {$email} už existuje. Nastaviť mu nové heslo a práva administrátora?", true)) {
            $this->info('Nič sa nezmenilo.');

            return self::SUCCESS;
        }

        $password = (string) $this->secret('Heslo');
        $passwordCheck = Validator::make(
            ['password' => $password, 'password_confirmation' => (string) $this->secret('Heslo znova')],
            ['password' => ['required', 'confirmed', Password::defaults()]],
        );
        if ($passwordCheck->fails()) {
            $this->error($passwordCheck->errors()->first('password'));

            return self::FAILURE;
        }

        $user ??= new User(['email' => $email, 'name' => $this->option('name') ?: ucfirst(strstr($email, '@', true))]);
        $user->password = $password;
        // is_admin and email_verified_at are not fillable on purpose; set them explicitly here.
        $user->forceFill(['is_admin' => true, 'email_verified_at' => $user->email_verified_at ?? now()])->save();

        $this->info("Administrátor {$email} je pripravený. Prihlásenie: ".route('login'));

        return self::SUCCESS;
    }
}
