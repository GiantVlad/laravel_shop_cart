<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Admin;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
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
}
