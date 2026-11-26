<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\LensOption;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreSetupSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put(array_merge([
            'store_name' => 'OpticValley',
            'store_tagline' => 'Eyewear, crafted for clarity.',
            'store_email' => 'opticvalleyglasses@gmail.com',
            'store_phone' => '+92 336 7464897',
            'whatsapp_number' => '923367464897',
            'store_address' => 'Ghalib Market, Gulberg III, Lahore, Punjab 54660, Pakistan',
            'store_hours' => 'Mon – Sat, 11:00 am – 9:00 pm',
            'currency_code' => 'PKR',
            'currency_symbol' => 'Rs.',
            'shipping_flat_rate' => '250',
            'free_shipping_threshold' => '5000',
            'shipping_note' => 'Delivery in 2–5 working days across Pakistan.',
            'tax_rate' => '0',
            'payment_cod_enabled' => '1',
            'payment_bank_enabled' => '1',
            'payment_bank_details' => "Bank: Meezan Bank\nAccount title: OpticValley\nIBAN: (add your IBAN in Admin → Settings → Payments)",
            'payment_online_enabled' => '0',
            'social_instagram' => 'https://instagram.com/',
            'social_facebook' => 'https://facebook.com/',
            'social_tiktok' => '',
            'social_youtube' => '',
            'meta_title' => 'OpticValley — Eyeglasses & Sunglasses Online in Pakistan',
            'meta_description' => 'Shop prescription eyeglasses, sunglasses, blue-light and computer glasses at OpticValley. Premium frames, precision lenses, cash on delivery across Pakistan.',
            'footer_about' => 'OpticValley is a Lahore eyewear house bringing premium frames and precision lenses to every doorstep in Pakistan.',
            'announcement' => 'Free delivery on orders over Rs. 5,000 · Cash on Delivery nationwide',
            'mail_from_address' => 'no-reply@opticvalley.co',
        ], []));

        $tree = [
            'Eyeglasses' => ["Men's Eyeglasses", "Women's Eyeglasses", 'Kids Eyeglasses'],
            'Sunglasses' => ["Men's Sunglasses", "Women's Sunglasses", 'Kids Sunglasses'],
            'Prescription Glasses' => [],
            'Blue Light Blocking Glasses' => [],
            'Computer Glasses' => [],
            'Reading Glasses' => [],
            'Contact Lenses' => [],
            'Accessories' => ['Eyewear Cases', 'Lens Cleaning Kits'],
        ];
        $i = 0;
        foreach ($tree as $parent => $children) {
            $p = Category::updateOrCreate(['slug' => Str::slug($parent)], ['name' => $parent, 'sort_order' => $i++, 'is_active' => true, 'show_in_menu' => true]);
            foreach ($children as $j => $child) {
                Category::updateOrCreate(['slug' => Str::slug(str_replace("'", '', $child))], ['name' => $child, 'parent_id' => $p->id, 'sort_order' => $j, 'is_active' => true]);
            }
        }

        foreach ([
            ['Single Vision (Clear)', 'Clear lenses for everyday distance or near vision.', 0],
            ['Blue Light Filter', 'Reduces digital eye strain from screens.', 1500],
            ['Anti-Glare Premium', 'Multi-coated, scratch resistant, anti-reflective.', 2500],
            ['Photochromic (Transition)', 'Clear indoors, darkens automatically outdoors.', 4500],
            ['Progressive', 'Seamless distance, intermediate and reading zones.', 8500],
        ] as $k => [$name, $desc, $price]) {
            LensOption::updateOrCreate(['name' => $name], ['description' => $desc, 'price' => $price, 'sort_order' => $k, 'requires_prescription' => true, 'is_active' => true]);
        }

        if (! Banner::exists()) {
            Banner::insert([
                ['position' => 'hero', 'eyebrow' => 'The Winter Edit', 'title' => 'See the world in sharper style.', 'subtitle' => 'Handpicked frames with precision lenses, fitted to your prescription and delivered to your door.', 'image' => 'uploads/demo/hero-1.svg', 'button_text' => 'Shop Eyeglasses', 'link' => '/category/eyeglasses', 'theme' => 'light', 'sort_order' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['position' => 'hero', 'eyebrow' => 'Sun Season', 'title' => 'Polarised. Protected. Unmistakably you.', 'subtitle' => 'UV400 sunglasses in iconic shapes, from aviators to cat-eyes.', 'image' => 'uploads/demo/hero-2.svg', 'button_text' => 'Shop Sunglasses', 'link' => '/category/sunglasses', 'theme' => 'dark', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['position' => 'promo', 'eyebrow' => 'Screen time', 'title' => 'Blue Light Glasses', 'subtitle' => 'Less strain, better sleep.', 'image' => 'uploads/demo/promo-1.svg', 'button_text' => 'Explore', 'link' => '/category/blue-light-blocking-glasses', 'theme' => 'light', 'sort_order' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['position' => 'promo', 'eyebrow' => 'Up to 30% off', 'title' => 'The Sale Edit', 'subtitle' => 'Selected frames, limited time.', 'image' => 'uploads/demo/promo-2.svg', 'button_text' => 'Shop Sale', 'link' => '/shop?sale=1', 'theme' => 'dark', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['position' => 'promo', 'eyebrow' => 'Little ones', 'title' => 'Kids Collection', 'subtitle' => 'Flexible, tough, fun.', 'image' => 'uploads/demo/promo-3.svg', 'button_text' => 'Shop Kids', 'link' => '/category/kids-eyeglasses', 'theme' => 'light', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (! MenuItem::exists()) {
            $menus = [
                'header' => [['Eyeglasses', '/category/eyeglasses'], ['Sunglasses', '/category/sunglasses'], ['Blue Light', '/category/blue-light-blocking-glasses'], ['Contact Lenses', '/category/contact-lenses'], ['Accessories', '/category/accessories'], ['Sale', '/shop?sale=1']],
                'footer_shop' => [['Men', '/shop?gender=men'], ['Women', '/shop?gender=women'], ['Kids', '/shop?gender=kids'], ['New Arrivals', '/shop?sort=newest'], ['Best Sellers', '/shop?sort=popular']],
                'footer_help' => [['Track Order', '/track-order'], ['Shipping Policy', '/pages/shipping-policy'], ['Returns & Refunds', '/pages/return-refund-policy'], ['FAQs', '/pages/faqs'], ['Contact Us', '/contact']],
            ];
            foreach ($menus as $loc => $items) {
                foreach ($items as $k => [$label, $url]) {
                    MenuItem::create(['location' => $loc, 'label' => $label, 'url' => $url, 'sort_order' => $k]);
                }
            }
        }

        $store = 'OpticValley';
        $pages = [
            'about-us' => ['About Us', "<h2>Clarity, crafted in Lahore.</h2><p>{$store} began with a simple belief: great eyewear should be precise, beautiful and within reach. We combine carefully selected frames with lab-finished lenses, verified against your prescription before they leave our studio in Gulberg III, Lahore.</p><h3>What we stand for</h3><ul><li><strong>Precision lenses</strong> — every pair is checked by a qualified optician.</li><li><strong>Honest pricing</strong> — premium frames without the boutique mark-up.</li><li><strong>Nationwide delivery</strong> — Cash on Delivery to every city in Pakistan.</li></ul><p>Visit us at Ghalib Market, Gulberg III, or reach us on WhatsApp — we're happy to help you find the perfect fit.</p>"],
            'privacy-policy' => ['Privacy Policy', '<p>We collect only the information needed to process your orders and improve your experience: your name, contact details, delivery address, and (if you choose to provide it) your prescription.</p><h3>How we use your data</h3><ul><li>To fulfil and deliver your orders.</li><li>To contact you about your order status.</li><li>To send newsletters only if you subscribe — you can unsubscribe at any time.</li></ul><h3>Prescription data</h3><p>Prescription details are used solely to manufacture your lenses and are never shared with third parties.</p><h3>Security</h3><p>Passwords are stored encrypted and payment information is never stored on our servers.</p><p>For any privacy questions, contact us at opticvalleyglasses@gmail.com.</p>'],
            'terms-conditions' => ['Terms & Conditions', '<p>By using this website you agree to these terms. Prices are listed in Pakistani Rupees and may change without notice. Orders are confirmed once we verify your details by phone or WhatsApp.</p><h3>Prescription orders</h3><p>You are responsible for the accuracy of the prescription you provide. Custom prescription lenses are made to order.</p><h3>Product images</h3><p>Colours may vary slightly from images due to screen settings.</p><h3>Governing law</h3><p>These terms are governed by the laws of Pakistan.</p>'],
            'shipping-policy' => ['Shipping Policy', '<ul><li>Delivery across Pakistan in 2–5 working days for ready-stock frames.</li><li>Prescription orders need an additional 3–5 working days for lens fitting.</li><li>Flat delivery charge of Rs. 250; free on orders above Rs. 5,000.</li><li>Cash on Delivery is available nationwide.</li><li>You will receive a tracking number by SMS/WhatsApp once your order ships.</li></ul>'],
            'return-refund-policy' => ['Return & Refund Policy', '<p>We want you to love your eyewear. If something is not right, you can request a return within <strong>7 days</strong> of delivery.</p><ul><li>Frames must be unused, in original packaging.</li><li>Custom prescription lenses are non-returnable unless defective or incorrectly made.</li><li>Refunds are processed within 5–7 working days after inspection, via bank transfer.</li><li>Manufacturing defects are covered by a 6-month frame warranty.</li></ul><p>To start a return, contact us on WhatsApp with your order number.</p>'],
            'faqs' => ['Frequently Asked Questions', '<h3>How do I order prescription glasses?</h3><p>Choose a frame marked "Prescription available", select your lens type, then enter your prescription or upload a photo of it.</p><h3>Do you offer Cash on Delivery?</h3><p>Yes, COD is available across Pakistan.</p><h3>How long does delivery take?</h3><p>2–5 working days for ready stock; add 3–5 days for prescription lenses.</p><h3>Can I visit your store?</h3><p>Yes — Ghalib Market, Gulberg III, Lahore.</p><h3>How do I track my order?</h3><p>Use the Track Order page with your order number and phone number.</p>'],
        ];
        foreach ($pages as $slug => [$title, $content]) {
            Page::firstOrCreate(['slug' => $slug], ['title' => $title, 'content' => $content, 'is_active' => true]);
        }
    }
}
