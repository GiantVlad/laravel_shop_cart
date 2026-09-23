<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PasswordResetPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is enforced in this app even while testing.
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function testForgotPasswordPageIsAnInertiaPage(): void
    {
        $this->get('/password/reset')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('ForgotPassword', false));
    }

    public function testForgotPasswordPageCarriesTheStatusOfTheLastRequest(): void
    {
        $this->withSession(['status' => 'We have emailed your password reset link!'])
            ->get('/password/reset')
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('ForgotPassword', false)
                    ->where('status', 'We have emailed your password reset link!')
            );
    }

    public function testResetPasswordPageIsAnInertiaPageCarryingItsToken(): void
    {
        $user = User::factory()->create(['email' => 'reset-target@example.dev']);
        $token = Password::broker()->createToken($user);

        $this->get('/password/reset/' . $token . '?email=' . urlencode($user->email))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('ResetPassword', false)
                    ->where('token', $token)
                    ->where('email', $user->email)
            );

        $user->forceDelete();
    }

    public function testPasswordPagesDoNotLoadTheLegacyBootstrapAssets(): void
    {
        $this->get('/password/reset')
            ->assertSuccessful()
            ->assertDontSee('css/app.css', false)
            ->assertDontSee('js/app.js', false);
    }

    public function testResetLinkRequestValidatesTheEmail(): void
    {
        $this->from('/password/reset')
            ->post('/password/email', ['email' => 'nobody@example.dev'])
            ->assertRedirect('/password/reset')
            ->assertSessionHasErrors('email');
    }

    public function testResetPasswordUpdatesThePasswordWithAValidToken(): void
    {
        $user = User::factory()->create(['email' => 'reset-post@example.dev']);
        $token = Password::broker()->createToken($user);

        $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secret-pass',
            'password_confirmation' => 'new-secret-pass',
        ])->assertRedirect('/');

        $this->assertTrue(Hash::check('new-secret-pass', $user->fresh()->password));

        $user->forceDelete();
    }
}
