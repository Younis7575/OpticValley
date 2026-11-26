<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\LensOption;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private array $address = ['name' => 'Ayesha Khan', 'email' => 'ayesha@example.com', 'phone' => '0300 1234567', 'line1' => '12 Main Boulevard', 'city' => 'Lahore', 'province' => 'Punjab', 'payment_method' => 'cod'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedStore();
        Notification::fake();
    }

    public function test_guest_can_checkout_with_coupon_and_stock_is_reduced(): void
    {
        $p = Product::where('slug', 'kestrel-rectangle')->first(); // 3990 sale, no rx required for frame-only
        $variant = $p->variants()->first();
        $stockBefore = $variant->stock;

        $this->postJson("/cart/add/{$p->slug}", ['qty' => 2, 'variant_id' => $variant->id])->assertOk()->assertJson(['count' => 2]);
        $this->post('/cart/coupon', ['code' => 'welcome10'])->assertSessionHas('success');
        $this->get('/cart')->assertSee('WELCOME10');

        $res = $this->post('/checkout', $this->address);
        $order = Order::first();
        $res->assertRedirect(route('checkout.thanks', $order));

        $subtotal = 3990 * 2;
        $discount = round($subtotal * 0.10, 2);
        $this->assertEquals($subtotal, (float) $order->subtotal);
        $this->assertEquals($discount, (float) $order->discount);
        $this->assertEquals(0.0, (float) $order->shipping); // 7182 > 5000 free shipping
        $this->assertEquals($subtotal - $discount, (float) $order->total);
        $this->assertEquals($stockBefore - 2, $variant->fresh()->stock);
        $this->assertEquals(1, Coupon::where('code', 'WELCOME10')->value('used_count'));
        $this->get(route('checkout.thanks', $order))->assertOk()->assertSee($order->order_number);
        $this->get('/cart')->assertSee('Your cart is empty');
    }

    public function test_shipping_is_charged_below_threshold(): void
    {
        $p = Product::where('slug', 'microfibre-cloth-pack-of-3')->first();
        $this->post("/cart/add/{$p->slug}", ['qty' => 1]);
        $this->post('/checkout', $this->address);
        $order = Order::first();
        $this->assertEquals(250.0, (float) $order->shipping);
        $this->assertEquals(450 + 250, (float) $order->total);
    }

    public function test_out_of_stock_product_cannot_be_added(): void
    {
        $p = Product::where('stock', 0)->first();
        $this->postJson("/cart/add/{$p->slug}", ['qty' => 1])->assertStatus(422);
    }

    public function test_checkout_rejects_when_stock_ran_out(): void
    {
        $p = Product::where('slug', 'microfibre-cloth-pack-of-3')->first();
        $this->post("/cart/add/{$p->slug}", ['qty' => 3]);
        $p->update(['stock' => 1]);
        $this->post('/checkout', $this->address)->assertSessionHasErrors('cart');
        $this->assertEquals(0, Order::count());
    }

    public function test_prescription_lens_requires_prescription_and_is_priced(): void
    {
        $p = Product::where('prescription_enabled', true)->where('stock', '>', 0)->first();
        $lens = LensOption::where('price', '>', 0)->first();

        $this->postJson("/cart/add/{$p->slug}", ['lens_id' => $lens->id])->assertStatus(422);

        $this->post("/cart/add/{$p->slug}", ['lens_id' => $lens->id, 'rx' => ['od_sph' => '-1.25', 'os_sph' => '-1.50', 'pd' => '63']])->assertSessionHas('success');
        $this->post('/checkout', $this->address);
        $item = Order::first()->items->first();
        $this->assertEquals($lens->name, $item->lens_name);
        $this->assertEquals(round($p->final_price + $lens->price, 2), (float) $item->unit_price);
        $this->assertEquals('-1.25', $item->prescription['od_sph']);
    }

    public function test_prescription_upload_is_stored_privately(): void
    {
        $p = Product::where('prescription_enabled', true)->where('stock', '>', 0)->first();
        $lens = LensOption::first();
        $this->post("/cart/add/{$p->slug}", ['lens_id' => $lens->id, 'rx_file' => UploadedFile::fake()->image('rx.jpg')]);
        $this->post('/checkout', $this->address);
        $file = Order::first()->items->first()->prescription_file;
        $this->assertStringStartsWith('prescriptions/', $file);
        $this->assertFileExists(storage_path('app/private/'.$file));
        @unlink(storage_path('app/private/'.$file));
    }

    public function test_invalid_coupon_and_validation(): void
    {
        $p = Product::where('slug', 'microfibre-cloth-pack-of-3')->first();
        $this->post("/cart/add/{$p->slug}");
        $this->post('/cart/coupon', ['code' => 'WELCOME10'])->assertSessionHas('error'); // below min order
        $this->post('/cart/coupon', ['code' => 'NOPE'])->assertSessionHas('error');
        $this->post('/checkout', ['payment_method' => 'cod'])->assertSessionHasErrors(['name', 'email', 'phone', 'line1', 'city']);
    }

    public function test_order_tracking_requires_matching_phone(): void
    {
        $p = Product::where('slug', 'microfibre-cloth-pack-of-3')->first();
        $this->post("/cart/add/{$p->slug}");
        $this->post('/checkout', $this->address);
        $order = Order::first();
        $this->post('/track-order', ['order_number' => $order->order_number, 'phone' => '0999 0000000'])->assertSessionHas('error');
        $this->post('/track-order', ['order_number' => strtolower($order->order_number), 'phone' => '+92 300 1234567'])->assertOk()->assertSee($order->order_number);
    }

    public function test_thank_you_page_is_private(): void
    {
        $p = Product::where('slug', 'microfibre-cloth-pack-of-3')->first();
        $this->post("/cart/add/{$p->slug}");
        $this->post('/checkout', $this->address);
        $order = Order::first();
        $this->flushSession();
        $this->get(route('checkout.thanks', $order))->assertNotFound();
    }
}
