<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /** group => [key => [label, type, rules, help]] */
    public const GROUPS = [
        'store' => ['Store information', [
            'store_name' => ['Store name', 'text', 'required|string|max:80'],
            'store_tagline' => ['Tagline', 'text', 'nullable|string|max:160'],
            'store_email' => ['Store email', 'email', 'required|email', 'Order notifications are sent here.'],
            'store_phone' => ['Phone', 'text', 'nullable|string|max:30'],
            'whatsapp_number' => ['WhatsApp number', 'text', 'nullable|string|max:20', 'International format without +, e.g. 923001234567'],
            'store_address' => ['Address', 'textarea', 'nullable|string|max:300'],
            'store_hours' => ['Opening hours', 'text', 'nullable|string|max:120'],
            'announcement' => ['Announcement bar text', 'text', 'nullable|string|max:200', 'Leave empty to hide the top bar.'],
            'footer_about' => ['Footer about text', 'textarea', 'nullable|string|max:500'],
        ]],
        'currency' => ['Currency & tax', [
            'currency_code' => ['Currency code', 'text', 'required|string|size:3'],
            'currency_symbol' => ['Currency symbol', 'text', 'required|string|max:6'],
            'tax_rate' => ['Tax rate (%)', 'number', 'required|numeric|min:0|max:50', 'Applied to the discounted subtotal. Use 0 if prices already include tax.'],
        ]],
        'shipping' => ['Shipping', [
            'shipping_flat_rate' => ['Flat delivery charge', 'number', 'required|numeric|min:0'],
            'free_shipping_threshold' => ['Free delivery above', 'number', 'required|numeric|min:0', 'Set 0 to disable free delivery.'],
            'shipping_note' => ['Delivery note shown to customers', 'text', 'nullable|string|max:200'],
        ]],
        'payments' => ['Payments', [
            'payment_cod_enabled' => ['Cash on Delivery', 'toggle', 'nullable|boolean'],
            'payment_bank_enabled' => ['Bank transfer / Easypaisa / JazzCash', 'toggle', 'nullable|boolean'],
            'payment_bank_details' => ['Bank account details', 'textarea', 'nullable|string|max:1000', 'Shown at checkout and on the thank-you page.'],
            'payment_online_enabled' => ['Online card payment', 'toggle', 'nullable|boolean', 'Customers are sent a secure payment link by your team. Connect a gateway (e.g. PayFast, Safepay) when merchant credentials are ready.'],
            'payment_online_label' => ['Online payment label', 'text', 'nullable|string|max:60'],
        ]],
        'seo' => ['SEO & social', [
            'meta_title' => ['Default meta title', 'text', 'nullable|string|max:120'],
            'meta_description' => ['Default meta description', 'textarea', 'nullable|string|max:300'],
            'social_instagram' => ['Instagram URL', 'url', 'nullable|url|max:255'],
            'social_facebook' => ['Facebook URL', 'url', 'nullable|url|max:255'],
            'social_tiktok' => ['TikTok URL', 'url', 'nullable|url|max:255'],
            'social_youtube' => ['YouTube URL', 'url', 'nullable|url|max:255'],
        ]],
        'email' => ['Email', [
            'mail_from_address' => ['From address', 'email', 'nullable|email', 'SMTP credentials are configured in the server .env file (MAIL_*), never stored in the database.'],
        ]],
    ];

    public function edit(?string $group = null)
    {
        $group = array_key_exists($group ?? '', self::GROUPS) ? $group : 'store';

        return view('admin.settings', ['groups' => self::GROUPS, 'group' => $group, 'values' => Setting::all_cached()]);
    }

    public function update(Request $request, string $group)
    {
        abort_unless(array_key_exists($group, self::GROUPS), 404);
        $fields = self::GROUPS[$group][1];
        $data = $request->validate(collect($fields)->mapWithKeys(fn ($f, $k) => [$k => $f[2]])->all());
        foreach ($fields as $k => $f) {
            if ($f[1] === 'toggle') {
                $data[$k] = $request->boolean($k) ? '1' : '0';
            }
        }
        Setting::put($data);
        ActivityLog::record('settings.update', null, 'Updated '.self::GROUPS[$group][0].' settings');

        return back()->with('success', 'Settings saved.');
    }
}
