<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedStore();
    }

    public function test_public_pages_render(): void
    {
        $product = Product::first();
        $urls = ['/', '/shop', '/category/eyeglasses', '/category/mens-sunglasses', '/product/'.$product->slug, '/cart', '/login', '/register',
            '/forgot-password', '/pages/about-us', '/pages/faqs', '/journal', '/journal/'.Post::first()->slug, '/contact', '/track-order', '/brand/solenne'];
        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_home_shows_featured_products_and_seo_tags(): void
    {
        $featured = Product::where('is_featured', true)->first();
        $this->get('/')->assertOk()->assertSee($featured->name)->assertSee('application/ld+json', false)->assertSee('og:title', false);
    }

    public function test_category_includes_descendant_products(): void
    {
        $child = Product::whereHas('category', fn ($q) => $q->where('slug', 'mens-eyeglasses'))->first();
        $this->get('/category/eyeglasses')->assertSee($child->name);
    }

    public function test_filters_and_search(): void
    {
        $this->get('/shop?shape[]=aviator')->assertOk()->assertSee('Maverick Aviator')->assertDontSee('Noor Cat-Eye');
        $this->get('/shop?q=cat-eye')->assertOk()->assertSee('Noor Cat-Eye');
        $this->get('/shop?gender[]=kids')->assertSee('Bubbles Kids Round')->assertDontSee('Maverick Aviator');
        $this->get('/shop?max=3000&sort=price_asc')->assertOk()->assertDontSee('Hayden Round Titanium');
        $this->get('/shop?sale=1')->assertSee('Aurelio Wayfarer')->assertDontSee('Noor Cat-Eye');
    }

    public function test_inactive_product_and_category_are_hidden(): void
    {
        $p = Product::first();
        $p->update(['is_active' => false]);
        $this->get('/product/'.$p->slug)->assertNotFound();
        Category::where('slug', 'reading-glasses')->update(['is_active' => false]);
        $this->get('/category/reading-glasses')->assertNotFound();
    }

    public function test_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')->assertSee('/product/');
        $this->get('/robots.txt')->assertOk();
    }

    public function test_contact_newsletter_and_review_submission(): void
    {
        $this->post('/contact', ['name' => 'Ali', 'email' => 'ali@example.com', 'message' => 'Do you have this frame in blue?'])->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', ['email' => 'ali@example.com']);
        $this->post('/newsletter', ['email' => 'News@Example.com'])->assertSessionHas('success');
        $this->assertDatabaseHas('subscribers', ['email' => 'news@example.com']);

        $p = Product::first();
        $this->post("/product/{$p->slug}/reviews", ['name' => 'Sara', 'rating' => 5, 'body' => 'Lovely frames, great fit.'])->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', ['product_id' => $p->id, 'name' => 'Sara', 'is_approved' => false]);
    }

    public function test_api_catalogue(): void
    {
        $this->getJson('/api/v1/products?category=sunglasses')->assertOk()->assertJsonStructure(['data' => [['name', 'slug', 'final_price', 'images']], 'meta']);
        $this->getJson('/api/v1/products/maverick-aviator')->assertOk()->assertJsonPath('data.name', 'Maverick Aviator');
        $this->getJson('/api/v1/categories')->assertOk();
    }
}
