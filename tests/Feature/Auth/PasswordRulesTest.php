<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PasswordRulesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is enforced in this app even while testing.
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function weakPasswords(): array
    {
        return [
            'too short (7 chars)' => ['Ab1!xyz'],
            'no number' => ['Strong-Password!'],
            'no special character' => ['StrongPassword12'],
            'no letters' => ['12345678!'],
            'common password' => ['password'],
        ];
    }

    #[DataProvider('weakPasswords')]
    public function testRegistrationRejectsWeakPasswords(string $password): void
    {
        $this->from('/register')
            ->post('/register', [
                'name' => 'Weak Password',
                'email' => 'weak-' . md5($password) . '@example.dev',
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['name' => 'Weak Password']);
    }

    public function testRegistrationRejectsAMismatchedConfirmation(): void
    {
        $this->from('/register')
            ->post('/register', [
                'name' => 'Mismatch',
                'email' => 'mismatch@example.dev',
                'password' => 'Str0ng!Pass',
                'password_confirmation' => 'Str0ng!Pas',
            ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('password');
    }

    public function testRegistrationAcceptsAStrongPassword(): void
    {
        $this->post('/register', [
            'name' => 'Strong Password',
            'email' => 'strong@example.dev',
            'password' => 'Str0ng!Pass',
            'password_confirmation' => 'Str0ng!Pass',
        ])->assertRedirect('/');

        $user = User::where('email', 'strong@example.dev')->firstOrFail();
        $this->assertTrue(password_verify('Str0ng!Pass', $user->password));

        $user->forceDelete();
    }

    public function testPasswordResetRejectsWeakPasswords(): void
    {
        $user = User::factory()->create(['email' => 'weak-reset@example.dev']);
        $token = Password::broker()->createToken($user);

        $this->from('/password/reset/' . $token)
            ->post('/password/reset', [
                'token' => $token,
                'email' => $user->email,
                'password' => 'nocapsordigits',
                'password_confirmation' => 'nocapsordigits',
            ])
            ->assertSessionHasErrors('password');

        $user->forceDelete();
    }
}
