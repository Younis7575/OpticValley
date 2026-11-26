<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedStore();
        Notification::fake();
    }

    public function test_register_login_logout(): void
    {
        $this->post('/register', ['name' => 'Hamza R', 'email' => 'hamza@example.com', 'password' => 'secret123', 'password_confirmation' => 'secret123'])->assertRedirect(route('account.dashboard'));
        $this->assertAuthenticated();
        $this->post('/logout');
        $this->assertGuest();
        $this->post('/login', ['email' => 'hamza@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'hamza@example.com', 'password' => 'secret123'])->assertRedirect(route('account.dashboard'));
    }

    public function test_blocked_customer_cannot_login(): void
    {
        $u = User::factory()->create(['is_active' => false, 'password' => 'secret123']);
        $this->post('/login', ['email' => $u->email, 'password' => 'secret123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_account_pages_and_wishlist(): void
    {
        $u = $this->customer();
        $p = Product::first();
        $this->actingAs($u);
        foreach (['/account', '/account/orders', '/account/wishlist', '/account/profile', '/account/addresses'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->postJson("/wishlist/{$p->slug}")->assertJson(['in_wishlist' => true]);
        $this->get('/account/wishlist')->assertSee($p->name);
        $this->postJson("/wishlist/{$p->slug}")->assertJson(['in_wishlist' => false]);
    }

    public function test_guest_wishlist_requires_login(): void
    {
        $this->postJson('/wishlist/'.Product::first()->slug)->assertStatus(401);
        $this->get('/account')->assertRedirect('/login');
    }

    public function test_addresses_and_profile(): void
    {
        $u = $this->customer();
        $this->actingAs($u)->post('/account/addresses', ['name' => 'Home', 'phone' => '03001234567', 'line1' => 'Street 1', 'city' => 'Karachi'])->assertSessionHas('success');
        $this->assertTrue($u->addresses()->first()->is_default);
        $this->put('/account/profile', ['name' => 'New Name', 'email' => $u->email, 'phone' => '03001112223'])->assertSessionHas('success');
        $this->assertEquals('New Name', $u->fresh()->name);
        $this->put('/account/password', ['current_password' => 'password', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])->assertSessionHas('success');
    }

    public function test_customer_can_cancel_pending_order_and_stock_returns(): void
    {
        $u = $this->customer();
        $p = Product::where('slug', 'microfibre-cloth-pack-of-3')->first();
        $this->actingAs($u)->post("/cart/add/{$p->slug}", ['qty' => 2]);
        $this->post('/checkout', ['name' => 'X', 'email' => $u->email, 'phone' => '03001234567', 'line1' => 'A', 'city' => 'Lahore', 'payment_method' => 'cod']);
        $order = Order::first();
        $this->assertEquals(98, $p->fresh()->stock);
        $this->get(route('account.order', $order))->assertOk();
        $this->post(route('account.order.cancel', $order))->assertSessionHas('success');
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals(100, $p->fresh()->stock);
    }

    public function test_customer_cannot_see_other_customers_orders(): void
    {
        $order = Order::create(['order_number' => 'OVTEST1', 'user_id' => $this->customer()->id, 'subtotal' => 1, 'total' => 1, 'customer_name' => 'a', 'customer_email' => 'a@a.com', 'customer_phone' => '1', 'shipping_line1' => 'a', 'shipping_city' => 'a']);
        $this->actingAs($this->customer())->get(route('account.order', $order))->assertNotFound();
    }
}
