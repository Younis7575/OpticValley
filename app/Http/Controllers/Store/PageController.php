<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function page(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('store.pages.page', compact('page'));
    }

    public function blog()
    {
        return view('store.pages.blog', ['posts' => Post::published()->paginate(9)]);
    }

    public function post(Post $post)
    {
        abort_unless($post->published_at && $post->published_at->isPast(), 404);

        return view('store.pages.post', [
            'post' => $post,
            'more' => Post::published()->whereKeyNot($post->id)->take(3)->get(),
        ]);
    }

    public function tryOn()
    {
        return view('store.pages.tryon', [
            'frames' => Product::active()->where('tryon_enabled', true)->whereNotNull('tryon_overlay')->with(['images', 'brand'])->get(),
        ]);
    }

    public function contact()
    {
        return view('store.pages.contact');
    }

    public function contactSend(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:160',
            'message' => 'required|string|min:10|max:3000',
            'website' => 'prohibited', // honeypot
        ]);
        ContactMessage::create($data);

        return back()->with('success', 'Thanks for reaching out — we will reply within one working day.');
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:160']);
        Subscriber::updateOrCreate(['email' => strtolower($data['email'])], ['is_active' => true]);

        return back()->with('success', 'You are on the list. Welcome to OpticValley!');
    }

    public function track()
    {
        return view('store.pages.track');
    }

    public function trackLookup(Request $request)
    {
        $data = $request->validate(['order_number' => 'required|string|max:30', 'phone' => 'required|string|max:20']);
        $digits = fn ($s) => substr(preg_replace('/\D/', '', $s), -10);
        $order = Order::with(['items', 'histories'])->where('order_number', strtoupper(trim($data['order_number'])))->first();

        if (! $order || $digits($order->customer_phone) !== $digits($data['phone'])) {
            return back()->withInput()->with('error', 'We could not find an order with those details.');
        }

        return view('store.pages.track', ['order' => $order]);
    }

    public function sitemap()
    {
        $urls = collect([route('home'), route('shop'), route('blog.index'), route('contact')]);
        $urls = $urls->merge(Category::active()->get()->map(fn ($c) => route('category.show', $c)))
            ->merge(Brand::active()->get()->map(fn ($b) => route('brand.show', $b)))
            ->merge(Page::where('is_active', true)->get()->map(fn ($p) => route('page.show', $p->slug)))
            ->merge(Post::published()->get()->map(fn ($p) => route('blog.show', $p)));
        $products = Product::active()->with('primaryImage')->get(['id', 'slug', 'name', 'updated_at']);

        return response()->view('store.sitemap', compact('urls', 'products'))->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', 'Disallow: /account', 'Disallow: /cart', 'Disallow: /checkout', 'Allow: /', '', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain');
    }
}
