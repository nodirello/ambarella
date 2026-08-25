<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EcoTask;
use App\Models\EcoTaskCompletion;
use Illuminate\View\View;

/**
 * Public landing for the eco module (journey + stats + tasks preview).
 */
class EcoController extends Controller
{
    public function index(): View
    {
        return view('eco.index', [
            'stats' => [
                'approved_actions' => EcoTaskCompletion::where('status', 'approved')->count(),
                'today_actions' => EcoTaskCompletion::where('status', 'approved')->whereDate('completed_on', today())->count(),
            ],
            'tasks' => EcoTask::active()->orderBy('reward')->take(6)->get(),
        ]);
    }
}
