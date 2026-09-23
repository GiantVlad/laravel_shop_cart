<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exceptions\PaymentException;
use App\Http\Middleware\VerifyCsrfToken;
use App\PaymentMethod;
use App\Product;
use App\Services\Cart\CartService;
use App\Services\Payment\CashPayment;
use App\ShippingMethod;
use App\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CartPaymentMethodsTest extends TestCase
{
    private User $user;

    private CartService $cartService;

    /** @var array<int, int> */
    private array $createdShippingMethods = [];

    /** @var array<int, int> */
    private array $createdPaymentMethods = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->user = User::factory()->create();
        $this->cartService = app()->get(CartService::class);

        // Test runs share one database and cache: ids get recycled after a truncate,
        // so start from a clean cart whatever a previous run left behind.
        $this->cartService->forget($this->user->id);

        // Other suites leave factory shipping methods behind, and a shipping method
        // whose class does not exist makes the cart page blow up while rendering.
        ShippingMethod::query()->delete();
    }

    public function tearDown(): void
    {
        ShippingMethod::whereIn('id', $this->createdShippingMethods)->delete();
        PaymentMethod::whereIn('id', $this->createdPaymentMethods)->delete();
        $this->cartService->forget($this->user->id);
        $this->createdShippingMethods = [];
        $this->createdPaymentMethods = [];

        parent::tearDown();
    }

    /**
     * @param array<int, PaymentMethod> $paymentMethods
     */
    private function shippingMethodOffering(array $paymentMethods, bool $enabled = true): ShippingMethod
    {
        /** @var ShippingMethod $shippingMethod */
        $shippingMethod = ShippingMethod::factory()->create([
            'enable' => $enabled,
            'class_name' => 'FreeShippingMethod',
        ]);
        $shippingMethod->paymentMethods()->sync(array_map(
            static fn (PaymentMethod $method): int => $method->id,
            $paymentMethods
        ));
        $this->createdShippingMethods[] = $shippingMethod->id;

        return $shippingMethod;
    }

    private function paymentMethod(string $key, bool $enabled = true, int $priority = 0): PaymentMethod
    {
        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = PaymentMethod::create([
            'label' => ucfirst($key),
            'config_key' => $key,
            'class_name' => 'App\\Services\\Payment\\CashPayment',
            'priority' => $priority,
            'enabled' => $enabled,
        ]);
        $this->createdPaymentMethods[] = $paymentMethod->id;

        return $paymentMethod;
    }

    private function cartWithProduct(): Product
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $this->cartService->addToCart($this->user->id, $product->id, 1);

        return $product;
    }

    public function testPaymentMethodAndShippingMethodAreRelated(): void
    {
        $paymentMethod = $this->paymentMethod('linked');
        $shippingMethod = $this->shippingMethodOffering([$paymentMethod]);

        $this->assertTrue($shippingMethod->paymentMethods->contains($paymentMethod));
        $this->assertTrue($paymentMethod->shippingMethods->contains($shippingMethod));
    }

    public function testPaymentMethodsEndpointReturnsOnlyTheMethodsTheShippingMethodOffers(): void
    {
        $offered = $this->paymentMethod('offered');
        $notOffered = $this->paymentMethod('not-offered');
        $offeredButDisabled = $this->paymentMethod('disabled', enabled: false);
        $shippingMethod = $this->shippingMethodOffering([$offered, $offeredButDisabled]);

        $response = $this->actingAs($this->user)->getJson('/cart/payment-methods/' . $shippingMethod->id);

        $response->assertSuccessful();
        $this->assertSame([$offered->id], array_column($response->json('payments'), 'id'));
        $this->assertNotContains($notOffered->id, array_column($response->json('payments'), 'id'));
    }

    public function testPaymentMethodsEndpointMarksDisabledShippingMethods(): void
    {
        $shippingMethod = $this->shippingMethodOffering([$this->paymentMethod('offered')], enabled: false);

        $response = $this->actingAs($this->user)->getJson('/cart/payment-methods/' . $shippingMethod->id);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['shippingMethodId']]);
    }

    public function testPaymentMethodsEndpointIsNotFoundForAnUnknownShippingMethod(): void
    {
        $this->actingAs($this->user)->getJson('/cart/payment-methods/99999')->assertNotFound();
    }

    public function testPaymentMethodsEndpointRequiresAuthentication(): void
    {
        $shippingMethod = $this->shippingMethodOffering([$this->paymentMethod('offered')]);

        $this->getJson('/cart/payment-methods/' . $shippingMethod->id)->assertUnauthorized();
    }

    public function testChangePaymentRejectsAMethodTheShippingMethodDoesNotOffer(): void
    {
        $offered = $this->paymentMethod('offered');
        $other = $this->paymentMethod('other');
        $shippingMethod = $this->shippingMethodOffering([$offered]);
        $this->cartWithProduct();

        $this->actingAs($this->user)->postJson('/cart/change-shipping', [
            'shippingMethodId' => $shippingMethod->id,
            'subtotal' => 10,
        ])->assertSuccessful();

        $response = $this->actingAs($this->user)->postJson('/cart/change-payment', [
            'paymentMethodId' => $other->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['paymentMethodId']]);
        $this->assertNull($this->cartService->getCart($this->user->id)['paymentMethodId']);
    }

    public function testChangePaymentRejectsAPaymentMethodWhenNoShippingMethodIsChosen(): void
    {
        $paymentMethod = $this->paymentMethod('offered');
        $this->cartWithProduct();

        $response = $this->actingAs($this->user)->postJson('/cart/change-payment', [
            'paymentMethodId' => $paymentMethod->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['paymentMethodId']]);
    }

    public function testChangePaymentStoresAnOfferedMethod(): void
    {
        $offered = $this->paymentMethod('offered');
        $shippingMethod = $this->shippingMethodOffering([$offered]);
        $this->cartWithProduct();

        $this->actingAs($this->user)->postJson('/cart/change-shipping', [
            'shippingMethodId' => $shippingMethod->id,
            'subtotal' => 10,
        ])->assertSuccessful();

        $this->actingAs($this->user)->postJson('/cart/change-payment', [
            'paymentMethodId' => $offered->id,
        ])->assertSuccessful();

        $this->assertSame($offered->id, $this->cartService->getCart($this->user->id)['paymentMethodId']);
    }

    public function testChangeShippingDropsAPaymentMethodTheNewShippingMethodDoesNotOffer(): void
    {
        $shared = $this->paymentMethod('shared');
        $pickupOnly = $this->paymentMethod('pickup-only');
        $delivery = $this->shippingMethodOffering([$shared]);
        $pickup = $this->shippingMethodOffering([$shared, $pickupOnly]);
        $this->cartWithProduct();

        $this->actingAs($this->user)->postJson('/cart/change-shipping', [
            'shippingMethodId' => $pickup->id,
            'subtotal' => 10,
        ])->assertSuccessful();
        $this->actingAs($this->user)->postJson('/cart/change-payment', [
            'paymentMethodId' => $pickupOnly->id,
        ])->assertSuccessful();

        $this->actingAs($this->user)->postJson('/cart/change-shipping', [
            'shippingMethodId' => $delivery->id,
            'subtotal' => 10,
        ])->assertSuccessful();

        $this->assertNull($this->cartService->getCart($this->user->id)['paymentMethodId']);
    }

    public function testChangeShippingKeepsAPaymentMethodBothMethodsOffer(): void
    {
        $shared = $this->paymentMethod('shared');
        $delivery = $this->shippingMethodOffering([$shared]);
        $pickup = $this->shippingMethodOffering([$shared]);
        $this->cartWithProduct();

        $this->actingAs($this->user)->postJson('/cart/change-shipping', [
            'shippingMethodId' => $delivery->id,
            'subtotal' => 10,
        ])->assertSuccessful();
        $this->actingAs($this->user)->postJson('/cart/change-payment', [
            'paymentMethodId' => $shared->id,
        ])->assertSuccessful();

        $this->actingAs($this->user)->postJson('/cart/change-shipping', [
            'shippingMethodId' => $pickup->id,
            'subtotal' => 10,
        ])->assertSuccessful();

        $this->assertSame($shared->id, $this->cartService->getCart($this->user->id)['paymentMethodId']);
    }

    public function testCartPageOffersNoPaymentMethodsBeforeAShippingMethodIsChosen(): void
    {
        $this->paymentMethod('offered');
        $this->cartWithProduct();

        $this->actingAs($this->user)->get('/cart')
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Cart', false)
                    ->has('shippingMethods')
                    ->where('payments', [])
            );
    }

    public function testCartPageOffersThePaymentMethodsOfTheSelectedShippingMethod(): void
    {
        $offered = $this->paymentMethod('offered');
        $other = $this->paymentMethod('other');
        $shippingMethod = $this->shippingMethodOffering([$offered]);
        $this->cartWithProduct();

        $this->actingAs($this->user)->postJson('/cart/change-shipping', [
            'shippingMethodId' => $shippingMethod->id,
            'subtotal' => 10,
        ])->assertSuccessful();

        $this->actingAs($this->user)->get('/cart')
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Cart', false)
                    ->has('payments', 1)
                    ->where('payments.0.id', $offered->id)
                    ->where('payments.0.selected', false)
            );
    }

    public function testCashPaymentDoesNotRedirectToAPaymentProvider(): void
    {
        $response = (new CashPayment())->pay(['order_id' => 'ORD-1', 'amount' => 10, 'currency' => 'USD']);

        $this->assertNull($response->getCheckoutUrl());
        $this->assertSame('ORD-1', $response->getExternalId());
        $this->assertSame('cash', $response->getData()['method']);
    }

    public function testCheckoutWithCashAndStorePickupGoesStraightToTheOrders(): void
    {
        $product = $this->cartWithProduct();
        // config_key is unique: the migrated "cash" row already owns the real key.
        $cash = $this->paymentMethod('cash-test');
        $pickup = $this->shippingMethodOffering([$cash]);

        $response = $this->actingAs($this->user)->postJson('/checkout', [
            'product_ids' => [$product->id],
            'productQty' => [1],
            'isRelatedProduct' => [0],
            'subtotal' => $product->price,
            'paymentMethodId' => $cash->id,
            'shippingMethodId' => $pickup->id,
        ]);

        $response->assertSuccessful();
        $this->assertSame(route('orders'), $response->json('redirect_to'));
        $this->assertDatabaseHas('orders', ['user_id' => $this->user->id]);
    }

    public function testCheckoutRejectsAPaymentMethodTheShippingMethodDoesNotOffer(): void
    {
        $this->withoutExceptionHandling();
        $product = $this->cartWithProduct();
        $offered = $this->paymentMethod('offered');
        $other = $this->paymentMethod('other');
        $shippingMethod = $this->shippingMethodOffering([$offered]);

        $this->expectException(PaymentException::class);

        $this->actingAs($this->user)->postJson('/checkout', [
            'product_ids' => [$product->id],
            'productQty' => [1],
            'isRelatedProduct' => [0],
            'subtotal' => $product->price,
            'paymentMethodId' => $other->id,
            'shippingMethodId' => $shippingMethod->id,
        ]);
    }
}
