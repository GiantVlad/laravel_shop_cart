<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Admin;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // This app enforces CSRF even in tests (the other feature tests do the same).
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function testAdminFactoryCreatesAnAdmin(): void
    {
        $admin = Admin::factory()->create();

        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertDatabaseHas('admins', ['email' => $admin->email]);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function testGuestIsRedirectedToTheAdminLoginForEveryAdminPage(): void
    {
        $paths = [
            '/admin',
            '/admin/categories',
            '/admin/add-category',
            '/admin/products',
            '/admin/add-product',
            '/admin/users',
            '/admin/orders',
            '/admin/shipping-methods',
            '/admin/payment-methods',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertRedirect('/admin/login');
        }
    }

    public function testShopUserIsRejectedFromTheAdminPanel(): void
    {
        $user = \App\User::factory()->create();

        $this->actingAs($user, 'web')
            ->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function testAuthenticatedAdminSeesTheDashboardWithItsSharedProps(): void
    {
        $admin = Admin::factory()->create(['name' => 'Root Admin']);

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Admin/Dashboard', false)
                    ->where('auth.admin.name', 'Root Admin')
            );
    }

    public function testAdminLoginPageIsAnInertiaPage(): void
    {
        $this->get('/admin/login')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Login', false));
    }

    public function testAdminCanLogInWithValidCredentials(): void
    {
        $admin = Admin::factory()->create(['password' => Hash::make('password')]);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function testAdminLoginShowsAnErrorForBadCredentials(): void
    {
        $admin = Admin::factory()->create(['password' => Hash::make('password')]);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'not-the-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function testAdminCanLogOut(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest('admin');
    }
}
