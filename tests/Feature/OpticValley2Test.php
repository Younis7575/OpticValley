<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OpticValley2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedStore();
    }

    public function test_homepage_has_signature_sections_and_lazy_3d_hooks(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('See the world differently.')
            ->assertSee('data-hero-3d', false)
            ->assertSee('data-lens-3d', false)
            ->assertSee('Beyond ordinary vision.')
            ->assertSee('Find your perfect frame')
            ->assertSee('Representative 3D render');
    }

    public function test_search_suggestions_and_quick_view(): void
    {
        $p = Product::where('is_active', true)->first();
        $this->getJson('/search/suggest?q='.urlencode(substr($p->name, 0, 6)))->assertOk()->assertJsonStructure(['data' => [['name', 'price', 'url', 'image']]]);
        $this->getJson('/search/suggest?q=a')->assertOk()->assertExactJson(['data' => []]);
        $this->get("/product/{$p->slug}/quick")->assertOk()->assertSee($p->name);
    }

    public function test_tryon_only_for_products_with_overlay(): void
    {
        $with = Product::first();
        $with->update(['tryon_enabled' => true, 'tryon_overlay' => 'uploads/tryon/x.png']);
        $without = Product::whereKeyNot($with->id)->first();

        $this->get("/product/{$with->slug}")->assertSee('Try it on with your camera')->assertSee('tryOnModal', false);
        $this->get("/product/{$without->slug}")->assertSee('Virtual Try-On not available for this frame')->assertSee($with->name);
        $this->get('/virtual-try-on')->assertOk()->assertSee($with->name)->assertSee('never sent to OpticValley');
    }

    public function test_admin_can_upload_tryon_overlay_and_calibration(): void
    {
        $this->actingAs($this->admin());
        $p = Product::first();
        $this->put("/admin/products/{$p->slug}", [
            'name' => $p->name, 'slug' => $p->slug, 'sku' => $p->sku, 'price' => $p->price, 'stock' => 5, 'low_stock_threshold' => 1, 'gender' => $p->gender,
            'is_active' => 1, 'tryon_enabled' => 1, 'tryon_scale' => 1.1, 'tryon_offset_y' => 0.05,
            'tryon_overlay_file' => UploadedFile::fake()->image('overlay.png', 600, 240),
        ])->assertSessionHas('success');
        $p->refresh();
        $this->assertTrue($p->hasTryOn());
        $this->assertEquals(1.1, $p->tryon_scale);
        $this->assertFileExists(public_path($p->tryon_overlay));
        @unlink(public_path($p->tryon_overlay));

        $this->put("/admin/products/{$p->slug}", ['name' => $p->name, 'slug' => $p->slug, 'sku' => $p->sku, 'price' => $p->price, 'stock' => 5, 'low_stock_threshold' => 1, 'gender' => $p->gender, 'tryon_scale' => 9])->assertSessionHasErrors('tryon_scale');
    }

    public function test_filter_chips_render_with_clear_all(): void
    {
        $this->get('/shop?shape[]=round&rx=1')->assertOk()->assertSee('Remove filter Round', false)->assertSee('Clear all');
    }

    public function test_ga4_setting_validates_and_renders(): void
    {
        $this->actingAs($this->admin())->put('/admin/settings/seo', ['ga4_measurement_id' => 'bad'])->assertSessionHasErrors('ga4_measurement_id');
        $this->put('/admin/settings/seo', ['ga4_measurement_id' => 'G-ABC1234'])->assertSessionHas('success');
        $this->get('/')->assertSee('googletagmanager.com/gtag/js?id=G-ABC1234', false);
    }
}
