<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.activity-log.index', [
            'logs' => ActivityLog::with('user')
                ->when($request->search, fn ($q, $s) => $q->where('description', 'like', "%{$s}%"))
                ->when($request->action, fn ($q, $a) => $q->where('action', $a))
                ->latest('created_at')
                ->paginate(30),
        ]);
    }
}
