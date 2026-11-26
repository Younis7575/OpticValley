<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')->when($request->q, fn ($q, $s) => $q->where('description', 'like', "%$s%")->orWhere('action', 'like', "%$s%"))->latest()->paginate(40)->withQueryString();

        return view('admin.activity.index', compact('logs'));
    }
}
