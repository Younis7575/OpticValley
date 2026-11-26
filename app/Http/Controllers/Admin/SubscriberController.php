<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.subscribers.index', ['subscribers' => Subscriber::when($request->q, fn ($q, $s) => $q->where('email', 'like', "%$s%"))->latest()->paginate(50)->withQueryString()]);
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber removed.');
    }

    public function export()
    {
        $rows = Subscriber::where('is_active', true)->orderBy('email')->get(['email', 'created_at']);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'subscribed_at']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->email, $r->created_at->toDateTimeString()]);
            }
            fclose($out);
        }, 'subscribers-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }
}
