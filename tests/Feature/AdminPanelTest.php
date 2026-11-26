<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedStore();
        Notification::fake();
    }

    public function test_access_control(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->actingAs($this->customer())->get('/admin')->assertForbidden();

        $staff = $this->admin('order-staff');
        $this->actingAs($staff)->get('/admin/orders')->assertOk();
        $this->get('/admin/products')->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
    }

    public function test_admin_login_rejects_customers(): void
    {
        $c = $this->customer();
        $this->post('/admin/login', ['email' => $c->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $a = $this->admin();
        $this->post('/admin/login', ['email' => $a->email, 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
    }

    public function test_every_admin_page_renders(): void
    {
        $this->placeOrder();
        $this->actingAs($this->admin());
        $p = Product::first();
        $order = Order::first();
        $pages = ['/admin', '/admin?days=7', '/admin/products', '/admin/products/create', "/admin/products/{$p->slug}/edit", '/admin/categories', '/admin/categories/create',
            '/admin/brands', '/admin/brands/create', '/admin/lens-options', '/admin/lens-options/create', '/admin/orders', '/admin/orders?status=pending',
            "/admin/orders/{$order->order_number}", "/admin/orders/{$order->order_number}/invoice", '/admin/customers', '/admin/coupons', '/admin/coupons/create',
            '/admin/banners', '/admin/banners/create', '/admin/subscribers', '/admin/pages', '/admin/pages/create', '/admin/posts', '/admin/posts/create',
            '/admin/menus', '/admin/menus/create', '/admin/reviews', '/admin/messages', '/admin/settings', '/admin/settings/payments', '/admin/users',
            '/admin/users/create', '/admin/roles', '/admin/roles/create', '/admin/activity', '/admin/profile'];
        foreach ($pages as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/admin/orders-export')->assertOk();
    }

    public function test_product_crud_with_images_and_variants(): void
    {
        $this->actingAs($this->admin());
        $cat = Category::where('slug', 'sunglasses')->first();
        $res = $this->post('/admin/products', [
            'name' => 'Test Aviator', 'sku' => 'TST-001', 'price' => 5000, 'sale_price' => 4500, 'stock' => 10, 'low_stock_threshold' => 2,
            'gender' => 'men', 'category_id' => $cat->id, 'is_active' => 1, 'description' => '<p onclick="x()">Nice</p><script>alert(1)</script>',
            'images' => [UploadedFile::fake()->image('a.jpg', 800, 600)],
            'variants' => [['name' => 'Gold', 'color_hex' => '#c9a24a', 'stock' => 4], ['name' => 'Silver', 'color_hex' => '#cccccc', 'stock' => 3]],
            'spec_keys' => ['Bridge'], 'spec_values' => ['18 mm'],
        ]);
        $p = Product::where('sku', 'TST-001')->first();
        $res->assertRedirect(route('admin.products.edit', $p));
        $this->assertEquals('test-aviator', $p->slug);
        $this->assertEquals(7, $p->stock); // sum of variants
        $this->assertEquals('<p>Nice</p>', $p->description);
        $this->assertEquals(['Bridge' => '18 mm'], $p->specifications);
        $this->assertCount(1, $p->images);
        $this->assertFileExists(public_path($p->images->first()->path));

        $this->put("/admin/products/{$p->slug}", ['name' => 'Test Aviator 2', 'slug' => 'test-aviator', 'sku' => 'TST-001', 'price' => 5000, 'stock' => 5, 'low_stock_threshold' => 2, 'gender' => 'men', 'is_active' => 0])->assertSessionHas('success');
        $p->refresh();
        $this->assertFalse($p->is_active);
        $this->assertCount(0, $p->variants);

        $this->post('/admin/products', ['name' => 'Bad', 'sku' => 'TST-001', 'price' => 100, 'sale_price' => 200, 'stock' => 1, 'low_stock_threshold' => 1, 'gender' => 'men'])->assertSessionHasErrors(['sku', 'sale_price']);

        $path = $p->images->first()->path;
        $this->delete("/admin/products/{$p->slug}")->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $p->id]);
        $this->assertFileDoesNotExist(public_path($path));
    }

    public function test_product_with_orders_is_archived_not_deleted(): void
    {
        $this->placeOrder();
        $p = Order::first()->items->first()->product;
        $this->actingAs($this->admin())->delete("/admin/products/{$p->slug}");
        $this->assertDatabaseHas('products', ['id' => $p->id, 'is_active' => false]);
    }

    public function test_category_brand_coupon_banner_page_post_crud(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/categories', ['name' => 'Sports Glasses', 'is_active' => 1])->assertRedirect();
        $this->assertDatabaseHas('categories', ['slug' => 'sports-glasses']);
        $this->post('/admin/brands', ['name' => 'Test Brand', 'is_active' => 1])->assertRedirect();
        $this->post('/admin/coupons', ['code' => 'eid20', 'type' => 'percent', 'value' => 20, 'is_active' => 1])->assertRedirect();
        $this->assertDatabaseHas('coupons', ['code' => 'EID20']);
        $this->post('/admin/coupons', ['code' => 'BAD', 'type' => 'percent', 'value' => 150])->assertSessionHasErrors('value');
        $this->post('/admin/banners', ['position' => 'hero', 'title' => 'Eid Sale', 'theme' => 'dark', 'link' => 'javascript:alert(1)'])->assertSessionHasErrors('link');
        $this->post('/admin/banners', ['position' => 'hero', 'title' => 'Eid Sale', 'theme' => 'dark', 'link' => '/shop?sale=1', 'is_active' => 1])->assertRedirect();
        $this->post('/admin/pages', ['title' => 'Lens Guide', 'content' => '<h2>Hi</h2>', 'is_active' => 1])->assertRedirect();
        $this->get('/pages/lens-guide')->assertOk()->assertSee('Hi');
        $this->post('/admin/posts', ['title' => 'Draft post', 'body' => '<p>x</p>', 'publish' => 0])->assertRedirect();
        $this->get('/journal/draft-post')->assertNotFound();
        $this->post('/admin/menus', ['location' => 'header', 'label' => 'Lens Guide', 'url' => '/pages/lens-guide', 'is_active' => 1])->assertRedirect();
        $this->get('/')->assertSee('Lens Guide');
    }

    public function test_order_status_changes_restock_and_refund(): void
    {
        $this->placeOrder(2);
        $order = Order::first();
        $product = $order->items->first()->product;
        $stock = $product->fresh()->stock;
        $this->actingAs($this->admin());

        $this->put("/admin/orders/{$order->order_number}", ['status' => 'shipped', 'payment_status' => 'unpaid', 'courier' => 'TCS', 'tracking_number' => 'TCS123'])->assertSessionHas('success');
        $this->assertNotNull($order->fresh()->shipped_at);
        $this->put("/admin/orders/{$order->order_number}", ['status' => 'delivered', 'payment_status' => 'unpaid'])->assertSessionHas('success');
        $this->assertEquals('paid', $order->fresh()->payment_status); // COD delivered => paid

        $this->post("/admin/orders/{$order->order_number}/refund", ['amount' => (float) $order->total])->assertSessionHas('success');
        $order->refresh();
        $this->assertEquals('refunded', $order->status);
        $this->assertEquals('refunded', $order->payment_status);
        $this->assertEquals($stock + 2, $product->fresh()->stock);
        $this->assertDatabaseHas('activity_logs', ['action' => 'order.refund']);
    }

    public function test_review_moderation_updates_rating(): void
    {
        $p = Product::first();
        $review = $p->reviews()->create(['name' => 'X', 'rating' => 1, 'body' => 'Not good at all', 'is_approved' => false]);
        $before = $p->fresh()->rating_count;
        $this->actingAs($this->admin())->put("/admin/reviews/{$review->id}", ['is_approved' => 1]);
        $this->assertEquals($before + 1, $p->fresh()->rating_count);
    }

    public function test_settings_update_affects_storefront(): void
    {
        $this->actingAs($this->admin())->put('/admin/settings/shipping', ['shipping_flat_rate' => 300, 'free_shipping_threshold' => 0, 'shipping_note' => 'Fast'])->assertSessionHas('success');
        $this->assertEquals('300', Setting::get('shipping_flat_rate'));
        $this->put('/admin/settings/payments', ['payment_cod_enabled' => 0, 'payment_bank_enabled' => 1])->assertSessionHas('success');
        $this->assertEquals('0', Setting::get('payment_cod_enabled'));
    }

    public function test_user_and_role_management_guards(): void
    {
        $me = $this->admin();
        $this->actingAs($me)->post('/admin/users', ['name' => 'Staff', 'email' => 'staff@example.com', 'role_id' => 2, 'password' => 'short', 'is_active' => 1])->assertSessionHasErrors('password');
        $this->post('/admin/users', ['name' => 'Staff', 'email' => 'staff@example.com', 'role_id' => 2, 'password' => 'Strongpass123', 'is_active' => 1])->assertRedirect();
        $this->delete("/admin/users/{$me->id}")->assertSessionHas('error');
        $this->put('/admin/roles/1', ['name' => 'Hacked', 'permissions' => []])->assertSessionHas('error');
        $this->post('/admin/roles', ['name' => 'Writer', 'permissions' => ['content']])->assertRedirect();
    }

    private function placeOrder(int $qty = 1): void
    {
        $p = Product::where('slug', 'microfibre-cloth-pack-of-3')->first();
        $this->post("/cart/add/{$p->slug}", ['qty' => $qty]);
        $this->post('/checkout', ['name' => 'Bilal', 'email' => 'b@example.com', 'phone' => '03001234567', 'line1' => 'A', 'city' => 'Lahore', 'payment_method' => 'cod']);
        $this->flushSession();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path('uploads/products/'.now()->format('Y')));
        parent::tearDown();
    }
}
