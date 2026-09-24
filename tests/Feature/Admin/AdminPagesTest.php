<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Admin;
use App\Catalog;
use App\Product;
use App\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->admin = Admin::factory()->create();
    }

    public function testDashboardRenders(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Dashboard', false));
    }

    public function testCategoriesIndexAndEditRender(): void
    {
        $category = Catalog::factory()->create(['name' => 'Tools']);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/categories')
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Admin/Categories/Index', false)
                    ->has('categories', 1)
                    ->where('categories.0.name', 'Tools')
            );

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/edit-category/' . $category->id)
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Admin/Categories/Edit', false)
                    ->where('category.id', $category->id)
                    ->has('parentCategories')
            );

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/add-category')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Categories/Edit', false)->where('category', null));
    }

    public function testProductsIndexAndEditRender(): void
    {
        $category = Catalog::factory()->create();
        $product = Product::factory()->create(['catalog_id' => $category->id, 'name' => 'Widget']);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/products')
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Admin/Products/Index', false)
                    ->has('products.data', 1)
                    ->where('products.data.0.name', 'Widget')
                    ->has('categories')
            );

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/edit-product/' . $product->id)
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Admin/Products/Edit', false)
                    ->where('product.id', $product->id)
            );

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/add-product')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Products/Edit', false)->where('product', null));
    }

    public function testUsersIndexAndEditRender(): void
    {
        $user = User::factory()->create(['name' => 'Buyer']);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/users')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Users/Index', false)->has('users.data'));

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/edit-user/' . $user->id)
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Admin/Users/Edit', false)
                    ->where('user.id', $user->id)
            );
    }

    public function testOrdersIndexRenders(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/orders')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Orders/Index', false)->has('orders.data'));
    }

    public function testShippingAndPaymentMethodPagesRender(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/shipping-methods')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/ShippingMethods/Index', false)->has('shippingMethods'));

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/payment-methods')
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Admin/PaymentMethods/Index', false)
                    ->has('paymentMethods')
                    ->has('statuses')
            );
    }

    public function testProductPropertyEndpointsAnswerJson(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->getJson('/admin/product/' . $product->id . '/properties')
            ->assertSuccessful()
            ->assertJsonStructure(['properties']);

        $this->actingAs($this->admin, 'admin')
            ->getJson('/admin/products/property-types')
            ->assertSuccessful()
            ->assertJsonStructure(['properties']);
    }
}
